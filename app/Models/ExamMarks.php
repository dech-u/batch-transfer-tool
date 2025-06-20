<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ExamMarks extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_timetable_id',
        'student_id',
        'class_subject_id',
        'obtained_marks',
        'passing_status',
        'session_year_id',
        'grade',
        'school_id',
    ];

    public function timetable()
    {
        return $this->belongsTo(ExamTimetable::class, 'exam_timetable_id');
    }

    public function class_subject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function subject()
    {
        /*Has Many through inverse*/
        return $this->hasManyThrough(Subject::class,ClassSubject::class,'id','id','class_subject_id','subject_id')->withTrashedParents()->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'student_id')->withTrashed();
    }

    public function scopeOwner($query)
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
    
                if (Auth::user()->hasRole('Student')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (!Auth::user()->school_id) {
                if (Auth::user()->hasRole('Super Admin')) {
                    return $query;
                }
                return $query;
            }
        }

        return $query;
    }
}
