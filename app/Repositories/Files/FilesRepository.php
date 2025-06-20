<?php

namespace App\Repositories\Files;

use App\Models\File;
use App\Repositories\Saas\SaaSRepository;

class FilesRepository extends SaaSRepository implements FilesInterface {
    public function __construct(File $model) {
        parent::__construct($model, 'files');
    }
}
