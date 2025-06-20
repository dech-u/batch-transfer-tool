<?php

namespace App\Repositories\PayrollSetting;

use App\Models\PayrollSetting;
use App\Repositories\Saas\SaaSRepository;

class PayrollSettingRepository extends SaaSRepository implements PayrollSettingInterface {
    public function __construct(PayrollSetting $model) {
        parent::__construct($model);
    }
}
