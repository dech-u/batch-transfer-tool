<?php

namespace App\Repositories\Leave;

use App\Models\Leave;
use App\Repositories\Saas\SaaSRepository;

class LeaveRepository extends SaaSRepository implements LeaveInterface {
    public function __construct(Leave $model) {
        parent::__construct($model);
    }
}
