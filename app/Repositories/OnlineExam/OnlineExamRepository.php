<?php

namespace App\Repositories\OnlineExam;

use App\Models\OnlineExam;
use App\Repositories\Saas\SaaSRepository;

class OnlineExamRepository extends SaaSRepository implements OnlineExamInterface {

    public function __construct(OnlineExam $model) {
        parent::__construct($model);
    }
}
