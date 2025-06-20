<?php

namespace App\Repositories\OnlineExamQuestionOption;

use App\Models\OnlineExamQuestionOption;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamQuestionOptionRepository extends SaaSRepository implements OnlineExamQuestionOptionInterface {

    public function __construct(OnlineExamQuestionOption $model) {
        parent::__construct($model);
    }
}
