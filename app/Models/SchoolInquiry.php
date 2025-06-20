<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_address',  
        'school_phone', 
        'school_name',        
        'school_email',
        'school_tagline',      
        'date',   
        'status'    
    ];

    public function extra_school_details()
    {
        return $this->hasMany(ExtraSchoolData::class, 'school_inquiry_id', 'id'); 
    }
}
