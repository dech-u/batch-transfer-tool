<?php

namespace App\Repositories\Addon;

use App\Models\Addon;
use App\Repositories\Base\BaseRepository;

class AddonRepository extends BaseRepository implements AddonInterface {
    public function __construct(Addon $model) {
        parent::__construct($model);
    }
}
