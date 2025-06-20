<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redirect;

class PaystackController extends Controller
{

    public function pay()  {
        $url = "https://api.paystack.co/transaction/initialize";

        $fields = [
            'email' => "customer@email.com",
            'amount' => "500000"
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $response = curl_exec($ch);
        echo $response;
    }

    public function callback(Request $request)
    {
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/:reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer SECRET_KEY",
            "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }


    // Initialize Paystack Transaction
    public function initializeTransaction(Request $request)
    {
        $email = $request->email; // The customer's email address
        $amount = $request->amount; // Amount in Kobo (e.g., 10000 Kobo = 100 NGN)
       
        $url = "https://api.paystack.co/transaction/initialize";

        $fields = [
            'email' => "customer@email.com",
            'amount' => "20000",
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = curl_exec($ch);
        echo $result;
    }

    // Verify Paystack Transaction
    public function verifyTransaction(Request $request)
    {
        $reference = $request->query('reference'); // Paystack transaction reference

        $client = new Client();
        $secretKey = env('PAYSTACK_SECRET_KEY'); // Secret key from .env

        try {
            $response = $client->get('https://api.paystack.co/transaction/verify/' . $reference, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secretKey,
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            if ($body['status'] && $body['data']['status'] === 'success') {
                // Payment was successful
                return response()->json([
                    'message' => 'Payment was successful!',
                    'payment_details' => $body['data'],
                ]);
            }

            return response()->json([
                'message' => 'Payment failed or was not completed.',
                'error' => $body['message'],
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error occurred while verifying payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
