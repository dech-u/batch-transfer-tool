<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExtraStudentData extends Model {
    use HasFactory;
    use SoftDeletes;

    protected $table = 'extra_user_datas';

    protected $fillable = [
        'student_id',
        'form_field_id',
        'data',
        'school_id',
    ];

    protected $appends = ['file_url'];

    public function scopeOwner($query) {
        if (Auth::user() && Auth::user()->hasRole('Super Admin')) {
            return $query;
        }

        if (Auth::user() &&Auth::user()->hasRole('School Admin')) {
            return $query->where('school_id', Auth::user()->school_id);
        }

        if (Auth::user() && Auth::user()->hasRole('Student')) { 
            return $query->where('school_id', Auth::user()->school_id);
        }

        return $query;
    }

    public function form_field() {
        return $this->belongsTo(FormField::class, 'form_field_id')->withTrashed();
    }


    public function getFileUrlAttribute() {
        if ($this->relationLoaded('form_field')) {
            if ($this->form_field->type == "file" && !empty($this->data)) {
                return url(Storage::url($this->data));
            }

            return null;
        }
        return null;

    }
}
