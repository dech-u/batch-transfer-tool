<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;
    protected $fillable = ['message_id','file','file_type'];

    public function getFileAttribute($value) {
        if ($value) {
            return url(Storage::url($value));
        }
        return '';
    }
    

}
