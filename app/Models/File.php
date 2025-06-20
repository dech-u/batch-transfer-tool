<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class File extends Model {
    use HasFactory;

    protected $fillable = [
        'modal_type',
        'modal_id',
        'file_name',
        'file_thumbnail',
        'type',
        'file_url',
        'school_id',
        'created_at',
        'updated_at',
    ];


    protected $appends = array('file_extension', 'type_detail','youtube_url_action');

    protected static function boot() {
        parent::boot();
        static::deleting(static function ($file) { // before delete() method call this
            if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                Storage::disk('public')->delete($file->getRawOriginal('file_url'));
            }
            if ($file->file_thumbnail && Storage::disk('public')->exists($file->getRawOriginal('file_thumbnail'))) {
                Storage::disk('public')->delete($file->getRawOriginal('file_thumbnail'));
            }
        });
    }

    public function modal() {
        return $this->morphTo();
    }

    //Getter Attributes
    public function getFileUrlAttribute($value) {
        if ($this->type == 1 || $this->type == 3) {
            // IF type is File Upload or Video Upload then add Full URL.
            return url(Storage::url($value));
        }

        return $value;
    }

    //Getter Attributes
    public function getFileThumbnailAttribute($value) {
        if (!empty($value)) {
            return url(Storage::url($value));
        }

        return "";
    }

    public function getFileExtensionAttribute() {
        if (!empty($this->file_url)) {
            return pathinfo(url(Storage::url($this->file_url)), PATHINFO_EXTENSION);
        }

        return "";
    }

    public function scopeOwner($query) {
        if(Auth::user()) {
            if (Auth::user()->hasRole('Super Admin')) {
                return $query;
            }
    
            if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
    
            if (Auth::user()->hasRole('Student')) {
                return $query->where('school_id', Auth::user()->school_id);
            }
        }

        return $query;
    }

    public function getTypeDetailAttribute() {
        //1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link
        if ($this->type == 1) {
            return "File Upload";
        }

        if ($this->type == 2) {
            return "Youtube Link";
        }

        if ($this->type == 3) {
            return "Video Upload";
        }

        if ($this->type == 4) {
            return "Other Link";
        }
        return "";
    }

    public function getYoutubeUrlActionAttribute() {
        if (!empty($this->file_url)) {
            // return pathinfo(url(Storage::url($this->file_url)), PATHINFO_EXTENSION);
            $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    
            // Check if the URL matches the pattern
            if (preg_match($pattern, $this->file_url, $matches)) {
                // Extract Video ID
                $videoId = $matches[1];
                
                // Construct Embed URL
                $embedUrl = "https://www.youtube.com/embed/$videoId";
                $img = "http://img.youtube.com/vi/".$videoId."/hqdefault.jpg";
                $data = [
                    'embed_url' => $embedUrl,
                    'img' => $img
                ];
                return (object)$data;
                
                return $embedUrl;
            }
        
            // Return null if URL doesn't match the pattern
            return null;
        }

        return "";
    }


}
