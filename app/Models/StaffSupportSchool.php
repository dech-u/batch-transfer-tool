<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StaffSupportSchool extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','school_id'];
    protected $connection = 'mysql';

    /**
     * Get the school that owns the StaffSupportSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(school::class)->withTrashed();
    }

    public function scopeOwner()
    {
        if (Auth::user() && Auth::user()->school_id) {
            return $this->where('school_id',Auth::user()->school_id);
        }
        return $this;
    }

    /**
     * Get the user that owns the StaffSupportSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
