<?php

namespace App\Http\Middleware;

use App\Models\School;
use Auth;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\PersonalAccessToken;

class CheckChild {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
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
        
        $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
        if (empty($children)) {
            return response()->json(array(
                'error'   => true,
                'message' => "Invalid Child ID Passed.",
                'code'    => 105,
            ));
        }
        return $next($request);
    }
}
