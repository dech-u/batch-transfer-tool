<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class TrimmedEnum implements Rule
{
    protected $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function passes($attribute, $value)
    {
        $value = trim($value); // Trim the value
        return in_array($value, $this->values);
    }

    public function message()
    {
        return 'The :attribute field is not valid.';
    }
}
