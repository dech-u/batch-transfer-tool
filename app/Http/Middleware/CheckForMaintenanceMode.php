<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckForMaintenanceMode
{
    protected $except = [];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $data = DB::connection('mysql')->table('system_settings')->get();
        
        // this is roles not allowes to access the site [ school admin, teacher ]
        foreach ($data as $row) {
            if ($row->name == 'web_maintenance') {
                if ($row->data == "1") {
                    if ($request->is('/') || $request->is('*.*') || $request->is('/*') ) {
                        return \Response::view('errors.503', [], 503);
                    } else {
                        if (Auth::user()->role == "School Admin" || Auth::user()->role == "Teacher") {
                            return \Response::view('errors.503', [], 503);
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
