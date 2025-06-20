<?php

namespace App\Repositories\AnnouncementClass;

use App\Models\AnnouncementClass;
use App\Repositories\Saas\SaaSRepository;

class AnnouncementClassRepository extends SaaSRepository implements AnnouncementClassInterface {
    public function __construct(AnnouncementClass $model) {
        parent::__construct($model);
    }
}
