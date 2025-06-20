<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends Model {
    use HasFactory;

    protected $fillable = ['package_id', 'feature_id'];

    /**
     * Get the feature that owns the PackageFeature
     *
     * @return BelongsTo
     */
    public function feature() {
        return $this->belongsTo(Feature::class);
    }
}
