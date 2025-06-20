<?php

namespace App\Services\Payment;

use App\Models\PaymentConfiguration;
use App\Repositories\PaymentConfiguration\PaymentConfigurationInterface;
use Dompdf\Exception;
use InvalidArgumentException;

class PaymentService {
    /**
     * @param string $paymentGateway - Stripe
     * @param null $schoolID - IF School id is null then Super Admin's Payment Gateway Credentials will be used
     * @return StripePayment
     * @throws Exception
     */
    public static function create(string $paymentGateway, $schoolID = null) {
        $paymentGateway = strtolower($paymentGateway);
        //IF School ID is not empty then find the details in PaymentConfiguration Model
        if (!empty($schoolID)) {
            $payment = app(PaymentConfigurationInterface::class)->builder()->where(['payment_method' => $paymentGateway, 'status' => 1])->first();
            if (empty($payment)) {
                throw new Exception("Payment gateway is not enabled");
            }
        } else {
            $payment = PaymentConfiguration::whereNull('school_id')->where(['payment_method' => $paymentGateway, 'status' => 1])->first();
        }
        return match ($paymentGateway) {
            'stripe' => new StripePayment($payment->secret_key, $payment->currency_code),
            'razorpay' => new RazorpayPayment($payment->secret_key, $payment->api_key, $payment->currency_code),
            'flutterwave' => new FlutterwavePayment($payment->secret_key, $payment->api_key, $payment->currency_code),
            'paystack' => new PaystackPayment($payment->secret_key, $payment->api_key, $payment->currency_code),

            // any other payment processor implementations
            default => throw new InvalidArgumentException('Invalid Payment Gateway.'),
        };
    }

    /***
     * @param string $paymentGateway
     * @param $paymentIntentData
     * @return array
     * Stripe Payment Intent : https://stripe.com/docs/api/payment_intents/object
     */
    public static function formatPaymentIntent(string $paymentGateway, $paymentIntentData) {
        $paymentGateway = strtolower($paymentGateway);
        //IF School ID is not empty then find the details in PaymentConfiguration Model
        return match ($paymentGateway) {
            'stripe' => [
                'id'              => $paymentIntentData['id'],
                'amount'          => $paymentIntentData['amount'],
                'amount_received' => $paymentIntentData['amount_received'],
                'currency'        => $paymentIntentData['currency'],
                'metadata'        => $paymentIntentData['metadata'],
                'status'          => match ($paymentIntentData['status']) {
                    "canceled" => "failed",
                    "succeeded" => "succeed",
                    "processing", "requires_action", "requires_capture", "requires_confirmation", "requires_payment_method" => "pending",
                },
                'actual_status'   => $paymentIntentData['status']
            ],
            // any other payment processor implementations
            default => $paymentIntentData,
        };
    }
}