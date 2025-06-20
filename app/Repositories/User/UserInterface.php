<?php

namespace App\Repositories\User;

use App\Repositories\Base\BaseInterface;

interface UserInterface extends BaseInterface {
    public function getTrashedAdminData($email = null);
    public function guardian();
}

