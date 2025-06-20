<?php

namespace App\Services;

use App\Models\PaymentConfiguration;
use App\Models\Students;
use App\Models\Subscription;
use App\Models\SubscriptionBill;
use App\Models\User;
use App\Repositories\AddonSubscription\AddonSubscriptionInterface;
use App\Repositories\Package\PackageInterface;
use App\Repositories\PaymentTransaction\PaymentTransactionInterface;
use App\Repositories\Staff\StaffInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Repositories\SubscriptionBill\SubscriptionBillInterface;
use App\Repositories\SubscriptionFeature\SubscriptionFeatureInterface;
use App\Repositories\User\UserInterface;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Facades\Log;
use App\Services\ResponseService;

class SubscriptionService
{
    private UserInterface $user;
    private SubscriptionInterface $subscription;
    private PackageInterface $package;
    private SubscriptionFeatureInterface $subscriptionFeature;
    private CachingService $cache;
    private AddonSubscriptionInterface $addonSubscription;
    private StaffInterface $staff;
    private SubscriptionBillInterface $subscriptionBill;
    private PaymentTransactionInterface $paymentTransaction;

    public function __construct(UserInterface $user, SubscriptionInterface $subscription, PackageInterface $package, SubscriptionFeatureInterface $subscriptionFeature, CachingService $cache, AddonSubscriptionInterface $addonSubscription, StaffInterface $staff, SubscriptionBillInterface $subscriptionBill, PaymentTransactionInterface $paymentTransaction)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->package = $package;
        $this->subscriptionFeature = $subscriptionFeature;
        $this->cache = $cache;
        $this->addonSubscription = $addonSubscription;
        $this->staff = $staff;
        $this->subscriptionBill = $subscriptionBill;
        $this->paymentTransaction = $paymentTransaction;
    }


    /**
     * @param $package_id
     * @param $school_id
     * @param $isCurrentPlan
     * @return Model|null
     */
    public function createSubscription($package_id, $school_id = null, $subscription_id = null, $isCurrentPlan = null)
    {
        // package_id => Create that package
        // school_id => if super admin can assign package, then school id is compulsory
        // subscription_id => if school admin already set upcoming plan update only that plan
        // isCurrentPlan => school admin can set current plan & upcoming plan also

        $settings = $this->cache->getSystemSettings();
        $package = $this->package->builder()->with('package_feature')->where('id', $package_id)->first();
        $end_date = '';
        if (!$school_id) {
            $school_id = Auth::user()->school_id;
        }
        if ($package->is_trial) {
            $end_date = Carbon::now()->addDays(($settings['trial_days']))->format('Y-m-d');
        } else {
            $end_date = Carbon::now()->addDays(($package->days - 1))->format('Y-m-d');
        }
        $start_date = Carbon::now()->format('Y-m-d');



        // If not current subscription plan
        if (!$isCurrentPlan) {
            // Attempt to get the current active subscription
            $current_subscription = $this->active_subscription($school_id);

            // Check if a current subscription was found
            if ($current_subscription) {
                $start_date = Carbon::parse($current_subscription->end_date)->addDays()->format('Y-m-d');
                $end_date = Carbon::parse($start_date)->addDays(($package->days - 1))->format('Y-m-d');
            } else {
                // Handle the case where there is no active subscription
                Log::warning("No active subscription found for school_id: {$school_id}");
                // You might want to set default dates or handle this case differently
                $start_date = Carbon::now()->format('Y-m-d');
                $end_date = Carbon::now()->addDays($package->days - 1)->format('Y-m-d');
            }
        }

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
            'school_id'      => $school_id,
            'charges'        => $package->charges
        ];

        // Check subscription update or create
        // If school has already set upcoming plan
        if ($subscription_id) {
            $subscription = $this->subscription->update($subscription_id, $subscription_data);
        } else {
            $subscription = $this->subscription->create($subscription_data);
        }


        // If current subscription plan then set package features
        if ($isCurrentPlan) {
            $subscription_features = array();
            foreach ($package->package_feature as $key => $feature) {
                $subscription_features[] = [
                    'subscription_id' => $subscription->id,
                    'feature_id'      => $feature->feature_id
                ];
            }
            $this->subscriptionFeature->upsert($subscription_features, ['subscription_id', 'feature_id'], ['subscription_id', 'feature_id']);
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.FEATURES'), $subscription->school_id);


            // If prepaid plan generate bill first
            if ($package->type == 0) {
                $subscription_bill[] = [
                    'subscription_id' => $subscription->id,
                    'amount'          => $package->charges,
                    'total_student'   => $package->no_of_students,
                    'total_staff'     => $package->no_of_staffs,
                    'due_date'        => Carbon::now(),
                    'school_id'       => $subscription->school_id
                ];
                if (Auth::user() && !Auth::user()->hasRole('School Admin')) {
                    $billData = [
                        'user_id' => $subscription->school->admin_id,
                        'amount' => $package->charges,
                        'payment_gateway' => 'Cash',
                        'school_id' => $subscription->school_id,
                        'payment_status' => 'succeed'
                    ];

                    $paymentTransaction = $this->paymentTransaction->create($billData);
                    $subscription_bill[0]['payment_transaction_id'] = $paymentTransaction->id;
                }
                // $subscription_bill = $this->subscriptionBill->create($subscription_bill);
                SubscriptionBill::upsert($subscription_bill, ['subscription_id', 'school_id'], ['amount', 'total_student', 'total_staff', 'due_date']);
                // return $subscription_bill = $this->subscriptionBill->upsert($subscription_bill,['subscription_id','school_id'],['amount','total_student','total_staff','due_date']);
            }
        }

        return $subscription;
    }

    /**
     * @param $generateBill
     * @return Model|null
     */
    public function createSubscriptionBill($subscription, $generateBill = null)
    {
        // GenerateBill [ null => Generate immediate bill, 1 => Generate regular bill ]

        // Set school database connection for getting user counts
        Config::set('database.connections.school.database', $subscription->school->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        // $students = User::on('school')->withTrashed()->where(function ($q) use ($subscription) {
        //     $q->whereBetween('deleted_at', [$subscription->start_date, $subscription->end_date]);
        // })->orWhereNull('deleted_at')->role('Student')->where('school_id', $subscription->school_id)->count();

        $students = Students::on('school')->whereHas('user',function($q) use($subscription) {
            $q->withTrashed()->where(function ($q) use ($subscription) {
                $q->whereBetween('deleted_at', [$subscription->start_date, $subscription->end_date]);
            })->orWhereNull('deleted_at')->where('school_id', $subscription->school_id);
        })->has('user')->count();

        $staffs = $this->staff->builder()->whereHas('user', function ($q) use ($subscription) {
            $q->where(function ($q) use ($subscription) {
                $q->withTrashed()->whereBetween('deleted_at', [$subscription->start_date, $subscription->end_date])
                    ->orWhereNull('deleted_at');
            })->where('school_id', $subscription->school_id);
        })->count();

        DB::setDefaultConnection('mysql');

        $today_date = Carbon::now()->format('Y-m-d');
        $start_date = Carbon::parse($subscription->start_date);
        if ($generateBill) {
            $usage_days = $start_date->diffInDays($subscription->end_date) + 1;
        } else {
            $usage_days = $start_date->diffInDays($today_date) + 1;
        }
        $bill_cycle_days = $subscription->billing_cycle;


        // Get addon total
        $addons = $this->addonSubscription->builder()->where('subscription_id', $subscription->id)->sum('price');

        $student_charges = number_format((($usage_days * $subscription->student_charge) / $bill_cycle_days), 2) * $students;
        $staff_charges = number_format((($usage_days * $subscription->staff_charge) / $bill_cycle_days), 2) * $staffs;

        $systemSettings = $this->cache->getSystemSettings();

        $subscription_bill = [
            'subscription_id' => $subscription->id,
            'amount'          => ($student_charges + $staff_charges + $addons),
            'total_student'   => $students,
            'total_staff'     => $staffs,
            'due_date'        => Carbon::now()->addDays($systemSettings['additional_billing_days'])->format('Y-m-d'),
            'school_id'       => $subscription->school_id
        ];
        // Create bill for active plan
        return $subscription_bill = $this->subscriptionBill->create($subscription_bill);
    }

    // Check subscription pending bills
    public function subscriptionPendingBill()
    {
        $subscriptionBill = $this->subscriptionBill->builder()->whereHas('transaction', function ($q) {
            $q->whereNot('payment_status', "succeed");
        })->orDoesntHave('transaction')->where('school_id', Auth::user()->school_id)->whereNot('amount', 0)->first();
        return $subscriptionBill;
    }


    // Stripe payment gateway
    public function stripe_payment($subscriptionBill_id = null, $package_id = null, $type = null, $subscription_id = null, $isCurrentPlan = null)
    {
        try {
            Log::info('Starting Stripe payment process');
            
            $settings = app(CachingService::class)->getSystemSettings();
            $name = '';
            $amount = 0;
            if ($subscriptionBill_id) {
                $subscriptionBill = $this->subscriptionBill->findById($subscriptionBill_id);
                $name = $subscriptionBill->subscription->name;
                $amount = $subscriptionBill->amount;
                $package_id = -1;
            }

            if ($package_id) {
                $package = $this->package->findById($package_id);
                $name = $package->name;
                $amount = $package->charges;
                $subscriptionBill_id = -1;

            }
        
            if ($type == null) {
                $type = -1;
            }
        
            if (!$subscription_id) {
                $subscription_id = -1;
            }
            if (!$isCurrentPlan) {
                $isCurrentPlan = -1;
            }

            // Get payment configuration
            DB::setDefaultConnection('mysql');
            $paymentConfiguration = PaymentConfiguration::where('school_id', null)
                ->where('payment_method', 'stripe')
                ->where('status', 1)
                ->first();

            if (!$paymentConfiguration) {
                Log::error('Stripe payment configuration not found or disabled');
                return redirect()->back()->with('error', trans('Stripe payment is not available'));
            }

            $stripe_secret_key = $paymentConfiguration->secret_key ?? null;
            if (empty($stripe_secret_key)) {
                Log::error('Stripe secret key is missing');
                return redirect()->back()->with('error', trans('Stripe API key is not configured'));
            }

            $currency = $paymentConfiguration->currency_code;
            Log::info('Processing payment with currency: ' . $currency);

            // Validate minimum amount
            $checkAmount = $this->checkMinimumAmount(strtoupper($currency), $amount);
            $checkAmount = (float)$checkAmount;
            $checkAmount = round($checkAmount, 2);

            Log::info('Validated amount: ' . $checkAmount . ' (' . $currency . ')');
            
            // Stripe payment
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.stripe.com/v1/checkout/sessions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $stripe_secret_key,
                    'Content-Type: application/x-www-form-urlencoded',
                    'Stripe-Version: 2022-11-15'
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'line_items[0][price_data][currency]' => strtolower($currency),
                    'line_items[0][price_data][product_data][name]' => $name,
                    'line_items[0][price_data][unit_amount]' => (int)($checkAmount * 100),
                    'line_items[0][quantity]' => 1,
                    'mode' => 'payment',
                    'success_url' => url('subscriptions/payment/success') . '/{CHECKOUT_SESSION_ID}' . '/' . $subscriptionBill_id . '/' . $package_id . '/' . $type . '/' . $subscription_id . '/' . $isCurrentPlan,
                    'cancel_url' => url('subscriptions/payment/cancel') . '/' . $subscriptionBill_id,
                    'metadata[subscription_bill_id]' => $subscriptionBill_id,
                    'metadata[package_id]' => $package_id, 
                    'metadata[type]' => $type,
                    'metadata[subscription_id]' => $subscription_id,
                    'metadata[is_current_plan]' => $isCurrentPlan,
                    'metadata[school_id]' => Auth::user()->school_id ?? null
                ])
            ]);

            // Execute cURL request
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                Log::error('Curl Error: ' . curl_error($ch));
                curl_close($ch);
                return redirect()->back()->with('error', trans('Connection error occurred'));
            }

            curl_close($ch);

            $session = json_decode($response, true);
            
            if (isset($session['error'])) {
                Log::error('Stripe API Error: ' . $session['error']['message']);
                return redirect()->back()->with('error', trans('Stripe error: ') . $session['error']['message']);
            }
       
            return redirect()->away($session['url'])->with('success', trans('The stripe payment has been successful'));

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage());
            Log::error('Stripe API Error Code: ' . $e->getStripeCode());
            Log::error('Stripe API Error Type: ' . $e->getStripeType());
            return redirect()->back()->with('error', trans('Stripe payment error: ') . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error in Stripe Payment: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', trans('server_not_responding'));
        }
    }

    // Paystack Payment
    public function paystack_payment($subscriptionBill_id = null, $package_id = null, $type = null, $subscription_id = null, $isCurrentPlan = null)
    {
        try {
            $settings = app(CachingService::class)->getSystemSettings();
            // dd($settings);
            // die;
            $name = '';
            $amount = 0;
        
            if ($subscriptionBill_id) {
                $subscriptionBill = $this->subscriptionBill->findById($subscriptionBill_id);
                $name = $subscriptionBill->subscription->name;
                $amount = $subscriptionBill->amount;
            }

            if ($package_id) {
                $package = $this->package->findById($package_id);
                $name = $package->name;
                $amount = $package->charges;
                $subscriptionBill_id = -1;

            }
        
            if ($type == null) {
                $type = -1;
            }
        
            if (!$subscription_id) {
                $subscription_id = -1;
            }
            if (!$isCurrentPlan) {
                $isCurrentPlan = -1;
            }
        

            // Set default values
            $type = $type ?? -1;
            $subscription_id = $subscription_id ?? -1; 
            $isCurrentPlan = $isCurrentPlan ?? -1;

            // Access the model directly via data for super admin data
            DB::setDefaultConnection('mysql');
            $paymentConfiguration = PaymentConfiguration::where('school_id', null)->where('payment_method', 'Paystack')->where('status', 1)->first();
          
            if ($paymentConfiguration && !$paymentConfiguration->status) {
                return redirect()->back()->with('error', trans('Current Paystack payment not available'));
            }

            $paystack_secret_key = $paymentConfiguration->secret_key ?? null;
            if (empty($paystack_secret_key)) {
                return redirect()->back()->with('error', trans('No API key provided'));
            }
            $currency = $paymentConfiguration->currency_code;

            \Log::info('Paystack currency: ' . $currency);

            $checkAmount = $this->checkMinimumAmount(strtoupper($currency), $amount);
            $checkAmount = (float)$checkAmount;
            $checkAmount = round($checkAmount, 2);

            // Prepare request data
            $data = [
                'amount' => ($checkAmount * 100),
                'email' => Auth::user()->email,
                'currency' => $currency,
                'redirect_url' => url('subscriptions/payment/success') . '/{CHECKOUT_SESSION_ID}' . '/' . $subscriptionBill_id . '/' . $package_id . '/' . $type . '/' . $subscription_id . '/' . $isCurrentPlan,
                'callback_url' => url('subscriptions/history'),
                'metadata' => [
                    'package_id' => $package_id,
                    'type' => $type, 
                    'name' => 'package', // Using name instead of package_type to match webhook handling
                    'subscription_id' => $subscription_id,
                    'is_current_plan' => $isCurrentPlan,
                    'school_id' => Auth::user()->school_id ?? null,
                    'user_id'         => Auth::user()->id,
                    'payment_transaction_id' => $paymentTransaction->id ?? null
                ]
            ];

            // Make HTTP request to Paystack API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystack_secret_key,
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache'
            ])->post('https://api.paystack.co/transaction/initialize', $data);

            \Log::info('Paystack response: ' . $response->body());

            
            // Check if request was successful
            if (!$response->successful()) {
                Log::error('Paystack API Error: ' . $response->body());
                return redirect()->back()->with('error', trans('Paystack error: ') . $response->json()['message']);
            }
            
            $result = $response->json();

            // if ($result['status']) {
            //     // update flutterwave payment transaction table
            //     $paymentTransactionData = array(
            //         'user_id'         => Auth::user()->id,
            //         'amount'          => $amount,
            //         'payment_gateway' => 'Paystack',
            //         'order_id'        => $result['data']['reference'],
            //         'payment_status'  => 'pending',
            //         'school_id'       => Auth::user()->school_id,
            //         'created_at'      => now(),
            //         'updated_at'      => now(),
            //     );

            //     $this->paymentTransaction->create($paymentTransactionData);
            // }

            // Verify response status
            if (!$result['status']) {
                Log::error('Paystack API Error: ' . $result['message']);
                return redirect()->back()->with('error', trans('Paystack error: ') . $result['message']);
            }

            Log::info('Paystack payment initialized successfully: ' . $result['data']['reference']);
            return redirect()->away($result['data']['authorization_url'])->with('success', trans('The paystack payment has been successful'));

        } catch (\Exception $e) {
            Log::error('General Error in Paystack Payment: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', trans('server_not_responding'));
        }
    }

    // Flutterwave Payment
    public function flutterwave_payment($subscriptionBill_id = null, $package_id = null, $type = null, $subscription_id = null, $isCurrentPlan = null)
    {
        try {
            DB::beginTransaction();
            
            $settings = app(CachingService::class)->getSystemSettings();
            $name = '';
            $amount = 0;
        
            if ($subscriptionBill_id) {
                $subscriptionBill = $this->subscriptionBill->findById($subscriptionBill_id);
                $name = $subscriptionBill->subscription->name;
                $amount = $subscriptionBill->amount;
                $package_id = -1;
            }
            
            if ($package_id != -1) {
                $package = $this->package->findById($package_id);
                $name = $package->name;
                $amount = $package->charges;
                $subscriptionBill_id = -1;
            }
        
            if ($type == null) {
                $type = -1;
            }
        
            if (!$subscription_id) {
                $subscription_id = -1;
            }
            if (!$isCurrentPlan) {
                $isCurrentPlan = -1;
            }
        
            // Get payment configuration
            DB::setDefaultConnection('mysql');
            $paymentConfiguration = PaymentConfiguration::where('school_id', null)
                ->where('status', 1)
                ->first();
                
            \Log::info("Flutterwave Payment Configuration: " . json_encode($paymentConfiguration));
            
            if (!$paymentConfiguration || !$paymentConfiguration->status) {
                throw new \Exception(trans('Current Flutterwave payment not available'));
            }
            
            if (empty($paymentConfiguration->api_key)) {
                throw new \Exception(trans('No API key provided for Flutterwave'));
            }
            
            $currency = $paymentConfiguration->currency_code;
            $checkAmount = (float)$this->checkMinimumAmount(strtoupper($currency), $amount);
            
            \Log::info("Flutterwave Check Amount: " . $checkAmount);
        
            // Create payment transaction record first
            $paymentTransactionData = [
                'user_id' => Auth::user()->id,
                'amount' => $amount,
                'payment_gateway' => 'Flutterwave',
                'order_id' => 'tx_' . time(),
                'payment_status' => 'pending',
                'school_id' => Auth::user()->school_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $paymentTransaction = $this->paymentTransaction->create($paymentTransactionData);
            
            if (!$paymentTransaction) {
                throw new \Exception(trans('Failed to create payment transaction'));
            }
            
            // Update subscription bill with payment transaction ID
            if ($subscriptionBill_id != -1) {
                $this->subscriptionBill->update($subscriptionBill_id, [
                    'payment_transaction_id' => $paymentTransaction->id
                ]);
            }
        
            // Prepare Flutterwave API request
            $request = [
                'tx_ref' => $paymentTransaction->order_id,
                'amount' => $checkAmount,
                'currency' => strtoupper($currency),
                'email' => Auth::user()->email,
                'order_id' => $subscriptionBill_id,
                'order_name' => $name,
                'customer' => [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->full_name,
                    'mobile' => Auth::user()->mobile,
                ],
                'meta' => [
                    'subscription_bill_id' => $subscriptionBill_id,
                    'package_id' => $package_id,
                    'type' => 'package',
                    'subscription_id' => $subscription_id,
                    'is_current_plan' => $isCurrentPlan,
                    'school_id' => Auth::user()->school_id ?? null,
                    'user_id' => Auth::user()->id,
                    'payment_transaction_id' => $paymentTransaction->id
                ],
                'redirect_url' => url('subscriptions/history'),
                'cancel_url' => url('subscriptions/payment/cancel') . '/' . $subscriptionBill_id,
            ];
                       
            // Make request to Flutterwave API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paymentConfiguration->secret_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', $request);

            $res = $response->json();
            \Log::info('Flutterwave response: ' . json_encode($res));
            
            if ($res['status'] !== 'success') {
                ResponseService::errorResponse($res['message']);
            }
            
            DB::commit();
            return redirect()->away($res['data']['link'])->with('success', trans('The flutterwave payment has been successful'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flutterwave Payment Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function active_subscription($schoolId)
    {
        $today_date = Carbon::now()->format('Y-m-d');
        $subscription = Subscription::where('school_id', $schoolId)->whereDate('start_date', '<=', $today_date)->whereDate('end_date', '>=', $today_date)->latest()->first();

        if ($subscription) {
            if ($subscription->package_type == 1) {
                // Postpaid
                $subscription = Subscription::where('school_id', $schoolId)->where('package_type', 1)->whereDate('start_date', '<=', $today_date)->whereDate('end_date', '>=', $today_date)->with('subscription_feature.feature', 'addons.feature', 'package.package_feature.feature')->doesntHave('subscription_bill')->has('subscription_feature')->latest()->first();
            } else {
                // Prepaid
                $subscription = Subscription::where('school_id', $schoolId)->where('package_type', 0)->whereDate('start_date', '<=', $today_date)->whereDate('end_date', '>=', $today_date)->with('package.package_feature.feature')->has('subscription_bill')->with('subscription_feature.feature')->with(['addons' => function ($q) {
                    $q->has('transaction')->with('feature')->whereHas('transaction', function ($q) {
                        $q->where('payment_status', "succeed");
                    });
                }])->whereHas('subscription_bill.transaction', function ($q) {
                    $q->where('payment_status', "succeed");
                })->has('subscription_feature')->latest()->first();
            }
        } else {
            return null;
        }

        return $subscription;
    }

    public function check_user_limit($subscription, $type)
    {
        // type [ Students / Staffs ]
        if ($type == "Students") {
            $students = $this->user->builder()->where('status', 1)->role('Student')->where('school_id', $subscription->school_id)->count();
            if ($students >= $subscription->no_of_students) {
                return false;
            }
            return true;
        } else {
            $staffs = $this->staff->builder()->whereHas('user', function ($q) use ($subscription) {
                $q->where('status', 1)->where('school_id', $subscription->school_id);
            })->count();
            if ($staffs >= $subscription->no_of_staffs) {
                return false;
            }
            return true;
        }
    }

    public function prepaid_addon_payment($addonSubscriptionId)
    {
        try {
            $settings = app(CachingService::class)->getSystemSettings();
            // $subscriptionBill = $this->subscriptionBill->findById($subscriptionBill_id);
            $addonSubscription = $this->addonSubscription->findById($addonSubscriptionId);

            // Access the model directly via data for super admin data, use the interface builder for school-specific data.
            DB::setDefaultConnection('mysql');
            $paymentConfiguration = PaymentConfiguration::where('school_id', null)->where('status', 1)->first();

            if ($paymentConfiguration && !$paymentConfiguration->status) {
                return redirect()->back()->with('error', trans('Current payment method not available'));
            }

            // Stripe Payment
            if($paymentConfiguration->payment_method == 'Stripe') {
                $stripe_secret_key = $paymentConfiguration->secret_key ?? null;

                if (empty($stripe_secret_key)) {
                    return redirect()->back()->with('error', trans('No API key provided'));
                }

                $amount = $addonSubscription->price;
                $currency = $paymentConfiguration->currency_code;

                // Validate minimum amount
                $checkAmount = $this->checkMinimumAmount(strtoupper($currency), $amount);
                $checkAmount = (float)$checkAmount;
                $checkAmount = round($checkAmount, 2);

                Log::info('Validated amount: ' . $checkAmount * 100);

                Stripe::setApiKey($stripe_secret_key);
                $session = StripeSession::create([
                    'line_items'  => [
                        [
                            'price_data' => [
                                'currency'     => $currency,
                                'product_data' => [
                                    'name'   => $addonSubscription->feature->name,
                                    'images' => [$settings['horizontal_logo'] ?? 'logo.svg'],
                                ],
                                'unit_amount'  => $checkAmount * 100,
                            ],
                            'quantity'   => 1,
                        ],
                    ],
                    'mode'        => 'payment',
                    'success_url' => url('addons/payment/success') . '/{CHECKOUT_SESSION_ID}' . '/' . $addonSubscriptionId,
                    'cancel_url'  => url('addons/payment/cancel'),
                ]);

                return redirect()->away($session->url);

            }

            // Paystack Payment
            if($paymentConfiguration->payment_method == 'Paystack') {

                try {
                    $paystack_secret_key = $paymentConfiguration->secret_key ?? null;
                    $currency = $paymentConfiguration->currency_code;
                    $checkAmount = $this->checkMinimumAmount(strtoupper($currency), $addonSubscription->price);
                    $amount = (float) str_replace(',', '', number_format($checkAmount, 2));

                    
                    if (empty($paystack_secret_key)) {
                        return redirect()->back()->with('error', trans('No API key provided'));
                    }

                    $data = [
                        'amount' => $amount * 100,
                        'email' => Auth::user()->email,
                        'currency' => strtoupper($currency),
                        'metadata' => [
                            'type' => 'addon',
                            'subscription_id' => $addonSubscription->subscription_id,
                            'feature_id' => $addonSubscription->feature_id,
                            'addon_subscription_id' => $addonSubscriptionId,
                            'school_id' => Auth::user()->school_id,
                            'user_id' => Auth::user()->id,
                            'price' => $addonSubscription->price,
                            'end_date' => $addonSubscription->end_date,
                        ],
                        'callback_url' => route('addons.payment.success_callback')
                    ];

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $paystack_secret_key,
                        'Content-Type' => 'application/json',
                    ])->post('https://api.paystack.co/transaction/initialize', $data);
                        
                    $res = $response->json();

                    \Log::info('Paystack response: ' . json_encode($res));
                   
                    if(!$res['status']) {
                        return redirect()->back()->with('error', $res['message']);
                    }

                    if ($res['status']) {
                        return redirect()->away($res['data']['authorization_url'])->with('reload', true)->with('success', trans('The paystack payment has been successful'));
                    }

                    return redirect('addons/plan')->back()->with('error', trans('Paystack payment failed'));

                } catch (\Throwable $th) {
                    \Log::error('Paystack Payment Error: ' . $th->getMessage());
                    DB::rollBack();
                    return redirect()->back()->with('error', trans('server_not_responding'));
                }
                
            }

            // Flutterwave Payment
            if($paymentConfiguration->payment_method == 'Flutterwave') {
                $flutterwave_secret_key = $paymentConfiguration->secret_key ?? null;
                $currency = $paymentConfiguration->currency_code;
                $amount = (float) number_format((float) ceil((float) $addonSubscription->price * 100) / 100, 2);

                if (empty($flutterwave_secret_key)) {
                    return redirect()->back()->with('error', trans('No API key provided'));
                }

                $data = [
                    'tx_ref' => 'tx_' . time(),
                    'amount' => $amount * 100,
                    'currency' => strtoupper($currency),
                    'email' => Auth::user()->email,
                    'customer' => [
                        'email' => Auth::user()->email,
                        'name' => Auth::user()->full_name,
                        'mobile' => Auth::user()->mobile,
                    ],
                    'meta' => [
                        'type' => 'addon',
                        'subscription_id' => $addonSubscription->subscription_id,
                        'feature_id' => $addonSubscription->feature_id,
                        'addon_subscription_id' => $addonSubscriptionId,
                        'school_id' => Auth::user()->school_id,
                        'user_id' => Auth::user()->id,
                    ],
                    'redirect_url' => url('addons/payment/success'),
                ];


                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $flutterwave_secret_key,
                    'Content-Type' => 'application/json',
                ])->post('https://api.flutterwave.com/v3/payments', $data);

                $res = $response->json();
                \Log::info('Flutterwave response: ' . json_encode($res));

                if (!$response->successful()) {
                    throw new \Exception('Flutterwave API request failed: ' . ($res['message'] ?? 'Unknown error'));
                }

                if(!$res['status']) {
                    return redirect()->back()->with('error', $res['message']);
                }

                if ($res['status']) {
                    return redirect()->away($res['data']['link'])->with('reload', true)->with('success', trans('The flutterwave payment has been successful'));
                }

            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', trans('server_not_responding'));
        }
    }


    /**
     * @param string|float $currency
     * @param string|float $amount
     * @return float
     */
    public function checkMinimumAmount($currency, $amount)
    {
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
}
