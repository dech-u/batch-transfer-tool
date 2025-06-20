<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxFileSize implements ValidationRule
{
    protected $maxSize;

    public function __construct(int $maxSize)
    {
        $this->maxSize = $maxSize * 1000000;
        \Log::info("construct called with ".$this->maxSize);
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute  The name of the attribute being validated
     * @param  mixed   $value  The value being validated (could be a file)
     * @param  \Closure  $fail  The closure to call if validation fails
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        \Log::info("validate");

        // If the value is null or not a valid file, skip the validation (nullable case)
        if (!$value) {
            return;
        }

        // Check if the value is an instance of `UploadedFile`
        if (is_object($value)) {
            $fileSize = $value->getSize();  // File size in bytes
            
            \Log::info("File size: $fileSize bytes");
            \Log::info("Max size in bytes: $this->maxSize");
            \Log::info("File size is greater than max size: " . ($fileSize > $this->maxSize));
        
            if ($fileSize <= $this->maxSize) {
                \Log::info("File size is valid.");
                return;
            }

            $this->maxSize = $this->maxSize / 1000000;
            $fail("The file size may not be greater than {$this->maxSize} MB.");

        } else {
            // If it's not a valid file, trigger the validation failure
            $fail("The {$attribute} must be a valid file.");
        }
        
    }
    
}
