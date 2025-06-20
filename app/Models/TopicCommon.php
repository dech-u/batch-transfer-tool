<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TopicCommon extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_topics_id',
        'class_section_id',
        'class_subject_id',
    ];

    protected $appends = ['class_section_with_medium','subject_with_name'];

    public function scopeOwner($query) {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }
    
            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Teacher')) {
                $teacherId = Auth::user()->id;
                return $query->whereHas('subject_teacher', function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId)
                          ->whereColumn('class_section_id', 'lessons.class_section_id');
                })->where('school_id',Auth::user()->school_id);
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }
  
    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'class_subject_id','class_subject_id');
    }
    
    public function class_subject() {
        return $this->belongsTo(ClassSubject::class);
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class)->with('class', 'section', 'medium')->withTrashed();
    }

    public function getClassSectionWithMediumAttribute() {
        if ($this->relationLoaded('class_section')) {
            return $this->class_section->class->name . ' ' . $this->class_section->section->name . ' - ' . $this->class_section->medium->name;
        }
        return null;
    }

    public function getSubjectWithNameAttribute() {
        if ($this->relationLoaded('class_subject')) {
            if ($this->class_subject) {
                return $this->class_subject->subject->name . ' - ' . $this->class_subject->subject->type;
            }
            
        }
        return null;
    }
}
