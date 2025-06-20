<?php

namespace App\Repositories\Subject;

use App\Models\Subject;
use App\Repositories\Saas\SaaSRepository;

class SubjectRepository extends SaaSRepository implements SubjectInterface {

    public function __construct(Subject $model) {
        parent::__construct($model, 'subject');
    }
}
