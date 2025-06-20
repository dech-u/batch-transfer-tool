<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class SessionYear extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'school_id',
        'default',
        'school_id',
        'include_fee_installments',
        'fee_due_date',
        'fee_due_charges',
    ];

    public function fee_installments() {
        return $this->hasMany(FeesInstallment::class, 'session_year_id');
    }

    public function semesters() {
        return $this->hasMany(Semester::class, 'session_year_id')->withTrashed();
    }

    public function scopeOwner($query) {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
    
                if (Auth::user()->hasRole('Student')) {
                    return $query->where('school_id', Auth::user()->school_id);
                }
                return $query->where('school_id', Auth::user()->school_id);
    
            }

            if (!Auth::user()->school_id) {
                if (Auth::user()->hasRole('Guardian')) {
                    return $query;
                }
                if (Auth::user()->hasRole('Super Admin')) {
                    return $query;
                }
                return $query;
            }
        }
        

        

        return $query;
    }

    protected function setStartDateAttribute($value) {
        $this->attributes['start_date'] = date('Y-m-d', strtotime($value));
    }

    protected function setEndDateAttribute($value) {
        $this->attributes['end_date'] = date('Y-m-d', strtotime($value));
    }
}
