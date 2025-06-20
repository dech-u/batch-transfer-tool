<?php

namespace App\Services\Payment;

use JetBrains\PhpStorm\Pure;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;

class StripePayment implements PaymentInterface {
    private StripeClient $stripe;
    private string $currencyCode;

    #[Pure] public function __construct($secretKey, $currencyCode) {
        // Call Stripe Class and Create Payment Intent
        $this->stripe = new StripeClient($secretKey);
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
            Log::info('Creating Stripe payment intent with amount: ' . $amount);
            
            $amount = $this->minimumAmountValidation($this->currencyCode, $amount);
            
            Log::info('Validated amount: ' . $amount . ' in currency: ' . $this->currencyCode);
            $amount = (float)$amount * 100;
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => $this->currencyCode,
                'metadata' => $customMetaData,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
            
            Log::info('Stripe payment intent created successfully: ' . $paymentIntent->id);
            return $paymentIntent;
            
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error('General Error in Stripe Payment: ' . $e->getMessage());
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
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentId);
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
            'metadata' => $paymentIntent->metadata,
        ];
    }
}