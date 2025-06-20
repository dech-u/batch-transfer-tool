<?php

namespace App\Repositories\ClassSubject;

use App\Models\ClassSubject;
use App\Repositories\Saas\SaaSRepository;

class ClassSubjectRepository extends SaaSRepository implements ClassSubjectInterface {
    public function __construct(ClassSubject $model) {
        parent::__construct($model);
    }
}
