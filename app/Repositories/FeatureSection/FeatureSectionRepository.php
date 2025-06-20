<?php

namespace App\Repositories\FeatureSection;

use App\Models\FeatureSection;
use App\Repositories\Base\BaseRepository;

class FeatureSectionRepository extends BaseRepository implements FeatureSectionInterface {
    public function __construct(FeatureSection $model) {
        parent::__construct($model,'FeatureSection');
    }
}
