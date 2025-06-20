<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Log;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        Log::info('******',[$request->all()]);
        $request->validate([
            'email' => 'required|email'
        ]);

        if ($request->school_code) {
            $school = School::where('code',$request->school_code)->first();
            if ($school) {
                DB::setDefaultConnection('school');
                Config::set('database.connections.school.database', $school->database_name);
                DB::purge('school');
                DB::connection('school')->reconnect();
                DB::setDefaultConnection('school');    
            }
        }

        try {
            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );
           
            switch ($response) {
                case \Illuminate\Auth\Passwords\PasswordBroker::RESET_LINK_SENT:
                    return back()->with('status', trans($response));
                case \Illuminate\Auth\Passwords\PasswordBroker::INVALID_USER:
                    return back()->withErrors(['email' => trans($response)]);
            }
        } catch (\Exception $e) {
            // Handle SMTP errors
            return back()->withErrors(['email' => 'Sorry, the server is currently busy. Please try again later.']);
        }
    }
}
