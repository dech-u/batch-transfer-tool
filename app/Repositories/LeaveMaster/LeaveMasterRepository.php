<?php

namespace App\Repositories\LeaveMaster;

use App\Models\LeaveMaster;
use App\Repositories\Saas\SaaSRepository;

class LeaveMasterRepository extends SaaSRepository implements LeaveMasterInterface {
    public function __construct(LeaveMaster $model) {
        parent::__construct($model);
    }
}
