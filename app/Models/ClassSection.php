<?php

namespace App\Models;

use App\Services\CachingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class ClassSection extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['class_id', 'section_id', 'class_teacher_id', 'school_id', 'medium_id'];
    protected $appends = ['name', 'full_name'];
    protected $hidden = ['created_at','updated_at'];

    public function class() {
        return $this->belongsTo(ClassSchool::class)->withTrashed();
    }
    
    public function shift() {
        return $this->belongsTo(Shift::class)->withTrashed();
    }

    public function section() {
        return $this->belongsTo(Section::class)->withTrashed();
    }

    public function medium() {
        return $this->belongsTo(Mediums::class)->withTrashed();
    }

    public function class_teachers() {
        return $this->hasMany(ClassTeacher::class, 'class_section_id');
    }

    public function announcement() {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function subject_teachers() {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function timetable() {
        return $this->hasMany(Timetable::class)->orderBy('start_time');
    }

    public function scopeClassTeacher($query) {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            return $query->WhereHas('class_teachers', function ($q) use ($user) {
                $q->where('teacher_id', $user->id);
            });
        }
        return $query;
    }

    public function subjects() {
        return $this->belongsToMany(Subject::class, ClassSubject::class, 'class_id', 'subject_id', 'class_id')->withPivot('id as class_subject_id')->withTrashed();
    }

    public function students(){
        return $this->hasMany(Students::class,'class_section_id')->withTrashed();
    }

    /**
     * Get all of the attendance for the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }


    public function scopeOwner($query) {
        if (Auth::user() && Auth::user()->school_id) {
            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Teacher')) {
                // $subjectTeacher = SubjectTeacher::where('teacher_id', Auth::user()->id)->pluck('class_section_id');
                $currentSemester = app(CachingService::class)->getDefaultSemesterData();
                $subjectTeacher = SubjectTeacher::where('teacher_id', Auth::user()->id)->whereHas('class_subject', function($q) use($currentSemester){
                    (!empty($currentSemester)) ? $q->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $q->orWhereNull('semester_id');
                })->pluck('class_section_id');

                $classTeacher = ClassTeacher::where('teacher_id', Auth::user()->id)->pluck('class_section_id');
                return $query->whereIn('id', array_merge(array_merge($subjectTeacher->toArray(), $classTeacher->toArray())));
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
            return $query->where('school_id', Auth::user()->school_id);
        }
        if (Auth::user() && !Auth::user()->school_id) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }
            return $query;
        }

        return $query;
    }

    public function getNameAttribute() {
        $name = '';
        if ($this->relationLoaded('class')) {
            $name .= $this->class->name;
        }
        if ($this->relationLoaded('class.stream')) {
            $name .= isset($this->class->stream->name) ? ' (' . $this->class->stream->name . ') ' : '';
        }
        if ($this->relationLoaded('section')) {
            $name .= ' ' . $this->section->name;
        }
        return $name;
    }

    public function getFullNameAttribute() {
        $name = '';
        if ($this->relationLoaded('class')) {
            $name .= $this->class->name;
        }

        if ($this->relationLoaded('section')) {
            $name .= ' ' . $this->section->name;
        }
        if ($this->relationLoaded('class') && $this->class->relationLoaded('stream')) {
            $name .= isset($this->class->stream->name) ? ' ( ' . $this->class->stream->name . ' ) ' : '';
        }
        if ($this->relationLoaded('medium')) {
            $name .= ' - ' . $this->medium->name;
        }
        if ($this->relationLoaded('class') && $this->class->relationLoaded('shift')) {
            $name .= isset($this->class->shift->name) ? ' ( ' . $this->class->shift->name . ' ) ' : '';
        }
        return $name;
    }

    /**
     * Get the class_subject that owns the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class_subject()
    {
        return $this->belongsTo(ClassSubject::class, 'class_id', 'class_id');
    }
}
