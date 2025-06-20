<?php

namespace App\Repositories\CompulsoryFee;

use App\Models\CompulsoryFee;
use App\Repositories\Saas\SaaSRepository;

class CompulsoryFeeRepository extends SaaSRepository implements CompulsoryFeeInterface {

    public function __construct(CompulsoryFee $model) {
        parent::__construct($model);
    }
}
