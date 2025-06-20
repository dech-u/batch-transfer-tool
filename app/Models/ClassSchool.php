<?php

namespace App\Models;

use App\Services\CachingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class ClassSchool extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = [
        'name',
        'include_semesters',
        'medium_id',
        'stream_id',
        'shift_id',
        'school_id'
    ];
    protected $appends = ['full_name','semester_name'];
    protected $hidden = ['created_at','updated_at'];

    public function announcement() {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function medium() {
        return $this->belongsTo(Mediums::class)->select('name', 'id')->withTrashed();
    }

    public function class_sections() {
        return $this->hasMany(ClassSection::class, 'class_id')->withTrashed();
    }

    public function sections() {
        return $this->belongsToMany(Section::class, 'class_sections', 'class_id', 'section_id')->withPivot('id')->wherePivot('deleted_at');
    }

    public function section() {
        return $this->belongsToMany(Section::class, 'class_sections', 'class_id', 'section_id')->withPivot('id');
    }

    public function core_subjects() {
        return $this->belongsToMany(Subject::class, ClassSubject::class, 'class_id', 'subject_id')->wherePivot('type', 'Compulsory')->withPivot('id as class_subject_id', 'semester_id')->where('class_subjects.deleted_at',null)->withTrashed();
    }

    public function elective_subjects() {
        return $this->belongsToMany(Subject::class, ClassSubject::class, 'class_id', 'subject_id')->wherePivot('type', 'Elective')->withPivot('id as class_subject_id', 'semester_id')->withTrashed();
    }

    public function all_subjects() {
        return $this->belongsToMany(Subject::class, ClassSubject::class, 'class_id', 'subject_id')->withPivot('id as class_subject_id', 'semester_id')->withTrashed();
    }

//    public function semester() {
//        return $this->hasMany(ClassSubject::class, 'class_id')->select('id', 'class_id', 'semester_id')->groupBy('semester_id');
////            ->with('semester:id,name');
//    }

    public function elective_subject_groups() {
        return $this->hasMany(ElectiveSubjectGroup::class, 'class_id');
    }

    public function fees_class() {
        return $this->hasMany(FeesClassType::class, 'class_id');
    }

    public function class_teachers() {
        return $this->hasManyThrough(ClassTeacher::class, ClassSection::class, 'class_id', 'class_section_id');
    }

    public function subject_teachers() {
        return $this->hasManyThrough(SubjectTeacher::class, ClassSection::class, 'class_id', 'class_section_id');
    }

    public function stream() {
        return $this->belongsTo(Stream::class)->withTrashed();
    }

    public function shift() {
        return $this->belongsTo(Shift::class)->withTrashed();
    }

    public function scopeOwner($query) {
        if (Auth::user()) {

            if (Auth::user()->school_id) {
                if (Auth::user()->hasRole('School Admin')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
    
    
                if (Auth::user()->hasRole('Teacher')) {
                    $subjectTeacher = SubjectTeacher::where('teacher_id', Auth::user()->id)->pluck('class_section_id');
                    $classTeacher = ClassTeacher::where('teacher_id', Auth::user()->id)->pluck('class_section_id');
                    $classSectionIDS = array_merge(array_merge($subjectTeacher->toArray(), $classTeacher->toArray()));
    
                    $classIDS = ClassSection::whereIn('id', $classSectionIDS)->pluck('class_id');
                    return $query->whereIn('id', $classIDS)->where('school_id',Auth::user()->school_id);
        //            return $query->where('school_id', Auth::user()->school_id)->whereHas('class_teachers', function ($q) {
        //                $q->where('teacher_id', Auth::user()->id);
        //            })->orWhereHas('subject_teachers', function ($q) {
        //                $q->where('teacher_id', Auth::user()->id);
        //            });
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

    public function getFullNameAttribute() {
        $name = $this->name;
        if ($this->relationLoaded('stream')) {
            $name .= isset($this->stream->name) ? ' (' . $this->stream->name . ') ' : '';
        }
        if ($this->relationLoaded('medium') && $this->medium) {
            $name .= ' - ' . $this->medium->name;
        }
        return $name;
    }

    public function getSemesterNameAttribute()
    {
        if ($this->include_semesters) {
            $cache = app(CachingService::class);
            $semester = $cache->getDefaultSemesterData();
            if ($semester) {
                return $semester->name;
            }
            return '';
        }
        return '';
    }
}
