<?php

namespace App\Repositories\Assignment;

use App\Models\Assignment;
use App\Repositories\Saas\SaaSRepository;

class AssignmentRepository extends SaaSRepository implements AssignmentInterface {
    public function __construct(Assignment $model) {
        parent::__construct($model);
    }
}
