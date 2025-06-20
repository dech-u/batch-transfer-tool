<?php

namespace App\Repositories\Feature;

use App\Models\Feature;
use App\Repositories\Base\BaseRepository;

class FeatureRepository extends BaseRepository implements FeatureInterface {
    public function __construct(Feature $model) {
        parent::__construct($model,'feature');
    }
}
