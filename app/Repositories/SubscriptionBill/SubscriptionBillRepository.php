<?php

namespace App\Repositories\SubscriptionBill;

use App\Models\SubscriptionBill;
use App\Repositories\Saas\SaaSRepository;


class SubscriptionBillRepository extends SaaSRepository implements SubscriptionBillInterface {
    public function __construct(SubscriptionBill $model) {
        parent::__construct($model);
    }
}
