<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Status {
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next) {

        $school_database_name = Session::get('school_database_name');
        if ($school_database_name) {
            Config::set('database.connections.school.database', $school_database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            if (Auth::user()->status != 1) {
                Auth::logout();
                $request->session()->flush();
                $request->session()->regenerate();
                return redirect()->route('login')->withErrors(trans('your_account_has_been_deactivated_please_contact_admin'));
            }
            
        }

        

        return $next($request);
    }
}
