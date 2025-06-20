<?php

namespace App\Models;

use App\Repositories\Leave\LeaveInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'ref_no', 'staff_id', 'month', 'year', 'title', 'description', 'amount', 'date', 'school_id', 'session_year_id', 'basic_salary', 'paid_leaves'];

    protected $appends = ['taken_leaves'];

    public function scopeOwner()
    {
        if (Auth::user() && Auth::user()->school_id) {
            return $this->where('school_id', Auth::user()->school_id);
        }
        if (Auth::user() && !Auth::user()->school_id) {
            return $this;
        }
        return $this;
    }

    /**
     * Get the category that owns the Expense
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class,'category_id','id')->withTrashed();
    }

    // public function getMonthAttribute($value)
    // {
    //     if ($value == null) {
    //         $value = rand(13,100);
    //     }
    //     return $value;
    // }

    /**
     * Get the staff that owns the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function getTakenLeavesAttribute()
    {
        if ($this->staff_id) {
            $leaves = Leave::where('status',1)->where('user_id',$this->staff->user_id)->withCount(['leave_detail as full_leave' => function ($q) {
                $q->whereMonth('date', $this->month)->whereYear('date',$this->year)->where('type', 'Full');
            }])->withCount(['leave_detail as half_leave' => function ($q) {
                $q->whereMonth('date', $this->month)->whereYear('date',$this->year)->whereNot('type', 'Full');
            }])->get();

            return $total_leaves = $leaves->sum('full_leave') + ($leaves->sum('half_leave') / 2);            
        }
        return '';
    }

    /**
     * Get all of the staff_payroll for the Expense
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff_payroll()
    {
        return $this->hasMany(StaffPayroll::class);
    }


}
