<?php

namespace App\Models;

use App\Repositories\Semester\SemesterInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class ClassSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['class_id', 'subject_id', 'type', 'semester_id' ,'elective_subject_group_id', 'school_id'];

    protected $appends = ['subject_with_name'];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class)->withTrashed();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class)->withTrashed();
    }

    public function subjectGroup()
    {
        return $this->belongsTo(ElectiveSubjectGroup::class, 'elective_subject_group_id');
    }

    public function scopeSubjectTeacher($query, $class_section_id = null)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            if ($class_section_id) {
                // TODO: Mahesh subject teacher teacher_id foreign key directly assigned to user
                // $subjects_ids = $user->teacher->subjects()->where('class_section_id', $class_section_id)->pluck('subject_id');
                $subjects_ids = $user->subjects()->where('class_section_id', $class_section_id)->pluck('subject_id');
            } else {
                // TODO: Mahesh subject teacher teacher_id foreign key directly assigned to user
                // $subjects_ids = $user->teacher->subjects()->pluck('subject_id');
                $subjects_ids = $user->subjects()->pluck('subject_id');
            }
            return $query->whereIn('subject_id', $subjects_ids);
        }
        return $query;
    }

    public function scopeSubjectTeacherClassTeacher($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacherId = Auth::user()->id;
            return $query->whereHas('subject_teacher', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->where('school_id',Auth::user()->school_id);
        }
        return $query->where('school_id',Auth::user()->school_id);
    }

    public function scopeOwner($query)
    {
        if(Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }
    
            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
            if (Auth::user()->hasRole('Teacher')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }

    public function subjectTeachers() {
        return $this->hasMany(SubjectTeacher::class, 'class_subject_id')->with('teacher');
    }

    public function scopeCurrentSemesterData($query){
        $currentSemester = app(SemesterInterface::class)->default();
        if($currentSemester){
            $query->where(function ($query) use($currentSemester){
                $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id');
            });
        }
    }

    public function getSubjectWithNameAttribute() {
        if ($this->relationLoaded('subject')) {
            return $this->subject->name . ' - ' . $this->subject->type;
        }
        return null;
    }

    /**
     * Get all of the subject for the ClassSubject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subject_teacher()
    {
        return $this->hasMany(SubjectTeacher::class);
    }

}
