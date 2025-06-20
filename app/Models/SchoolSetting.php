<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SchoolSetting extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'data',
        'type',
        'school_id'
    ];
    
    public $timestamps = false;

    public function getDataAttribute($value) {
        if (isset($this->attributes['type']) && $this->attributes['type'] == 'file') {
            if ($value) {
                return url(Storage::url($value));
            }
            return '';
        }

        if (isset($this->attributes['name']) && $this->attributes['name'] == 'holiday_days') {
            if ($value) {
                return explode(",", $value);
            }
            return '';
        }

        return $value;
    }

    public function scopeOwner($query) {
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
