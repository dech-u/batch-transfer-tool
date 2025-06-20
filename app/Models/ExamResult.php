<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class ExamResult extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_id',
        'class_section_id',
        'student_id',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'session_year_id',
        'school_id',
        'status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'student_id')->withTrashed();
    }

    public function session_year()
    {
        return $this->belongsTo(SessionYear::class, 'session_year_id')->withTrashed();
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id')->withTrashed();
    }

    public function scopeOwner($query)
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                if (Auth::user()->hasRole('School Admin')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }

                if (Auth::user()->hasRole('Teacher')) {
                    // Show only the Results in which Teacher is assigned as Class Teacher
                    $classSectionId = ClassTeacher::where('teacher_id', Auth::user()->id)->pluck('class_section_id');
                    return $this->whereIn('class_section_id' , $classSectionId);
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
                    
                    if (request('child_id')) {
                        $childId = request('child_id');
                        $studentAuth = Students::where('id',$childId)->first();
                    } else {
                        $childId = request('student_id');
                        $studentAuth = User::where('id',$childId)->first();
                    }
                    
                    
                    return $query->where('school_id', $studentAuth->school_id);
                }
                return $query;
            }
        }
        return $query;
    }

    /**
     * Get the class_section that owns the ExamResult
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class_section()
    {
        return $this->belongsTo(ClassSection::class);
    }

}
