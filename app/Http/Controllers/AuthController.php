<?php

namespace App\Http\Controllers;

use App\Repositories\FormField\FormFieldsInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Repositories\User\UserInterface;
use App\Services\CachingService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Throwable;

class AuthController extends Controller {
    private UserInterface $user;
    private CachingService $cache;
    private SystemSettingInterface $systemSettings;
    private FormFieldsInterface $formFields;

    public function __construct(UserInterface $user, CachingService $cachingService, SystemSettingInterface $systemSettings, FormFieldsInterface $formFields) {
        // $this->middleware('auth');
        $this->user = $user;
        $this->cache = $cachingService;
        $this->systemSettings = $systemSettings;
        $this->formFields = $formFields;
    }

    public function login() {
        if (Auth::user()) {
            return redirect('/dashboard');
        }
        $systemSettings = $this->cache->getSystemSettings();
        $extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();
        // $schoolSettings = $this->cache->getSchoolSettings();
        return view('auth.login', compact('systemSettings', 'extraFields'));
    }


    public function changePasswordIndex() {
        return view('auth.change-password');
    }

    public function changePasswordStore(request $request) {
        if (env('DEMO_MODE')) {
            return response()->json(array(
                'error'   => true,
                'message' => "This is not allowed in the Demo Version.",
                'code'    => 112
            ));
        }
        $id = Auth::id();
        $request->validate([
            'old_password'     => 'required',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
        try {
            if (Hash::check($request->old_password, Auth::user()->password)) {
                $data['password'] = Hash::make($request->new_password);
                $this->user->builder()->where('id', $id)->update($data);
                $response = array(
                    'error'   => false,
                    'message' => trans('Data Updated Successfully')
                );
            } else {
                ResponseService::errorResponse('In valid old password');
            }


        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "HomeController --> Change Password Method");
            ResponseService::errorResponse();
        }
        return response()->json($response);
    }

    public function checkPassword(Request $request) {
        $old_password = $request->old_password;
        $password = $this->user->findById(Auth::id());
        if (Hash::check($old_password, $password->password)) {
            return response()->json(1);
        }

        return response()->json(0);
    }


    public function logout(Request $request) {
        session(['logout_time' => now()]);
        $user = Auth::user();
        DB::table('users')->where('email',$user->email)->update(['two_factor_secret' => null,'two_factor_expires_at' => null]);
        Auth::logout();
        $request->session()->flush();
        $request->session()->regenerate();
        session()->forget('school_database_name');
        Session::forget('school_database_name');
        return redirect('/');
    }

    public function profileEdit() {
        $userData = Auth::user();
        return view('auth.profile', compact('userData'));
    }

    public function profileUpdate(Request $request) {
        if (env('DEMO_MODE')) {
            return response()->json(array(
                'error'   => true,
                'message' => "This is not allowed in the Demo Version.",
                'code'    => 112
            ));
        }
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'mobile'     => 'nullable|numeric|digits_between:1,16',
            'gender'     => 'required',
            'dob'        => 'required',
            'email'      => 'required|email|unique:users,email,' . Auth::user()->id,
            'image' => 'required|image|mimes:jpeg,png,jpg,svg,gif,webp',

            'current_address'   => 'required',
            'permanent_address' => 'required',
        ]);
        try {
            $userData = array(
                ...$request->all()
            );
            if (!empty($request->image)) {
                $userData['image'] = $request->image;
            }
            $this->user->update(Auth::user()->id, $userData);

            if (Auth::user()->hasRole('Super Admin')) {
                $data[] = [
                    'name' => 'super_admin_name',
                    'data' => $request->first_name .' '. $request->last_name,
                    'type' => 'string'
                ];
                $this->systemSettings->upsert($data,['name'],['data','type']);
                $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.SETTINGS'));
            }

            if (Auth::user()->hasRole('School Admin')) {
                $id = Auth::user()->id;
                DB::setDefaultConnection('mysql');
                $this->user->update($id, $userData);
            }
            

            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Home Controller -> updateProfile Method");
            ResponseService::errorResponse();
        }
    }

    public function twoFactorAuthentication() {
        if(Auth::user()->two_factor_enabled == 1 && Auth::user()->two_factor_expires_at ) {
            return redirect()->route('dashboard');
        } else {
            $systemSettings = $this->cache->getSystemSettings();
            return view('auth.2fa', compact('systemSettings'));
        }
    }
    
    public function twoFactorAuthenticationCode(Request $request) {
        // Maximum allowed failed attempts
        $maxFailedAttempts = 3;
        $failedAttempts = session('failed_attempts', 0);
        $user = Auth::user();
    
        // If the 2FA secret has expired (based on the user's `updated_at`)
        $actual_start_at = Carbon::parse($user->updated_at);
        $actual_end_at = Carbon::now();
        $mins = $actual_start_at->diffInMinutes($actual_end_at);
    
        if ($mins >= 5) {
            // Expired, clear 2FA secret and expire session
            $this->clearTwoFactorData($user);
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
            session()->forget('school_database_name');
            Session::forget('school_database_name');
    
            return redirect('/login')->withErrors(['code' => '2FA Expired. Please Login Again.']);
        }
    
        // Verify 2FA secret
        if ($user->two_factor_secret == $request->two_factor_secret) {
            // Reset failed attempts and set expiration date for 2FA
            session()->forget('failed_attempts');
            DB::table('users')->where('email', $user->email)->update(['two_factor_expires_at' => Carbon::now()->addDays(1)]);

            return redirect()->intended('/dashboard');
        } else {
            if ($failedAttempts >= $maxFailedAttempts) {
                // Last failed attempt, clear session data
                $this->clearTwoFactorData($user);
                Auth::logout();
                $request->session()->flush();
                $request->session()->regenerate();
                session()->forget('school_database_name');
                Session::forget('school_database_name');
    
                return redirect('/')->withErrors(['code' => 'Too many failed attempts. Please try again.']);
            }
    
            // Increment failed attempts and store in session
            $failedAttempts++;
            session(['failed_attempts' => $failedAttempts]);
    
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }
    }
    
    // function to clear 2FA data
    private function clearTwoFactorData($user) {
        DB::table('users')->where('email', $user->email)->update([
            'two_factor_secret' => null,
            'two_factor_expires_at' => null
        ]);
    }

    
}
