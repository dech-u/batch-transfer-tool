<?php

namespace App\Exports;

use App\Repositories\FormField\FormFieldsInterface;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class MarksDataExport implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStrictNullComparison, WithMultipleSheets {
    protected mixed $results;
    protected $data;
    
    public function __construct(array $data) {
        $this->data = $data;
    }

    public function title(): string {
        return 'Marks Bulk Upload';
    }

    public function headings(): array {
        $columns = [
            'exam_marks_id',
            'student_id',
            'student_name',
            'total_marks',
            'obtained_marks', 
        ];
        return $columns;
    }

    public function sheets(): array {
        return [$this];
    }

    public function collection() {
        return collect($this->data);
    }
}
