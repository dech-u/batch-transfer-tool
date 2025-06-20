<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\hasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class AddonSubscription extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'school_id',
        'feature_id',
        'price',
        'start_date',
        'end_date',
        'status',
        'subscription_id',
        'payment_transaction_id'
    ];
    protected $connection = 'mysql';

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
     * Get the feature that owns the AddonSubscription
     *
     * @return BelongsTo
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Get the addon that owns the AddonSubscription
     *
     * @return hasOne
     */
    public function addon()
    {
        return $this->hasOne(Addon::class, 'feature_id', 'feature_id')->withTrashed();
    }

    public function getDaysAttribute()
    {
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date));
    }

    /**
     * Get the transaction that owns the AddonSubscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class,'payment_transaction_id','id');
    }
}
