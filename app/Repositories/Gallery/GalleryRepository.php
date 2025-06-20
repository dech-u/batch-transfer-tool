<?php

namespace App\Repositories\Gallery;

use App\Models\Gallery;
use App\Repositories\Saas\SaaSRepository;

class GalleryRepository extends SaaSRepository implements GalleryInterface {

    public function __construct(Gallery $model) {
        parent::__construct($model, 'gallery');
    }
}
