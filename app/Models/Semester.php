<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Semester extends Model {
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'start_month',
        'end_month',
        'school_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['current', 'start_month_name', 'end_month_name'];

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
                if (Auth::user()->hasRole('Super Admin')) {
                    return $query;
                }
        
                if (Auth::user()->hasRole('Guardian')) {
                    return $query;
                }
                return $query;
            }
        }

        return $query;
    }

    public function class_subjects() {
        return $this->hasMany(ClassSubject::class, 'semester_id', 'id')->with('subject');
    }

    public function getCurrentAttribute() {
        $currentMonth = date('m');
        if ($this->start_month < $this->end_month) {
            for ($i = $this->start_month; $i <= $this->end_month; $i++) {
                $semesterRange[] = $i;
            }
        } else {
            for ($i = $this->start_month; $i <= 12; $i++) {
                $semesterRange[] = $i;
            }

            for ($i = 1; $i <= $this->end_month; $i++) {
                $semesterRange[] = $i;
            }
        }

        return in_array($currentMonth,$semesterRange);

    }

    public function getStartMonthNameAttribute() {
        $months = [
            trans("January"),
            trans("February"),
            trans("March"),
            trans("April"),
            trans("May"),
            trans("June"),
            trans("July"),
            trans("August"),
            trans("September"),
            trans("October"),
            trans("November"),
            trans("December")
        ];

        return $months[$this->start_month - 1] ?? '';
    }

    public function getEndMonthNameAttribute() {
        $months = [
            trans("January"),
            trans("February"),
            trans("March"),
            trans("April"),
            trans("May"),
            trans("June"),
            trans("July"),
            trans("August"),
            trans("September"),
            trans("October"),
            trans("November"),
            trans("December")
        ];
        return $months[$this->end_month - 1] ?? '';
    }
}
