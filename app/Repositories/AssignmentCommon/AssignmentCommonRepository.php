<?php

namespace App\Repositories\AssignmentCommon;

use App\Models\AssignmentCommon;
use App\Repositories\Saas\SaaSRepository;

class AssignmentCommonRepository extends SaaSRepository implements AssignmentCommonInterface {
    public function __construct(AssignmentCommon $model) {
        parent::__construct($model);
    }
}
