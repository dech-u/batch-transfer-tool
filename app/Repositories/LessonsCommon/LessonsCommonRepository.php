<?php

namespace App\Repositories\LessonsCommon;

use App\Models\LessonCommon;
use App\Repositories\Saas\SaaSRepository;

class LessonsCommonRepository extends SaaSRepository implements LessonsCommonInterface {
    public function __construct(LessonCommon $model) {
        parent::__construct($model);
    }
}
