<?php

namespace App\Repositories\SessionYear;

use App\Repositories\Base\BaseInterface;

interface SessionYearInterface extends BaseInterface {
    public function default($schoolId=null);

}
