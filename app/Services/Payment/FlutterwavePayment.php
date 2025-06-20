<?php
namespace App\Services\Payment;
use Auth;
use Illuminate\Support\Facades\URL;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class FlutterwavePayment implements PaymentInterface {
    private string $secretKey;
    private string $apiKey;
    private string $currencyCode;
    public function __construct($secretKey, $apiKey, $currencyCode) {
        $this->secretKey = $secretKey;
        $this->apiKey = $apiKey;
        $this->currencyCode = $currencyCode;
    }
    /**
     * Create a payment intent using Flutterwave
     * @param $amount
     * @param $customMetaData
     * @return array
     * @throws Exception
     */
    public function createPaymentIntent($amount, $customMetaData): array {
        try {
            // Apply minimum amount validation
            $finalAmount = $this->minimumAmountValidation($this->currencyCode, $amount);
    
            // Generate a unique transaction reference (tx_ref)
            $tx_ref = 'FLW-' . uniqid() . '-' . time();
    
            // Validate required fields in $customMetaData
            if (empty($customMetaData['school_id'])) {
                throw new Exception('School ID is required');
            }
            if (empty($customMetaData['student_id'])) {
                throw new Exception('Student ID is required');
            }
    
            // Set up redirect URLs for the payment outcome
            $redirectUrl = config('app.url') . 'payment/status?status=success&school_id='.$customMetaData['school_id'];
            $cancelUrl = config('app.url').'payment/status?status=cancelled&school_id='.$customMetaData['school_id'];
    
            // Prepare data to send to Flutterwave API
            \Log::info('Final amount', ['final_amount' => $finalAmount]);
            $data = [
                'tx_ref' => $tx_ref,
                'amount' => $finalAmount,
                'currency' => $this->currencyCode,
                'payment_options' => 'card,account,banktransfer,mpesa,mobilemoneyghana,mobilemoneyfranco,mobilemoneyuganda,mobilemoneyrwanda,mobilemoneyzambia,barter,nqr,ussd,credit,opay',
                'customer' => [
                    'name' => $customMetaData['name'],
                    'email' => $customMetaData['email'],
                    'phonenumber' => $customMetaData['mobile']
                ],
                'meta' => [
                    'school_id' => $customMetaData['school_id'], // Custom metadata field
                    'student_id' => $customMetaData['student_id'],
                    'parent_id' => $customMetaData['parent_id'],
                    'fees_type' => $customMetaData['fees_type'],
                    'fees_id' => $customMetaData['fees_id'],
                    'installment' => $customMetaData['installment'] ?? [],
                    'dueChargesAmount' => $customMetaData['dueChargesAmount'],
                    'advance_amount' => $customMetaData['advance_amount'],
                    'session_year_id' => $customMetaData['session_year_id']
                ],
                'redirect_url' => $redirectUrl,
                'cancel_url' => $cancelUrl
            ];
    
            // Send request to Flutterwave API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', $data);
    
            // Check for API failure
            if ($response->failed()) {
                throw new Exception('Flutterwave API Error: ' . ($response->json()['message'] ?? 'Unknown error occurred'));
            }
    
            $responseData = $response->json();
    
            // Handle non-success status from API
            if (!isset($responseData['status']) || $responseData['status'] !== 'success') {
                throw new Exception('Payment Creation Failed: ' . ($responseData['message'] ?? 'Unknown error occurred'));
            }
    
            // Format the response to return to your frontend
            return [
                'order_id' => $tx_ref,
                'payment_link' => $responseData['data']['link'] ?? null,
                'public_key' => $this->apiKey,
                'tx_ref' => $tx_ref,
                'amount' => $finalAmount,
                'currency' => $this->currencyCode,
                'customer' => [
                    'name' => $customMetaData['name'],
                    'email' => $customMetaData['email'],
                    'phone_number' => $customMetaData['mobile'] ?? '',
                    'school_id' => (int)$customMetaData['school_id'],
                    'student_id' => (int)$customMetaData['student_id'],
                    'parent_id' => (int)$customMetaData['parent_id'],
                    'session_year_id' => (int)$customMetaData['session_year_id']
                ],
                'meta' => [
                    'school_id' => $customMetaData['school_id'], // Custom metadata field
                    'student_id' => $customMetaData['student_id'],
                    'parent_id' => $customMetaData['parent_id'],
                    'fees_type' => $customMetaData['fees_type'],
                    'fees_id' => $customMetaData['fees_id'],
                    'installment' => $customMetaData['installment'] ?? [],
                    'advance_amount' => $customMetaData['advance_amount'],
                    'dueChargesAmount' => $customMetaData['dueChargesAmount'],
                    'session_year_id' => $customMetaData['session_year_id']
                ],
                'status' => 'pending',
                'redirect_url' => $redirectUrl,
                'cancel_url' => $cancelUrl
            ];
    
        } catch (Exception $e) {
            // Log error details for debugging
            Log::error('Flutterwave Payment Error:', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
    
            // Return a response indicating payment processing failure
            throw new Exception('Payment Processing Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Create and format a payment intent
     * @param $amount
     * @param $customMetaData
     * @return array
     */
    public function createAndFormatPaymentIntent($amount, $customMetaData): array {
        $paymentIntent = $this->createPaymentIntent($amount, $customMetaData);
        return $this->format($paymentIntent,$this->currencyCode, $amount, $customMetaData);
    }
    /**
     * Retrieve and format a payment intent
     * @param $paymentId
     * @return array
     * @throws Exception
     */
    public function retrievePaymentIntent($paymentId): array {
        try {
            if (empty($paymentId)) {
                throw new Exception('Payment ID is required for verification');
            }

            // Check if the paymentId is a transaction reference (tx_ref)
            $isTxRef = str_starts_with($paymentId, 'FLW-');

            if ($isTxRef) {
                Log::info('Verifying Flutterwave Transaction by Reference:', [
                    'tx_ref' => $paymentId,
                    'verification_url' => "https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref={$paymentId}"
                ]);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type' => 'application/json',
                ])->get("https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref={$paymentId}");
            } else {
                Log::info('Verifying Flutterwave Transaction by ID:', [
                    'transaction_id' => $paymentId,
                    'verification_url' => "https://api.flutterwave.com/v3/transactions/{$paymentId}/verify"
                ]);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type' => 'application/json',
                ])->get("https://api.flutterwave.com/v3/transactions/{$paymentId}/verify");
            }

            Log::info('Flutterwave Verification Raw Response:', [
                'status_code' => $response->status(),
                'body' => $response->json(),
                'headers' => $response->headers()
            ]);

            // Handle 404 Not Found - Transaction might be pending
            if ($response->status() === 404) {
                Log::info('Transaction not found, returning pending status', [
                    'payment_id' => $paymentId
                ]);
                
                // Return a pending status for transactions not found
                return $this->format(
                    [
                        'id' => $paymentId,
                        'status' => 'pending',
                        'message' => 'Transaction is pending or not completed'
                    ],
                    0,
                    $this->currencyCode,
                    [
                        'tx_ref' => $isTxRef ? $paymentId : null,
                        'transaction_id' => !$isTxRef ? $paymentId : null,
                        'status' => 'pending'
                    ]
                );
            }

            // Handle other API errors
            if ($response->failed()) {
                $responseData = $response->json();
                $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
                $errorCode = $responseData['code'] ?? 'unknown';
                
                Log::error('Flutterwave Verification API Error:', [
                    'status_code' => $response->status(),
                    'error_code' => $errorCode,
                    'error' => $errorMessage,
                    'body' => $response->json()
                ]);
                
                throw new Exception("API Error: {$errorMessage} (Code: {$errorCode})");
            }

            $responseData = $response->json();

            // Validate response structure
            if (!isset($responseData['status'])) {
                Log::error('Invalid Flutterwave Response Format:', [
                    'response' => $responseData
                ]);
                throw new Exception('Invalid response format from Flutterwave');
            }

            // Check for unsuccessful status
            if ($responseData['status'] !== 'success') {
                $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
                $errorCode = $responseData['code'] ?? 'unknown';
                
                Log::error('Flutterwave Verification Failed:', [
                    'error_code' => $errorCode,
                    'error' => $errorMessage,
                    'response' => $responseData
                ]);
                
                // If the error indicates the transaction is pending, return pending status
                if (stripos($errorMessage, 'pending') !== false || stripos($errorMessage, 'not found') !== false) {
                    return $this->format(
                        [
                            'id' => $paymentId,
                            'status' => 'pending',
                            'message' => $errorMessage
                        ],
                        0,
                        $this->currencyCode,
                        [
                            'tx_ref' => $isTxRef ? $paymentId : null,
                            'transaction_id' => !$isTxRef ? $paymentId : null,
                            'status' => 'pending'
                        ]
                    );
                }
                
                throw new Exception("Verification Failed: {$errorMessage} (Code: {$errorCode})");
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
                $transactionData,
                $transactionData['amount'] ?? 0,
                $transactionData['currency'] ?? $this->currencyCode,
                $transactionData
            );

        } catch (Exception $e) {
            Log::error('Flutterwave Verification Error:', [
                'payment_id' => $paymentId,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Transaction Verification Error: ' . $e->getMessage());
        }
    }
    /**
     * Format the payment intent response
     * @param $paymentIntent
     * @param $amount
     * @param $currency
     * @param $metadata
     * @return array
     */
    public function format($paymentIntent, $amount, $currency, $metadata): array {
        $status = $paymentIntent['status'] ?? 'unknown';
        
        if (isset($paymentIntent['data']) && isset($paymentIntent['data']['status'])) {
            $status = $paymentIntent['data']['status'];
        }

        // Extract metadata from the payment intent or use provided metadata
        $metadataFields = [
            'school_id' => $metadata['school_id'] ?? null,
            'student_id' => $metadata['student_id'] ?? null,
            'parent_id' => $metadata['parent_id'] ?? null,
            'session_year_id' => $metadata['session_year_id'] ?? null,
            'payment_transaction_id' => $metadata['payment_transaction_id'] ?? null,
            'fees_type' => $metadata['fees_type'] ?? null,
            'fees_id' => $metadata['fees_id'] ?? null,
            'metadata' => $metadata['metadata'] ?? []
        ];

        return [
            'id' => $paymentIntent['id'] ?? $paymentIntent['transaction_id'] ?? null,
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $metadataFields,
            'status' => match (strtolower($status)) {
                'successful', 'completed' => 'success',
                'failed', 'cancelled' => 'failed',
                'pending' => 'pending',
                default => 'unknown',
            },
            'payment_gateway_response' => array_merge($paymentIntent, [
                'school_id' => $metadataFields['school_id'],
                'student_id' => $metadataFields['student_id'],
                'parent_id' => $metadataFields['parent_id'],
                'session_year_id' => $metadataFields['session_year_id']
            ])
        ];
    }

    /**
     * Validate the minimum amount based on currency
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
            'id' => $id ?? $paymentIntent['id'] ?? $paymentIntent['transaction_id'] ?? null,
            'amount' => $amount ?? $paymentIntent['amount'] ?? 0,
            'currency' => $currency ?? $paymentIntent['currency'] ?? $this->currencyCode,
            'metadata' => $metadata ?? $paymentIntent,
            'status' => match (strtolower($status ?? $paymentIntent['status'] ?? 'unknown')) {
                'successful', 'completed' => 'success',
                'failed', 'cancelled' => 'failed',
                'pending' => 'pending',
                default => 'unknown',
            },
            'payment_gateway_response' => $paymentIntent
        ];
    }
}



