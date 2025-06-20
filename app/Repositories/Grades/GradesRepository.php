<?php

namespace App\Repositories\Grades;

use App\Models\Grade;
use App\Repositories\Saas\SaaSRepository;

class GradesRepository extends SaaSRepository implements GradesInterface {

    public function __construct(Grade $model) {
        parent::__construct($model);
    }
}
