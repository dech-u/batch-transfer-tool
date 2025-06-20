<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class OnlineExamQuestionChoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'online_exam_id',
        'question_id',
        'marks',
        'school_id'
    ];

    public function online_exam()
    {
        return $this->belongsTo(OnlineExam::class, 'online_exam_id')->withTrashed();
    }

    public function questions()
    {
        return $this->belongsTo(OnlineExamQuestion::class, 'question_id');
    }

    public function scopeOwner($query)
    {
        if (Auth::user()) {
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
}
