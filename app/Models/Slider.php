<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Slider extends Model {
    use HasFactory;

    protected $fillable = [
        'image',
        'school_id',
        'link',
        'type'
    ];

    protected static function boot() {
        parent::boot();
        static::deleting(static function ($slider) { // before delete() method call this
            // if ($slider->isForceDeleting()) {
                if (Storage::disk('public')->exists($slider->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($slider->getRawOriginal('image'));
                }
            // }
        });
    }

    //Getter Attributes
    public function getImageAttribute($value) {
        return url(Storage::url($value));
    }

    public function scopeOwner($query) {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                if (Auth::user()->hasRole('School Admin')) {
                    return $query->where('school_id', Auth::user()->school_id);
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
                return $query;
            }
        }

        return $query;
    }
}
