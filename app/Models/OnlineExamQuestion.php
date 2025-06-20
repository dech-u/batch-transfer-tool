<?php

namespace App\Models;

use App\Repositories\Semester\SemesterInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OnlineExamQuestion extends Model {
    use HasFactory;

    protected $fillable = [
        'class_section_id',
        'class_subject_id',
        'question',
        'image_url',
        'note',
        'last_edited_by',
        'school_id'
    ];
    protected $appends = ['class_section_with_medium','subject_with_name'];


    protected static function boot() {
        parent::boot();
        static::deleting(static function ($data) { // before delete() method call this
            if($data->getAttributes()['image_url']){
                if (Storage::disk('public')->exists($data->getAttributes()['image_url'])) {
                    Storage::disk('public')->delete($data->getAttributes()['image_url']);
                }
            }
        });
    }

    public function options() {
        return $this->hasMany(OnlineExamQuestionOption::class, 'question_id');
    }

    public function class_section(){
        return $this->belongsTo(ClassSection::class, 'class_section_id')->with('section', 'class', 'medium:id,name')->withTrashed();

    }

    public function class_subject() {
        return $this->belongsTo(ClassSubject::class,'class_subject_id');
    }

    public function online_exam_question_commons() {
        return $this->hasMany(OnlineExamQuestionCommon::class, 'online_exam_question_id');
    }

    public function getImageUrlAttribute($value) {
        if ($value) {
            return url(Storage::url($value));
        }

        return null;
    }

    public function scopeOwner($query)
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }
    
            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if(Auth::user()->hasRole('Teacher')){
                // $subjectTeacherData = SubjectTeacher::where('teacher_id',Auth::user()->id)->get();
                // $classSubjectIds = $subjectTeacherData->pluck('class_subject_id');
                // return $query->whereIn('class_subject_id',$classSubjectIds)->where('school_id', Auth::user()->school_id);
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
    
            if (Auth::user()->school_id) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }

    public function getClassSectionWithMediumAttribute() {
        if ($this->relationLoaded('class_section')) {
            return $this->class_section->class->name . ' ' . $this->class_section->section->name . ' - ' . $this->class_section->medium->name;
        }
        return null;
    }

    public function getSubjectWithNameAttribute() {
        if ($this->relationLoaded('class_subject')) {
            return $this->class_subject->subject->name . ' - ' . $this->class_subject->subject->type;
        }
        return null;
    }

    public function scopeCurrentSemesterData($query){
        $currentSemester = app(SemesterInterface::class)->default();
        if($currentSemester){
            $query->where(function ($query) use($currentSemester){
                $query->where('semester_id', $currentSemester->id)->orWhereNull('semester_id');
            });
        }
    }

    public function subject_teacher() {
        return $this->hasMany(SubjectTeacher::class, 'class_subject_id','class_subject_id');
    }

}
