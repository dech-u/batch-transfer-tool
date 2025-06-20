<?php

namespace App\Repositories\Attendance;

use App\Models\Attendance;
use App\Repositories\Saas\SaaSRepository;

class AttendanceRepository extends SaaSRepository implements AttendanceInterface {

    public function __construct(Attendance $model) {
        parent::__construct($model);
    }
}
