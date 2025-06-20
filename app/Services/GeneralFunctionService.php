<?php
namespace App\Services;

use App\Models\School;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GeneralFunctionService
{



    public function wrongNotificationSetup($e)
    {
        $status = 1;
        if (Str::contains($e->getMessage(), [ 'does not exist', 'file_get_contents','Cannot access offset of type string on string' ])) {
            $status = 0;
        }
        return $status;
    }

    public function reCaptcha($request)
    {
        if (env('RECAPTCHA_SECRET_KEY') ?? '') {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);
        
            $responseData = $response->json();
        
            if (!$responseData['success']) {
                return 0;
            }
            return 1;
        } else {
            return 1;
        }
    }

    public function schoolreCaptcha($request, $schoolSettings)
    {
        if ($schoolSettings['SCHOOL_RECAPTCHA_SECRET_KEY'] ?? '') {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $schoolSettings['SCHOOL_RECAPTCHA_SECRET_KEY'],
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);
            $responseData = $response->json();
            if (!$responseData['success']) {
                return 0;
            }
            return 1;
        } else {
            return 1;
        }
    }
}
