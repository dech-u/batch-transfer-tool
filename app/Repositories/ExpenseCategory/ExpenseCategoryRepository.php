<?php

namespace App\Repositories\ExpenseCategory;

use App\Models\ExpenseCategory;
use App\Repositories\Saas\SaaSRepository;

class ExpenseCategoryRepository extends SaaSRepository implements ExpenseCategoryInterface {

    public function __construct(ExpenseCategory $model) {
        parent::__construct($model);
    }
}
