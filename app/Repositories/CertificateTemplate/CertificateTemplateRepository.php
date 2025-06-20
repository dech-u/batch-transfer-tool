<?php

namespace App\Repositories\CertificateTemplate;

use App\Models\CertificateTemplate;
use App\Repositories\Saas\SaaSRepository;

class CertificateTemplateRepository extends SaaSRepository implements CertificateTemplateInterface {
    public function __construct(CertificateTemplate $model) {
        parent::__construct($model , 'certificate');
    }
}
