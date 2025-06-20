<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends Model {
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'feature_id',
        'status'
    ];
    protected $connection = 'mysql';

    public function feature() {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Get all of the addon_subscription for the Addon
     *
     * @return HasMany
     */
    public function addon_subscription()
    {
        return $this->hasMany(AddonSubscription::class,'feature_id','feature_id');
    }

    public function addon_subscription_count()
    {
        return $this->hasMany(AddonSubscription::class,'feature_id','feature_id')->withTrashed();
    }
}
