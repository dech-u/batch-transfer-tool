<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PayrollSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'amount',
        'percentage',
        'type',
        'school_id'
    ];

    public function scopeOwner()
    {
        if(Auth::user()) {
            return $this->where('school_id', Auth::user()->school_id);
        }
    }

   
    public function staffSalary()
    {
        return $this->hasMany(StaffSalary::class , 'payroll_setting_id', 'id');
    }
}
