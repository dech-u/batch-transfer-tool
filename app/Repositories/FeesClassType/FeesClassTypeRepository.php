<?php

namespace App\Repositories\FeesClassType;

use App\Models\FeesClassType;
use App\Repositories\Saas\SaaSRepository;

class FeesClassTypeRepository extends SaaSRepository implements FeesClassTypeInterface {

    public function __construct(FeesClassType $model) {
        parent::__construct($model);
    }
}
