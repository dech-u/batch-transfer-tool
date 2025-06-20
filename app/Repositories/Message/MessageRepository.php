<?php

namespace App\Repositories\Message;

use App\Models\Message;
use App\Repositories\Base\BaseRepository;

class MessageRepository extends BaseRepository implements MessageInterface {

    public function __construct(Message $model) {
        parent::__construct($model);
    }
}
