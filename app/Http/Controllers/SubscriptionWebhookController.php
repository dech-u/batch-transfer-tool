<?php

namespace App\Http\Controllers;

use App\Models\AddonSubscription;
use App\Models\Package;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionBill;
use App\Models\SubscriptionFeature;
use App\Repositories\AddonSubscription\AddonSubscriptionInterface;
use App\Repositories\PaymentTransaction\PaymentTransactionInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Repositories\SubscriptionFeature\SubscriptionFeatureInterface;
use App\Services\CachingService;
use App\Services\SubscriptionService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Stripe\Exception\SignatureVerificationException;
use Throwable;
use UnexpectedValueException;
use Exception;
use App\Models\School;
use Illuminate\Support\Facades\Config;

class SubscriptionWebhookController extends Controller
{
    //
    private CachingService $cache;
    private PaymentTransactionInterface $paymentTransaction;
    private SubscriptionService $subscriptionService;
    private SubscriptionInterface $subscription;
    private SubscriptionFeatureInterface $subscriptionFeature;
    private AddonSubscriptionInterface $addonSubscription;

    public function __construct(CachingService $cachingService, PaymentTransactionInterface $paymentTransaction, SubscriptionService $subscriptionService, SubscriptionInterface $subscription, SubscriptionFeatureInterface $subscriptionFeature, AddonSubscriptionInterface $addonSubscription)
    {
        $this->cache = $cachingService;
        $this->paymentTransaction = $paymentTransaction;
        $this->subscriptionService = $subscriptionService;

        $this->subscription = $subscription;
        $this->subscriptionFeature = $subscriptionFeature;
        $this->addonSubscription = $addonSubscription;
    }

    public function stripe(Request $request)
    {
        DB::setDefaultConnection('mysql');
        $systemSettings = PaymentConfiguration::where('school_id',NULL)->where('payment_method','Stripe')->first();
        $endpoint_secret = $systemSettings->webhook_secret_key;
        
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            Log::info("Stripe Webhook Event: " . json_encode($event));
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            Log::error("Payload Mismatch");
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error("Signature Verification Failed");
            http_response_code(400);
            exit();
        }
        
        $transaction_id = $event->data->object->id;
        $paymentTransaction = PaymentTransaction::where('order_id',$transaction_id)->first();
        if ($paymentTransaction) {
            $transaction_id = $paymentTransaction->id;
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    Log::error($transaction_id);
                    $paymentTransactionData = $this->paymentTransaction->findById($transaction_id);
                    if (!empty($paymentTransactionData)) {
                        if ($paymentTransactionData->status != 1) {
                            $school_id = $paymentTransactionData->school_id;
    
                            $this->paymentTransaction->update($transaction_id,['payment_status' => "succeed",'school_id' => $school_id]);
                            Log::error("Payment Success");
                        }else{
                            Log::error("Transaction Already Successes --->");
                            break;
                        }
                    }else {
                        Log::error("Payment Transaction id not found --->");
                        break;
                    }
                    http_response_code(200);
                    break;
    
                case 'payment_intent.payment_failed':
                    $paymentTransactionData = $this->paymentTransaction->findById($transaction_id);
                    if (!empty($paymentTransactionData)) {
                        if ($paymentTransactionData->status != 1) {
                            $school_id = $paymentTransactionData->school_id;
    
                            $this->paymentTransaction->update($transaction_id,['payment_status' => "failed",'school_id' => $school_id]);
                            http_response_code(400);
                            break;
                        }
                    }else {
                        Log::error("Payment Transaction id not found --->");
                        break;
                    }
                case 'charge.succeeded':
                    Log::error($transaction_id);
                    $paymentTransactionData = $this->paymentTransaction->findById($transaction_id);
                    if (!empty($paymentTransactionData)) {
                        if ($paymentTransactionData->status != 1) {
                            $school_id = $paymentTransactionData->school_id;
    
                            $this->paymentTransaction->update($transaction_id,['payment_status' => "succeed",'school_id' => $school_id]);
                        }else{
                            Log::error("Transaction Already Successes --->");
                            break;
                        }
                    }else {
                        Log::error("Payment Transaction id not found --->");
                        break;
                    }
                    http_response_code(200);
                    break;
                default:
                    // Unexpected event type
                    Log::error('Received unknown event type');
            }
        }

        

        // End Stripe
    }

    public function razorpay(Request $request)
    {

        Log::info('Called');
        $webhookBody = file_get_contents('php://input');
        try {
            $data = json_decode($webhookBody, false, 512, JSON_THROW_ON_ERROR);
            Log::info("Razorpay Webhook Data : ", [$data]);

            $payload = $request->all();
            // Log::info("Payload", $payload);
            $data = (object)$payload;
            $metadata = $data->payload['payment']['entity']['notes'];

            // You can find your endpoint's secret in your webhook settings
            DB::setDefaultConnection('mysql');
            $paymentConfiguration = PaymentConfiguration::select('webhook_secret_key')->where('payment_method', 'razorpay')->where('school_id', null)->first();
          
            $webhookSecret = $paymentConfiguration['webhook_secret_key'];
            $webhookPublic = $paymentConfiguration["webhook_public_key"];


            $api = new Api($webhookPublic, $webhookSecret);
            $new_subscription = '';

            // $metadata = $data->payload->payment->entity->notes;
            Log::info($metadata);
            $metadata = json_decode(json_encode($metadata));
            

            if ($metadata && isset($data->event) && $data->event == 'payment.captured') {

                //checks the signature
                // $expectedSignature = hash_hmac("SHA256", $webhookBody, $webhookSecret);
                // $api->utility->verifyWebhookSignature($webhookBody, $expectedSignature, $webhookSecret);
                $paymentTransactionData = PaymentTransaction::where('id', $metadata->payment_transaction_id)->first();
                
                if ($paymentTransactionData == null) {
                    Log::error("Razorpay Webhook : Payment Transaction id not found");
                }

                if ($paymentTransactionData && $paymentTransactionData->status == "succeed") {
                    Log::info("Razorpay Webhook : Transaction Already Succeed");
                } else {
                    DB::beginTransaction();
                    $paymentTransactionStatus = PaymentTransaction::find($metadata->payment_transaction_id);
                    if ($paymentTransactionStatus) {
                        $paymentTransactionStatus->payment_status = "succeed";
                        $paymentTransactionStatus->save();
                    }
                    
                    // Addon
                    if ($metadata->type == 'addon') {
                        
                        $addon_data = [
                            'subscription_id' => $metadata->subscription_id,
                            'school_id' => $metadata->school_id,
                            'feature_id' => $metadata->feature_id,
                            'price' => $metadata->amount,
                            'start_date' => Carbon::now(),
                            'end_date' => $metadata->end_date,
                            'status' => 1,
                            'payment_transaction_id' => $metadata->payment_transaction_id,
                        ];

                        AddonSubscription::create($addon_data);
                    }
                    
                    // Package
                    if ($metadata->type == 'package') {
                        // New package subscription
                        if ($metadata->package_type == 'new') {
                            $new_subscription = $this->subscriptionService->createSubscription($metadata->package_id, $metadata->school_id, null, 1);
                        }

                        // Upcoming prepaid plan
                        if ($metadata->package_type == 'upcoming') {

                            $active_plan = $this->subscriptionService->active_subscription($metadata->school_id);
                            $package = Package::find($metadata->package_id);

                            $start_date = Carbon::parse($active_plan->end_date)->addDays()->format('Y-m-d');
                            $end_date = Carbon::parse($start_date)->addDays(($package->days - 1))->format('Y-m-d');

                            $subscription_data = [
                                'package_id'     => $package->id,
                                'name'           => $package->name,
                                'student_charge' => $package->student_charge,
                                'staff_charge'   => $package->staff_charge,
                                'start_date'     => $start_date,
                                'end_date'       => $end_date,
                                'package_type'   => $package->type,
                                'no_of_students' => $package->no_of_students,
                                'no_of_staffs'   => $package->no_of_staffs,
                                'billing_cycle'  => $package->days,
                                'school_id'      => $metadata->school_id,
                                'charges'        => $package->charges
                            ];

                            if ($active_plan->id == $metadata->subscription_id) {
                                // Same upcoming plan
                                $new_subscription = Subscription::create($subscription_data);

                            } else {
                                // Already set, update records
                                $new_subscription = Subscription::find($metadata->subscription_id)->update($subscription_data);
                                $new_subscription = Subscription::find($metadata->subscription_id);
                            }

                            // Add features
                            $subscription_features = array();
                            foreach ($new_subscription->package->package_feature as $key => $feature) {
                                $subscription_features[] = [
                                    'subscription_id' => $new_subscription->id,
                                    'feature_id'      => $feature->feature_id
                                ];
                            }
                            SubscriptionFeature::upsert($subscription_features, ['subscription_id', 'feature_id'], ['subscription_id', 'feature_id']);

                            // Generate bill
                            $systemSettings = $this->cache->getSystemSettings();

                            $subscription_bill = [
                                'subscription_id'        => $new_subscription->id,
                                'amount'                 => $new_subscription->charges,
                                'total_student'          => 0,
                                'total_staff'            => 0,
                                'due_date'               => Carbon::now()->addDays($systemSettings['additional_billing_days'])->format('Y-m-d'),
                                'school_id'              => $metadata->school_id,
                                'payment_transaction_id' => $metadata->payment_transaction_id
                            ];
                            // Create bill for active plan
                            SubscriptionBill::create($subscription_bill);

                        }

                        // Immediate change current package
                        if ($metadata->package_type == 'immediate') {
                            // Create current subscription bill
                            // Get current plan
                            $subscription = $this->subscriptionService->active_subscription($metadata->school_id);

                            // Postpaid plan generate bill
                            if ($subscription->package_type == 1) {
                                // Create current subscription plan bill
                                $this->subscriptionService->createSubscriptionBill($subscription, null);
                            }
                            $current_subscription_expiry = Subscription::find($subscription->id)->update(['end_date' => Carbon::now()->format('Y-m-d')]);
                            $current_subscription_expiry = Subscription::find($subscription->id);
                            Log::info('I am here................');
                            $this->subscriptionFeature->builder()->where('subscription_id', $subscription->id)->delete();

                            // Delete upcoming
                            $this->subscription->builder()->with('package')->doesntHave('subscription_bill')->whereDate('start_date', '>', $subscription->end_date)->delete();

                            // Delete addons
                            $addons = $this->addonSubscription->builder()->where('subscription_id', $subscription->id)->get();

                            $soft_delete_addon = array();
                            foreach ($addons as $key => $addon) {
                                AddonSubscription::find($addon->id)->update(['end_date' => $current_subscription_expiry->end_date]);
                                $soft_delete_addon[] = $addon->id;
                            }

                            $this->addonSubscription->builder()->whereIn('id', $soft_delete_addon)->delete();

                            // Set new plan
                            $package = Package::find($metadata->package_id);
                            $start_date = Carbon::now();
                            $end_date = Carbon::now()->addDays(($package->days - 1))->format('Y-m-d');
                            $subscription_data = [
                                'package_id'     => $package->id,
                                'name'           => $package->name,
                                'student_charge' => $package->student_charge,
                                'staff_charge'   => $package->staff_charge,
                                'start_date'     => $start_date,
                                'end_date'       => $end_date,
                                'package_type'   => $package->type,
                                'no_of_students' => $package->no_of_students,
                                'no_of_staffs'   => $package->no_of_staffs,
                                'billing_cycle'  => $package->days,
                                'school_id'      => $metadata->school_id,
                                'charges'        => $package->charges
                            ];

                            $new_subscription = Subscription::create($subscription_data);

                            // Add features
                            $subscription_features = array();
                            foreach ($new_subscription->package->package_feature as $key => $feature) {
                                $subscription_features[] = [
                                    'subscription_id' => $new_subscription->id,
                                    'feature_id'      => $feature->feature_id
                                ];
                            }
                            SubscriptionFeature::upsert($subscription_features, ['subscription_id', 'feature_id'], ['subscription_id', 'feature_id']);

                            // Generate bill
                            $systemSettings = $this->cache->getSystemSettings();

                            $subscription_bill = [
                                'subscription_id'        => $new_subscription->id,
                                'amount'                 => $new_subscription->charges,
                                'total_student'          => 0,
                                'total_staff'            => 0,
                                'due_date'               => Carbon::now()->addDays($systemSettings['additional_billing_days'])->format('Y-m-d'),
                                'school_id'              => $metadata->school_id,
                                'payment_transaction_id' => $metadata->payment_transaction_id
                            ];
                            // Create bill for active plan
                            SubscriptionBill::create($subscription_bill);
                        }

                        if ($new_subscription && $metadata->package_type != 'upcoming' && $metadata->package_type != 'immediate') {
                            if ($new_subscription->subscription_bill && $new_subscription->subscription_bill->id) {
                                SubscriptionBill::find($new_subscription->subscription_bill->id)->update(['payment_transaction_id' => $metadata->payment_transaction_id]);
                            }
                        }
                        if ($metadata->subscription_id && $metadata->package_type != 'upcoming' && $metadata->package_type != 'immediate') {
                            // Check if the SubscriptionBill exists before attempting to find it
                            $subscriptionBill = SubscriptionBill::find($metadata->subscription_id);
                            if ($subscriptionBill) {
                                $subscriptionBill->update(['payment_transaction_id' => $metadata->payment_transaction_id]);
                            } else {
                                Log::error("SubscriptionBill with ID {$metadata->subscription_id} not found");
                            }
                        }
                    }
                }
                

                $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.FEATURES'),$metadata->school_id);

                Log::info("Razorpay Webhook : payment.captured");
              
                http_response_code(200);
                DB::commit();
               
            } elseif ($metadata && isset($data->event) && $data->event == 'payment.failed') {
                $paymentTransactionData = PaymentTransaction::find($metadata->payment_transaction_id);
                if (!$paymentTransactionData) {
                    Log::error("Razorpay Webhook : Payment Transaction id not found --->");
                }

                PaymentTransaction::find($metadata->payment_transaction_id)->update(['payment_status' => "failed"]);

                http_response_code(400);
            } elseif (isset($data->event) && $data->event == 'payment.authorized') {
                Log::error('Razorpay Webhook : payment.authorized');
                http_response_code(200);
            }
            else {
                Log::error('Razorpay Webhook : Received unknown event type');
            }

            
            
            
        } catch (UnexpectedValueException) {
            // Invalid payload
            echo "Razorpay Webhook : Payload Mismatch";
            Log::error("Razorpay  : Payload Mismatch");
            http_response_code(400);
            exit();
        } catch (SignatureVerificationException) {
            // Invalid signature
            echo "Razorpay  Webhook : Signature Verification Failed";
            Log::error("Razorpay  Webhook : Signature Verification Failed");
            http_response_code(400);
            exit();
        } catch(Throwable $e) {
            DB::rollBack();
            Log::error("Razorpay Webhook : Error occurred", [$e->getMessage() . ' --> ' . $e->getFile() . ' At Line : ' . $e->getLine()]);
            http_response_code(400);
            exit();
        }

        Log::error('Webhook class');
    }

    public function flutterwave(Request $request)
    {
        Log::info('Flutterwave Webhook Called');
        $webhookBody = file_get_contents('php://input');
        try {
            $data = json_decode($webhookBody, true);
            Log::info("Flutterwave Webhook Data:", [$data]);

            if (!$data) {
                throw new Exception('Invalid webhook payload');
            }

            // Extract basic information from webhook
            $event = $data['event'] ?? '';
            $txRef = $data['data']['tx_ref'] ?? '';
            $amount = $data['data']['amount'] ?? 0;
            $status = $data['data']['status'] ?? '';
            $customerEmail = $data['data']['customer']['email'] ?? '';

            if (empty($txRef)) {
                throw new Exception('Transaction reference not found in webhook data');
            }

            Log::info("Processing Flutterwave tx_ref: {$txRef}, event: {$event}, status: {$status}");

            // Extract metadata from the webhook data
            $metadata = $data['meta_data'];

            // Log metadata
            Log::info("Original metadata:", ['metadata' => $metadata]);

            // Extract school_id from metadata
            if (is_string($metadata)) {
                // Try to parse JSON string metadata
                $metadataObj = json_decode($metadata, true);
                if ($metadataObj) {
                    $metadata = $metadataObj;
                }
            }

            // Ensure metadata is an array
            if (!is_array($metadata)) {
                $metadata = [];
            }

            // Try to find the school_id in other places if not in metadata
            $schoolId = $metadata['school_id'] ?? null;
            
            // Find transaction in database
            DB::setDefaultConnection('mysql');
            $paymentTransaction = PaymentTransaction::where('order_id', $txRef)->first();
            
            if (!$paymentTransaction) {
                Log::info("Creating new payment transaction for tx_ref: {$txRef}");
                
                // Default to pending status
                // $paymentTransaction = new PaymentTransaction();
                $paymentTransaction = $this->paymentTransaction->create([
                    'user_id' => $metadata['user_id'],
                    'amount' => $amount,
                    'payment_gateway' => 'Flutterwave',
                    'order_id' => $txRef,
                    'payment_status' => 'pending',
                    'school_id' => $schoolId,
                ]);

                // $paymentTransaction->order_id = $txRef;
                // $paymentTransaction->payment_id = $data['data']['id'] ?? null;
                // $paymentTransaction->payment_gateway = 'Flutterwave';
                // $paymentTransaction->amount = $amount;
                // $paymentTransaction->payment_status = 'pending';
                // $paymentTransaction->save();
                
                Log::info("Created new payment transaction with ID: {$paymentTransaction->id}");
            } else {
                // Use school_id from payment transaction if not in metadata
                if (!$schoolId && $paymentTransaction->school_id) {
                    $schoolId = $paymentTransaction->school_id;
                    $metadata['school_id'] = $schoolId;
                }
            }
            
            // Check if payment already processed
            if ($paymentTransaction->payment_status == 'succeed') {
                Log::info("Payment already processed for tx_ref: {$txRef}");
                return response()->json(['status' => 'success', 'message' => 'Payment already processed']);
            }
            
            // Update transaction status based on webhook status
            if ($event == 'charge.completed') {
                Log::info("Updating payment status to succeed for tx_ref: {$txRef}");
                
                DB::beginTransaction();
                
                try {
                    
                    Log::info("Payment status updated to succeed for tx_ref: {$txRef}");
                    
                    // Add payment_transaction_id to metadata for processing
                    $metadata['payment_transaction_id'] = $paymentTransaction->id;
                    
                    // Process payment based on type
                    \Log::info("metadata=>". $metadata['type']);
                    if (isset($metadata['type']) ) {
                        $type = $metadata['type'];
                        if ($type == 'addon') {
                            Log::info("Processing addon payment");

                            // Update payment status
                            $paymentTransaction->payment_status = 'succeed';
                            $paymentTransaction->save();
                            
                            $this->handleAddonPayment($metadata);
                            
                        } else if ($type == 'package') {
                            Log::info("Processing package payment");

                            // Update payment status
                            $paymentTransaction->payment_status = 'succeed';
                            $paymentTransaction->save();
                            
                            // Extract package details
                            $packageId = $metadata['package_id'] ?? null;
                            $packageType = $metadata['package_type'] ?? 'new';
                            
                            if (!$packageId) {
                                throw new Exception("Package ID not found in metadata");
                            }
                            
                            // Set school_id in metadata
                            $metadata['school_id'] = $schoolId;
                            
                            Log::info("Package details: ID={$packageId}, Type={$packageType}, SchoolID={$schoolId}");
                            
                            if ($packageType == 'new') {
                                Log::info("Processing new package payment with metadata:", $metadata);
                                $this->handlePackagePayment($metadata);
                            }
                            else if ($packageType == 'upcoming') {
                                // Process upcoming prepaid plan
                                Log::info("Processing upcoming prepaid plan with metadata:", $metadata);
                                $this->processUpcomingPrepaidPlan($metadata);
                            }
                            else if ($packageType == 'immediate') {
                                // Process immediate package change
                                Log::info("Processing immediate package change with metadata:", $metadata);
                                $this->processImmediatePackageChange($metadata);
                            }
                        }
                    }
                    
                    // Clear cache
                    if ($schoolId) {
                        $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.FEATURES'), $schoolId);
                    }
                    
                    DB::commit();
                    Log::info("Payment processed successfully for tx_ref: {$txRef}");
                    
                    return response()->json(['status' => 'success']);
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error("Error processing payment: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                        'tx_ref' => $txRef
                    ]);
                    throw $e;
                }
            }
            else if ($status == 'failed') {
                $paymentTransaction->payment_status = 'failed';
                $paymentTransaction->save();
                
                Log::info("Payment status updated to failed for tx_ref: {$txRef}");
                return response()->json(['status' => 'failed'], 400);
            }
            
            return response()->json(['status' => 'received'], 200);
            
        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error("Flutterwave Webhook Error: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(), 
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function paystack(Request $request)
    {
        Log::info('Paystack Webhook Called');
        
        $webhookBody = file_get_contents('php://input');
        try {
            $data = json_decode($webhookBody, true);
            Log::info("Paystack Webhook Data:", [$data]);

            if (!$data) {
                throw new Exception('Invalid webhook payload');
            }

            $event = $data['event'] ?? '';
            $txRef = $data['data']['reference'] ?? '';
            $amount = $data['data']['amount'] ?? 0;
            $status = $data['data']['status'] ?? '';
            $customerEmail = $data['data']['customer']['email'] ?? '';

            if (empty($txRef)) {
                throw new Exception('Transaction reference not found in webhook data');
            }

            Log::info("Processing Paystack tx_ref: {$txRef}, event: {$event}, status: {$status}");

           
            $metadata = $data['data']['metadata'];

            if (is_string($metadata)) {
                $metadata = json_decode($metadata, true);
            }

            $schoolId = $metadata['school_id'] ?? null;

            if (!$schoolId) {
                Log::warning("Paystack Webhook: School ID not found in metadata");
            }

            DB::setDefaultConnection('mysql');
            $paymentTransaction = PaymentTransaction::where('order_id', $txRef)->first();

            if (!$paymentTransaction) {
                Log::info("Creating new payment transaction for tx_ref: {$txRef}");
                
                $paymentTransaction = $this->paymentTransaction->create([
                    'user_id' => $metadata['user_id'],
                    'amount' => $amount,
                    'payment_gateway' => 'Paystack',
                    'order_id' => $txRef,
                    'payment_status' => 'pending',
                    'school_id' => $schoolId,
                ]);
                
                Log::info("Created new payment transaction with ID: {$paymentTransaction->id}");
            } else {
                if (!$schoolId && $paymentTransaction->school_id) {
                    $schoolId = $paymentTransaction->school_id;
                    $metadata['school_id'] = $schoolId;
                }
            }
            
            if ($paymentTransaction->payment_status == 'succeed') {
                Log::info("Payment already processed for tx_ref: {$txRef}");
                return response()->json(['status' => 'success', 'message' => 'Payment already processed']);
            }
            
            if ($event == 'charge.success') {
                Log::info("Updating payment status to succeed for tx_ref: {$txRef}");
                
                DB::beginTransaction();
                
                try {
                    $metadata['payment_transaction_id'] = $paymentTransaction->id;
                    
                    // Process payment based on type
                    if (isset($metadata['type'])) {
                        $type = $metadata['type'];
                        \Log::info("Processing payment type: {$type}");
                        if ($type == 'addon') {
                            Log::info("Processing addon payment");

                            // Update payment status
                            $this->paymentTransaction->builder()->where('id', $paymentTransaction->id)->update([
                                'payment_status' => 'succeed',
                                'payment_id' => $data['data']['id'] ?? null,
                            ]);
                            
                            $this->handleAddonPayment($metadata);
                        } else if ($type == 'package') {
                            Log::info("Processing package payment");

                            // Update payment status
                            $this->paymentTransaction->builder()->where('id', $paymentTransaction->id)->update([
                                'payment_status' => 'succeed',
                                'payment_id' => $data['data']['id'] ?? null,
                            ]);
                            
                            // Extract package details
                            $packageId = $metadata['package_id'] ?? null;
                            $packageType = $metadata['package_type'] ?? 'new';
                            
                            if (!$packageId) {
                                throw new Exception("Package ID not found in metadata");
                            }
                            
                            // Set school_id in metadata
                            $metadata['school_id'] = $schoolId;
                            
                            Log::info("Package details: ID={$packageId}, Type={$packageType}, SchoolID={$schoolId}");
                            
                            if ($packageType == 'new') {
                                Log::info("Processing new package payment with metadata:", $metadata);
                                $this->handlePackagePayment($metadata);
                            } else if ($packageType == 'upcoming') {
                                // Process upcoming prepaid plan
                                Log::info("Processing upcoming prepaid plan with metadata:", $metadata);
                                $this->processUpcomingPrepaidPlan($metadata);
                            } else if ($packageType == 'immediate') {
                                // Process immediate package change
                                Log::info("Processing immediate package change with metadata:", $metadata);
                                $this->processImmediatePackageChange($metadata);
                            }
                        }
                    }
                    
                    // Clear cache
                    if ($schoolId) {
                        $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.FEATURES'), $schoolId);
                    }
                    
                    DB::commit();
                    Log::info("Payment processed successfully for tx_ref: {$txRef}");
                    
                    return response()->json(['status' => 'success']);
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error("Error processing payment: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString(),
                        'tx_ref' => $txRef
                    ]);
                    throw $e;
                }
            }
            
            return response()->json(['status' => 'received'], 200);
        } catch (Exception $e) {
            Log::error("Paystack Webhook Error: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function handleAddonPayment($metadata)
    {
        Log::info("Handling addon payment with metadata:", $metadata);
        
        // Validate required fields and use fallbacks
        $subscriptionId = $metadata['subscription_id'] ?? null;
        if (empty($subscriptionId)) {
            Log::warning("Addon payment: subscription_id is missing");
        }
        
        $schoolId = $metadata['school_id'] ?? null;
        if (empty($schoolId)) {
            Log::warning("Addon payment: school_id is missing");
            return;
        }
        
        $featureId = $metadata['feature_id'] ?? null;
        if (empty($featureId)) {
            Log::warning("Addon payment: feature_id is missing");
        }
        
        $price = $metadata['amount'] ?? $metadata['price'] ?? 0;
        
        // Use provided end_date or default to 30 days from now
        $endDate = null;
        if (!empty($metadata['end_date'])) {
            $endDate = $metadata['end_date'];
        } else {
            $endDate = Carbon::now()->addDays(30)->format('Y-m-d');
            Log::info("Using default end_date (30 days from now): {$endDate}");
        }
        
        $paymentTransactionId = $metadata['payment_transaction_id'] ?? null;
        if (empty($paymentTransactionId)) {
            Log::warning("Addon payment: payment_transaction_id is missing");
        }
        \Log::info("paymentTransactionId=>". $paymentTransactionId);
        try {
            $addon_data = [
                'subscription_id' => $subscriptionId,
                'school_id' => $schoolId,
                'feature_id' => $featureId,
                'price' => $price,
                'start_date' => Carbon::now(),
                'end_date' => $endDate,
                'status' => 1,
                'payment_transaction_id' => $paymentTransactionId,
            ];

            Log::info("Creating addon subscription with data:", $addon_data);
            $addonSubscription = AddonSubscription::create($addon_data);
            
            Log::info("Addon subscription created successfully with ID: " . $addonSubscription->id);
            return $addonSubscription;
        } catch (Exception $e) {
            Log::error("Error creating addon subscription: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function handlePackagePayment($metadata)
    {
        Log::info("Handling package payment with metadata:", $metadata);
        
        $new_subscription = null;
        
        // Validate required fields
        $packageId = $metadata['package_id'] ?? null;
        if (empty($packageId)) {
            Log::warning("Package payment: package_id is missing");
            return null;
        }
        
        $schoolId = $metadata['school_id'] ?? null;
        if (empty($schoolId)) {
            Log::warning("Package payment: school_id is missing");
            return null;
        }
        
        $paymentTransactionId = $metadata['payment_transaction_id'] ?? null;
        if (empty($paymentTransactionId)) {
            Log::warning("Package payment: payment_transaction_id is missing");
        }
        
        // Get package type, default to 'new' if not specified
        $packageType = $metadata['package_type'] ?? 'new';
        Log::info("Processing {$packageType} package subscription");
        
        try {
            DB::beginTransaction();
            
            // New package subscription
            if ($packageType == 'new') {
                Log::info("Creating new subscription for package_id: {$packageId}");
                $new_subscription = $this->subscriptionService->createSubscription(
                    $packageId, 
                    $schoolId, 
                    null, 
                    1
                );
                
                // Update subscription bill with payment transaction ID
                if ($new_subscription && isset($new_subscription->subscription_bill)) {
                    Log::info("Updating subscription bill with payment transaction ID: {$paymentTransactionId}");
                    SubscriptionBill::where('subscription_id', $new_subscription->id)
                        ->update(['payment_transaction_id' => $paymentTransactionId]);
                } else {
                    Log::warning("No subscription bill found for new subscription");
                }
            }
            
            // Upcoming prepaid plan
            else if ($packageType == 'upcoming') {
                Log::info("Processing upcoming prepaid plan");
                
                // Ensure all required data is present in metadata
                $metadata['payment_transaction_id'] = $paymentTransactionId;
                $metadata['school_id'] = $schoolId;
                $metadata['package_id'] = $packageId;
                
                $new_subscription = $this->processUpcomingPrepaidPlan($metadata);
                if (!$new_subscription) {
                    throw new Exception("Failed to process upcoming prepaid plan");
                }
            }
            
            // Immediate change current package
            else if ($packageType == 'immediate') {
                Log::info("Processing immediate package change");
                
                // Ensure all required data is present in metadata
                $metadata['payment_transaction_id'] = $paymentTransactionId;
                $metadata['school_id'] = $schoolId;
                $metadata['package_id'] = $packageId;
                
                $new_subscription = $this->processImmediatePackageChange($metadata);
                if (!$new_subscription) {
                    throw new Exception("Failed to process immediate package change");
                }
            }
            
            // Update payment transaction ID for all subscription bills
            if ($new_subscription) {
                Log::info("Updating payment transaction ID for all subscription bills");
                SubscriptionBill::where('subscription_id', $new_subscription->id)
                    ->update(['payment_transaction_id' => $paymentTransactionId]);
            }
            
            DB::commit();
            Log::info("Package payment processed successfully");
            return $new_subscription;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error in handlePackagePayment: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    private function processUpcomingPrepaidPlan($metadata)
    {
        $schoolId = $metadata['school_id'] ?? null;
        $packageId = $metadata['package_id'] ?? null;
        $paymentTransactionId = $metadata['payment_transaction_id'] ?? null;
        $subscriptionId = $metadata['subscription_id'] ?? null;
        
        Log::info("Starting upcoming prepaid plan processing. School ID: {$schoolId}, Package ID: {$packageId}");
        
        if (!$schoolId || !$packageId || !$paymentTransactionId) {
            Log::error("Missing required data for upcoming prepaid plan:", [
                'school_id' => $schoolId,
                'package_id' => $packageId,
                'payment_transaction_id' => $paymentTransactionId
            ]);
            throw new Exception("Missing required data for upcoming prepaid plan");
        }
        
        try {
            // Get active plan and package
            $active_plan = $this->subscriptionService->active_subscription($schoolId);
            if (!$active_plan) {
                Log::error("No active plan found for school ID: {$schoolId}");
                return null;
            }
            
            Log::info("Current active plan found: ID={$active_plan->id}, end_date={$active_plan->end_date}");
            
            $package = Package::find($packageId);
            if (!$package) {
                Log::error("Package not found with ID: {$packageId}");
                return null;
            }
            
            Log::info("Package found: ID={$package->id}, Name={$package->name}, Days={$package->days}");
            
            // Calculate start and end dates
                            $start_date = Carbon::parse($active_plan->end_date)->addDays()->format('Y-m-d');
                            $end_date = Carbon::parse($start_date)->addDays(($package->days - 1))->format('Y-m-d');
            
            Log::info("Calculated date range: start_date={$start_date}, end_date={$end_date}");

                            $subscription_data = [
                                'package_id'     => $package->id,
                                'name'           => $package->name,
                                'student_charge' => $package->student_charge,
                                'staff_charge'   => $package->staff_charge,
                                'start_date'     => $start_date,
                                'end_date'       => $end_date,
                                'package_type'   => $package->type,
                                'no_of_students' => $package->no_of_students,
                                'no_of_staffs'   => $package->no_of_staffs,
                                'billing_cycle'  => $package->days,
                'school_id'      => $schoolId,
                                'charges'        => $package->charges
                            ];

            Log::info("Creating subscription with data:", $subscription_data);
            
            // Create or update subscription
            $new_subscription = null;
            if ($subscriptionId && $active_plan->id == $subscriptionId) {
                                // Same upcoming plan
                Log::info("Creating new subscription for upcoming plan (same as active plan)");
                $new_subscription = Subscription::create($subscription_data);
            } else if ($subscriptionId) {
                // Update existing record
                Log::info("Updating existing subscription ID: {$subscriptionId}");
                $existing = Subscription::find($subscriptionId);
                if ($existing) {
                    $existing->update($subscription_data);
                    $new_subscription = $existing->fresh();
                    Log::info("Updated existing subscription successfully");
                } else {
                    Log::warning("Subscription ID {$subscriptionId} not found, creating new instead");
                    $new_subscription = Subscription::create($subscription_data);
                }
            } else {
                // Create new if no subscription ID
                Log::info("No subscription ID provided, creating new subscription");
                                $new_subscription = Subscription::create($subscription_data);
            }
            
            if (!$new_subscription) {
                throw new Exception("Failed to create or update subscription");
            }
            
            Log::info("Subscription created/updated successfully: ID={$new_subscription->id}");

                            // Add features
            $subscription_features = [];
            if (isset($new_subscription->package) && isset($new_subscription->package->package_feature)) {
                            foreach ($new_subscription->package->package_feature as $key => $feature) {
                                $subscription_features[] = [
                                    'subscription_id' => $new_subscription->id,
                                    'feature_id'      => $feature->feature_id
                                ];
                            }
                
                if (count($subscription_features) > 0) {
                    SubscriptionFeature::upsert(
                        $subscription_features, 
                        ['subscription_id', 'feature_id'], 
                        ['subscription_id', 'feature_id']
                    );
                    Log::info("Added " . count($subscription_features) . " features to subscription");
                }
            } else {
                Log::warning("No package features found for subscription ID: {$new_subscription->id}");
            }

                            // Generate bill
                            $systemSettings = $this->cache->getSystemSettings();
                            $subscription_bill = [
                                'subscription_id'        => $new_subscription->id,
                                'amount'                 => $new_subscription->charges,
                                'total_student'          => 0,
                                'total_staff'            => 0,
                'due_date'               => Carbon::now()->addDays($systemSettings['additional_billing_days'] ?? 7)->format('Y-m-d'),
                'school_id'              => $schoolId,
                'payment_transaction_id' => $paymentTransactionId
            ];
            
            Log::info("Creating subscription bill:", $subscription_bill);
            
                            // Create bill for active plan
            $bill = SubscriptionBill::create($subscription_bill);
            Log::info("Created subscription bill: ID={$bill->id}");
            
            Log::info("Upcoming prepaid plan processing completed successfully");
            return $new_subscription;
            
        } catch (Exception $e) {
            Log::error("Error in processUpcomingPrepaidPlan: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    private function processImmediatePackageChange($metadata)
    {
        $schoolId = $metadata['school_id'] ?? null;
        $packageId = $metadata['package_id'] ?? null;
        $paymentTransactionId = $metadata['payment_transaction_id'] ?? null;
        
        Log::info("Starting immediate package change. School ID: {$schoolId}, Package ID: {$packageId}");
        
        if (!$schoolId || !$packageId || !$paymentTransactionId) {
            Log::error("Missing required data for immediate package change:", [
                'school_id' => $schoolId,
                'package_id' => $packageId,
                'payment_transaction_id' => $paymentTransactionId
            ]);
            throw new Exception("Missing required data for immediate package change");
        }
        
        try {
                            // Get current plan
            $subscription = $this->subscriptionService->active_subscription($schoolId);
            if (!$subscription) {
                Log::error("No active subscription found for school ID: {$schoolId}");
                return null;
            }
            
            Log::info("Current active subscription found: ID={$subscription->id}, end_date={$subscription->end_date}");
            
            // Get package
            $package = Package::find($packageId);
            if (!$package) {
                Log::error("Package not found with ID: {$packageId}");
                return null;
            }
            
            Log::info("Package found: ID={$package->id}, Name={$package->name}, Days={$package->days}");
            
            DB::beginTransaction();

                            // Postpaid plan generate bill
                            if ($subscription->package_type == 1) {
                // Create bill for current subscription
                $result = $this->subscriptionService->createSubscriptionBill($subscription, null);
                Log::info("Created bill for current subscription: " . ($result ? "Success" : "Failed"));
            }
            
            // Update end date for current subscription
            $oldEndDate = $subscription->end_date;
            $subscription->end_date = Carbon::now()->format('Y-m-d');
            $subscription->save();
            
            Log::info("Updated end date for current subscription from {$oldEndDate} to {$subscription->end_date}");
            
            // Delete subscription features
            $featureCount = $this->subscriptionFeature->builder()->where('subscription_id', $subscription->id)->count();
                            $this->subscriptionFeature->builder()->where('subscription_id', $subscription->id)->delete();
            Log::info("Deleted {$featureCount} features for current subscription");
            
            // Delete upcoming subscriptions
            $upcomingCount = $this->subscription->builder()
                ->with('package')
                ->doesntHave('subscription_bill')
                ->whereDate('start_date', '>', $subscription->end_date)
                ->count();
                
            $this->subscription->builder()
                ->with('package')
                ->doesntHave('subscription_bill')
                ->whereDate('start_date', '>', $subscription->end_date)
                ->delete();
                
            Log::info("Deleted {$upcomingCount} upcoming subscriptions");
            
            // Update and delete addons
                            $addons = $this->addonSubscription->builder()->where('subscription_id', $subscription->id)->get();
            $soft_delete_addon = [];
            foreach ($addons as $addon) {
                AddonSubscription::find($addon->id)->update(['end_date' => $subscription->end_date]);
                                $soft_delete_addon[] = $addon->id;
                            }

            if (count($soft_delete_addon) > 0) {
                            $this->addonSubscription->builder()->whereIn('id', $soft_delete_addon)->delete();
                Log::info("Updated and deleted " . count($soft_delete_addon) . " addons for current subscription");
            }

            // Create new subscription
                            $start_date = Carbon::now();
                            $end_date = Carbon::now()->addDays(($package->days - 1))->format('Y-m-d');
            
                            $subscription_data = [
                                'package_id'     => $package->id,
                                'name'           => $package->name,
                                'student_charge' => $package->student_charge,
                                'staff_charge'   => $package->staff_charge,
                                'start_date'     => $start_date,
                                'end_date'       => $end_date,
                                'package_type'   => $package->type,
                                'no_of_students' => $package->no_of_students,
                                'no_of_staffs'   => $package->no_of_staffs,
                                'billing_cycle'  => $package->days,
                'school_id'      => $schoolId,
                                'charges'        => $package->charges
                            ];
            
            Log::info("Creating new subscription with data:", $subscription_data);

                            $new_subscription = Subscription::create($subscription_data);
            Log::info("Created new subscription with ID: {$new_subscription->id}");

                            // Add features
            $subscription_features = [];
            foreach ($new_subscription->package->package_feature as $feature) {
                                $subscription_features[] = [
                                    'subscription_id' => $new_subscription->id,
                                    'feature_id'      => $feature->feature_id
                                ];
                            }
            
            if (count($subscription_features) > 0) {
                SubscriptionFeature::upsert(
                    $subscription_features, 
                    ['subscription_id', 'feature_id'], 
                    ['subscription_id', 'feature_id']
                );
                Log::info("Added " . count($subscription_features) . " features to new subscription");
            }

                            // Generate bill
                            $systemSettings = $this->cache->getSystemSettings();
                            $subscription_bill = [
                                'subscription_id'        => $new_subscription->id,
                                'amount'                 => $new_subscription->charges,
                                'total_student'          => 0,
                                'total_staff'            => 0,
                'due_date'               => Carbon::now()->addDays($systemSettings['additional_billing_days'] ?? 7)->format('Y-m-d'),
                'school_id'              => $schoolId,
                'payment_transaction_id' => $paymentTransactionId
            ];
            
            Log::info("Creating subscription bill for new subscription:", $subscription_bill);
            
            // Create bill for new subscription
            $bill = SubscriptionBill::create($subscription_bill);
            Log::info("Created subscription bill for new subscription: ID={$bill->id}");
            
                DB::commit();
            Log::info("Immediate package change completed successfully");
            return $new_subscription;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error in processImmediatePackageChange: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

}
