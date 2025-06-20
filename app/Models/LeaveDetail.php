<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveDetail extends Model
{
    use HasFactory;
    protected $fillable = ['leave_id','date','status'];
    protected $hidden = ['created_at','updated_at'];

    /**
     * Get the leave that owns the LeaveDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function scopeOwner()
    {
        if (Auth::user()) {
            return $this->where('school_id', Auth::user()->school_id);
        }
    }

    public function getLeaveDateAttribute()
    {
        return date('d - M',strtotime($this->date));
    }
}
