<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StaffSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'staff_id',
        'payroll_setting_id',
        'amount',
        'percentage'
    ];
    public function scopeOwner()
    {
        if (Auth::user()) {
            return $this->where('school_id', Auth::user()->school_id);
        }
    }

    public function payrollSetting()
    {
        return $this->belongsTo(PayrollSetting::class)->withTrashed();
    }

    /**
     * Get the staff that owns the StaffSalary
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
