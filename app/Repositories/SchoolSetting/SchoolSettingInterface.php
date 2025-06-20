<?php

namespace App\Repositories\SchoolSetting;

use App\Repositories\Base\BaseInterface;

interface SchoolSettingInterface extends BaseInterface {
    public function getSpecificData($name);
    public function getBulkData($array);
}
