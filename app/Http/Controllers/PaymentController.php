<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentConfiguration;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\School;
use Illuminate\Support\Facades\Http;
class PaymentController extends Controller
{
    /**
     * Handle the payment status callback
     */
    public function status(Request $request)
    {
        Log::info('Payment Status Callback:', $request->all());

        // Get school code from request
        $schoolId = $request->query('school_id');
        if (!$schoolId) {
            return response()->json(['error' => 'School Id is required'], 400);
        }

        // Get school details from main database
        $school = School::on('mysql')->where('id', $schoolId)->first();

        if (!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }

        // Set up school database connection
        Config::set('database.connections.school.database', $school->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        // Get payment gateway configuration from school database
        $paymentGateway = PaymentConfiguration::where('school_id', $school->id)->where('status', 1)->first();

        if (!$paymentGateway) {
            return response()->json(['error' => 'Payment Gateway not found'], 404);
        }

        if($paymentGateway->payment_method == 'Paystack') {
            // Get payment reference from request
            $reference = $request->query('reference');
            if (!$reference) {
                return response()->json(['error' => 'Transaction reference is required'], 400);
            }

            // Get payment status from request
            $status = $request->query('status');
            
            // Handle cancelled payment
            if ($status === 'cancelled') {
                Log::info('Payment was cancelled:', [
                    'reference' => $reference,
                    'school_id' => $schoolId
                ]);
                
                // Update payment transaction status to failed
                $paymentTransaction = PaymentTransaction::where('order_id', $reference)->first();
                if ($paymentTransaction) {
                    $paymentTransaction->update(['payment_status' => 'failed']);
                }
                
                return redirect()->route('payment.status', ['status' => 'cancelled', 'school_id' => $schoolId, 'trxref' => $reference, 'reference' => $reference])->with('error', 'Payment was cancelled.');
            }

            // For successful payments, verify with Paystack API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentGateway->secret_key,
                'Content-Type' => 'application/json',
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            $data = $response->json();
            Log::info('Paystack verification response:', $data);

            if ($response->successful() && isset($data['data']['status']) && $data['data']['status'] === 'success') {
                // Update payment transaction
                $paymentTransaction = PaymentTransaction::where('order_id', $reference)->first();
                if ($paymentTransaction) {
                    $paymentTransaction->update([
                        'payment_status' => 'succeed',
                        'payment_id' => $reference
                    ]);
                }
                
                return redirect()->route('payment.status', ['status' => 'success', 'school_id' => $schoolId, 'trxref' => $reference, 'reference' => $reference])
                    ->with('success', 'Payment completed successfully');
            } else {
                Log::error('Paystack payment verification failed:', [
                    'reference' => $reference,
                    'response' => $data
                ]);
                
                // Update payment transaction status to failed
                $paymentTransaction = PaymentTransaction::where('order_id', $reference)->first();
                if ($paymentTransaction) {
                    $paymentTransaction->update(['payment_status' => 'failed']);
                }
                
                return redirect()->route('payment.cancel')
                    ->with('error', $data['message'] ?? 'Payment verification failed');
            }
        } else if ($paymentGateway->payment_method == 'Flutterwave') {
            // Flutterwave implementation
            $paymentTransactionId = $request->query('tx_ref');
            if (!$paymentTransactionId) {
                return response()->json(['error' => 'Transaction ID is required'], 400);
            }

            $paymentTransaction = PaymentTransaction::where('order_id', $paymentTransactionId)->first();

            if (!$paymentTransaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            if ($paymentTransaction->payment_status === "succeed") {
                return response()->json(['status' => 'success', 'message' => 'Transaction already processed']);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentGateway->secret_key,
                'Content-Type' => 'application/json',
            ])->get("https://api.flutterwave.com/v3/transactions/{$paymentTransactionId}/verify");

            $data = $response->json();

            if ($response->successful() && $data['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'transaction' => $data['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed',
                    'error' => $data['message'] ?? 'Unknown error'
                ]);
            }     
            
        } else {
            return response()->json(['error' => 'Payment Gateway not found'], 404);
        }
    }

    /**
     * Handle payment cancellation
     */
    public function cancel()
    {
        return view('payment.cancel')->with('error', 'Payment was cancelled or failed.');
    }

    public function success()
    {
        return view('payment.success')->with('success', 'Payment completed successfully.');
    }
} 