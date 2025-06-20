<?php

namespace App\Services;

use App\Repositories\CompulsoryFee\CompulsoryFeeInterface;
use App\Repositories\Fees\FeesInterface;
use App\Repositories\FeesPaid\FeesPaidInterface;
use App\Repositories\Student\StudentInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralService {
    private FeesInterface $fees;
    private FeesPaidInterface $feesPaid;
    private CompulsoryFeeInterface $compulsoryFee;
    private StudentInterface $student;
    private $paymentTransaction;

    public function __construct(FeesInterface $fees, FeesPaidInterface $feesPaid, CompulsoryFeeInterface $compulsoryFee, StudentInterface $student) {
        $this->fees = $fees;
        $this->feesPaid = $feesPaid;
        $this->compulsoryFee = $compulsoryFee;
        $this->student = $student;
    }

    public function feesWebhook($metadata) {

        //get the current today's date
        $current_date = date('Y-m-d');

        // handle the events
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentTransactionData = $this->paymentTransaction->findById($metadata['payment_transaction_id']);
                if ($paymentTransactionData == null) {
                    Log::error("Stripe Webhook : Payment Transaction id not found");
                    break;
                }

                if ($paymentTransactionData->status == "succeed") {
                    Log::info("Stripe Webhook : Transaction Already Successes");
                    break;
                }


                DB::beginTransaction();
                $this->paymentTransaction->update($metadata['payment_transaction_id'], ['payment_status' => "succeed", 'school_id' => $paymentTransactionData->school_id]);

                $feesPaidQuery = $this->feesPaid->builder()->where([
                    'fees_id'         => $metadata['fees_id'],
                    'student_id'      => $metadata['student_id'],
                    'session_year_id' => $metadata['session_year_id'],
                    'school_id'       => $paymentTransactionData->school_id
                ]); // Get Query Of Fees Paid

                $feesPaidDB = $feesPaidQuery->first(); // Get Fees Paid Data

                // Check if Fees Paid Exists Then Add The optional Fees Amount with Fess Paid Amount
                $totalAmount = $feesPaidQuery->count() ? $feesPaidDB->amount + $paymentTransactionData->amount : $paymentTransactionData->amount;

                // Fees Paid Array
                $feesPaidData = array(
                    'amount'          => $totalAmount,
                    'date'            => date('Y-m-d', strtotime($current_date)),
                    'is_fully_paid'   => $metadata['is_full_paid'],
                    "school_id"       => $paymentTransactionData->school_id,
                    'fees_id'         => $metadata['fees_id'],
                    'student_id'      => $metadata['student_id'],
                    'session_year_id' => $metadata['session_year_id'],
                );

                $feesPaidResult = $this->feesPaid->updateOrCreate($feesPaidDB->id, $feesPaidData);


                if ($metadata['fee_type'] == "compulsory") {
                    $this->compulsoryFees->builder()->where('payment_transaction_id', $paymentTransactionData->id)->update([
                        'status'       => "Success",
                        'fees_paid_id' => $feesPaidResult->id,
                    ]);
                } else if ($metadata['fee_type'] == "optional") {
                    $this->optionalFees->builder()->where('payment_transaction_id', $paymentTransactionData->id)->update([
                        'status'       => "Success",
                        'fees_paid_id' => $feesPaidResult->id,
                    ]);
                }

                $user = $this->user->findById($metadata['parent_id']);
                $body = 'Amount :- ' . $paymentTransactionData->amount;
                $type = 'Online';
                send_notification($user, 'Payment Success', $body, $type);
                http_response_code(200);
                DB::commit();
                break;

            case
            'payment_intent.payment_failed':
                $paymentTransactionData = $this->paymentTransaction->findById($metadata['payment_transaction_id']);
                if ($paymentTransactionData !== null) {
                    if ($paymentTransactionData->status != 1) {

                        $this->paymentTransaction->update($paymentTransactionData->id, ['payment_status' => "0", 'school_id' => $paymentTransactionData->school_id]);

                        if ($metadata['fee_type'] == "compulsory") {
                            $this->compulsoryFees->builder()->where('payment_transaction_id', $paymentTransactionData->id)->update([
                                'status' => "failed",
                            ]);
                        } else if ($metadata['fee_type'] == "optional") {
                            $this->optionalFees->builder()->where('payment_transaction_id', $paymentTransactionData->id)->update([
                                'status' => "failed",
                            ]);
                        }

                        http_response_code(400);
                        $user = $this->user->builder()->role('Guardian')->where('id', $metadata['parent_id'])->pluck('user_id');
                        $body = 'Amount :- ' . $paymentTransactionData->amount;
                        $type = 'Online';
                        send_notification($user, 'Payment Failed', $body, $type);
                        break;
                    }
                } else {
                    Log::error("Stripe Webhook : Payment Transaction id not found --->");
                    break;
                }
                break;
            default:
                Log::error('Stripe Webhook : Received unknown event type');
        }
    }
}
