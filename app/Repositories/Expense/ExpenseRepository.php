<?php

namespace App\Repositories\Expense;

use App\Models\Expense;
use App\Repositories\Saas\SaaSRepository;

class ExpenseRepository extends SaaSRepository implements ExpenseInterface {

    public function __construct(Expense $model) {
        parent::__construct($model);
    }
}
