<?php

namespace App\Repositories\ExamMarks;

use App\Models\ExamMarks;
use App\Repositories\Saas\SaaSRepository;

class ExamMarksRepository extends SaaSRepository implements ExamMarksInterface {

    public function __construct(ExamMarks $model) {
        parent::__construct($model);
    }
}
