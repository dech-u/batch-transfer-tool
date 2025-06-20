<?php

namespace App\Providers;

use App\Models\Gallery;
use App\Models\Package;
use App\Models\School;
use App\Models\User;
use App\Services\CachingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        $cache = app(CachingService::class);

        $galleries = '';
        $teachers = '';
        $schoolSettings = '';
        $school = '';
        // Get school domain
        $fullDomain = $_SERVER['HTTP_HOST'] ?? '';
        $parts = explode('.', $fullDomain);
        $subdomain = $parts[0];
        
        $demoSchoolUrl = '';
        try {

            $demoDomain = School::where('type', 'demo')->pluck('domain')->first();
            if($demoDomain)
            {
                $baseUrl = url('/');
                $baseUrlParts = parse_url($baseUrl);
                $host = $baseUrlParts['host']; 
                $host = str_replace("www.", "", $host);
                $hostParts = explode('.', $host);
    
                // Check if it's a subdomain or main domain
                if (count($hostParts) > 2) {
                    $hostParts[0] = $demoDomain;
                } else {
                    array_unshift($hostParts, $demoDomain);
                }
    
                $newHost = implode('.', $hostParts);
                $demoSchoolUrl = $baseUrlParts['scheme'] . '://' . $newHost;
    
                if (!empty($baseUrlParts['port'])) {
                    $demoSchoolUrl .= ':' . $baseUrlParts['port'];
                }
    
                if (!empty($baseUrlParts['path'])) {
                    $demoSchoolUrl .= $baseUrlParts['path'];
                }
            }
        } catch (\Throwable $th) {  

        }
        
        try {
            $school = School::on('mysql')->with('user')->where('domain', $fullDomain)->orwhere('domain', $subdomain)->first();
        } catch (\Throwable $th) {
            
        }
        
        
        
        if ($school) {
            DB::setDefaultConnection('school');
            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');
            $teachers = User::where('school_id',$school->id)->role('Teacher')->select('id','first_name','last_name','image')->with('staff')->get();
            
            $schoolSettings = $cache->getSchoolSettings('*',$school->id);
            if (isset($schoolSettings['our_mission_points'])) {
                $schoolSettings['our_mission_points'] = explode(",",$schoolSettings['our_mission_points']);    
            }
            $galleries = Gallery::where('school_id',$school->id)->with('file')->withCount(['file' => function($q) {
                $q->where('type',2);
            }])->where('session_year_id',$schoolSettings['session_year'] ?? 1)->get();
        }

        

        /*** Header File ***/
        View::composer('layouts.header', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
            $view->with('languages', $cache->getLanguages());

            if (!empty(Auth::user()->school_id)) {
                $view->with('sessionYear', $cache->getDefaultSessionYear());
                $view->with('schoolSettings', $cache->getSchoolSettings());
                $view->with('semester', $cache->getDefaultSemesterData());
            }
        });

        /*** Include File ***/
        View::composer('layouts.include', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
            if (!empty(Auth::user()->school_id)) {
                $view->with('schoolSettings', $cache->getSchoolSettings());
            }
        });
        View::composer('auth.login', static function (\Illuminate\View\View $view) use ($cache, $schoolSettings, $school) {
            $view->with('schoolSettings', $schoolSettings);
            $view->with('systemSettings', $cache->getSystemSettings());
            $view->with('school', $school);
        });
        View::composer('auth.2fa', static function (\Illuminate\View\View $view) use ($cache, $schoolSettings, $school) {
            $view->with('schoolSettings', $schoolSettings);
            $view->with('systemSettings', $cache->getSystemSettings());
            $view->with('school', $school);
        });
        /*** Email  ***/

        View::composer('auth.passwords.email', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
        });
        View::composer('auth.passwords.reset', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
        });
        View::composer('auth.login', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());

            $trail_package = Package::where('is_trial', 1)->first();
            if ($trail_package) {
                $trail_package = $trail_package->id;
            }
            $view->with('trail_package', $trail_package);
        });
        View::composer('home', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
        });

        View::composer('layouts.home_page.master', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
            if (!empty(Auth::user()->school_id)) {
                $view->with('schoolSettings', $cache->getSchoolSettings());
            }
        });

        View::composer('layouts.school.master', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
        });

        View::composer('layouts.sidebar', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
        });

        /*** Footer File ***/
        View::composer('layouts.footer_js', static function (\Illuminate\View\View $view) use ($cache) {
            $view->with('systemSettings', $cache->getSystemSettings());
            if (!empty(Auth::user()->school_id)) {
                $view->with('schoolSettings', $cache->getSchoolSettings());
            }
        });


        /*** School website ***/
        View::composer('school-website.*', static function (\Illuminate\View\View $view) use ($cache, $galleries, $teachers, $schoolSettings) {
            // if ($school) {
            //     $schoolSettings = $cache->getSchoolSettings('*',$school->id);
            //     if (isset($schoolSettings['our_mission_points'])) {
            //         $schoolSettings['our_mission_points'] = explode(",",$schoolSettings['our_mission_points']);    
            //     }
            //     $view->with('schoolSettings', $schoolSettings);
            // }
            $view->with('schoolSettings', $schoolSettings);
            $view->with('teachers', $teachers);
            $view->with('galleries', $galleries);
            $view->with('systemSettings', $cache->getSystemSettings());
        });
    }
}
