<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PaymentTransaction extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'payment_gateway', 'order_id', 'payment_id', 'payment_signature', 'payment_status', 'school_id'];

    // protected $connection = 'mysql';

    public function student() {
        return $this->belongsTo(Students::class, 'student_id')->withTrashed();
    }

    public function class() {
        return $this->belongsTo(ClassSchool::class, 'class_id')->withTrashed();
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class)->withTrashed();
    }

    public function school() {
        return $this->belongsTo(School::class)->withTrashed();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function scopeOwner($query) {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }

            if (Auth::user()->hasRole('School Admin')) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }

    /**
     * Get the subscription_bill associated with the PaymentTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription_bill()
    {
        return $this->hasOne(SubscriptionBill::class);
    }

    /**
     * Get the addon_subscription associated with the PaymentTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function addon_subscription()
    {
        return $this->hasOne(AddonSubscription::class);
    }

    public function getConnectionName()
    {
        // Replace this with your logic to determine the connection name
        // For example, you might get it from session or config
        return session('db_connection_name') ?? config('database.default');
    }
}
