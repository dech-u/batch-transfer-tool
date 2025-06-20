<?php

namespace App\Repositories\Sliders;

use App\Models\Slider;
use App\Repositories\Saas\SaaSRepository;

class SlidersRepository extends SaaSRepository implements SlidersInterface {
    public function __construct(Slider $model) {
        parent::__construct($model,'sliders');
    }
}
