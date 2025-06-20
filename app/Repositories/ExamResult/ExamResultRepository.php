<?php

namespace App\Repositories\ExamResult;

use App\Models\ExamResult;
use App\Repositories\Saas\SaaSRepository;

class ExamResultRepository extends SaaSRepository implements ExamResultInterface {

    public function __construct(ExamResult $model) {
        parent::__construct($model);
    }
}
