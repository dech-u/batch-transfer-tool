<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\PersonalAccessToken;

class CheckSchoolStatus {

    public function handle(Request $request, Closure $next) {

        $url = $request->getRequestUri();
        // For api routes
        if (strpos($url, 'api') !== false) {
            $schoolCode = $request->header('school-code');
            if ($schoolCode) {
                $school = School::on('mysql')->where('code',$schoolCode)->first();

                if ($school) {
                    DB::setDefaultConnection('school');
                    Config::set('database.connections.school.database', $school->database_name);
                    DB::purge('school');
                    DB::connection('school')->reconnect();
                    DB::setDefaultConnection('school');
                    $token = $request->bearerToken();
                    $user = PersonalAccessToken::findToken($token);
                    
                    if ($user) {
                        Auth::loginUsingId($user->tokenable_id);
                    } else {
                        return response()->json(['message' => 'Unauthenticated.']);    
                    }
    
                } else {
                    return response()->json(['message' => 'Invalid school code'], 400);
                }
            }
        } else {
            // For web routes
            $school_database_name = Session::get('school_database_name');
            if ($school_database_name) {
                DB::setDefaultConnection('school');
                Config::set('database.connections.school.database', $school_database_name);
                DB::purge('school');
                DB::connection('school')->reconnect();
                DB::setDefaultConnection('school');
            } else {
                DB::purge('school');
                DB::connection('mysql')->reconnect();
                DB::setDefaultConnection('mysql');
            }
        }
        // ==========================================================
        // $school_database_name = Session::get('school_database_name');
        // if ($school_database_name) {
        //     DB::setDefaultConnection('school');
        //     Config::set('database.connections.school.database', $school_database_name);
        //     DB::purge('school');
        //     DB::connection('school')->reconnect();
        //     DB::setDefaultConnection('school');
        // } else {
        //     DB::purge('school');
        //     DB::connection('mysql')->reconnect();
        //     DB::setDefaultConnection('mysql');
        // }

        // =========================================================
        $user = Auth::user();
        if (isset(Auth::user()->school)) {
            // Check Student, Teacher status for app
            $requestURL = $request->getRequestUri();
            if (stripos($requestURL, 'api') !== false) { // Api routes
                if (Auth::user()->hasRole('Student') || Auth::user()->hasRole('Teacher')) {
                    if ($user->school->status == 0 || $user->status == 0) {
                        $user = $request->user();
                        $user->fcm_id = '';
                        $user->save();
                        $user->currentAccessToken()->delete();
                        return response()->json(['error' => true, 'message' => trans('your_account_has_been_deactivated_please_contact_admin')]);
                    }
                }
            } else {
                if ($user->hasRole('Student') || $user->hasRole('Parent')) {
                    Auth::logout();
                    $request->session()->flush();
                    $request->session()->regenerate();
                    return redirect()->route('login')->withErrors(trans('no_permission_message'));
                }

                if ($user->school->status == 0) {
                    Auth::logout();
                    $request->session()->flush();
                    $request->session()->regenerate();
                    return redirect()->route('login')->withErrors(trans('your_account_has_been_deactivated_please_contact_admin'));
                }
                
            }
        }
        return $next($request);
    }
}
