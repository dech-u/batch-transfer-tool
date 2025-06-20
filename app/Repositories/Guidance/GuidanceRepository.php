<?php

namespace App\Repositories\Guidance;

use App\Models\Guidance;
use App\Repositories\Base\BaseRepository;

class GuidanceRepository extends BaseRepository implements GuidanceInterface {
    public function __construct(Guidance $model) {
        parent::__construct($model,'guidance');
    }
}
