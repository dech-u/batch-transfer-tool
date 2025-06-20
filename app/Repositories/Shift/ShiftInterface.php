<?php

namespace App\Repositories\Shift;

use App\Repositories\Base\BaseInterface;
use Illuminate\Database\Eloquent\Collection;

interface ShiftInterface extends BaseInterface {
    public function active(): Collection;
}
