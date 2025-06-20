<?php

namespace App\Repositories\Semester;

use App\Repositories\Base\BaseInterface;

interface SemesterInterface extends BaseInterface {
    public function default($schoolId=null);

}
