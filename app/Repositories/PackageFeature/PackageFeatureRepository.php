<?php

namespace App\Repositories\PackageFeature;

use App\Models\PackageFeature;
use App\Repositories\Base\BaseRepository;

class PackageFeatureRepository extends BaseRepository implements PackageFeatureInterface {
    public function __construct(PackageFeature $model) {
        parent::__construct($model,'package_feature');
    }
}
