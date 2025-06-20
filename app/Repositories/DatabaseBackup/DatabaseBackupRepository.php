<?php

namespace App\Repositories\DatabaseBackup;

use App\Models\DatabaseBackup;
use App\Repositories\Saas\SaaSRepository;

class DatabaseBackupRepository extends SaaSRepository implements DatabaseBackupInterface {

    public function __construct(DatabaseBackup $model) {
        parent::__construct($model);
    }
}
