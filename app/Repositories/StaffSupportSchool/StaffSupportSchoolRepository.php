<?php

namespace App\Repositories\StaffSupportSchool;

use App\Models\StaffSupportSchool;
use App\Repositories\Base\BaseRepository;

class StaffSupportSchoolRepository extends BaseRepository implements StaffSupportSchoolInterface {
    public function __construct(StaffSupportSchool $model) {
        parent::__construct($model,'staff_support_school');
    }
}
