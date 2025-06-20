<?php

namespace App\Repositories\SystemSetting;

use App\Repositories\Base\BaseInterface;

interface SystemSettingInterface extends BaseInterface {
    public function getSpecificData($name);
    public function getBulkData($array);

}
