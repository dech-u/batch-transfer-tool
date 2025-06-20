<?php

namespace App\Repositories\PaymentTransaction;

use App\Models\PaymentTransaction;
use App\Repositories\Saas\SaaSRepository;

class PaymentTransactionRepository extends SaaSRepository implements PaymentTransactionInterface {

    public function __construct(PaymentTransaction $model) {
        parent::__construct($model);
    }
}
