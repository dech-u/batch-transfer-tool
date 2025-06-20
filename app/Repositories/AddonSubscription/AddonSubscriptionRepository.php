<?php

namespace App\Repositories\AddonSubscription;

use App\Models\AddonSubscription;
use App\Repositories\Saas\SaaSRepository;
use App\Repositories\Subscription\SubscriptionInterface;
use Carbon\Carbon;

class AddonSubscriptionRepository extends SaaSRepository implements AddonSubscriptionInterface {
    private SubscriptionInterface $subscription;
    public function __construct(AddonSubscription $model, SubscriptionInterface $subscription) {
        parent::__construct($model);
        $this->subscription = $subscription;
    }

    public function default()
    {
        $today_date = Carbon::now()->format('Y-m-d');
        $subscription = $this->subscription->default()->first();
        if ($subscription) {
            return $this->defaultModel()->where('subscription_id',$subscription->id);
        }
        return $this->defaultModel()->whereDate('start_date','<=',$today_date)->whereDate('end_date','>=',$today_date);
    }
}
