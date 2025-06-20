<?php

namespace App\Repositories\PaymentConfiguration;

use App\Models\PaymentConfiguration;
use App\Repositories\Saas\SaaSRepository;

class PaymentConfigurationRepository extends SaaSRepository implements PaymentConfigurationInterface {

    public function __construct(PaymentConfiguration $model) {
        parent::__construct($model);
    }
}
