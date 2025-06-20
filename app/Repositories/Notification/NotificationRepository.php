<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use App\Repositories\Saas\SaaSRepository;

class NotificationRepository extends SaaSRepository implements NotificationInterface {

    public function __construct(Notification $model) {
        parent::__construct($model,'notification');
    }
}
