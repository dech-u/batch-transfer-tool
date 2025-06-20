<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CompulsoryFee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'payment_transaction_id',
        'type',
        'installment_id',
        'mode',
        'cheque_no',
        'amount',
        'due_charges',
        'fees_paid_id',
        'status',
        'date',
        'session_year_id',
        'school_id',
        'created_at',
        'updated_at'
    ];
    protected $appends = ['mode_name'];

    public function scopeOwner($query)
    {
        if(Auth::user()){
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

    public function fees_paid() {
        return $this->belongsTo(FeesPaid::class, 'fees_paid_id')->withTrashed();
    }

    public function student(){
        return $this->belongsTo(User::class, 'student_id')->withTrashed();
    }

    public function installment_fee(){
        return $this->belongsTo(FeesInstallment::class, 'installment_id');
    }

    public function advance_fees(){
        return $this->hasMany(FeesAdvance::class);
    }

    public function getModeNameAttribute(){
        if ($this->mode == 1) {
            return 'Cash';
        }

        if($this->mode == 2) {
            return 'Cheque';
        }

        return 'Online';
    }

    /**
     * Get the payment_transaction that owns the CompulsoryFee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_transaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }
}
