<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class ClassGroup extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','image','class_ids','school_id'];

    protected $appends = ['class_name'];

    public function scopeOwner()
    {
        if (Auth::user()) {
            return $this->where('school_id',Auth::user()->school_id);
        }
        return $this;
    }

    public function getImageAttribute($image)
    {
        return url(Storage::URL($image));
    }

    public function getClassNameAttribute()
    {
        $class_ids = explode(",",$this->class_ids);
        return ClassSchool::whereIn('id',$class_ids)->get();
    }
}
