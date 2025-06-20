<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService {
    public static function compression($requestImage, $folder) {
        $file_name = uniqid('', true) . time() . '.' . $requestImage->getClientOriginalExtension();
        $image = Image::make($requestImage)->encode(null, 60);
        Storage::disk('public')->put($folder . '/' . $file_name, $image);
        return $folder . '/' . $file_name;
    }

    public static function delete($image) {
        if (Storage::disk('public')->exists($image)) {
            return Storage::disk('public')->delete($image);
        }
        //Image does not exist in server so feel free to upload new image
        return true;
    }

}
