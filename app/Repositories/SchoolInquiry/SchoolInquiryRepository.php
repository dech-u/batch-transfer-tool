<?php

namespace App\Repositories\SchoolInquiry;

use App\Models\SchoolInquiry;
use App\Repositories\Base\BaseRepository;

class SchoolInquiryRepository extends BaseRepository implements SchoolInquiryInterface {
    public function __construct(SchoolInquiry $model) {
        parent::__construct($model);
    }
}
