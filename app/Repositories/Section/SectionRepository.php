<?php

namespace App\Repositories\Section;

use App\Models\Section;
use App\Repositories\Saas\SaaSRepository;

class SectionRepository extends SaaSRepository implements SectionInterface {

    public function __construct(Section $model) {
        parent::__construct($model);
    }
}
