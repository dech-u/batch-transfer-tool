<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Auth;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class LanguageManager
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        try {            
            if (Session::has('locale') && Auth::user() && Session::get('locale') == Auth::user()->language) {
                // Admin dashboard
                app()->setLocale(Session::get('locale'));
            } else {
                // When users log in to the system, make sure to set their preferred panel language.
                if (Auth::user()) {
                    Session::put('locale', Auth::user()->language);
                    Session::save();
                    $language = Language::where('code', Auth::user()->language)->first();
                    Session::put('language', $language);
                    app()->setLocale(Auth::user()->language);
                } else {
                    // Landing page
                    if (Session::has('landing_locale')) {
                        app()->setLocale(Session::get('landing_locale'));
                    } else {
                        $lang = env('APPLANG');
                        if (is_null($lang)) {
                            $lang = "en";
                        }

                        $language = Language::where('code', $lang)->first();
                        Session::put('landing_locale', $lang);
                        Session::save();
                        Session::put('language', $language);
                        app()->setLocale(Session::get('landing_locale'));
                    }
                }
            }
        } catch (\Throwable $th) {
            if (Session::has('locale')) {
                app()->setLocale(Session::get('locale'));
            }
        }
        return $next($request);
    }
}
