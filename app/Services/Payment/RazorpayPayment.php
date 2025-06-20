<?php

namespace App\Services\Payment;

use JetBrains\PhpStorm\Pure;
use Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Razorpay\Api\Api;

class RazorpayPayment implements PaymentInterface {
    private Api $api;
    private string $currencyCode;

    #[Pure] public function __construct($secretKey, $publicKey, $currencyCode) {
        // Call Stripe Class and Create Payment Intent
        $this->api = new Api($publicKey, $secretKey);
        $this->currencyCode = $currencyCode;
    }

    /**
     * @param $amount
     * @param $customMetaData
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createPaymentIntent($amount, $customMetaData) {
        try {
            // Convert amount to integer (in paise)
            $amount = $this->minimumAmountValidation($this->currencyCode, $amount);
    
            // Limit notes to 15 fields and ensure all values are strings
            $notes = [];
            $count = 0;
            foreach ($customMetaData as $key => $value) {
                if ($count >= 15) {
                    break;
                }
                // Convert any non-string values to strings
                $notes[$key] = (string)$value;
                $count++;
            }
            
            $amount = (float)$amount * 100;
            $paymentData = [
                'amount'   => $amount,
                'currency' => $this->currencyCode,
                'notes' => $notes,
            ];
            return $this->api->order->create($paymentData);
            
        } catch (ApiErrorException $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param $paymentId
     * @return array
     * @throws ApiErrorException
     */
    public function retrievePaymentIntent($paymentId): array {
        try {
            $paymentIntent = $this->api->order->fetch($paymentId);
            return $paymentIntent->toArray();
        } catch (ApiErrorException $e) {
            throw $e;
        }
    }


    /**
     * @param $currency
     * @param $amount
     * @return float|int
     */
    public function minimumAmountValidation($currency, $amount) {
        
        $currencies = array(
            'USD' => 0.50,
            'AED' => 2.00,
            'AUD' => 0.50,
            'BGN' => 1.00,
            'BRL' => 0.50,
            'CAD' => 0.50,
            'CHF' => 0.50,
            'CZK' => 15.00,
            'DKK' => 2.50,
            'EUR' => 0.50,
            'GBP' => 0.30,
            'HKD' => 4.00,
            'HUF' => 175.00,
            'INR' => 0.50,
            'JPY' => 50,
            'MXN' => 10,
            'MYR' => 2.00,
            'NOK' => 3.00,
            'NZD' => 0.50,
            'PLN' => 2.00,
            'RON' => 2.00,
            'SEK' => 3.00,
            'SGD' => 0.50,
            'THB' => 10,
            'ZAR' => 10,
        );
        if ($amount != 0) {
            if (array_key_exists($currency, $currencies)) {
                if ($currencies[$currency] >= $amount) {
                    return $currencies[$currency];
                } else {
                    return $amount;
                }
            } else {
                return $amount;
            }
        }
        return 0;
    }

    /**
     * @param $amount
     * @param $customMetaData
     * @return array
     * @throws ApiErrorException
     */
    public function createAndFormatPaymentIntent($amount, $customMetaData): array {
        $paymentIntent = $this->createPaymentIntent($amount, $customMetaData);
        return $this->formatPaymentIntent($paymentIntent->id, $paymentIntent->amount, $paymentIntent->currency, $paymentIntent->status, $paymentIntent->notes, $paymentIntent);
    }

    /**
     * @param $paymentIntent
     * @return array
     */
    public function formatPaymentIntent($id, $amount, $currency, $status, $metadata, $paymentIntent): array {
        return [
            'id' => $id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'created_at' => $paymentIntent->created_at,
            'notes' => $paymentIntent->notes,
        ];
    }
}