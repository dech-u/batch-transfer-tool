<?php

namespace App\Repositories\UserStatusForNextCycle;

use App\Models\UserStatusForNextCycle;
use App\Repositories\Saas\SaaSRepository;

class UserStatusForNextCycleRepository extends SaaSRepository implements UserStatusForNextCycleInterface {

    public function __construct(UserStatusForNextCycle $model) {
        parent::__construct($model);
    }
}
