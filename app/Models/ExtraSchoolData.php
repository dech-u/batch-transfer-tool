<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExtraSchoolData extends Model
{
    use HasFactory;

    protected $table = 'extra_school_datas';

    protected $fillable = [
        'school_inquiry_id',
        'school_id',
        'form_field_id',
        'data',
    ];

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
