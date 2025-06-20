<?php

namespace App\Repositories\StudentOnlineExamStatus;

use App\Models\StudentOnlineExamStatus;
use App\Repositories\Saas\SaaSRepository;

class StudentOnlineExamStatusRepository extends SaaSRepository implements StudentOnlineExamStatusInterface {

    public function __construct(StudentOnlineExamStatus $model) {
        parent::__construct($model);
    }
}
