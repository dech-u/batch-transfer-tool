<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPayroll extends Model
{
    use HasFactory;
    protected $fillable = ['expense_id', 'payroll_setting_id', 'amount', 'percentage', 'school_id'];

    public function scopeOwner()
    {
        if (Auth::user()) {
            return $this->where('school_id', Auth::user()->school_id);
        }
    }

    /**
     * Get the payroll_setting that owns the StaffPayroll
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payroll_setting()
    {
        return $this->belongsTo(PayrollSetting::class)->withTrashed();
    }
}
