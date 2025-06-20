<?php

namespace App\Repositories\Chat;

use App\Models\Chat;
use App\Repositories\Base\BaseRepository;

class ChatRepository extends BaseRepository implements ChatInterface {

    public function __construct(Chat $model) {
        parent::__construct($model);
    }
}
