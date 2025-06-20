<?php

namespace App\Repositories\TopicCommon;

use App\Models\TopicCommon;
use App\Repositories\Saas\SaaSRepository;

class TopicCommonRepository extends SaaSRepository implements TopicCommonInterface {
    public function __construct(TopicCommon $model) {
        parent::__construct($model);
    }
}
