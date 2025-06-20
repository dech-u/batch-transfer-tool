<?php

namespace App\Repositories\Student;

use App\Models\Students;
use App\Repositories\Saas\SaaSRepository;

class StudentRepository extends SaaSRepository implements StudentInterface {
    public function __construct(Students $model) {
        parent::__construct($model);
    }
}
