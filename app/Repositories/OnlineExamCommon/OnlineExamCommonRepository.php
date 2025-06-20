<?php

namespace App\Repositories\OnlineExamCommon;

use App\Models\OnlineExamCommon;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamCommonRepository extends SaaSRepository implements OnlineExamCommonInterface {
    public function __construct(OnlineExamCommon $model) {
        parent::__construct($model);
    }
}
