<?php

namespace App\Repositories\FeesInstallment;

use App\Models\FeesInstallment;
use App\Repositories\Saas\SaaSRepository;

class FeesInstallmentRepository extends SaaSRepository implements FeesInstallmentInterface {

    public function __construct(FeesInstallment $model) {
        parent::__construct($model);
    }

    public function default() {
        return $this->defaultModel()->where('default', 1)->first();
    }
}
