<?php

namespace App\Repositories\FeesType;

use App\Models\FeesType;
use App\Repositories\Saas\SaaSRepository;

class FeesTypeRepository extends SaaSRepository implements FeesTypeInterface {

    public function __construct(FeesType $model) {
        parent::__construct($model);
    }
}
