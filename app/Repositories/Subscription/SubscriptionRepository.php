<?php

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use App\Repositories\Saas\SaaSRepository;
use App\Services\CachingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository extends SaaSRepository implements SubscriptionInterface {
    public function __construct(Subscription $model) {
        parent::__construct($model);
    }

    public function default()
    {
        $today_date = Carbon::now()->format('Y-m-d');
        
        // V1.2.0
        // return $this->defaultModel()->where('start_date','<=',$today_date)->where('end_date','>=',$today_date)->doesntHave('subscription_bill');

        $subscription = $this->defaultModel()->where('start_date','<=',$today_date)->where('end_date','>=',$today_date)->first();

        if ($subscription) {
            if ($subscription->package_type == 1) {
                $subscription = $this->defaultModel()->where('start_date','<=',$today_date)->where('end_date','>=',$today_date)->doesntHave('subscription_bill');
            } else {
                $subscription = $this->defaultModel()->where('start_date','<=',$today_date)->where('end_date','>=',$today_date)->has('subscription_bill')->whereHas('subscription_bill.transaction', function($q) {
                    $q->where('payment_status',"succeed");
                });
            }
        } else {
            $subscription = $this->defaultModel()->where('start_date','<=',$today_date)->where('end_date','>=',$today_date);
        }

        return $subscription;
    }
}
