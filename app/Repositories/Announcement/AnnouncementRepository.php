<?php

namespace App\Repositories\Announcement;

use App\Models\Announcement;
use App\Repositories\Saas\SaaSRepository;

class AnnouncementRepository extends SaaSRepository implements AnnouncementInterface {
    public function __construct(Announcement $model) {
        parent::__construct($model,'announcements');
    }
}
