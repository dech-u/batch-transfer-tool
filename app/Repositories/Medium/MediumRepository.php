<?php

namespace App\Repositories\Medium;

use App\Models\Mediums;
use App\Repositories\Saas\SaaSRepository;

class MediumRepository extends SaaSRepository implements MediumInterface {

    public function __construct(Mediums $model) {
        parent::__construct($model);
    }
}
