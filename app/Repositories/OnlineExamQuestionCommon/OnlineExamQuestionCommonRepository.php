<?php

namespace App\Repositories\OnlineExamQuestionCommon;

use App\Models\OnlineExamQuestionCommon;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamQuestionCommonRepository extends SaaSRepository implements OnlineExamQuestionCommonInterface {
    public function __construct(OnlineExamQuestionCommon $model) {
        parent::__construct($model);
    }
}
