<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeesAdvance extends Model
{
    use HasFactory;
    protected $table = 'fees_advance';
    protected $fillable = [
        'compulsory_fee_id',
        'student_id',
        'parent_id',
        'amount'
    ];

    /**
     * Get the user that owns the FeesAdvance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
