<?php

namespace App\Repositories\ClassGroup;

use App\Models\ClassGroup;
use App\Repositories\Saas\SaaSRepository;

class ClassGroupRepository extends SaaSRepository implements ClassGroupInterface {

    public function __construct(ClassGroup $model) {
        parent::__construct($model, 'class_group');
    }
}
