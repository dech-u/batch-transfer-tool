<?php

namespace App\Repositories\SessionYear;

use App\Models\SessionYear;
use App\Repositories\Saas\SaaSRepository;
use Illuminate\Support\Facades\DB;

class SessionYearRepository extends SaaSRepository implements SessionYearInterface {

    public function __construct(SessionYear $model) {
        parent::__construct($model);
    }

    public function default($schoolId = null) {
        if($schoolId){
            return $this->defaultModel()->where(['default' => 1, 'school_id' => $schoolId])->first();
        }else{
            return $this->defaultModel()->where('default', 1)->first();
        }
    }
}
