<?php

namespace App\Repositories\StaffSalary;

use App\Models\StaffSalary;
use App\Repositories\Base\BaseRepository;

class StaffSalaryRepository extends BaseRepository implements StaffSalaryInterface {
    public function __construct(StaffSalary $model) {
        parent::__construct($model);
    }
}
