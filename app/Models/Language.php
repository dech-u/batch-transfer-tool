<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model {
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'code',
        'file',
        'status',
        'is_rtl'
    ];
    protected $connection = 'mysql';

}
