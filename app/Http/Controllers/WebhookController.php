<?php

namespace App\Http\Controllers;

use App\Models\CompulsoryFee;
use App\Models\Fee;
use App\Models\FeesAdvance;
use App\Models\FeesPaid;
use App\Models\OptionalFee;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\School;
use App\Models\User;
use App\Repositories\User\UserInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Razorpay\Api\Api;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Throwable;
use UnexpectedValueException;

class WebhookController extends Controller {

    public function __construct(UserInterface $user) {

    }

    public function stripe() {
        $payload = @file_get_contents('php://input');
        Log::info(PHP_EOL . "----------------------------------------------------------------------------------------------------------------------");
        try {
            // Verify webhook signature and extract the event.
            // See https://stripe.com/docs/webhooks/signatures for more information.
            $data = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);

            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

            $school_id = $data->data->object->metadata->school_id;
            $school = School::on('mysql')->where('id',$school_id)->first();

            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            // You can find your endpoint's secret in your webhook settings
            $paymentConfiguration = PaymentConfiguration::select('webhook_secret_key')->where('payment_method', 'stripe')->where('school_id', $data->data->object->metadata->school_id ?? null)->first();
            $endpoint_secret = $paymentConfiguration['webhook_secret_key'];
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            $metadata = $event->data->object->metadata;
            // Log::info("School ID : ", $metadata['school_id']);




           // Use this lines to Remove Signature verification for debugging purpose
        //    $event = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
        //    $metadata = (array)$event->data->object->metadata;


            //get the current today's date
            $current_date = date('Y-m-d');

            Log::info("Stripe Webhook : ", [$event->type]);

            // handle the events
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentTransactionData = PaymentTransaction::where('id', $metadata['payment_transaction_id'])->first();
                    if ($paymentTransactionData == null) {
                        Log::error("Stripe Webhook : Payment Transaction id not found");
                        break;
                    }

                    if ($paymentTransactionData->status == "succeed") {
                        Log::info("Stripe Webhook : Transaction Already Successes");
                        break;
                    }
                    $fees = Fee::where('id', $metadata['fees_id'])->with(['fees_class_type', 'fees_class_type.fees_type'])->firstOrFail();

                    DB::beginTransaction();
                    try {
                        // Update payment transaction status
                        PaymentTransaction::find($metadata['payment_transaction_id'])->update(['payment_status' => "succeed"]);
                        
                        // Get or create fees_paid record
                        $feesPaidDB = FeesPaid::where([
                            'fees_id'    => $metadata['fees_id'],
                            'student_id' => $metadata['student_id'],
                            'school_id'  => $metadata['school_id']
                        ])->first();

                            // Calculate total amount including any existing payments
                        $totalAmount = !empty($feesPaidDB) ? $feesPaidDB->amount + $paymentTransactionData->amount : $paymentTransactionData->amount;

                            // Prepare fees_paid data
                        $feesPaidData = array(
                            'amount'     => $totalAmount,
                            'date'       => date('Y-m-d', strtotime($current_date)),
                            "school_id"  => $metadata['school_id'],
                            'fees_id'    => $metadata['fees_id'],
                            'student_id' => $metadata['student_id'],
                                'is_fully_paid' => $totalAmount >= $fees->total_compulsory_fees,
                                'is_used_installment' => !empty($metadata['installment_details'])
                            );

                            // Update or create fees_paid record
                            $feesPaidResult = FeesPaid::updateOrCreate(
                                ['id' => $feesPaidDB->id ?? null], 
                                $feesPaidData
                            );

                        if ($metadata['fees_type'] == "compulsory") {
                                $installments = json_decode($metadata['installment_details'], true);
                                if (!empty($installments)) {
                                foreach ($installments as $installment) {
                                    CompulsoryFee::create([
                                        'student_id'             => $metadata['student_id'],
                                        'payment_transaction_id' => $paymentTransactionData->id,
                                        'type'                   => 'Installment Payment',
                                        'installment_id'         => $installment['id'],
                                        'mode'                   => 'Online',
                                        'cheque_no'              => null,
                                        'amount'                 => $installment['amount'],
                                        'due_charges'            => $installment['dueChargesAmount'],
                                        'fees_paid_id'           => $feesPaidResult->id,
                                        'status'                 => "Success",
                                        'date'                   => date('Y-m-d'),
                                        'school_id'              => $metadata['school_id'],
                                    ]);
                                }
                                } else {
                                    // Full payment
                                CompulsoryFee::create([
                                    'student_id'             => $metadata['student_id'],
                                    'payment_transaction_id' => $paymentTransactionData->id,
                                    'type'                   => 'Full Payment',
                                    'installment_id'         => null,
                                    'mode'                   => 'Online',
                                    'cheque_no'              => null,
                                    'amount'                 => $paymentTransactionData->amount,
                                        'due_charges'            => $metadata['dueChargesAmount'] ?? 0,
                                    'fees_paid_id'           => $feesPaidResult->id,
                                    'status'                 => "Success",
                                    'date'                   => date('Y-m-d'),
                                    'school_id'              => $metadata['school_id'],
                                ]);
                            }

                                // Handle advance payment if any
                                if (!empty($metadata['advance_amount']) && $metadata['advance_amount'] > 0) {
                                    $updateCompulsoryFees = CompulsoryFee::where('student_id', $metadata['student_id'])
                                        ->with('fees_paid')
                                        ->whereHas('fees_paid', function ($q) use ($metadata) {
                                    $q->where('fees_id', $metadata['fees_id']);
                                        })
                                        ->orderBy('id', 'DESC')
                                        ->first();

                                    if ($updateCompulsoryFees) {
                                $updateCompulsoryFees->amount += $metadata['advance_amount'];
                                $updateCompulsoryFees->save();

                                FeesAdvance::create([
                                    'compulsory_fee_id' => $updateCompulsoryFees->id,
                                    'student_id'        => $metadata['student_id'],
                                    'parent_id'         => $metadata['parent_id'],
                                    'amount'            => $metadata['advance_amount']
                                ]);
                            }
                        }
                    } else if ($metadata['fees_type'] == "optional") {
                            $optionalFees = json_decode($metadata['optional_fees_id'], true);
                            foreach ($optionalFees as $optionalFee) {
                            OptionalFee::create([
                                'student_id'             => $metadata['student_id'],
                                'payment_transaction_id' => $paymentTransactionData->id,
                                    'fees_class_id'          => $optionalFee['id'],
                                    'amount'                 => $optionalFee['amount'],
                                'fees_paid_id'           => $feesPaidResult->id,
                                    'status'                 => "Success",
                                'date'                   => date('Y-m-d'),
                                'school_id'              => $metadata['school_id'],
                            ]);
                        }
                    }

                    // Send success notification
                    \Log::info("Success Notification in Stripe");
                    $user = User::where('id', $metadata['parent_id'])->first();
                    \Log::info("User ID : ",[$user]);
                    $body = 'Amount :- ' . $paymentTransactionData->amount;
                    $type = 'payment';
                    send_notification([$user->id], 'Fees Payment Successful', $body, $type, ['is_payment_success'=> "true"]);
                        \Log::info("send_notification",[$user->id]);
                    DB::commit();
                        Log::info("Payment processed successfully for transaction ID: " . $metadata['payment_transaction_id']);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("Error processing payment: " . $e->getMessage());
                        throw $e;
                    }
                    break;
                case
                'payment_intent.payment_failed':
                    $paymentTransactionData = PaymentTransaction::find($metadata['payment_transaction_id']);
                    if (!$paymentTransactionData) {
                        Log::error("Stripe Webhook : Payment Transaction id not found --->");
                        break;
                    }

                    PaymentTransaction::find($metadata['payment_transaction_id'])->update(['payment_status' => "0"]);
                    if ($metadata['fees_type'] == "compulsory") {
                        CompulsoryFee::where('payment_transaction_id', $paymentTransactionData->id)->update([
                            'status' => "failed",
                        ]);
                    } else if ($metadata['fees_type'] == "optional") {
                        OptionalFee::where('payment_transaction_id', $paymentTransactionData->id)->update([
                            'status' => "failed",
                        ]);
                    }

                    http_response_code(400);
                    \Log::info("Failed Notification in Stripe");
                    $user = User::where('id', $metadata['parent_id'])->first();
                    \Log::info("User ID : ",[$user]);
                    $body = 'Amount :- ' . $paymentTransactionData->amount;
                    $type = 'payment';
                    send_notification([$user->id], 'Fees Payment Failed', $body, $type,['is_payment_success'=> "false"]);
                    \Log::info("send_notification",[$user->id]);
                    break;
                default:
                    Log::error('Stripe Webhook : Received unknown event type');
            }
        } catch (UnexpectedValueException) {
            // Invalid payload
            echo "Stripe Webhook : Payload Mismatch";
            Log::error("Stripe Webhook : Payload Mismatch");
            http_response_code(400);
            exit();
        } catch (SignatureVerificationException) {
            // Invalid signature
            echo "Stripe Webhook : Signature Verification Failed";
            Log::error("Stripe Webhook : Signature Verification Failed");
            http_response_code(400);
            exit();
        } catch
        (Throwable $e) {
            DB::rollBack();
            Log::error("Stripe Webhook : Error occurred", [$e->getMessage() . ' --> ' . $e->getFile() . ' At Line : ' . $e->getLine()]);
            http_response_code(400);
            exit();
        }
    }

    public function razorpay()
    {
        $webhookBody = file_get_contents('php://input');
        Log::info(PHP_EOL . "----------------------------------------------------------------------------------------------------------------------");
        try {
            // Parse webhook data
            $data = json_decode($webhookBody);
            Log::info("Razorpay Webhook Data:", ['data' => $data]);

            // Extract transaction data from the correct path in payload
            $webhookData = $data->payload->payment->entity;
            $metadata = $data->payload->payment->entity->notes;

            if (!$webhookData) {
                throw new \Exception('No transaction data in webhook payload');
            }

            if (!$metadata) {
                throw new \Exception('No metadata found in webhook payload');
            }

            Log::info("Razorpay Transaction:", ['transaction' => $webhookData]);
            Log::info("Razorpay Metadata:", ['metadata' => $metadata]);

            // Find payment transaction using order_id
            $paymentTransaction = PaymentTransaction::where('order_id', $webhookData->order_id)->first();
            if (!$paymentTransaction) {
                throw new \Exception('Payment transaction not found for order: ' . $webhookData->order_id);
            }

            Log::info("Payment Transaction:", ['transaction' => $paymentTransaction]);

            // Set up database connection for school
            $schoolId = $metadata->school_id;
            $school = School::on('mysql')->where('id', $schoolId)->first();
            if (!$school) {
                throw new \Exception('School not found for ID: ' . $schoolId);
            }

            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            // Get payment configuration
            $paymentConfiguration = PaymentConfiguration::select('webhook_secret_key', 'webhook_public_key')
                ->where('payment_method', 'Razorpay')
                ->where('school_id', $schoolId)
                ->first();

            if (!$paymentConfiguration) {
                throw new \Exception('Payment configuration not found');
            }

            // Verify webhook signature
            $webhookSecret = $paymentConfiguration->webhook_secret_key;
            $signature = $data->signature;
            $payload = $webhookBody;
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if ($signature !== $expectedSignature) {
                throw new \Exception('Invalid webhook signature');
            }

            // Process based on transaction status
            $status = $webhookData->status ?? '';
            \Log::info("Transaction Status : ",[$status]);
            if ($status === 'captured') {
                return $this->handleRazorpaySuccess($paymentTransaction, $webhookData, $metadata);
                // Send success notification
                \Log::info("Success Notification in Razorpay");
                $user = User::where('id', $metadata['parent_id'])->first();
                \Log::info("User ID : ",[$user]);
                $body = 'Amount :- ' . $paymentTransactionData->amount;
                $type = 'payment';
                send_notification([$user->id], 'Fees Payment Successful', $body, $type, ['is_payment_success'=> "true"]);
                \Log::info("send_notification",[$user->id]);
            } else if ($status === 'failed') {
                return $this->handleRazorpayFailed($paymentTransaction, $webhookData, $metadata);
            }

            Log::info("Unhandled transaction status:", ['status' => $status]);
            return response()->json(['status' => 'unhandled_status'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Razorpay Webhook Error:", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function paystack()
    {
        $webhookBody = file_get_contents('php://input');
        Log::info(PHP_EOL . "----------------------------------------------------------------------------------------------------------------------");
        try {
            $data = json_decode($webhookBody, false, 512, JSON_THROW_ON_ERROR);
            Log::info("Paystack Webhook : ", [$data]);

            // Get metadata from the webhook payload
            $metadata = $data->data->metadata;
            $school_id = $metadata->school_id;
            $school = School::on('mysql')->where('id', $school_id)->first();

            // Set up database connection for the school
            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            // Get payment configuration
            $paymentConfiguration = PaymentConfiguration::select('secret_key')
                ->where('payment_method', 'Paystack')
                ->where('school_id', $school_id)
                ->first();

            $webhookSecret = $paymentConfiguration['secret_key'];

            // Verify webhook signature
            $expectedSignature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'];
            $calculatedSignature = hash_hmac('sha512', $webhookBody, $webhookSecret);

            if ($expectedSignature !== $calculatedSignature) {

                // send notification
                \Log::info("Failed Notification in Paystack");
                $user = User::where('id', $metadata->parent_id)->first();
                \Log::info("User ID : ",[$user]);
                $body = 'Amount :- ' . $paymentTransactionData->amount;
                $type = 'payment';
                send_notification([$user->id], 'Fees Payment Failed', $body, $type,['is_payment_success'=>'false']);
                \Log::info("send_notification",[$user->id]);
                throw new SignatureVerificationException('Invalid signature');
            }

            // Get the payment transaction data

            $paymentTransactionData = PaymentTransaction::where('order_id', $data->data->reference)->first();

            

            if (!$paymentTransactionData) {
                // Create a new payment transaction
                // $paymentTransactionData = new PaymentTransaction();
                $paymentTransactionData = PaymentTransaction::create([
                    'user_id' => $data->data->metadata->parent_id,
                    'amount' => $data->data->metadata->total_amount,
                    'payment_gateway' => 'Paystack',
                    'order_id' => $data->data->reference,
                    'payment_status' => 'pending',
                ]);
            }

            $current_date = date('Y-m-d');

            if ($data->event === 'charge.success') {
                Log::info('Payment successful');
                \Log::info("Payment reference :- ".$data->data->reference);
                $paymentTransactionData = PaymentTransaction::where('order_id', $data->data->reference)->first();

                if (!$paymentTransactionData) {
                    Log::error("Paystack Webhook : Payment Transaction id not found");
                    return response()->json(['error' => 'Transaction not found'], 404);
                }

                if ($paymentTransactionData->payment_status === "succeed") {
                    Log::info("Paystack Webhook : Transaction Already Succeed");
                    return response()->json(['status' => 'success'], 200);
                }

                $fees = Fee::where('id', $metadata->fees_id)
                    ->with(['fees_class_type', 'fees_class_type.fees_type'])
                    ->firstOrFail();

                DB::beginTransaction();
                // Update payment transaction status
                PaymentTransaction::where('order_id', $data->data->reference)
                    ->update(['payment_status' => "succeed"]);

                // Get or create fees paid record
                $feesPaidDB = FeesPaid::where([
                    'fees_id' => $metadata->fees_id,
                    'student_id' => $metadata->student_id,
                    'school_id' => $metadata->school_id
                ])->first();

                $totalAmount = !empty($feesPaidDB) 
                    ? $feesPaidDB->amount + $paymentTransactionData->amount 
                    : $paymentTransactionData->amount;

                $feesPaidData = [
                    'amount' => $totalAmount,
                    'date' => $current_date,
                    'school_id' => $metadata->school_id,
                    'fees_id' => $metadata->fees_id,
                    'student_id' => $metadata->student_id,
                ];

                $feesPaidResult = FeesPaid::updateOrCreate(
                    ['id' => $feesPaidDB->id ?? null],
                    $feesPaidData
                );

                if ($metadata->fees_type === "compulsory") {
                    $installments = json_decode($metadata->installment, true, 512, JSON_THROW_ON_ERROR);
                    
                    if (count($installments) > 0) {
                        foreach ($installments as $installment) {
                            CompulsoryFee::create([
                                'student_id' => $metadata->student_id,
                                'payment_transaction_id' => $paymentTransactionData->id,
                                'type' => 'Installment Payment',
                                'installment_id' => $installment['id'],
                                'mode' => 'Online',
                                'cheque_no' => null,
                                'amount' => $installment['amount'],
                                'due_charges' => $installment['dueChargesAmount'],
                                'fees_paid_id' => $feesPaidResult->id,
                                'status' => "Success",
                                'date' => $current_date,
                                'school_id' => $metadata->school_id,
                            ]);
                        }
                    } else if ($metadata->advance_amount == 0) {
                        CompulsoryFee::create([
                            'student_id' => $metadata->student_id,
                            'payment_transaction_id' => $paymentTransactionData->id,
                            'type' => 'Full Payment',
                            'installment_id' => null,
                            'mode' => 'Online',
                            'cheque_no' => null,
                            'amount' => $paymentTransactionData->amount,
                            'due_charges' => $metadata->dueChargesAmount,
                            'fees_paid_id' => $feesPaidResult->id,
                            'status' => "Success",
                            'date' => $current_date,
                            'school_id' => $metadata->school_id,
                        ]);
                    }

                    if ($metadata->advance_amount > 0) {
                        $updateCompulsoryFees = CompulsoryFee::where('student_id', $metadata->student_id)
                            ->with('fees_paid')
                            ->whereHas('fees_paid', function ($q) use ($metadata) {
                                $q->where('fees_id', $metadata->fees_id);
                            })
                            ->orderBy('id', 'DESC')
                            ->first();

                        $updateCompulsoryFees->amount += $metadata->advance_amount;
                        $updateCompulsoryFees->save();

                        FeesAdvance::create([
                            'compulsory_fee_id' => $updateCompulsoryFees->id,
                            'student_id' => $metadata->student_id,
                            'parent_id' => $metadata->parent_id,
                            'amount' => $metadata->advance_amount
                        ]);
                    }

                    $feesPaidResult->is_fully_paid = $totalAmount >= $fees->total_compulsory_fees;
                    $feesPaidResult->is_used_installment = !empty($metadata->installment);
                    $feesPaidResult->save();

                } else if ($metadata->fees_type === "optional") {
                    $optional_fees = json_decode($metadata->optional_fees_id, false, 512, JSON_THROW_ON_ERROR);
                    foreach ($optional_fees as $optional_fee) {
                        OptionalFee::create([
                            'student_id' => $metadata->student_id,
                            'class_id' => $metadata->class_id,
                            'payment_transaction_id' => $paymentTransactionData->id,
                            'fees_class_id' => $optional_fee->id,
                            'mode' => 'Online',
                            'cheque_no' => null,
                            'amount' => $optional_fee->amount,
                            'fees_paid_id' => $feesPaidResult->id,
                            'date' => $current_date,
                            'school_id' => $metadata->school_id,
                            'status' => "Success",
                        ]);
                    }
                }

                // Send notification
                \Log::info("Send Notification in Paystack");
                $user = User::where('id', $metadata->parent_id)->first();
                \Log::info("User ID : ",[$user]);
                $body = 'Amount :- ' . $paymentTransactionData->amount;
                $type = 'payment';
                send_notification([$user->id], 'Fees Payment Successful', $body, $type, ['is_payment_success'=> "true"]);
                \Log::info("send_notification",[$user->id]);
                DB::commit();
                return response()->json(['status' => 'success'], 200);

            } else if ($data->event === 'charge.failed') {
                $paymentTransactionData = PaymentTransaction::where('order_id', $data->data->reference)->first();

                if (!$paymentTransactionData) {
                    Log::error("Paystack Webhook : Payment Transaction id not found");
                    return response()->json(['error' => 'Transaction not found'], 404);
                }

                DB::beginTransaction();

                PaymentTransaction::find($metadata->payment_transaction_id)
                    ->update(['payment_status' => "failed"]);

                if ($metadata->fees_type === "compulsory") {
                    CompulsoryFee::where('payment_transaction_id', $paymentTransactionData->id)
                        ->update(['status' => "failed"]);
                } else if ($metadata->fees_type === "optional") {
                    OptionalFee::where('payment_transaction_id', $paymentTransactionData->id)
                        ->update(['status' => "failed"]);
                }

                // Send notification
                \Log::info("Send Notification in Paystack");
                $user = User::where('id', $metadata->parent_id)->first();
                \Log::info("User ID : ",[$user]);
                if ($user) {
                    $body = 'Amount: ' . $paymentTransactionData->amount;
                    send_notification([$user->id], 'Fees Payment Failed', $body, 'payment', ['is_payment_success' => 'false']);
                    \Log::info("send_notification",[$user->id]);
                }

                DB::commit();
                return response()->json(['status' => 'failed'], 400);
            }

            return response()->json(['status' => 'ignored'], 200);

        } catch (UnexpectedValueException $e) {
            Log::error("Paystack Webhook : Invalid payload", [$e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error("Paystack Webhook : Invalid signature", [$e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Paystack Webhook Error: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function flutterwave()
    {
        $webhookBody = file_get_contents('php://input');
        Log::info(PHP_EOL . "----------------------------------------------------------------------------------------------------------------------");

        try {
           
            $data = json_decode($webhookBody, false, 512, JSON_THROW_ON_ERROR);
            Log::info("Flutterwave Webhook : ", [$data]);
             
            $school_id = $data->meta_data->school_id;
            $school = School::on('mysql')->where('id',$school_id)->first();

            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');
            
            // You can find your endpoint's secret in your webhook settings
            $paymentConfiguration = PaymentConfiguration::select('secret_key','api_key')->where('payment_method', 'flutterwave')->where('school_id', $school_id ?? null)->first();
          
           
            $webhookSecret = $paymentConfiguration['secret_key'];
            $webhookPublic = $paymentConfiguration['api_key'];

         
            $api = new Api($webhookPublic, $webhookSecret);


            //get the current today's date
            $current_date = date('Y-m-d');

            if (isset($data->event) && $data->event == 'charge.completed') {

                Log::info('Payment completed');
               
                //checks the signature
                $expectedSignature = hash_hmac("SHA256", $webhookBody, $webhookSecret);

                $api->utility->verifyWebhookSignature($webhookBody, $expectedSignature, $webhookSecret);
                $paymentTransactionData = PaymentTransaction::where('order_id', $data->data->tx_ref)->first();

                if ($paymentTransactionData == null) {
                    Log::error("Flutterwave Webhook : Payment Transaction id not found");
                }

                if ($paymentTransactionData->status == "succeed") {
                    Log::info("Flutterwave Webhook : Transaction Already Succeed");
                }
                $fees = Fee::where('id', $data->meta_data->fees_id)->with(['fees_class_type', 'fees_class_type.fees_type'])->firstOrFail();

                DB::beginTransaction();
                PaymentTransaction::where('id', $paymentTransactionData->id)->update(['payment_status' => "succeed"]);   
                $feesPaidDB = FeesPaid::where([
                    'fees_id'    => $data->meta_data->fees_id,
                    'student_id' => $data->meta_data->student_id,
                    'school_id'  => $data->meta_data->school_id
                ])->first();

                // Check if Fees Paid Exists Then Add The optional Fees Amount with Fess Paid Amount
                $totalAmount = !empty($feesPaidDB) ? $feesPaidDB->amount + $data->data->amount : $data->data->amount;
                // Fees Paid Array
                $feesPaidData = array(
                    'amount'     => $totalAmount,
                    'date'       => date('Y-m-d', strtotime($current_date)),
                    "school_id"  => $data->meta_data->school_id,
                    'fees_id'    => $data->meta_data->fees_id,
                    'student_id' => $data->meta_data->student_id,
                );

                $feesPaidResult = FeesPaid::updateOrCreate(['id' => $feesPaidDB->id ?? null], $feesPaidData);

                if ($data->meta_data->fees_type == "compulsory") {
                    $installments = json_decode($data->meta_data->installment, true, 512, JSON_THROW_ON_ERROR);
                    if (count($installments) > 0) {
                        foreach ($installments as $installment) {  \Log::info("data.meta_data.dueChargesAmount - ");
                            \Log::info([$data->meta_data]);
                        
                            CompulsoryFee::create([
                                'student_id'             => $data->meta_data->student_id,
                                'payment_transaction_id' => $paymentTransactionData->id,
                                'type'                   => 'Installment Payment',
                                'installment_id'         => $installment['id'],
                                'mode'                   => 'Online',
                                'cheque_no'              => null,
                                'amount'                 => $installment['amount'],
                                'due_charges'            => $data->meta_data->dueChargesAmount ?? 0,
                                'fees_paid_id'           => $feesPaidResult->id,
                                'status'                 => "Success",
                                'date'                   => date('Y-m-d'),
                                'school_id'              => $data->meta_data->school_id,
                            ]);
                        }
                    } else if ($data->meta_data->advance_amount == 0) {
                      
                        
                        CompulsoryFee::create([
                            'student_id'             => $data->meta_data->student_id,
                            'payment_transaction_id' => $paymentTransactionData->id,
                            'type'                   => 'Full Payment',
                            'installment_id'         => null,
                            'mode'                   => 'Online',
                            'cheque_no'              => null,
                            'amount'                 => $paymentTransactionData->amount,
                            'due_charges'            => $data->meta_data->dueChargesAmount ?? 0,
                            'fees_paid_id'           => $feesPaidResult->id,
                            'status'                 => "Success",
                            'date'                   => date('Y-m-d'),
                            'school_id'              => $data->meta_data->school_id,
                        ]);
                    }

                    // Add advance amount in installment
                    if ($data->meta_data->advance_amount > 0) {
                        $updateCompulsoryFees = CompulsoryFee::where('student_id', $data->meta_data->student_id)->with('fees_paid')->whereHas('fees_paid', function ($q) use ($data) {
                            $q->where('fees_id', $data->meta_data->fees_id);
                        })->orderBy('id', 'DESC')->first();

                        $updateCompulsoryFees->amount += $data->meta_data->advance_amount;
                        $updateCompulsoryFees->save();

                        FeesAdvance::create([
                            'compulsory_fee_id' => $updateCompulsoryFees->id,
                            'student_id'        => $data->meta_data->student_id,
                            'parent_id'         => $data->meta_data->parent_id,
                            'amount'            => $data->meta_data->advance_amount
                        ]);
                    }
                    $feesPaidResult->is_fully_paid = $totalAmount >= $fees->total_compulsory_fees;
                    $feesPaidResult->is_used_installment = !empty($data->meta_data->installment);
                    $feesPaidResult->save();

                } else if ($data->meta_data->fees_type == "optional") {
                    $optional_fees = json_decode($data->data->meta_data->optional_fees_id, false, 512, JSON_THROW_ON_ERROR);
                    foreach ($optional_fees as $optional_fee) {
                        OptionalFee::create([
                            'student_id'             => $data->data->meta_data->student_id,
                            'class_id'               => $data->data->meta_data->class_id,
                            'payment_transaction_id' => $paymentTransactionData->id,
                            'fees_class_id'          => $optional_fee->id,
                            'mode'                   => 'Online',
                            'cheque_no'              => null,
                            'amount'                 => $optional_fee->amount,
                            'fees_paid_id'           => $feesPaidResult->id,
                            'date'                   => date('Y-m-d'),
                            'school_id'              => $data->data->meta_data->school_id,
                            'status'                 => "Success",
                        ]);
                    }
                }

                Log::info("payment_intent.succeeded called successfully");
                \Log::info("Send Notification in Flutterwave");
                $user = User::where('id', $data->meta_data->parent_id)->first();
                \Log::info("User ID : ",[$user]);
                $body = 'Amount :- ' . $paymentTransactionData->amount;
                $type = 'payment';
                send_notification([$user->id], 'Fees Payment Successful', $body, $type, ['is_payment_success'=> 'true']);
                \Log::info("send_notification",[$user->id]);
                http_response_code(200);
                DB::commit();
               
            } elseif (isset($data->event) && $data->event == 'charge.failed') {
                $paymentTransactionData = PaymentTransaction::find($data->data->id);
                if (!$paymentTransactionData) {
                    Log::error("Flutterwave Webhook : Payment Transaction id not found --->");
                }

                PaymentTransaction::find($data->data->id)->update(['payment_status' => "failed"]);
                if ($data->data->meta_data->fees_type == "compulsory") {
                    CompulsoryFee::where('payment_transaction_id', $paymentTransactionData->id)->update([
                        'status' => "failed",
                    ]);
                } else if ($data->data->meta_data->fees_type == "optional") {
                    OptionalFee::where('payment_transaction_id', $paymentTransactionData->id)->update([
                        'status' => "failed",
                    ]);
                }

                http_response_code(400);
                \Log::info("Failed Notification in Flutterwave");
                $user = User::where('id', $data->data->meta_data->parent_id)->first();
                \Log::info("User ID : ",[$user]);
                $body = 'Amount :- ' . $paymentTransactionData->amount;
                $type = 'payment';
                send_notification(  [$user->id], 'Fees Payment Failed', $body, $type,['is_payment_success'=>'false']);
                \Log::info("send_notification",[$user->id]);
            } elseif (isset($data->event) && $data->event == 'charge.authorized') {
                http_response_code(200);
            }
            else {
                Log::error('Flutterwave Webhook : Received unknown event type');
            }
        }catch (UnexpectedValueException) {
            // Invalid payload
            echo "Flutterwave Webhook : Payload Mismatch";
            Log::error("Flutterwave  : Payload Mismatch");
            http_response_code(400);
            exit();
        } catch (SignatureVerificationException) {
            // Invalid signature
            echo "Flutterwave  Webhook : Signature Verification Failed";
            Log::error("Flutterwave  Webhook : Signature Verification Failed");
            http_response_code(400);
            exit();
        } catch(Throwable $e) {
            DB::rollBack();
            Log::error("Flutterwave Webhook : Error occurred", [$e->getMessage() . ' --> ' . $e->getFile() . ' At Line : ' . $e->getLine()]);
            http_response_code(400);
            exit();
        }
    }


    private function handleRazorpaySuccess($paymentTransaction, $webhookData, $metadata)
    {
        if ($paymentTransaction->status === "succeed") {
            Log::info("Transaction already processed successfully");
            return response()->json(['status' => 'success', 'message' => 'Transaction already processed']);
        }

        DB::beginTransaction();
        try {
            // Update payment transaction status
            $paymentTransaction->payment_status = "succeed";
            $paymentTransaction->save();

            // Get fees details
            $fees = Fee::where('id', $paymentTransaction->fees_id)
                ->with(['fees_class_type', 'fees_class_type.fees_type'])
                ->firstOrFail();

            // Update fees paid record
            $feesPaidDB = FeesPaid::where([
                'fees_id'    => $paymentTransaction->fees_id,
                'student_id' => $metadata->student_id,
                'school_id'  => $metadata->school_id
            ])->first();

            // Convert amount to integer
            $amount = (int)$webhookData->amount / 100; // Razorpay amount is in paise

            $totalAmount = !empty($feesPaidDB) ? 
                $feesPaidDB->amount + $amount : 
                $amount;

            $feesPaidData = [
                'amount'     => $totalAmount,
                'date'       => date('Y-m-d'),
                'school_id'  => $metadata->school_id,
                'fees_id'    => $paymentTransaction->fees_id,
                'student_id' => $metadata->student_id,
                'is_fully_paid' => $totalAmount >= $fees->total_compulsory_fees,
                'is_used_installment' => !empty($paymentTransaction->installment_details)
            ];

            $feesPaidResult = FeesPaid::updateOrCreate(
                ['id' => $feesPaidDB->id ?? null],
                $feesPaidData
            );

            // Process fees based on type
            if ($paymentTransaction->fees_type == "compulsory") {
                $this->processCompulsoryFees($paymentTransaction, $feesPaidResult, $metadata);
            } else if ($paymentTransaction->fees_type == "optional") {
                $this->processOptionalFees($paymentTransaction, $feesPaidResult, $metadata);
            }

            // Send success notification
            $user = User::find($metadata->parent_id);
            if ($user) {
                $body = 'Amount: ' . $amount;
                send_notification([$user->id], 'Fees Payment Successful', $body, 'payment', ['is_payment_success' => 'true']);
            }

            DB::commit();
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleRazorpayFailed($paymentTransaction, $webhookData, $metadata)
    {
        DB::beginTransaction();
        try {
            $paymentTransaction->payment_status = "failed";
            $paymentTransaction->save();

            if ($paymentTransaction->fees_type == "compulsory") {
                CompulsoryFee::where('payment_transaction_id', $paymentTransaction->id)
                    ->update(['status' => "failed"]);
            } else if ($paymentTransaction->fees_type == "optional") {
                OptionalFee::where('payment_transaction_id', $paymentTransaction->id)
                    ->update(['status' => "failed"]);
            }

            // Send failure notification
            $user = User::find($metadata->parent_id);
            if ($user) {
                $body = 'Amount: ' . ((int)$webhookData->amount / 100);
                send_notification([$user->id], 'Fees Payment Failed', $body, 'payment', ['is_payment_success' => 'false']);
            }

            DB::commit();
            return response()->json(['status' => 'failed'], 400);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }   

    // public function flutterwave(Request $request)
    // {
    //     $body = $request->getContent();
    //     $body = file_get_contents('php://input');
    //     $data = json_decode($body);
     
    //     $signature = (isset($_SERVER['FLW_SECRET_HASH'])) ? $_SERVER['FLW_SECRET_HASH'] : '';
    //     // Your secret hash from environment variables
    //     $secretHash = env('FLW_SECRET_HASH');
        
    //     if( $signature !==  $secretHash ){
    //         exit();
    //     }
    
    //     // Retrieve the payment details
    //     $transactionId = $data->id;
    //     $status = $data->status;
    //     $metadata = $data->meta_data ?? [];
    //     $current_date = Carbon::now()->format('Y-m-d');
    //     $payment_transaction_id = $metadata->payment_transaction_id ?? null;
    //     $student_id = $metadata->student_id;
    //     $class_id = $metadata->class_id;
    //     $parent_id = $metadata->parent_id;
    //     $session_year_id = $metadata->session_year_id;
    //     $is_fully_paid = $metadata->is_fully_paid;
    //     $type_of_fee = $metadata->type_of_fee;
    //     $is_due_charges = $metadata->is_due_charges ?? 0;
    //     $due_charges = $metadata->due_charges ?? '';
    //     $optional_paid_data =  $metadata->optional_fees_paid ?? [];
    //     $installment_paid_data = $metadata->installment_fees_paid ?? [];
    
    //     if (!$payment_transaction_id) {
    //         Log::warning('Payment transaction ID not found in metadata');
    //         return response()->json(['status' => 'error', 'message' => 'Payment transaction ID missing'], 400);
    //     }
    
    //     $transactionDb = PaymentTransaction::find($payment_transaction_id);
    
    //     if (!$transactionDb) {
    //         Log::error('Payment transaction not found in database');
    //         return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
    //     }
    //     $transaction_db = PaymentTransaction::find($payment_transaction_id);
    //     if (!empty($transaction_db)) {
    //         Log::error("INSIDE TRANSACTION DB");
    //         if ($transaction_db->status != 1 && $status == "successful") {
    //             Log::error("INSIDE TRANSACTION DB STATUS");
    //             //get the total amount from table
    //             $total_amount = $transaction_db->total_amount;

    //             //udpate the values in payment transaction
    //             $transaction_db->order_id = $transactionId;
    //             $transaction_db->payment_status = 1;
    //             $transaction_db->save();

    //             // Add due charges of fully Paid Complusory Amount
    //             if ($type_of_fee == 0 && $is_due_charges == 1) {
    //                 $add_due_charges = new FeesChoiceable();
    //                 $add_due_charges->student_id = $student_id;
    //                 $add_due_charges->class_id = $class_id;
    //                 $add_due_charges->is_due_charges = 1;
    //                 $add_due_charges->total_amount = $due_charges;
    //                 $add_due_charges->session_year_id = $session_year_id;
    //                 $add_due_charges->save();
    //             }

    //             if(isset($installment_paid_data) && !empty($installment_paid_data)){
    //                 Log::info("Paid Installment Fee Status Updated");
    //                 foreach($installment_paid_data as $row)
    //                 {
    //                     $db =  PaidInstallmentFee::find($row);
    //                     if(!empty($db))
    //                     {
    //                         if($db->status != 1)
    //                         {
    //                             $db->status = 1;
    //                             $db->save();
    //                         }
                          
    //                     }
    //                 }

    //             }else{
    //                 Log::info('NO INSTALLMENT DATA');
    //             }

    //             if(isset($optional_paid_data) && !empty($optional_paid_data)){
    //                 Log::info("Optional Fees Status Updated");
    //                 foreach($optional_paid_data as $row)
    //                 {
    //                     $db =  FeesChoiceable::find($row);
    //                     if(!empty($db))
    //                     {
    //                         if($db->status != 1)
    //                         {
    //                             $db->status = 1;
    //                             $db->save();
    //                         }
    //                         // Log::error("FeesChoiceable status updated", ['id' => $db->id, 'status' => $db->status]);
    //                     }
    //                 }
    //             }else{
    //                 Log::info('NO OPTIONAL DATA');
    //             }

    //             // add data in fees paid table local
    //             $update_fees_paid_query = FeesPaid::where(['student_id'=> $student_id, 'class_id' => $class_id , 'session_year_id' => $session_year_id]);
    //             if($update_fees_paid_query->count()){
    //                 $update_fee_paid_data = FeesPaid::findOrFail($update_fees_paid_query->first()->id);
    //                 $update_fee_paid_data->total_amount = ($update_fees_paid_query->first()->total_amount + $total_amount);
    //                 $update_fee_paid_data->is_fully_paid = $is_fully_paid;
    //                 $update_fee_paid_data->save();
    //             }else{
    //                 $fees_paid_db = new FeesPaid();
    //                 $fees_paid_db->parent_id = $parent_id;
    //                 $fees_paid_db->student_id = $student_id;
    //                 $fees_paid_db->class_id = $class_id;
    //                 $fees_paid_db->payment_transaction_id = $payment_transaction_id ?? null;
    //                 $fees_paid_db->mode = 2;
    //                 $fees_paid_db->total_amount = $total_amount;
    //                 $fees_paid_db->date = $current_date;
    //                 $fees_paid_db->session_year_id = $session_year_id;
    //                 $fees_paid_db->is_fully_paid = $is_fully_paid;
    //                 $fees_paid_db->save();
    //             }

    //             http_response_code(200);

    //             $user = Parents::where('id', $parent_id)->pluck('user_id');
    //             $body = 'Amount :- ' . $total_amount;
    //             $type = 'online';
    //             $image = null;
    //             $userinfo = null;

    //             $notification = new Notification();
    //             $notification->send_to = 2;
    //             $notification->title = 'Payment Success';
    //             $notification->message = $body;
    //             $notification->type = $type;
    //             $notification->date = Carbon::now();
    //             $notification->is_custom = 0;
    //             $notification->save();
    //             foreach($user as $data)
    //             {
    //                 $user_notification = new UserNotification();
    //                 $user_notification->notification_id = $notification->id;
    //                 $user_notification->user_id = $data;
    //                 $user_notification->save();
    //             }

    //             send_notification($user, 'Payment Success', $body, $type, $image, $userinfo);

    //             Log::info("Payment Successfull");
    //         }else{
    //             Log::error("Transaction Already Successed --->");
    //             return false;
    //         }
    //         if($transaction_db->status != 1 && $status == "failed"){
    //             $transaction_db = PaymentTransaction::find($payment_transaction_id);
    //             if (!empty($transaction_db)) {
    //                 $total_amount = $transaction_db->total_amount;
    //                 $transaction_db->payment_id = null;
    //                 $transaction_db->payment_status = 0;
    //                 $transaction_db->save();
    //                 http_response_code(400);

    //                 FeesChoiceable::where('payment_transaction_id',$payment_transaction_id)->where('status',0)->delete();
    //                 PaidInstallmentFee::where('payment_transaction_id',$payment_transaction_id)->where('status',0)->delete();

    //                 $user = Parents::where('id', $parent_id)->pluck('user_id');
    //                 $body = 'Amount :- ' . $total_amount;
    //                 $type = 'online';
    //                 $image = null;
    //                 $userinfo = null;

    //                 $notification = new Notification();
    //                 $notification->send_to = 2;
    //                 $notification->title = 'Payment Failed';
    //                 $notification->message = $body;
    //                 $notification->type = $type;
    //                 $notification->date = Carbon::now();
    //                 $notification->is_custom = 0;
    //                 $notification->save();

    //                 foreach($user as $data)
    //                 {
    //                     $user_notification = new UserNotification();
    //                     $user_notification->notification_id = $notification->id;
    //                     $user_notification->user_id = $data;
    //                     $user_notification->save();
    //                 }
    //                 send_notification($user, 'Payment Failed', $body, $type, $image, $userinfo);
    //             }else{
    //                 Log::error("Payment Transaction id not found --->");
    //                 return false;
    //             }
    //         }
    //     } else {
    //         Log::error("Payment Transaction id not found --->");
    //         return false;
    //     }
        
    // }
    
    // public function paystackSuccessCallback(){
    //     $response = array(
    //         'error' => false,
    //         'message' => "Payment Successfully Completed",
    //         'code' => 200,
    //     );
    //     return response()->json($response);
    // }

    // public function flutterwaveSuccessCallback(){
    //     $response = array(
    //         'error' => false,
    //         'message' => "Payment Successfully Completed",
    //         'code' => 200,
    //     );
    //     return response()->json($response);
    // }

}