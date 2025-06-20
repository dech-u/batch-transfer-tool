<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory;
    protected $fillable = [
        'title','description','thumbnail','session_year_id','school_id'
    ];

    protected $appends = array('file_extension');
    protected $connection = 'school';
    protected static function boot() {
        parent::boot();
        static::deleting(static function ($gallery) { // before delete() method call this
            if ($gallery->file) {
                foreach ($gallery->file as $file) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }
                    if ($file->file_thumbnail && Storage::disk('public')->exists($file->getRawOriginal('file_thumbnail'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_thumbnail'));
                    }
                }

                $gallery->file()->delete();
            }
            if ($gallery->topic) {
                $gallery->topic()->delete();
            }
        });
    }

    public function scopeOwner()
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                return $this->where('school_id',Auth::user()->school_id);
            }
            return $this;
        }

        return $this;
    }

    public function file() {
        return $this->morphMany(File::class, 'modal');
    }

    public function getThumbnailAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }
        return null;
    }

    public function getFileExtensionAttribute() {
        if (!empty($this->thumbnail)) {
            return pathinfo(url(Storage::url($this->thumbnail)), PATHINFO_EXTENSION);
        }

        return "";
    }

}
