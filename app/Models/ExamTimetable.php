<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExamTimetable extends Model {
    use HasFactory;
    use Compoships;

    protected $fillable = [
        'exam_id',
        'class_id',
        'class_subject_id',
        'total_marks',
        'passing_marks',
        'start_time',
        'end_time',
        'date',
        'session_year_id',
        'school_id',
        'created_at',
        'updated_at',
    ];
    protected $hidden = ['created_at','updated_at'];
    protected $appends = ['subject_with_name'];

    public function class_subject() {
        return $this->belongsTo(ClassSubject::class, 'class_subject_id');
    }

    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id')->withTrashed();
    }

    public function class() {
        return $this->belongsTo(ClassSchool::class, 'class_id')->withTrashed();
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class, 'session_year_id')->withTrashed();
    }

    public function exam_marks() {
        return $this->hasMany(ExamMarks::class, 'exam_timetable_id');
    }

    public function scopeOwner($query) {
        if(Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }

            if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                $studentAuth = Auth::user()->student;
                $studentAuth->selectedStudentSubjects();
                $class_subject_ids = $studentAuth->selectedStudentSubjects()->pluck('class_subject_id');
                return $query->whereIn('class_subject_id',$class_subject_ids)->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Guardian')) {
                $childId = request('child_id');
                $studentAuth = Students::where('id',$childId)->first();
                $class_subject_ids = $studentAuth->selectedStudentSubjects()->pluck('class_subject_id');
                return $query->whereIn('class_subject_id',$class_subject_ids)->where('school_id', $studentAuth->school_id);
            }
        }

        return $query;
    }

    public function getSubjectWithNameAttribute() {
        if ($this->relationLoaded('class_subject')) {
            if ($this->class_subject) {
                return $this->class_subject->subject->name . ' - ' . $this->class_subject->subject->type;    
            }
        }
        return null;
    }

    /**
     * Get the subject_teacher that owns the ExamTimetable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'class_subject_id', 'class_subject_id');
    }

}
