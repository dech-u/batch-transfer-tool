<?php

namespace App\Repositories\Package;

use App\Models\Package;
use App\Repositories\Base\BaseRepository;

class PackageRepository extends BaseRepository implements PackageInterface {
    public function __construct(Package $model) {
        parent::__construct($model,'package');
    }
}
