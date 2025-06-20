<?php

namespace App\Repositories\Staff;

use App\Models\Staff;
use App\Repositories\Base\BaseRepository;

class StaffRepository extends BaseRepository implements StaffInterface {

    public function __construct(Staff $model) {
        parent::__construct($model);
    }
}
