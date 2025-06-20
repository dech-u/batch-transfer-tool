<?php

namespace App\Repositories\Timetable;

use App\Models\Timetable;
use App\Repositories\Saas\SaaSRepository;

class TimetableRepository extends SaaSRepository implements TimetableInterface
{
    public function __construct(Timetable $model)
    {
        parent::__construct($model);
    }
}
