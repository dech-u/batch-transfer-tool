<?php

namespace App\Repositories\OnlineExamQuestionChoice;

use App\Models\OnlineExamQuestionChoice;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamQuestionChoiceRepository extends SaaSRepository implements OnlineExamQuestionChoiceInterface {

    public function __construct(OnlineExamQuestionChoice $model) {
        parent::__construct($model);
    }
}
