<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class FeatureSectionList extends Model
{
    use HasFactory;
    protected $fillable = ['feature_section_id','feature','description','image'];


    public function getImageAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }
        return '';
    }
}
