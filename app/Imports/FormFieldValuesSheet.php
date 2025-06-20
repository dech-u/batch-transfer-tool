<?php


namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class FormFieldValuesSheet implements FromCollection, WithTitle, ShouldAutoSize {
    private mixed $formFields;

    public function title(): string {
        return 'Possible Options';
    }

    public function __construct($formFields) {
        $this->formFields = $formFields;
    }

    public function collection() {
        $data = [
            ['gender', 'male', 'female', '', 'eg :- male'],
            [[]],
            ['guardian_gender', 'male', 'female', '', 'eg :- male'],
            [[]]
        ];

        foreach ($this->formFields as $field) {
            if (in_array($field->type, ['dropdown', 'radio', 'checkbox'])) {
                $values = $field->default_values;
                $values[] = [];
                if ($field->type == 'checkbox') {
                    $values[] = 'eg :- ' . $field->default_values[0] . ',' . $field->default_values[1];
                } else {
                    $values[] = 'eg :- ' . $field->default_values[0];
                }
                $data[] = array_merge([$field->name], $values);
                $data[] = [[]];
            }
        }
        return collect($data);
    }
}
