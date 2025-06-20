<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Fee extends Model {
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'due_date',
        'due_charges',
        'due_charges_amount',
        'include_fee_installments',
        'class_id',
        'school_id',
        'session_year_id',
        'created_at',
        'updated_at'
    ];
    protected $appends = ['include_fee_installments', 'total_compulsory_fees', 'total_optional_fees', 'compulsory_fees', 'optional_fees'];

    //'compulsory_fees','optional_fees',

    public function installments() {
        return $this->hasMany(FeesInstallment::class, 'fees_id');
    }

    public function fees_class_type() {
        return $this->hasMany(FeesClassType::class, 'fees_id');
    }

    //    public function type() {
    //        /*
    //         * NOTE : This relationship is currently only depends on Fees ID. Ideally there should be some way to add multiple foreignkey.
    //         * Find out the way to check class_id also here.
    //        */
    //        return $this->hasManyThrough(FeesType::class,FeesClassType::class, 'fees_id','id','id','fees_type_id');
    //    }

    public function fees_paid() {
        return $this->hasMany(FeesPaid::class, 'fees_id')->withTrashed();
    }

    public function class() {
        return $this->belongsTo(ClassSchool::class)->withTrashed();
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class)->withTrashed();
    }

    //    public function getCompulsoryFeesAttribute() {
    //        if ($this->relationLoaded('fees_class')) {
    //            return $this->fees_class->where('optional',0);
    //        }
    //        return null;
    //    }
    //
    //    public function getOptionalFeesAttribute() {
    //        if ($this->relationLoaded('fees_class')) {
    //            return $this->fees_class->where('optional',1);
    //        }
    //        return null;
    //    }

    public function getIncludeFeeInstallmentsAttribute() {
        if ($this->relationLoaded('installments')) {
            return $this->installments->count() > 0;
        }
        return null;
    }

    public function getTotalCompulsoryFeesAttribute() {
        if ($this->relationLoaded('fees_class_type')) {
            $compulsoryFees = $this->fees_class_type->filter(function ($data) {
                return $data->optional == 0;
            });
            return $compulsoryFees->sum('amount');
        }
        return null;
    }

    public function getTotalOptionalFeesAttribute() {
        if ($this->relationLoaded('fees_class_type')) {
            $optionalFees = $this->fees_class_type->filter(function ($data) {
                return $data->optional == 1;
            });
            return $optionalFees->sum('amount');
        }
        return null;
    }


    public function getCompulsoryFeesAttribute() {
        if ($this->relationLoaded('fees_class_type')) {
            $compulsoryFees = $this->fees_class_type->filter(function ($data) {
                return $data->optional == 0;
            });
            // Reset the keys
            $compulsoryFees = $compulsoryFees->values();

            return $compulsoryFees;
        }
        return null;
    }

    public function getOptionalFeesAttribute() {
        if ($this->relationLoaded('fees_class_type')) {
            $optionalFees = $this->fees_class_type->filter(function ($data) {
                return $data->optional == 1;
            });
            // Reset the keys
            $optionalFees = $optionalFees->values();

            return $optionalFees;
        }
        return null;
    }

    public function getDueDateAttribute($value) {
        //        $data = getSchoolSettings('date_format');
        return date('d-m-Y', strtotime($value));
    }

    protected function setDueDateAttribute($value) {
        $this->attributes['due_date'] = date('Y-m-d', strtotime($value));
    }

    public function scopeOwner($query) {
        if (Auth::check()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }

            if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->school_id) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }
}
