<?php

namespace App\Repositories\SubjectTeacher;

use App\Models\SubjectTeacher;
use App\Repositories\Saas\SaaSRepository;


class SubjectTeacherRepository extends SaaSRepository implements SubjectTeacherInterface {
    public function __construct(SubjectTeacher $model) {
        parent::__construct($model);
    }
}
