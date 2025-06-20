<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class Lesson extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'class_section_id',
        'class_subject_id',
        'semester_id',
        'school_id'
    ];

    protected $appends = ['class_section_with_medium','subject_with_name'];


    protected static function boot() {
        parent::boot();
        static::deleting(static function ($lesson) { // before delete() method call this
            if ($lesson->file) {
                foreach ($lesson->file as $file) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }
                    if ($file->file_thumbnail && Storage::disk('public')->exists($file->getRawOriginal('file_thumbnail'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_thumbnail'));
                    }
                }

                $lesson->file()->delete();
            }
            if ($lesson->topic) {
                $lesson->topic()->delete();
            }
        });
    }

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
                          ->whereColumn('class_section_id', 'class_section_id');
                })->where('school_id',Auth::user()->school_id);
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }

    public function class_subject() {
        return $this->belongsTo(ClassSubject::class);
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class)->with('class', 'section', 'medium')->withTrashed();
    }

    public function file() {
        return $this->morphMany(File::class, 'modal');
    }

    public function topic() {
        return $this->hasMany(LessonTopic::class);
    }

    public function lesson_commons() {
        return $this->hasMany(LessonCommon::class);
    }

    public function assignment_commons() {
        return $this->hasMany(AssignmentCommon::class,'assignment_id');
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

    /**
     * Get all of the subject_teacher for the Assignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'class_subject_id','class_subject_id');
    }

}
