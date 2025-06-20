<?php

namespace App\Repositories\Exam;

use App\Models\Exam;
use App\Repositories\Saas\SaaSRepository;

class ExamRepository extends SaaSRepository implements ExamInterface {

    public function __construct(Exam $model) {
        parent::__construct($model);
    }
}
