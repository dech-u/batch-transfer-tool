<?php

namespace App\Repositories\ClassSection;

use App\Models\ClassSection;
use App\Repositories\Saas\SaaSRepository;

class ClassSectionRepository extends SaaSRepository implements ClassSectionInterface {
    public function __construct(ClassSection $model) {
        parent::__construct($model);
    }
}
