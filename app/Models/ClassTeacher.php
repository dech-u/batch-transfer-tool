<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ClassTeacher extends Model
{
    use HasFactory;
    protected $fillable = array(
        "class_section_id",
        "teacher_id",
        "school_id",
    );

    protected $appends = ['class_id'];
    protected $hidden = ['created_at','updated_at'];

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

    public function class_section() {
        return $this->belongsTo(ClassSection::class, 'class_section_id')->withTrashed();
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id')->withTrashed();
    }


    public function getClassIdAttribute(){
        if ($this->relationLoaded('class_section')) {
            return $this->class_section->class_id;
        }
    }
}
