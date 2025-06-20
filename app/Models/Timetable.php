<?php

namespace App\Models;

use App\Repositories\Semester\SemesterInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Timetable extends Model {
    protected $fillable = [
        "subject_teacher_id",
        "class_section_id",
        "subject_id",
        "start_time",
        "end_time",
        "note",
        "day",
        "type",
        "semester_id",
        "school_id"
    ];

    protected $appends = ['title'];
    protected $hidden = ['created_at','updated_at'];

    public function subject_teacher() {
        return $this->belongsTo(SubjectTeacher::class);
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class)->withTrashed();
    }

    public function subject() {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    public function teacher() {
        return $this->hasOneThrough(User::class, SubjectTeacher::class, 'id', 'id', 'subject_teacher_id', 'teacher_id')->withTrashed();
    }

    public function scopeOwner($query) {
        if (Auth::user()) {
            $user = Auth::user();
            if ($user->hasRole('School Admin')) {
                return $this->where('school_id', $user->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    //        if ($user->hasRole('Teacher')) {
    //            $teacher_id = $user->teacher()->pluck('id');
    //            return $query->whereIn('teacher_id', $teacher_id);
    //        }
            if (Auth::user()->school_id) {
                return $this->where('school_id', $user->school_id);
            }
        }
        return $query;
    }

    public function getTitleAttribute() {
        if ($this->type === "Lecture") {
            if ($this->relationLoaded('subject') && $this->relationLoaded('teacher')) {
                if (!isset($this->subject->name) && !isset($this->teacher->full_name)) {
                    return $this->note;
                }
                $teacherName = $this->teacher->full_name ?? '';
                return $this->subject->name . ' ( ' .$this->subject->type . ' ) ' . ' - ' . $teacherName;
            }

            if ($this->relationLoaded('subject')) {
                return $this->subject->name;
            }

            return $this->note;
        }

        if ($this->type === "Break") {
            return trans("Break");
        }
        return $this->note;
    }

    public function scopeCurrentSemesterData($query){
        $currentSemester = app(SemesterInterface::class)->default();
        if($currentSemester){
            $query->where(function ($query) use($currentSemester){
                $query->where('timetables.semester_id', $currentSemester->id)->orWhereNull('timetables.semester_id');
            });
        }
    }
}
