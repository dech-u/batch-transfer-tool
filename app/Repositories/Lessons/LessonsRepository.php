<?php

namespace App\Repositories\Lessons;

use App\Models\Lesson;
use App\Repositories\Saas\SaaSRepository;

class LessonsRepository extends SaaSRepository implements LessonsInterface {
    public function __construct(Lesson $model) {
        parent::__construct($model);
    }
}
