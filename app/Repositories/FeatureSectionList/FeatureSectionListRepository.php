<?php

namespace App\Repositories\FeatureSectionList;

use App\Models\FeatureSectionList;
use App\Repositories\Base\BaseRepository;

class FeatureSectionListRepository extends BaseRepository implements FeatureSectionListInterface {
    public function __construct(FeatureSectionList $model) {
        parent::__construct($model,'FeatureSectionList');
    }
}
