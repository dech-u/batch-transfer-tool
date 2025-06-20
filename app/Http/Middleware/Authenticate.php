<?php

namespace App\Http\Middleware;

use Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
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
        
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
