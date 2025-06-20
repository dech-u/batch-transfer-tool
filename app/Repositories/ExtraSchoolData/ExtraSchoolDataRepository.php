<?php

namespace App\Repositories\ExtraSchoolData;

use App\Models\ExtraSchoolData;
use App\Repositories\Base\BaseRepository;

class ExtraSchoolDataRepository extends BaseRepository implements ExtraSchoolDataInterface {
    public function __construct(ExtraSchoolData $model) {
        parent::__construct($model);
    }
}
