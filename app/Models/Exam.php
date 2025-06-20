<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Exam extends Model {
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'session_year_id',
        'description',
        'start_date',
        'end_date',
        'school_id',
        'publish',
        'last_result_submission_date'
    ];

    protected $hidden = ['created_at','updated_at'];

    protected $appends = ["exam_status", "exam_status_name", "has_timetable", "class_name"];

    public function class() {
        return $this->belongsTo(ClassSchool::class, 'class_id')->withTrashed();
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class, 'session_year_id')->withTrashed();
    }

    public function marks() {
        return $this->hasManyThrough(ExamMarks::class, ExamTimetable::class, 'exam_id', 'exam_timetable_id')->orderBy('date');
    }

    public function timetable() {
        return $this->hasMany(ExamTimetable::class);
    }

    public function results() {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    public function semester() {
        return $this->belongsTo(Semester::class, 'semester_id')->withTrashed();
    }
    public function scopeOwner($query) {
        if (Auth::user()) {

            if (Auth::user()->school_id) {
                if (Auth::user()->hasRole('School Admin')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
    
                if(Auth::user()->hasRole('Teacher')){
                    $classTeacherData = ClassTeacher::where('teacher_id',Auth::user()->id)->with('class_section')->get();
                    $subjectTeacherData = SubjectTeacher::where('teacher_id',Auth::user()->id)->with('class_section')->get();
                    $subjectTeacherData = $subjectTeacherData->pluck('class_section.class_id')->toArray();
                    $classIds = $classTeacherData->pluck('class_id')->toArray();
                    $classIds = array_merge($subjectTeacherData, $classIds);
                    $classIds = array_unique($classIds);
                    return $query->whereIn('class_id',$classIds)->where('school_id', Auth::user()->school_id);
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
                if (Auth::user()->hasRole('Guardian')) {
                    $childId = request('child_id');
                    $studentAuth = Students::where('id',$childId)->first();
                    return $query->where('school_id', $studentAuth->school_id);
                }
                return $query;
            }
        }
        return $query;
    }


    public function getExamStatusAttribute() {
        if ($this->relationLoaded('timetable')) {
            $startDate = $this->timetable->min('date');
            $endDate = $this->timetable->max('date');

            $currentTime = Carbon::now();
            $current_date = date($currentTime->toDateString());
            $current_time = Carbon::now();
            //  0- Upcoming, 1-On Going, 2-Completed, 3-All Details
            $exam_status = 3;
            if ($current_date == $startDate && $current_date == $endDate) {
                if (count($this->timetable)) {
                    $exam_end_time = Carbon::parse($this->timetable->first()->end_time);
                    $exam_start_time = Carbon::parse($this->timetable->first()->start_time);
                    if ($current_time->lt($exam_start_time)) {
                        $exam_status = "0";
                    } elseif ($current_time->gt($exam_end_time)) {
                        $exam_status = "2";
                    } else {
                        $exam_status = "1";
                    }
                }
                return $exam_status;
            } else {
                // if ($current_date >= $startDate && $current_date <= $endDate) {
                //     $exam_status = "2"; 
                // } elseif ($current_date < $startDate) {
                //     $exam_status = "1";
                // } else if($current_date >= $endDate) {
                //     $exam_status = "3";
                // } else {
                //     $exam_status = "0";
                // } 
                
                if ($current_date >= $startDate && $current_date <= $endDate) {
                    $exam_status = "1"; 
                } else if ($current_date < $startDate) {
                    $exam_status = "0";
                } else if($current_date >= $endDate) {
                    $exam_status = "2";
                } else {
                    $exam_status = null;
                }
            }

            return $exam_status;


            // $currentDate = now()->toDateString();
            // if ($currentDate >= $startDate && $currentDate <= $endDate) {
            //     return "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
            // }

            // if ($currentDate < $startDate) {
            //     return "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
            // }
            // return "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
        }

        return null;
    }

    public function getExamStatusNameAttribute() {
        if ($this->relationLoaded('timetable')) {
            $startDate = $this->timetable->min('date');
            $endDate = $this->timetable->max('date');

            $currentDate = now()->toDateString();
            if ($currentDate >= $startDate && $currentDate <= $endDate) {
                return "On Going"; // Upcoming = 0 , On Going = 1 , Completed = 2
            }

            if ($currentDate < $startDate) {
                return "Upcoming"; // Upcoming = 0 , On Going = 1 , Completed = 2
            }
            return "Completed"; // Upcoming = 0 , On Going = 1 , Completed = 2
        }

        return null;
    }

    public function getHasTimetableAttribute() {
        if ($this->relationLoaded('timetable')) {
            return count($this->timetable) > 0;
        }

        return false;
    }

    public function getPrefixNameAttribute()
    {
        return $this->name.' # '.$this->class->name.' - '.$this->class->medium->name;
    }

    public function getClassNameAttribute()
    {
        if ($this->relationLoaded('class')) {
            return $this->class->name.' - '.$this->class->medium->name;
        }
        return '';
    }
}
