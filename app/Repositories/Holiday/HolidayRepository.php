<?php

namespace App\Repositories\Holiday;

use App\Models\Holiday;
use App\Repositories\Saas\SaaSRepository;

class HolidayRepository extends SaaSRepository implements HolidayInterface {
    public function __construct(Holiday $model) {
        parent::__construct($model);
    }
}
