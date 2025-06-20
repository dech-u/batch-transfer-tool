<?php

namespace App\Models;

use App\Services\CachingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class Announcement extends Model {
    protected $fillable = [
        'title',
        'description',
        'table_type',
        'table_id',
        'session_year_id',
        'school_id',
    ];

    public function file() {
        return $this->morphMany(File::class, 'modal');
    }

    public function scopeOwner($query) {
        if (Auth::user()) {

            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if(Auth::user()->hasRole('Teacher')) {
                // $cache = app(CachingService::class);
                // $currentSemester = $cache->getDefaultSemesterData();
                // $class_subject_ids = ClassSubject::where(function($query)use($currentSemester){
                //     $query->where('semester_id',$currentSemester->id)->orWhereNull('semester_id');
                // })->where(['school_id' => Auth::user()->school_id])->pluck('id');
                // $teacher_class_subjects = SubjectTeacher::where(['teacher_id' => Auth::user()->id, 'school_id' => Auth::user()->school_id])->whereIn('class_subject_id',$class_subject_ids)->pluck('class_subject_id');
                // return $query->whereIn('class_subject_id',$teacher_class_subjects)->where('school_id', Auth::user()->school_id);
    
                $teacherId = Auth::user()->id;
                return $query->whereHas('announcement_class.subject_teacher', function ($query) use ($teacherId) {
                    $query->where('teacher_id', $teacherId)
                        ->whereColumn('class_section_id', 'announcement_classes.class_section_id');
                })->orWhereHas('announcement_class',function($q) {
                    $q->where('class_subject_id',null);
                })->where('school_id',Auth::user()->school_id);
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->school_id) {
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

    protected static function boot() {
        parent::boot();
        static::deleting(static function ($announcement) { // before delete() method call this
            if ($announcement->file) {
                foreach ($announcement->file as $file) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }
                }

                $announcement->file()->delete();
            }
        });
    }

    public function announcement_class() {
        return $this->hasMany(AnnouncementClass::class);
    }

    public function scopeSubjectTeacher($query) {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacherId = Auth::user()->id;
            return $query->whereHas('announcement_class.subject_teacher', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->whereColumn('class_section_id', 'announcement_classes.class_section_id');
            })->orWhereHas('announcement_class',function($q) {
                $q->where('class_subject_id',null);
            });
            return $query->where('school_id', Auth::user()->school_id);
        }
        return $query;
    }
}
