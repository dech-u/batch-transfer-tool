<?php

namespace App\Repositories\Shift;

use App\Models\Shift;
use App\Repositories\Saas\SaaSRepository;
use Illuminate\Database\Eloquent\Collection;

class ShiftRepository extends SaaSRepository implements ShiftInterface {

    public function __construct(Shift $model) {
        parent::__construct($model);
    }

    public function active(): Collection {
        return $this->all(['*'], (array)null, ['status' => 1]);
    }
}
