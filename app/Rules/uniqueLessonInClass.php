<?php

namespace App\Rules;

use App\Models\Lesson;
use Illuminate\Contracts\Validation\Rule;

class uniqueLessonInClass implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($class_section_id, $class_subject_id, $lesson_id = NULL) {
        $this->class_section_id = $class_section_id;
        $this->class_subject_id = $class_subject_id;
        $this->lesson_id = $lesson_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */

    public function passes($attribute, $value) {
        if ($this->lesson_id == NULL) {
            $count = Lesson::where('name', $value)->where(['class_section_id' => $this->class_section_id, 'class_subject_id' => $this->class_subject_id])->count();
            return $count == 0;
        }

        $count = Lesson::where('name', $value)->where(['class_section_id' => $this->class_section_id, 'class_subject_id' => $this->class_subject_id])->whereNot('id', $this->lesson_id)->count();
        return $count == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return trans('lesson_already_exists');
    }
}
