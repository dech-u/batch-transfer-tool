<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StudentSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_section_id',
        'class_subject_id',
        'session_year_id'
    ];

    public function class_subject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function scopeOwner()
    {
        if (Auth::user() && Auth::user()->hasRole("School Admin")) {
            return $this->where('school_id',Auth::user()->school_id);
        }
        if (Auth::user() && Auth::user()->hasRole("Teacher")) {
            return $this->where('school_id',Auth::user()->school_id);
        }
        if (Auth::user() && Auth::user()->hasRole("Student")) {
            return $this->where('school_id',Auth::user()->school_id);
        }
        return $this;
    }
}
