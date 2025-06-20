<?php

namespace App\Repositories\Faqs;

use App\Models\Faq;
use App\Repositories\Saas\SaaSRepository;

class FaqsRepository extends SaaSRepository implements FaqsInterface {
    public function __construct(Faq $model) {
        parent::__construct($model,'faq');
    }
}
