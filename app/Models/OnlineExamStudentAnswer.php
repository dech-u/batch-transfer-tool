<?php

namespace App\Models;

use App\Repositories\Student\StudentInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class OnlineExamStudentAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'online_exam_id',
        'question_id',
        'option_id',
        'submitted_date',
        'school_id'
    ];

    public function online_exam()
    {
        return $this->belongsTo(OnlineExam::class, 'online_exam_id')->withTrashed();
    }

    public function user_submitted_questions(){
        return $this->belongsTo(OnlineExamQuestionChoice::class,'question_id');
    }

    public function scopeOwner($query)
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }
    
            if (Auth::user()->hasRole('School Admin')) {
                $student = app(StudentInterface::class);
                $subjectTeacher = app(SubjectTeacherInterface::class);
                $classSectionId = $subjectTeacher->builder()->pluck('class_section_id');
                $studentId = $student->builder()->whereIn('class_section_id', $classSectionId)->pluck('user_id');
                return $query->whereIn('student_id',$studentId)->where('school_id', Auth::user()->school_id);
                // return $query->where('school_id', Auth::user()->school_id);
            }
    
            if(Auth::user()->hasRole('Teacher')) {
                $subjectTeacher = app(SubjectTeacherInterface::class);
                $student = app(StudentInterface::class);
                $teacherId = Auth::user()->id;
                $classSectionId = $subjectTeacher->builder()->where('teacher_id', $teacherId)->pluck('class_section_id');
                $studentId = $student->builder()->whereIn('class_section_id', $classSectionId)->pluck('user_id');
                return $query->whereIn('student_id',$studentId)->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }
}
