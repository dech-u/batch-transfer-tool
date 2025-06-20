<?php

namespace App\Repositories\StaffPayroll;

use App\Models\StaffPayroll;
use App\Repositories\Saas\SaaSRepository;

class StaffPayrollRepository extends SaaSRepository implements StaffPayrollInterface {
    public function __construct(StaffPayroll $model) {
        parent::__construct($model,'StaffPayroll');
    }
}
