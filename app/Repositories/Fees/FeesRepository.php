<?php

namespace App\Repositories\Fees;

use App\Models\Fee;
use App\Repositories\Saas\SaaSRepository;

class FeesRepository extends SaaSRepository implements FeesInterface {
    public function __construct(Fee $model) {
        parent::__construct($model);
    }
}
