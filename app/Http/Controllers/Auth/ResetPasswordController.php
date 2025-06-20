<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected function reset(Request $request)
    {
        if($request->password != $request->password_confirmation)
        {
            return redirect()->back()->withErrors(['email' => 'The password confirmation does not match.']);
        }
        
        // Fetch the school code from the request
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


        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);

       
        if ($response == Password::PASSWORD_RESET) {
            Auth::logout();
            return redirect()->route('login')->with('emailSuccess', 'Your password has been successfully updated. Please log in with your new credentials.');
        } else {
            return redirect()->back()->withErrors(['email' => trans($response)]);
        }

       
    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
}
