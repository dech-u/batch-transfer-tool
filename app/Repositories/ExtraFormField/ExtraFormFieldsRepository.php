<?php

namespace App\Repositories\ExtraFormField;

use App\Models\ExtraStudentData;
use App\Repositories\Saas\SaaSRepository;

class ExtraFormFieldsRepository extends SaaSRepository implements ExtraFormFieldsInterface {
    public function __construct(ExtraStudentData $model) {
        parent::__construct($model, 'extra-data');
    }
}
