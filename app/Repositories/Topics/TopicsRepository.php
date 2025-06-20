<?php

namespace App\Repositories\Topics;

use App\Models\LessonTopic;
use App\Repositories\Saas\SaaSRepository;

class TopicsRepository extends SaaSRepository implements TopicsInterface {
    public function __construct(LessonTopic $model) {
        parent::__construct($model);
    }
}
