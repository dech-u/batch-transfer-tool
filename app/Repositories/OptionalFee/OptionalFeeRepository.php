<?php

namespace App\Repositories\OptionalFee;

use App\Models\OptionalFee;
use App\Repositories\Saas\SaaSRepository;

class OptionalFeeRepository extends SaaSRepository implements OptionalFeeInterface {

    public function __construct(OptionalFee $model) {
        parent::__construct($model);
    }
}
