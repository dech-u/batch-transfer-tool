<?php

namespace App\Repositories\ClassSchool;

use App\Models\ClassSchool;
use App\Repositories\Saas\SaaSRepository;

class ClassSchoolRepository extends SaaSRepository implements ClassSchoolInterface {
    public function __construct(ClassSchool $model) {
        parent::__construct($model);
    }
}
