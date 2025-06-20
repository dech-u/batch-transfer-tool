<?php

namespace App\Repositories\Stream;

use App\Models\Stream;
use App\Repositories\Saas\SaaSRepository;

class StreamRepository extends SaaSRepository implements StreamInterface {

    public function __construct(Stream $model) {
        parent::__construct($model);
    }
}
