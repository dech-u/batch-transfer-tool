<?php

namespace App\Repositories\ElectiveSubjectGroup;

use App\Models\ElectiveSubjectGroup;
use App\Repositories\Saas\SaaSRepository;

class ElectiveSubjectGroupRepository extends SaaSRepository implements ElectiveSubjectGroupInterface {
    public function __construct(ElectiveSubjectGroup $model) {
        parent::__construct($model);
    }
}
