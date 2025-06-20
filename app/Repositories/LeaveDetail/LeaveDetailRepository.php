<?php

namespace App\Repositories\LeaveDetail;

use App\Models\LeaveDetail;
use App\Repositories\Saas\SaaSRepository;

class LeaveDetailRepository extends SaaSRepository implements LeaveDetailInterface {
    public function __construct(LeaveDetail $model) {
        parent::__construct($model);
    }
}
