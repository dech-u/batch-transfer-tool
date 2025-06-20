<?php

namespace App\Repositories\School;

use App\Repositories\Base\BaseInterface;

interface SchoolInterface extends BaseInterface{

    public function updateSchoolAdmin($array, $image);

    public function active();
}
