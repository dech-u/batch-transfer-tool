<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
    protected $fillable = ['title','description', 'school_id'];

    public function scopeOwner()
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                return $this->where('school_id', Auth::user()->school_id);
            }
            if (!Auth::user()->school_id) {
                return $this->where('school_id', null);
            } 
        }
        
        return $this;
    }
}
