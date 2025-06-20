<?php

namespace App\Repositories\SubscriptionBillPayment;

use App\Models\SubscriptionBillPayment;
use App\Repositories\Saas\SaaSRepository;

class SubscriptionBillPaymentRepository extends SaaSRepository implements SubscriptionBillPaymentInterface
{
    public function __construct(SubscriptionBillPayment $model)
    {
        parent::__construct($model);
    }
}
