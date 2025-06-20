<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class uniqueForSchool implements Rule {
    /**
     * @var array|mixed
     */
    private mixed $column;
    private mixed $table;
    /**
     * @var mixed|null
     */
    private mixed $ignoreID;
    /**
     * @var mixed|null
     */
    private mixed $schoolID;

    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public function __construct($table, $column = null, $ignoreID = null, $schoolID = null) {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreID = $ignoreID;
        $this->schoolID = $schoolID;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $columns = $this->column ?? $attribute;

        $query = DB::table($this->table);

        if (!is_array($columns)) {
            $query = $query->where($columns, $value);
        } else {
            $query = $query->where($columns);
        }

        if (!empty($this->ignoreID)) {
            $query = $query->whereNot('id', $this->ignoreID);
        }

        // Check for School ID
        if (!empty($this->schoolID)) {
            $query = $query->where('school_id', $this->schoolID);
        } else {
            $query = $query->where('school_id', Auth::user()->school_id);
        }
        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'The :attribute is already exists.';
    }
}
