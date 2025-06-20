<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PaystackPayment implements PaymentInterface {
    private string $secretKey;
    private string $publicKey;
    private string $currencyCode;
    private string $baseUrl = 'https://api.paystack.co';

    /**
     * PaystackPayment constructor.
     * @param string $secretKey
     * @param string $publicKey
     * @param string $currencyCode
     */
    public function __construct($secretKey, $publicKey, $currencyCode) {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
        $this->currencyCode = $currencyCode;
    }

    /**
     * Create a payment intent using Paystack
     * @param float|int $amount
     * @param array $customMetaData
     * @return array
     * @throws Exception
     */
    public function createPaymentIntent($amount, $customMetaData) {
        try {
            if (empty($customMetaData['email'])) {
                throw new Exception("Email cannot be empty");
            }
            
            // Convert amount to kobo (Paystack's smallest currency unit)
            $finalAmount = $this->minimumAmountValidation($this->currencyCode, $amount);

            // Generate a unique transaction reference
            $reference = 'PSK-' . uniqid() . '-' . time();
            
            // Set up callback URL for payment outcome
            $callback_url = config('app.url').'payment/status?status=success&school_id='.$customMetaData['school_id'];
            $cancelUrl = config('app.url').'payment/status?status=cancelled&school_id='.$customMetaData['school_id'];
            
            // Add cancel URL to metadata
            $customMetaData['cancel_action'] = $cancelUrl;
            $customMetaData['callback_url'] = $callback_url;
            \Log::info('Final amount', ['final_amount' => $finalAmount]);
            $finalAmount = (float)$finalAmount * 100;
            \Log::info('Final amount', ['final_amount' => $finalAmount]);

            $data = [
                'amount' => (int)$finalAmount,
                'currency' => $this->currencyCode,
                'email' => $customMetaData['email'],
                'metadata' => $customMetaData,
                'reference' => $reference,
                'callback_url' => $callback_url,
                'channels' => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
                'cancel_action' => $cancelUrl
            ];
            
            // Send request to Paystack API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', $data);
            
            // Check for API failure
            if ($response->failed()) {
                throw new Exception('Paystack API Error: ' . ($response->json()['message'] ?? 'Unknown error occurred'));
            }
            
            $responseData = $response->json();
            
            // Handle non-success status from API
            if (!isset($responseData['status']) || $responseData['status'] !== true) {
                throw new Exception('Payment Creation Failed: ' . ($responseData['message'] ?? 'Unknown error occurred'));
            }   

            // Format the response to return
            return [
                'status' => 'success',
                'message' => 'Payment initialized',
                'data' => [
                    'status' => $responseData['status'],
                    'authorization_url' => $responseData['data']['authorization_url'],
                    'access_code' => $responseData['data']['access_code'],
                    'school_id' => $customMetaData['school_id'],
                    'reference' => $responseData['data']['reference'],
                    'amount' => $finalAmount,
                    'currency' => $this->currencyCode,
                    'metadata' => $customMetaData,
                    'public_key' => $this->publicKey,
                    'callback_url' => $callback_url,
                    'cancel_url' => $cancelUrl
                ]
            ];
            
        } catch (Throwable $e) {
            // Log error details for debugging
            Log::error('Paystack Payment Error:', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            throw new Exception('Payment Processing Error: ' . $e->getMessage());
        }
    }

    /**
     * Create and format a payment intent
     * @param float|int $amount
     * @param array $customMetaData
     * @return array
     */
    public function createAndFormatPaymentIntent($amount, $customMetaData): array {
        $response = $this->createPaymentIntent($amount, $customMetaData);
        return $this->format($response, $amount, $this->currencyCode, $customMetaData);
    }

    /**
     * Retrieve and format a payment intent
     * @param string $paymentId
     * @return array
     * @throws Exception
     */
    public function retrievePaymentIntent($paymentId): array {
        try {
            if (empty($paymentId)) {
                throw new Exception('Payment ID is required for verification');
            }
            
            Log::info('Verifying Paystack Transaction:', [
                'reference' => $paymentId,
                'verification_url' => "{$this->baseUrl}/transaction/verify/{$paymentId}"
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/transaction/verify/{$paymentId}");
            
            Log::info('Paystack Verification Raw Response:', [
                'status_code' => $response->status(),
                'body' => $response->json()
            ]);
            
            // Handle API errors
            if ($response->failed()) {
                $responseData = $response->json();
                $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
                
                Log::error('Paystack Verification API Error:', [
                    'status_code' => $response->status(),
                    'error' => $errorMessage,
                    'body' => $response->json()
                ]);
                
                throw new Exception("API Error: {$errorMessage}");
            }
            
            $responseData = $response->json();
            
            // Validate response structure
            if (!isset($responseData['status']) || $responseData['status'] !== true) {
                $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
                
                Log::error('Paystack Verification Failed:', [
                    'error' => $errorMessage,
                    'response' => $responseData
                ]);
                
                throw new Exception("Verification Failed: {$errorMessage}");
            }
            
            // Validate transaction data
            if (!isset($responseData['data']) || empty($responseData['data'])) {
                Log::error('Empty Transaction Data:', [
                    'payment_id' => $paymentId,
                    'response' => $responseData
                ]);
                throw new Exception('Transaction data is empty or invalid');
            }
            
            $transactionData = $responseData['data'];
            
            // Format and return the transaction data
            return $this->format(
                $responseData,
                $transactionData['amount'] / 100, // Convert from kobo back to currency units
                $transactionData['currency'] ?? $this->currencyCode,
                $transactionData['metadata'] ?? []
            );
            
        } catch (Throwable $e) {
            Log::error('Paystack Verification Error:', [
                'payment_id' => $paymentId,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw new Exception('Transaction Verification Error: ' . $e->getMessage());
        }
    }

    /**
     * Validate minimum amount based on currency
     * @param string $currency
     * @param float|int $amount
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
     * Format the payment intent response
     * @param array $paymentIntent
     * @param float|int $amount
     * @param string $currencyCode
     * @param array $metadata
     * @return array
     */
    public function format($paymentIntent, $amount, $currencyCode, $metadata): array {
        // Extract the reference from the payment intent
        $reference = $paymentIntent['data']['reference'] ?? null;
        
        // Determine payment status
        $status = $paymentIntent['status'] ?? false;
        $transactionStatus = $paymentIntent['data']['status'] ?? null;
        
        return $this->formatPaymentIntent(
            $reference,
            $amount,
            $currencyCode,
            $transactionStatus ?? ($status ? 'success' : 'failed'),
            $metadata,
            $paymentIntent
        );
    }

    /**
     * Format the payment intent according to the PaymentInterface specification
     * @param string|null $id
     * @param float|int $amount
     * @param string $currency
     * @param string $status
     * @param array $metadata
     * @param array $paymentIntent
     * @return array
     */
    public function formatPaymentIntent($id, $amount, $currency, $status, $metadata, $paymentIntent): array {
        return [
            'id' => $id,
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $metadata,
            'status' => match (strtolower($status)) {
                'success', 'successful' => 'succeed',
                'failed', 'abandoned', 'cancelled' => 'failed',
                'pending' => 'pending',
                default => $status === true ? 'succeed' : 'unknown'
            },
            'payment_gateway_response' => $paymentIntent
        ];
    }
}
