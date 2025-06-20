<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class DynamicMimes implements Rule {
    protected $attribute;

    public function passes($attribute, $value) {
        $this->attribute = $attribute;

        $type = Arr::get(request()->all(), str_replace('.file', '.type', $attribute));

        if ($type === 'video_upload') {
            return in_array($value->getMimeType(), ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-flv', 'video/webm']);
        }

        if ($type === 'file_upload') {
            return in_array($value->getMimeType(), ['text/plain', 'application/pdf', 'image/jpeg', 'image/png']);
        }

        return true;
    }

    public function message() {
        $type = Arr::get(request()->all(), str_replace('.file', '.type', $this->attribute));

        preg_match('/file_data\.(\d+)\.file/', $this->attribute, $matches);
        $rowNumber = isset($matches[1]) ? (int)$matches[1] + 1 : null;

        if ($type === 'video_upload') {
            return "{$rowNumber} Row Video Uploads File must be a video file (mp4, avi, mov, flv).";
        }

        if ($type === 'file_upload') {
            return "{$rowNumber} Row File Uploads File must be a file (txt, pdf, jpeg, png).";
        }


        return 'The :attribute must be a valid file type.';
    }
}
