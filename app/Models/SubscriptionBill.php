<?php

namespace App\Models;

use App\Services\CachingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionBill extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $fillable = ['subscription_id','amount','total_student','total_staff','payment_transaction_id','due_date','school_id'];
    protected $appends = ['format_due_date'];

    public function scopeOwner()
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                return $this->where('school_id',Auth::user()->school_id);
            }
        }
        
        return $this;
    }

    /**
     * Get the subscription that owns the SubscriptionBill
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the transaction that owns the SubscriptionBill
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class,'payment_transaction_id','id');
    }

    /**
     * Get the school that owns the SubscriptionBill
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class)->withTrashed();
    }

    public function getFormatDueDateAttribute()
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                $setting = app(CachingService::class)->getSchoolSettings();
                return date($setting['date_format'],strtotime($this->due_date));
            } else {
                $setting = app(CachingService::class)->getSystemSettings();
                return date($setting['date_format'],strtotime($this->due_date));
            }
        }
        return $this->due_date;
    }
}
