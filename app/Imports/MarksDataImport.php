<?php

namespace App\Imports;

use App\Models\ExamTimetable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Services\ResponseService;
use App\Repositories\ExamMarks\ExamMarksInterface;
use App\Repositories\ExamTimetable\ExamTimetableInterface;

use Throwable;
use JsonException;

class MarksDataImport implements WithMultipleSheets
{
    private mixed $classSectionID;
    private mixed $examID;
    private mixed $classSubjectID; 
   

    public function __construct($classSectionID, $examID, $classSubjectID)
    {
        $this->classSectionID = $classSectionID;
        $this->examID = $examID;
        $this->classSubjectID = $classSubjectID;
    }

    /**
     * @throws Throwable
     */
    public function sheets(): array
    {
        return [
            new FirstSheetImport($this->classSectionID, $this->examID, $this->classSubjectID)
        ];
    }
}

class FirstSheetImport implements ToCollection, WithHeadingRow
{
    private mixed $classSectionID;
    private mixed $examID;
    private mixed $classSubjectID;

    /**
     * @param $classSectionID
     * @param $examID
     */

    // Import the Class Section and Repositories
    public function __construct($classSectionID, $examID, $classSubjectID)
    {
        $this->classSectionID = $classSectionID;
        $this->examID = $examID;
        $this->classSubjectID = $classSubjectID;
    }

    /**
     * @throws JsonException
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {
        // Validate incoming CSV data
        $validator = Validator::make($collection->toArray(), [
            '*.student_id' => 'required|numeric',
            '*.obtained_marks' => 'required|numeric',
            '*.total_marks' => 'required|numeric',
        ], [
            'student_id.required' => 'Student ID field is required.',
            'obtained_marks.required' => 'Obtained Marks field is required.',
            'total_marks.required' => 'Total Marks field is required.',
        ]);

        $validator->validate();

        DB::beginTransaction();
        try {

            $examTimetable = app(ExamTimetableInterface::class);
            $examMarks = app(ExamMarksInterface::class);

            $exam_timetable = $examTimetable->builder()->where(['exam_id' =>  $this->examID, 'class_subject_id' => $this->classSubjectID])->firstOrFail();
            foreach ($collection as $row) {
              
                $passing_marks = $exam_timetable->passing_marks;
                if ($row['obtained_marks'] >= $passing_marks) {
                    $status = 1;
                } else {
                    $status = 0;
                }
                $marks_percentage = ($row['obtained_marks'] / $row['total_marks']) * 100;
                $exam_grade = findExamGrade($marks_percentage);

                if ($exam_grade == null) {
                    ResponseService::errorResponse('Grades data does not exists');
                }

                $examMarks->updateOrCreate([
                    'id' => $row['exam_marks_id'] ?? null], 
                    ['exam_timetable_id' => $exam_timetable->id, 
                    'student_id' => $row['student_id'], 
                    'class_subject_id' => $this->classSubjectID, 
                    'obtained_marks' => $row['obtained_marks'], 
                    'passing_status' => $status, 
                    'session_year_id' => $exam_timetable->session_year_id, 
                    'grade' => $exam_grade,]);
            }

            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }    
}
