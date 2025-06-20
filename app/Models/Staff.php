<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Staff extends Model {
    protected $table = "staffs";
    protected $fillable = [
        'user_id',
        'qualification',
        'salary',
        'joining_date'
    ];
    protected $hidden = ['created_at','updated_at'];

    public function announcement() {
        return $this->morphMany(Announcement::class, 'modal');
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    //Getter Attributes
    public function getImageAttribute($value) {
        if ($value) {
            return url(Storage::url($value));    
        }
        return '';
        
    }

   /**
    * Get all of the expense for the Staff
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
   public function expense()
   {
       return $this->hasMany(Expense::class);
   }

   /**
    * Get all of the leave for the Staff
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
   public function leave()
   {
       return $this->hasMany(Leave::class, 'user_id', 'user_id');
   }

   public function scopeClasses($query)
   {
        return $query;
   }

   public function subjects()
   {
       return $this->hasMany(SubjectTeacher::class, 'teacher_id', 'user_id');
   }

   public function classes() {
       return $this->hasMany(SubjectTeacher::class, 'teacher_id', 'user_id')->groupBy('class_section_id');
   }

   public function class_teacher() {
       return $this->hasMany(ClassTeacher::class, 'teacher_id', 'user_id')->with('class_section.class.stream','class_section.section','class_section.medium');
   }

   public function staffSalary()
    {
        return $this->hasMany(StaffSalary::class, 'staff_id','id');
    }

    public function extra_user_datas()
    {
        return $this->hasMany(ExtraStudentData::class, 'user_id');
    }
    
//    public function scopeTeachers($query)
//    {
//        if (Auth::user()->hasRole('Teacher')) {
//            return $query->where('user_id', Auth::user()->id);
//        }
//        return $query;
//    }

}
