<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Attendance extends Model
{
    use HasFactory;

    protected $hidden = ["remark"];
    protected $fillable = [
        'class_section_id',
        'student_id',
        'session_year_id',
        'type',
        'date',
        'remark',
        'school_id'
    ];

    protected $appends = ['roll_number'];

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id')->withTrashed();
    }

    public function scopeOwner($query)
    {
        if(Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }

            if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }
        return $query;
    }

    public function getRollNumberAttribute()
    {   
        if ($this->user) {
            if ($this->user->student) {
                return $this->user->student->roll_number;        
            }
        }
        return '';
    }
}
