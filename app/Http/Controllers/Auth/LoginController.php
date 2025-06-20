<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Providers\RouteServiceProvider;
use App\Services\CachingService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    private CachingService $cache;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CachingService $cachingService)
    {
        $this->cache = $cachingService;
        $this->middleware('guest')->except('logout');
        // $this->middleware('2fa')->except('logout');
    }

    public function username()
    {
        $loginValue = request('email');
        $this->username = filter_var($loginValue ,FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        request()->merge([$this->username => $loginValue]);
        return $this->username == 'mobile' ? 'mobile' : 'email';
    }

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
            'code' => 'nullable|string',
        ]);

        $loginField = $this->username();

        // maintainence mode is roles not allowes to access the site [ school admin, teacher ] only super admin allowed
        $data = DB::connection('mysql')->table('system_settings')->get();
        foreach ($data as $row) {
            if ($row->name == 'web_maintenance') {
                if ($row->data == "1") {
                    if ($request->code != null) {
                        return \Response::view('errors.503', [], 503);
                    }
                }
            }
        }

        if ($request->code ) {
            // Retrieve the school's database connection info
            $school = School::on('mysql')->where('code', $request->code)->first();

            if (!$school) {
                return back()->withErrors(['code' => 'Invalid school identifier.']);
            }

            // Set the dynamic database connection
            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            \Log::info('Switched to database: ' . DB::connection('school')->getDatabaseName());
            // Attempt login using the user's credentials within the school's database
            if (Auth::guard('web')->attempt([
                $loginField => $request->email,
                'password' => $request->password,
            ])) {
                \Log::info('User authenticated successfully.', [
                    'user_id' => Auth::guard('web')->id(),
                    'email' => $request->email,
                ]);

                // Optionally, log in the user explicitly
                Auth::loginUsingId(Auth::guard('web')->id());
                $user = Auth::guard('web')->user();
                
                // Web Login in Student/Guardian Not Allowed (only App Login)
                if($user->hasRole('Student') || $user->hasRole('Guardian')) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'You are not authorized to access Web Login (Student/Guardian)');
                }
            
                // Set custom session data
                session(['user_id' => $user->id]);
                session(['user_email' => $user->email]);
                
                session()->save();

                Auth::login($user);
                
                Session::put('school_database_name', $school->database_name);

                $data = DB::table('users')->where('email',$request->email)->first();
                
                if ($data && $school->status == 1) {
                    if (( $data->two_factor_secret == null || $data->two_factor_expires_at == null ) && $data->two_factor_enabled == 1 && !Auth::user()->hasRole('Teacher') && $request->email != 'demo@school.com' && !env('DEMO_MODE')) {
                        $twoFACode = $this->generate2FACode();
                        $settings = $this->cache->getSystemSettings();
                        $user = Auth::user();

                        DB::table('users')->where('email',$user->email)->update(['two_factor_secret' => $twoFACode, 'updated_at' => Carbon::now()]);

                        $schools = DB::table('users')->where('email',$user->email)->first();

                        $this->send2FAEmail($schools, $user, $settings, $twoFACode);

                        return redirect()->route('auth.2fa');
                    } else {
                        return redirect()->intended('/dashboard');
                    }
                }

                
                // return redirect()->intended('/dashboard');
            } else {
                \Log::error('Login attempt failed in school database. Email: ' . $request->email);
            }
        } else {
            // Attempt login on the main connection
            DB::setDefaultConnection('mysql');
            Session::forget('school_database_name');
            Session::flush();
            Session::put('school_database_name', null);
            if (Auth::guard('web')->attempt([
                $loginField => $request->email,
                'password' => $request->password,
            ])) {

                if (Auth::user()->school) {
                    Auth::logout();
                    $request->session()->flush();
                    $request->session()->regenerate();
                    session()->forget('school_database_name');
                    Session::forget('school_database_name');
                    return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
                }

                $data = DB::table('users')->where('email',$request->email)->first();

                if ($data) {
                    if (( $data->two_factor_secret == null || $data->two_factor_expires_at == null ) && $data->two_factor_enabled == 1 && !Auth::user()->hasRole('Teacher') && $request->email != 'superadmin@gmail.com' && !env('DEMO_MODE')) {
                       
                        $twoFACode = $this->generate2FACode();
                        $settings = $this->cache->getSystemSettings();
                        $user = Auth::user();

                        DB::table('users')->where('email',$user->email)->update(['two_factor_secret' => $twoFACode, 'updated_at' => Carbon::now()]);

                        $adminData = DB::table('users')->where('email',$user->email)->first();
                        // dd('done',$adminData);
                        $this->send2FAEmail($adminData, $user, $settings, $twoFACode);

                        return redirect()->route('auth.2fa');
                    } else {
                        return redirect()->intended('/dashboard');
                    }
                }

                session(['db_connection_name' => 'mysql']);
                return redirect()->intended('/home');
            } else {
                \Log::error('Login attempt failed in main database. Email: ' . $request->email);
            }
        }

        // Login failed, redirect back with an error message
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    private function generate2FACode($length = 6)
    {
        // Define the characters to be used in the code
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        
        // Loop through and generate each character
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $code;
    }


    public function send2FAEmail($schools, $user, $settings, $twoFACode)
    {

        try {
            $schools_name = $schools->first_name ." ". $schools->last_name;
            $emailBody = $this->replacePlaceholders($schools_name, $user, $settings, $twoFACode, $twoFACode);

            // Prepare the email data
            $data = [
                'subject' => '2FA Code for ' . $schools_name,
                'email' => $user['email'],
                'email_body' => $emailBody,
                'verification_code' => $twoFACode,
            ];

            // Send the email with the 2FA code
            Mail::send('schools.email', $data, static function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
            });

            // Log the email sent for debugging purposes
            \Log::info('2FA code sent to: ' . $data['email']);
        } catch (\Throwable $th) {
            if (Str::contains($th->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Message send successfully. But Email not sent.");
            } else {
                ResponseService::errorResponse(trans('error_occured'));
            }
        }
    }

    private function replacePlaceholders($school_name, $user, $settings, $school_code, $twoFACode)
    {
        $templateContent = $settings['email_template_two_factor_authentication_code'] ?? '';

        $systemSettings = $this->cache->getSystemSettings();

        $placeholders = [
            '{school_admin_name}' => $user->full_name,
            '{school_name}' => $school_name,
        
            '{super_admin_name}' => $settings['super_admin_name'] ?? 'Super Admin',
            '{support_email}' => $settings['mail_send_from'] ?? 'example@gmail.com',
            '{support_contact}' => $systemSettings['mobile'] ?? '9876543210',
            '{system_name}' => $settings['system_name'] ?? 'eSchool Saas',
            '{expiration_time}' => '5',
            '{url}' => url('/'),
        
            '{verification_code}' => $twoFACode,
        ];
        
        // Replace the placeholders in the template content
        foreach ($placeholders as $placeholder => $replacement) {
            $templateContent = str_replace($placeholder, $replacement, $templateContent);
        }

        return $templateContent;
    }
    
}
