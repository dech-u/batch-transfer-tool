<?php

namespace App\Repositories\PromoteStudent;

use App\Models\PromoteStudent;
use App\Repositories\Saas\SaaSRepository;

class PromoteStudentRepository extends SaaSRepository implements PromoteStudentInterface {
    public function __construct(PromoteStudent $model) {
        parent::__construct($model);
    }
}
