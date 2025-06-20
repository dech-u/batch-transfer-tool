<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveMaster extends Model
{
    use HasFactory;
    protected $fillable = ['leaves','holiday','session_year_id','school_id'];
    protected $hidden = ['created_at','updated_at'];
    
    public function scopeOwner()
    {
        if (Auth::user()) {
            return $this->where('school_id', Auth::user()->school_id);
        }
    }

    /**
     * Get the session_year that owns the LeaveMaster
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session_year()
    {
        return $this->belongsTo(SessionYear::class);
    }

    /**
     * Get the school that owns the LeaveMaster
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all of the leave for the LeaveMaster
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leave()
    {
        return $this->hasMany(Leave::class);
    }
}
