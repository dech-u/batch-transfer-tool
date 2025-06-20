<?php

namespace App\Repositories\Attachment;

use App\Models\Attachment;
use App\Repositories\Base\BaseRepository;

class AttachmentRepository extends BaseRepository implements AttachmentInterface {

    public function __construct(Attachment $model) {
        parent::__construct($model);
    }
}
