<?php

namespace App\Services;

use App\Models\Feature;
use App\Models\SubscriptionBill;
use App\Repositories\School\SchoolInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FeaturesService {
    public function __construct() {
        // $this->features = app(UserInterface::class)->features();
    }

    public static function getFeatures($schoolID = null) {
        // Fetch All the Features of the School in which User is associated. Then Cache that result for 30 minutes
        $schoolID = !empty($schoolID) ? $schoolID : Auth::user()->school_id;
        if (!empty($schoolID)) {
            return app(CachingService::class)->schoolLevelCaching(config('constants.CACHE.SCHOOL.FEATURES'), function () use ($schoolID) {
                $active_subscription = app(SubscriptionService::class)->active_subscription($schoolID);
                if ($active_subscription) {
                    
                    // Check any outstanding subscription bills
                    $today_date = Carbon::now()->format('Y-m-d');
                    $subscriptionBill = SubscriptionBill::with(['subscription' => function($q) {
                        $q->where('package_type',1);
                    }])->where('school_id',$schoolID)->whereHas('transaction', function($q) {
                        $q->whereNot('payment_status',"succeed");
                    })->where('due_date','<',$today_date)->first();

                    // If null outstanding subscription bills then continue
                    if (!$subscriptionBill) {
                        $packageFeatures = $addon = [];
                        if (!empty($active_subscription->subscription_feature)) {
                            $packageFeatures = $active_subscription->subscription_feature->pluck('feature_id')->toArray();
                        }
                        
                        if (!empty($active_subscription->addons)) {
                            $addon = $active_subscription->addons->pluck('feature_id')->toArray();
                        }

                        $features = array_merge($packageFeatures, $addon);
                        if (!empty($features)) {
                            return Feature::whereIn('id', array_unique($features))->pluck('name', 'id')->toArray();
                        }
                    }

                    
                }
                return [];
            }, $schoolID);
        }

//        if (empty(Auth::user()->school_id)) {
//            // IF it's a Super Admin or Staff then Fetch all the Features
//            return Feature::pluck('name', 'id')->toArray();
//        }

        return [];
    }

    /**
     * @param $argument
     * @return bool
     */
    public static function hasFeature($argument) {
        $features = self::getFeatures();
        return in_array($argument, $features);
    }

    /**
     * @param array $argument
     * @return bool
     */
    public static function hasAnyFeature(array $argument) {
        $features = self::getFeatures();
        return !empty(array_intersect($argument, $features));
    }

    /**
     * @param array $argument
     * @return bool
     */
    public static function hasAllFeature(array $argument) {
        $features = self::getFeatures();

        return empty(array_diff($argument, $features));
    }
}
