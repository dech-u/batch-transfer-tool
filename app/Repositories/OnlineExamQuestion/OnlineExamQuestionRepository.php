<?php

namespace App\Repositories\OnlineExamQuestion;

use App\Models\OnlineExamQuestion;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamQuestionRepository extends SaaSRepository implements OnlineExamQuestionInterface {

    public function __construct(OnlineExamQuestion $model) {
        parent::__construct($model, 'online-exam-question');
    }
}
