<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChapaPayment implements PaymentInterface {
    private string $secretKey;
    private string $apiKey;
    private string $currencyCode;
    private string $baseUrl = 'https://api.chapa.co/v1';

    public function __construct($secretKey, $apiKey, $currencyCode) {
        $this->secretKey = $secretKey;
        $this->apiKey = $apiKey;
        $this->currencyCode = $currencyCode;
    }

    /**
     * Create a payment intent using Chapa
     * @param float|int $amount
     * @param array $customMetaData
     * @return array
     * @throws Exception
     */
    public function createPaymentIntent($amount, $customMetaData) {
        $amount = $this->minimumAmountValidation($this->currencyCode, $amount);
        $tx_ref = $customMetaData['tx_ref'] ?? uniqid('chapa_', true);
        $email = $customMetaData['email'] ?? 'no-email@domain.com';
        $name = $customMetaData['name'] ?? 'No Name';
        $callback_url = $customMetaData['callback_url'] ?? url('payment/status');
        $return_url = $customMetaData['return_url'] ?? url('payment/status');

        $data = [
            'amount' => $amount,
            'currency' => $this->currencyCode,
            'email' => $email,
            'first_name' => $name,
            'tx_ref' => $tx_ref,
            'callback_url' => $callback_url,
            'return_url' => $return_url,
            // Add other Chapa fields as needed
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/transaction/initialize', $data);

        if ($response->failed()) {
            Log::error('Chapa Payment Init Failed', ['response' => $response->json()]);
            throw new Exception('Chapa payment initialization failed');
        }

        return $response->json();
    }

    public function createAndFormatPaymentIntent($amount, $customMetaData): array {
        $intent = $this->createPaymentIntent($amount, $customMetaData);
        return $this->formatPaymentIntent(
            $intent['data']['tx_ref'] ?? null,
            $amount,
            $this->currencyCode,
            $intent['status'] ?? 'pending',
            $customMetaData,
            $intent
        );
    }

    public function retrievePaymentIntent($paymentId): array {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/transaction/verify/' . $paymentId);

        if ($response->failed()) {
            Log::error('Chapa Payment Verify Failed', ['response' => $response->json()]);
            throw new Exception('Chapa payment verification failed');
        }

        return $response->json();
    }

    public function minimumAmountValidation($currency, $amount) {
        // Chapa minimums: ETB 10, USD 1, etc. (adjust as needed)
        $minimums = [
            'ETB' => 10,
            'USD' => 1,
        ];
        if (isset($minimums[$currency]) && $amount < $minimums[$currency]) {
            return $minimums[$currency];
        }
        return $amount;
    }

    public function formatPaymentIntent($id, $amount, $currency, $status, $metadata, $paymentIntent): array {
        return [
            'id' => $id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'metadata' => $metadata,
            'payment_intent' => $paymentIntent,
        ];
    }
} 