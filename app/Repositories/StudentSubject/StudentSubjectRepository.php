<?php

namespace App\Repositories\StudentSubject;

use App\Models\StudentSubject;
use App\Repositories\Saas\SaaSRepository;

class StudentSubjectRepository extends SaaSRepository implements StudentSubjectInterface {
    public function __construct(StudentSubject $model) {
        parent::__construct($model);
    }
}
