<?php

namespace App\Repositories\FeesPaid;

use App\Models\FeesPaid;
use App\Repositories\Saas\SaaSRepository;

class FeesPaidRepository extends SaaSRepository implements FeesPaidInterface {
    public function __construct(FeesPaid $model) {
        parent::__construct($model);
    }
}
