<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'data',
        'type',
    ];

    public $timestamps = false;
    protected $connection = 'mysql';

    public function getDataAttribute($value) {
        if ($this->attributes['type'] == 'file') {
            return url(Storage::url($value));
        }

        return $value;
    }

}
