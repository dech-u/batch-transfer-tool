<?php

namespace App\Repositories\OnlineExamStudentAnswer;

use App\Models\OnlineExamStudentAnswer;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamStudentAnswerRepository extends SaaSRepository implements OnlineExamStudentAnswerInterface {

    public function __construct(OnlineExamStudentAnswer $model) {
        parent::__construct($model);
    }
}
