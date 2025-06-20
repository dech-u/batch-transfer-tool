<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DemoMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next) {
//        echo $request->getRequestUri();
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
        $exclude_uri = array(
            '/login',
            '/api/student/login',
            '/api/parent/login',
            '/api/teacher/login',
            '/contact',
            '/api/student/submit-online-exam-answers',
            '/students/generate-id-card',
            '/certificate',
            '/certificate/staff-certificate',
            '/staff/generate-id-card'
        );
        $excludeEmails = [
            "info@crestwoodacademy.com",
            "alex.johnson@elementary.org",
            "jamie.smith@gmail.com",
            "thor@gmail.com",
            "2024-2571",
            "subhamsharma5961@gmail.com"
        ];
        if (env('DEMO_MODE') && !$request->isMethod('get') && Auth::user() && !in_array(Auth::user()->email, $excludeEmails) && !in_array($request->getRequestUri(), $exclude_uri)) {
            $excluded_ips = ['103.30.227.53','103.30.227.54','103.30.226.49']; // replace with the IPs you want to exclude
            $test_school_panel = ['jamie.smith@gmail.com','thor@gmail.com','2024-2571','subhamsharma5961@gmail.com'];  // Add testing school user email
            if (!in_array($request->ip(), $excluded_ips)) {
                return response()->json(array(
                    'error'   => true,
                    'message' => "This is not allowed in the Demo Version.",
                    'code'    => 112
                ));
            }
        }
        return $next($request);
    }
}
