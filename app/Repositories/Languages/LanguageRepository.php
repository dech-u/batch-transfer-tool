<?php

namespace App\Repositories\Languages;

use App\Models\Language;
use App\Repositories\Base\BaseRepository;

class LanguageRepository extends BaseRepository implements LanguageInterface {

    public function __construct(Language $model) {
        parent::__construct($model);
    }
}
