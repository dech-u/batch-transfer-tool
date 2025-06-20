<?php

namespace App\Http\Controllers;

use App\Models\PaymentConfiguration;
use App\Repositories\Addon\AddonInterface;
use App\Repositories\AddonSubscription\AddonSubscriptionInterface;
use App\Repositories\Feature\FeatureInterface;
use App\Repositories\PaymentTransaction\PaymentTransactionInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\FeaturesService;
use App\Services\ResponseService;
use App\Services\SubscriptionService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Checkout\Session as StripeSession;

class AddonController extends Controller {

    private FeatureInterface $feature;
    private AddonInterface $addon;
    private SubscriptionInterface $subscription;
    private AddonSubscriptionInterface $addonSubscription;
    private CachingService $cache;
    private SubscriptionService $subscriptionService;
    private PaymentTransactionInterface $paymentTransaction;
    private FeaturesService $featureSerive;

    public function __construct(FeatureInterface $feature, AddonInterface $addon, SubscriptionInterface $subscription, AddonSubscriptionInterface $addonSubscription, CachingService $cachingService, SubscriptionService $subscriptionService, PaymentTransactionInterface $paymentTransaction, FeaturesService $featureSerive) {
        $this->feature = $feature;
        $this->addon = $addon;
        $this->subscription = $subscription;
        $this->addonSubscription = $addonSubscription;
        $this->cache = $cachingService;
        $this->subscriptionService = $subscriptionService;
        $this->paymentTransaction = $paymentTransaction;
        $this->featureSerive = $featureSerive;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('addons-list');
        $features = $this->feature->builder()->where('is_default', 0)->orderBy('name','ASC')->get();
        return view('addons.index', compact('features'));
    }

    public function plan() {
        ResponseService::noRoleThenRedirect('School Admin');
        $addons = $this->addon->builder()->with('feature')->where('status', 1)->get();
        $settings = app(CachingService::class)->getSystemSettings();
        $system_settings = $settings;
        $features = $this->featureSerive->getFeatures();
        $features = array_keys($features);
        $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
        
        DB::setDefaultConnection('mysql');
        $paymentConfiguration = PaymentConfiguration::where('school_id', null)->where('status',1)->first();
        DB::setDefaultConnection('school');
        return view('addons.plan', compact('addons', 'settings','subscription','features', 'paymentConfiguration','system_settings'));
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('addons-create');
        $request->validate([
            'name'       => 'required',
            'price'      => 'required|decimal:0,2',
            'feature_id' => 'required|unique:addons'
        ], [
            'feature_id.required' => trans('please_select_feature'),
            'feature_id.unique'   => trans('you_have_previously_created_an_addon_for_this_feature'),
        ]);
        try {
            DB::beginTransaction();
            $data = [
                ...$request->all(),
                'status'     => 0
            ];
            $this->addon->create($data);
            DB::commit();
            ResponseService::successResponse('Data Store Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Store method');
            ResponseService::errorResponse();
        }
    }


    public function show() {
        ResponseService::noPermissionThenRedirect('addons-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $showDeleted = request('show_deleted');

        $sql = $this->addon->builder()->with('feature')
            ->where(function ($q) use ($search) {
                $q->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->orwhere('price', 'LIKE', "%$search%");
                    });
                });
            })->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed();
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            if ($showDeleted) {
                $operate = BootstrapTableService::restoreButton(route('addons.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('addons.trash', $row->id));
            } else {
                if ($row->status == 0) {
                    $operate = BootstrapTableService::button('fa fa-check', '#', ['change-addon-status', 'btn-gradient-success'], ['title' => trans("publish_addon"), 'data-id' => $row->id]);
                } else {
                    $operate = BootstrapTableService::button('fa fa-times', '#', ['change-addon-status', 'btn-gradient-warning'], ['title' => trans("unpublished_addon"), 'data-id' => $row->id]);
                }
                $operate .= BootstrapTableService::editButton(route('addons.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('addons.destroy', $row->id));
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['price'] = number_format($row->price, 2);
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function edit($id) {
        //
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('addons-edit');
        $request->validate([
            'name'       => 'required',
            'price'      => 'required|decimal:0,2',
            'feature_id' => 'required|unique:addons,feature_id,' . $id,
        ], [
            'feature_id.unique' => trans('you_have_previously_created_an_addon_for_this_feature'),
            'name.required' => 'The name is required',
            'price.required' => 'The price is required',
        ]);
        try {
            DB::beginTransaction();
            $data = [
                ...$request->all(),
            ];
            $this->addon->update($id, $data);
            DB::commit();
            ResponseService::successResponse('Data updated successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Update method');
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('addons-delete');
        try {
            DB::beginTransaction();
            $this->addon->findById($id)->delete();
            DB::commit();
            ResponseService::successResponse('Data deleted successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Delete method');
            ResponseService::errorResponse();
        }
    }

    public function restore($id) {
        ResponseService::noPermissionThenSendJson('addons-edit');
        try {
            DB::beginTransaction();
            $this->addon->restoreById($id);
            DB::commit();
            ResponseService::successResponse('Data restore successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Restore method');
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('addons-delete');
        try {
            DB::beginTransaction();
            $addon = $this->addon->findOnlyTrashedById($id);
            if (count($addon->addon_subscription)) {
                ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
            } else {
                $this->addon->permanentlyDeleteById($id);
            }


            DB::commit();
            ResponseService::successResponse('Data deleted successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Trash method');
            ResponseService::errorResponse();
        }
    }

    public function status($id) {
        ResponseService::noAnyPermissionThenSendJson(['addons-create', 'addons-edit']);
        try {
            DB::beginTransaction();
            $addon = $this->addon->findById($id);
            $addon = ['status' => $addon->status == 1 ? 0 : 1];
            $this->addon->update($id, $addon);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Status method');
            ResponseService::errorResponse();
        }
    }

    public function subscribe($id, $type) {
        if (env('DEMO_MODE')) {
            return response()->json(array(
                'error'   => true,
                'message' => "This is not allowed in the Demo Version.",
                'code'    => 112
            ));
        }
        ResponseService::noRoleThenRedirect('School Admin');
        try {
            DB::beginTransaction();
            $addon_id = $id;
            $date = Carbon::now()->format('Y-m-d');
            // $subscription = $this->subscription->builder()->with('package')->where('start_date', '<=', $date)->where('end_date', '>=', $date)->doesntHave('subscription_bill')->first();
            
            $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
            $package_features = $subscription->subscription_feature->pluck('feature_id')->toArray();
            // Check active plan
            if (!$subscription) {
                ResponseService::errorResponse('please_choose_a_plan_before_proceeding');
            }
            // Not Allowed in free trial subscription package
            if ($subscription->package->is_trial == 1) {
                ResponseService::errorResponse('Restricted in the free trial subscription');
            }

            $addon = $this->addon->findById($addon_id);

            if (in_array($addon->feature_id,$package_features)) {
                ResponseService::errorResponse('you_presently_have_access_to_this_functionality_as_part_of_your_current_subscription');
            }


            $date = Carbon::now()->format('Y-m-d');
            if ($type == 0) {
                $addon_check = $this->addonSubscription->builder()->where('feature_id', $addon->feature_id)->where('subscription_id', $subscription->id)->whereHas('transaction',function($q) {
                    $q->where('payment_status','succeed');
                })->first();
            } else {
                $addon_check = $this->addonSubscription->builder()->where('feature_id', $addon->feature_id)->where('subscription_id', $subscription->id)->first();
            }            

            if ($addon_check) {
                ResponseService::errorResponse('this_addon_has_already_been_included_by_you');
            }

            $status = 0;
            $upcoming_plan = $this->subscription->builder()->whereDate('start_date','>',$subscription->end_date)
            ->whereHas('package.package_feature',function($q) use($addon) {
                $q->where('feature_id',$addon->feature_id);
            })
            ->first();
            if (!$upcoming_plan) {
                $status = 1;
            }
            $data = [
                'feature_id' => $addon->feature_id,
                'price' => $addon->price,
                'start_date' => Carbon::now(),
                'end_date' => $subscription->end_date,
                'status' => $status,
                'subscription_id' => $subscription->id
            ];

            // createBulk
            $addonSubscription = $this->addonSubscription->create($data);

            DB::commit();
            // If prepaid plan receive payment first
            if ($type == 0) {
                $response = [
                    'error'   => false,
                    'message' => trans('prepaid_plan'),
                    'type' => 'prepaid',
                    'url'    => url('addons/prepaid-package').'/'.$addonSubscription->id
                ];
                return response()->json($response);
            }

            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.FEATURES'));
            // $this->addonSubscription->upsert($data,['school_id','addon_id'],['price','start_date','end_date']);
            ResponseService::successResponse('Addon added successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> subscribe method');
            ResponseService::errorResponse();
        }
    }

    public function discontinue($id) {
        if (env('DEMO_MODE')) {
            return response()->json(array(
                'error'   => true,
                'message' => "This is not allowed in the Demo Version.",
                'code'    => 112
            ));
        }
        ResponseService::noRoleThenRedirect('School Admin');
        try {
            DB::beginTransaction();
            $this->addonSubscription->update($id, ['status' => 0]);
            DB::commit();
            ResponseService::successResponse('Addon discontinue successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Addon Controller -> Discontinue method');
            ResponseService::errorResponse();
        }
    }

    public function prepaid_package_addon($id)
    {
        if (env('DEMO_MODE')) {
            return response()->json(array(
                'error'   => true,
                'message' => "This is not allowed in the Demo Version.",
                'code'    => 112
            ));
        }

        try {
            return $this->subscriptionService->prepaid_addon_payment($id);
        } catch (\Throwable $th) {
            DB::rollBack();
            ResponseService::logErrorResponse($th, 'Addon Controller -> Prepaid Package Addon method');
            ResponseService::errorResponse();
        }
    }

    public function payment_success($check_out_session_id, $id)
    {
        $settings = app(CachingService::class)->getSystemSettings();
        $currency = $settings['currency_code'];

        DB::setDefaultConnection('mysql');
        $paymentConfiguration = PaymentConfiguration::where('school_id', null)->first();
        $currency = $paymentConfiguration->currency_code;
        $addonSubscription = $this->addonSubscription->findById($id);
        
        if ($paymentConfiguration->payment_method == 'Stripe') {
            $stripe_secret_key = $paymentConfiguration->secret_key ?? null;
            Stripe::setApiKey($stripe_secret_key);

            $session = StripeSession::retrieve($check_out_session_id);
            $status = "pending";
            if ($session->payment_status == 'paid') {
                $status = "succeed";
            }

            $payment_data = [
                'user_id'         => Auth::user()->id,
                'amount'          => ($session->amount_total / 100),
                'payment_gateway' => 'Stripe',
                'order_id'        => $session->payment_intent,
                'payment_id'      => $session->id,
                'payment_status'  => $status,
            ];

            $paymentTransaction = $this->paymentTransaction->create($payment_data);
            $addonSubscription = $this->addonSubscription->update($id, ['payment_transaction_id' => $paymentTransaction->id]);
            $stripe = new StripeClient($stripe_secret_key);
            $stripeData = $stripe->customers->create(
                [
                    'metadata' => [
                        'user_id' => Auth::user()->id,
                        'amount'         => $paymentTransaction->amount,
                        'transaction_id' => $paymentTransaction->id,
                        'order_id'       => $paymentTransaction->order_id,
                        'payment_id'     => $paymentTransaction->payment_id,
                        'payment_status' => $paymentTransaction->payment_status,
                    ]
                ]
            );
        }


        $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.FEATURES'), $addonSubscription->school_id);

        return redirect()->route('addons.plan')->with('success', trans('the_payment_has_been_completed_successfully'));
    }

    public function payment_cancel()
    {
        return redirect()->route('addons.plan')->with('error', trans('the_payment_has_been_cancelled'));
    }

    public function payment_success_callback()
    {
       
        $request = request();

        if($request->trxref && $request->reference || ($request->status == 'successful' && $request->transaction_id)) {
            return redirect()->route('addons.plan')->with('success', trans('the_payment_has_been_completed_successfully'));
        } else {
            return redirect()->route('addons.plan')->with('error', trans('the_payment_has_been_cancelled'));
        }
    }

    public function payment_cancel_callback()
    {
        return redirect()->route('addons.plan')->with('error', trans('the_payment_has_been_cancelled'));
    }
}
