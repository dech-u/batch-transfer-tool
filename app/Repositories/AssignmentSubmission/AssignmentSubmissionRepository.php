<?php

namespace App\Repositories\AssignmentSubmission;

use App\Models\AssignmentSubmission;
use App\Repositories\Saas\SaaSRepository;

class AssignmentSubmissionRepository extends SaaSRepository implements AssignmentSubmissionInterface {
    public function __construct(AssignmentSubmission $model) {
        parent::__construct($model);
    }
}
