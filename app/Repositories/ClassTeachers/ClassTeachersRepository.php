<?php

namespace App\Repositories\ClassTeachers;

use App\Models\ClassTeacher;
use App\Repositories\Saas\SaaSRepository;

class ClassTeachersRepository extends SaaSRepository implements ClassTeachersInterface {

    public function __construct(ClassTeacher $model) {
        parent::__construct($model);
    }
}
