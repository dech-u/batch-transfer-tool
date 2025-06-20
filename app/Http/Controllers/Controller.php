<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ClassGroup;
use App\Models\ClassSchool;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\FeatureSection;
use App\Models\Gallery;
use App\Models\Language;
use App\Models\Package;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\SessionYear;
use App\Models\Slider;
use App\Models\Stream;
use App\Models\Students;
use App\Models\User;
use App\Repositories\ExtraFormField\ExtraFormFieldsInterface;
use App\Repositories\Guidance\GuidanceInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Services\CachingService;
use App\Services\GeneralFunctionService;
use App\Services\ResponseService;
use App\Services\SubscriptionService;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Str;
use Throwable;
use App\Repositories\FormField\FormFieldsInterface;


class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private SystemSettingInterface $systemSettings;
    private GuidanceInterface $guidance;
    private SubscriptionService $subscriptionService;
    private CachingService $cache;
    private FormFieldsInterface $formFields;
    private ExtraFormFieldsInterface $extraFormFields;

    public function __construct(SystemSettingInterface $systemSettings, GuidanceInterface $guidance, SubscriptionService $subscriptionService, CachingService $cache, FormFieldsInterface $formFields, ExtraFormFieldsInterface $extraFormFields) {
        $this->systemSettings = $systemSettings;
        $this->guidance = $guidance;
        $this->subscriptionService = $subscriptionService;
        $this->cache = $cache;
        $this->formFields = $formFields;
        $this->extraFormFields = $extraFormFields;
    }

    public function makeParentPassword($mobile) {
        return $mobile;
    }

    public function makeStudentPassword($dob) {
        return str_replace('-', '', date('d-m-Y', strtotime($dob)));
    }

    public function index() {

        if (Auth::user() && (Auth::user()->two_factor_enabled == 1 && Auth::user()->two_factor_expires_at)) {
            return redirect('/dashboard');
        }
    
    	if (Auth::user()) {
            return redirect('/dashboard');
        }

        // School website
        $fullDomain = $_SERVER['HTTP_HOST'];
        $fullDomain = str_replace("www.", "", $fullDomain);
        $parts = explode('.', $fullDomain);
        $subdomain = $parts[0];
        $school = '';
        $extraFields = [];
        $demoSchoolUrl = '';
        $isDemoSchool = 0;
        try {
            $demoDomain = School::where('type', 'demo')->pluck('domain')->first();

            if($demoDomain)
            {
                $baseUrl = url('/');
                $baseUrlParts = parse_url($baseUrl);
                $host = $baseUrlParts['host']; 
                $host = str_replace("www.", "", $host);
                $hostParts = explode('.', $host);
                $isDemoSchool = 1;

                // Check if it's a subdomain or main domain
                if (count($hostParts) < 2) {
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
            $school = School::on('mysql')->where('domain', $fullDomain)->orwhere('domain', $subdomain)->first();
        } catch (\Throwable $th) {
            
        }

        // www.school.com or school.com
        // $APP_URL = !(\Request::ip() == '127.0.0.1') ? explode('.', parse_url(env('APP_URL'))['host'])[0] : null;
        $host = \Request::getHost();

        // Check if the request is NOT from localhost
        $APP_URL = !(\Request::ip() == '127.0.0.1') 
           ? (Str::contains($host, 'www') 
               ? explode('.', parse_url(env('APP_URL'))['host'])[0] // If 'www.' exists, skip it
               : explode('.', parse_url(env('APP_URL'))['host'])[0]) // Else, get the first part of the domain
           : null; // If localhost, set to null
            

        if ($school) {
            // Get current subscription features
            $subscription = $this->subscriptionService->active_subscription($school->id);
            if ($subscription) {
                $features = $subscription->subscription_feature->pluck('feature.name')->toArray();
                $addons = $subscription->addons->pluck('feature.name')->toArray();
                $features = array_merge($features, $addons);
                // Check website management feature
                if (in_array('Website Management', $features)) {
                    return $this->school_website($school);    
                }    
            }
            
        }
        if (($subdomain == $APP_URL) || (\Request::ip() == '127.0.0.1' && ( $APP_URL == 'localhost' || 'http://127.0.0.1/' ) ) ) {

        	$features = Feature::activeFeatures()->get();
        	$settings = app(CachingService::class)->getSystemSettings();
            $schoolSettings = SchoolSetting::where('name', 'horizontal_logo')->get();

            $about_us_lists = $settings['about_us_points'] ?? 'Affordable price, Easy to manage admin panel, Data Security';
            $about_us_lists = explode(",", $about_us_lists);
            $faqs = Faq::where('school_id', null)->get();
            $featureSections = FeatureSection::with('feature_section_list')->orderBy('rank', 'ASC')->get();
            $guidances = $this->guidance->builder()->get();
            $languages = Language::get();

            $school = School::count();
            $allSchools = School::all();

            try {
                $student = User::role('Student')->whereHas('school',function($q) {
                    $q->whereNull('deleted_at')->where('status',1);
                })->count();
                $teacher = User::role('Teacher')->whereHas('school',function($q) {
                    $q->whereNull('deleted_at')->where('status',1);
                })->count();
            } catch (Throwable) {
                // If role does not exist in fresh installation then set the counter to 0
                $student = 0;
                $teacher = 0;
            }


        	$counter = [
            	'school'  => $school,
            	'student' => $student,
            	'teacher' => $teacher,
        	];

        	$packages = Package::where('status', 1)->with('package_feature.feature')->where('status', 1)->orderBy('rank', 'ASC')->get();

        	$trail_package = $packages->where('is_trial', 1)->first();
        	if ($trail_package) {
            	$trail_package = $trail_package->id;
        	}

        	$extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();

            //
            // try {
            //     $demoSchool = School::where('type', 'demo')->withTrashed()->first() !== null ? 1 : 0;
            // } catch (\Exception $e) {
            //     $demoSchool = 0;
            // }

        	return view('home', compact('features', 'packages', 'settings', 'faqs', 'guidances', 'languages', 'schoolSettings', 'featureSections', 'about_us_lists', 'counter', 'trail_package', 'extraFields', 'demoSchoolUrl', 'allSchools','isDemoSchool'));
        } else {
        	return view('errors.400');
        }
        // End school website
    }

    public function school_website($school)
    {

        Config::set('database.connections.school.database', $school->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        $sliders = Slider::where('school_id',$school->id)->whereIn('type',[2,3])->get();
        if (!count($sliders)) {
            $sliders = [
                url('assets/school/images/heroImg1.jpg'),
                url('assets/school/images/heroImg2.jpg'),
            ];
        }
        $faqs = Faq::where('school_id',$school->id)->get();

        $students = Students::where('school_id', $school->id)->whereHas('user', function($q) {
            $q->where('status',1);
        })->count();

        $classes = ClassSchool::where('school_id', $school->id)->count();
        $streams = Stream::where('school_id', $school->id)->count();

        $counters = [
            'students' => $students,
            'classes' => $classes,
            'streams' => $streams,
        ];

        $announcements = Announcement::where('school_id',$school->id)->whereHas('announcement_class',function($q) {
            $q->where('class_subject_id',null);
        })->with('announcement_class.class_section.class.stream','announcement_class.class_section.section','announcement_class.class_section.medium')->orderBy('id','DESC')->take(10)->get();

        $class_groups = ClassGroup::where('school_id',$school->id)->get();

        return view('school-website.index',compact('sliders','faqs','counters','announcements','class_groups'));
    }

    public function contact(Request $request) {
        try {
            $admin_email = app(CachingService::class)->getSystemSettings('mail_username');
            $data = [
                'name'        => $request->name,
                'email'       => $request->email,
                'description' => $request->message,
                'admin_email' => $admin_email
            ];

            if (env('RECAPTCHA_SECRET_KEY') ?? '') {
                $validator = Validator::make($request->all(), [
                    'g-recaptcha-response' => 'required',
                ]);
    
                if ($validator->fails()) {
                    ResponseService::errorResponse($validator->errors()->first());
                }
    
                $googleCaptcha = app(GeneralFunctionService::class)->reCaptcha($request);
    
                if (!$googleCaptcha) {
                    ResponseService::errorResponse(trans('reCAPTCHA verification failed. Please try again.'));
                }
            }

            Mail::send('contact', $data, static function ($message) use ($data) {
                $message->to($data['admin_email'])->subject('Get In Touch');
            });

            ResponseService::successResponse(trans('Message send successfully'));

        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Message send successfully. But Email not sent.");
            } else {
                ResponseService::errorResponse(trans('Apologies for the Inconvenience: Please Try Again Later'));
            }
        }


    }

    public function cron_job() {
        Artisan::call('schedule:run');
    }

    public function relatedDataIndex($table, $id) {
        $databaseName = config('database.connections.mysql.database');

        //Fetch all the tables in which current table's id used as foreign key
        $relatedTables = DB::select("SELECT TABLE_NAME,COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $databaseName]);
        $data = [];
        foreach ($relatedTables as $relatedTable) {
            $q = DB::table($relatedTable->TABLE_NAME)->where($relatedTable->TABLE_NAME . "." . $relatedTable->COLUMN_NAME, $id);
            $data[$relatedTable->TABLE_NAME] = $this->buildRelatedJoinStatement($q, $relatedTable->TABLE_NAME)->get()->toArray();
        }

        $currentDataQuery = DB::table($table);

        $currentData = $this->buildRelatedJoinStatement($currentDataQuery, $table)->first();
        return view('related-data.index', compact('data', 'currentData', 'table'));
    }

    private function buildSelectStatement($query, $table) {
        $select = [
            "classes"        => "classes.*,CONCAT(classes.name,'(',mediums.name,')') as name,streams.name as stream_name,shifts.name as shift_name",
            "class_sections" => "class_sections.*,CONCAT(classes.name,' ',sections.name,'(',mediums.name,')') as class_section",
            "users"          => "users.first_name,users.last_name",
            //            "student_subjects" => "student_subjects.*,CONCAT(users.first_name,' ',users.last_name) as student,"
        ];
        return $query->select(DB::raw($select[$table] ?? "*," . $table . ".id as id"));
    }


    private function buildRelatedJoinStatement($query, $table) {
        $databaseName = config('database.connections.mysql.database');
        // If all the child tables further have foreign keys than fetch that table also
        $getTableSchema = DB::select("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$table, $databaseName]);

        $tableAlias = [];
        //Build Join query for all the foreign key using the Table Schema
        foreach ($getTableSchema as $foreignKey) {
        //, 'edited_by', 'created_by', 'guardian_id'
            if ($foreignKey->REFERENCED_TABLE_NAME == $table) {
                //If Related table has foreign key of the same table then no need to add that in join to reduce the query load
                continue;
            }

            // Sometimes there will be same table is used in multiple foreign key at that time alias of the table should be different
            if (in_array($foreignKey->REFERENCED_TABLE_NAME, $tableAlias)) {
                $count = array_count_values($tableAlias)[$foreignKey->REFERENCED_TABLE_NAME] + 1;
                $currentAlias = $foreignKey->REFERENCED_TABLE_NAME . $count;
            } else {
                $currentAlias = $foreignKey->REFERENCED_TABLE_NAME;
            }
            $tableAlias[] = $foreignKey->REFERENCED_TABLE_NAME;

            if (!in_array($foreignKey->COLUMN_NAME, ['school_id', 'session_year_id'])) {
                $query->leftJoin($foreignKey->REFERENCED_TABLE_NAME . " as " . $currentAlias, $foreignKey->REFERENCED_TABLE_NAME . "." . $foreignKey->REFERENCED_COLUMN_NAME, '=', $table . "." . $foreignKey->COLUMN_NAME);
            }
        }

        return $this->buildSelectStatement($query, $table);
    }

    public function relatedDataDestroy($table, $id) {
        try {
            DB::table($table)->where('id', $id)->delete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Controller -> relatedDataDestroy Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }

    }

    public function about_us()
    {
        return view('school-website.about_us');
    }

    public function contact_us()
    {
        return view('school-website.contact');
    }

    public function contact_form(Request $request) {


        $fullDomain = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $fullDomain);
        $subdomain = $parts[0];
     
        $school = School::on('mysql')->where('domain', $fullDomain)->orwhere('domain', $subdomain)->first();

        // Verify google captcha
        $schoolSettings = $this->cache->getSchoolSettings('*', $school->id);
        if ($schoolSettings['SCHOOL_RECAPTCHA_SITE_KEY'] ?? '') {
            $validator = Validator::make($request->all(), [
                'g-recaptcha-response' => 'required',
            ]);
            if ($validator->fails()) {
                ResponseService::errorResponse($validator->errors()->first());
            }
            $googleCaptcha = app(GeneralFunctionService::class)->schoolreCaptcha($request, $schoolSettings);

            if (!$googleCaptcha) {
                ResponseService::errorResponse('reCAPTCHA verification failed. Please try again.');
            }
        }

        try {
            $admin_email = app(CachingService::class)->getSystemSettings('mail_username');
            $data = [
                'name'        => $request->name,
                'email'       => $request->email,
                'subject'     => $request->subject,
                'description' => $request->message,
                'admin_email' => $admin_email,
                'school_email' => $request->school_email
            ];

            Mail::send('contact', $data, static function ($message) use ($data) {
                $message->to($data['school_email'])->subject($data['subject']);
            });

            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Message send successfully. But Email not sent.");
            } else {
                ResponseService::errorResponse('Apologies for the Inconvenience: Please Try Again Later');
            }
        }
    }

    public function photo()
    {
        return view('school-website.photo');
    }

    public function photo_file($id)
    {
        try {
            $photos = Gallery::with(['file' => function($q) {
                $q->where('type',1);
            }])->find($id);
            if ($photos) {
                return view('school-website.photo_file',compact('photos'));
            } else {
                return redirect('school/photos');
            }
        } catch (\Throwable $th) {
            return redirect('school/photos');
        }
    }

    public function video()
    {
        return view('school-website.video');
    }

    public function video_file($id)
    {
        try {
            $videos = Gallery::with(['file' => function($q) {
                $q->where('type',2);
            }])->find($id);
            if ($videos) {
                return view('school-website.video_file',compact('videos'));
            } else {
                return redirect('school/videos');
            }
        } catch (\Throwable $th) {
            return redirect('school/videos');
        }
    }

    public function terms_conditions()
    {
        return view('school-website.terms_conditions');
    }

    public function privacy_policy()
    {
        return view('school-website.privacy_policy');
    }

    public function refund_cancellation()
    {
        return view('school-website.refund_cancellation');
    }

    public function systemLinks($type = null)
    {
        if ($type) {
            
            $faqs = Faq::where('school_id', null)->get();
            $guidances = $this->guidance->builder()->get();
            $languages = Language::get();

            $settings = app(CachingService::class)->getSystemSettings();

            return view('terms_conditions',compact('faqs','guidances','languages','settings','type'));
        }

        return redirect()->back();
    }
   
    public function admission()
    {
         // School website
        $fullDomain = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $fullDomain);
        $subdomain = $parts[0];
        $schoolId = '';
 
        $school = School::on('mysql')->where('domain', $fullDomain)->orwhere('domain', $subdomain)->first();
        
        Config::set('database.connections.school.database', $school->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        // $schoolId = $school->id;
        $classes = ClassSchool::with('medium','stream')->where('school_id', $school->id)->get();   
        if($school) {
            $extraFields = $this->formFields->defaultModel()->where('user_type', 1)->orderBy('rank')->get();    
        } else {
            $extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();
        }  
        return view('school-website.admission', compact('classes', 'extraFields'));
    }
    
    public function registerStudent(Request $request)
    {
   
        $request->validate([
            'first_name'          => 'required',
            'last_name'           => 'required',
            'mobile'              => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            'image'               => 'nullable|mimes:jpeg,png,jpg,svg|image|max:2048',
            'dob'                 => 'required',
            'class_id'            => 'required|numeric',
            /*NOTE : Unique constraint is used because it's not school specific*/
            'guardian_email'      => 'required|email',
            'guardian_first_name' => 'required|string',
            'guardian_last_name'  => 'required|string',
            'guardian_mobile'     => 'required|numeric',
            'guardian_gender'     => 'required|in:male,female',
            'guardian_image'      => 'nullable|mimes:jpg,jpeg,png|max:4096',
            'status'              => 'nullable|in:0,1',
        ]);

        try {
            DB::beginTransaction();
            $admission_date = Carbon::now()->format('Y-m-d');
            $fullDomain = $_SERVER['HTTP_HOST'];
            $parts = explode('.', $fullDomain);
            $subdomain = $parts[0];
     
            $school = School::on('mysql')->where('domain', $fullDomain)->orwhere('domain', $subdomain)->first();

            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            // $defaultSessionYear = SessionYear::where('school_id',$school->id)->where('default', 1)->first();
            $sessionYear = $this->cache->getDefaultSessionYear($school->id);
            $sessionYearId = $sessionYear->id;
            $get_student = Students::where('school_id', $school->id)->latest('id')->withTrashed()->pluck('id')->first();
            $admission_no = $sessionYear->name .'0'. $school->id . '0' . ($get_student + 1);

            // Verify google captcha
            $schoolSettings = $this->cache->getSchoolSettings('*',$school->id);
            if ($schoolSettings['SCHOOL_RECAPTCHA_SITE_KEY'] ?? '') {
                $validator = Validator::make($request->all(), [
                    'g-recaptcha-response' => 'required',
                ]);
    
                if ($validator->fails()) {
                    ResponseService::errorResponse($validator->errors()->first());
                }
    
                $googleCaptcha = app(GeneralFunctionService::class)->schoolreCaptcha($request, $schoolSettings);
    
                if (!$googleCaptcha) {
                    ResponseService::errorResponse('reCAPTCHA verification failed. Please try again.');
                }
            }
           
            // Get the user details from the guardian details & identify whether that user is guardian or not. if not the guardian and has some other role then show appropriate message in response
            $guardianUser = User::whereHas('roles', function ($q) {
                $q->where('name', '!=', 'Guardian');
            })->where('email', $request->guardian_email)->withTrashed()->first();
            if ($guardianUser) {
                ResponseService::errorResponse("Email ID is already taken for Other Role");
            }

            $password = $this->makeParentPassword($request->guardian_mobile);

            $parent = array(
                'first_name' => $request->guardian_first_name,
                'last_name'  => $request->guardian_last_name,
                'mobile'     => $request->guardian_mobile,
                'gender'     => $request->guardian_gender,
                'school_id'  => $school->id
            );
    
            //NOTE : This line will return the old values if the user is already exists
            $parentUser = User::where('email', $request->guardian_email)->first();
            if (!empty($request->guardian_image)) {
                $parent['image'] = UploadService::upload($request->guardian_image, 'guardian');
            }
            if (!empty($parentUser)) {
                if (isset($parent['image'])) {
                    if ($parentUser->getRawOriginal('image') && Storage::disk('public')->exists($parentUser->getRawOriginal('image'))) {
                        Storage::disk('public')->delete($parentUser->getRawOriginal('image'));
                    }
                }
                
                $parentUser->update($parent);
            } else {
                $parent['password'] = Hash::make($password);
                $parent['email'] = $request->guardian_email;
                $parentUser = User::create($parent);
                $parentUser->assignRole('Guardian');
            }
            $image = null;
            if ($request->hasFile('image')) {
                $image = UploadService::upload($request->image, 'user');    
            }
            $password = $this->makeStudentPassword($request->dob);
            //Create Student User First
            $user = User::create([
                'first_name'        => $request->first_name,
                'last_name'         => $request->last_name,
                'email'             => $admission_no,
                'mobile'            => $request->mobile,
                'dob'               => date('Y-m-d', strtotime($request->dob)),
                'gender'            => $request->gender,
                'password'          => Hash::make($password),
                'school_id'         => $school->id,
                'image'             => $image,
                'status'            => 0,
                'current_address'   => $request->current_address,
                'permanent_address' => $request->permanent_address,
                'deleted_at'        => $request->status == 1 ? null : '1970-01-01 01:00:00'
            ]);
            $user->assignRole('Student');
            $student = Students::create([
                'user_id'          => $user->id,
                'class_section_id' => null,
                'admission_no'     => $admission_no,
                'roll_number'      => null,
                'admission_date'   => date('Y-m-d', strtotime($admission_date)),
                'guardian_id'      => $parentUser->id,
                'session_year_id'  => $sessionYearId,
                'class_id'         => $request->class_id ?? null,
                'application_type' => "online",
                'application_status' => 0,
                'school_id'         => $school->id,
            ]);

            $extraDetails = array();
            foreach ($request->extra_fields ?? []as $fields) {
                $data = null;
                if (isset($fields['data'])) {
                    $data = (is_array($fields['data']) ? json_encode($fields['data'], JSON_THROW_ON_ERROR) : $fields['data']);
                }
                $extraDetails[] = array(
                    'user_id'    => $user->id,
                    'form_field_id' => $fields['form_field_id'],
                    'data'          => $data,
                    'school_id'    => $school->id,
                );
            }
            if (!empty($extraDetails)) {
                $this->extraFormFields->createBulk($extraDetails);
            }
        
            DB::commit();
            ResponseService::successResponse('Student Registered successfully');
        } catch (Throwable $e) {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "Student Controller -> Store method");
                ResponseService::errorResponse();
        }
    }

    public function school_db_test()
    {
        // return 1;
        // return $request;
        // Config::set('database.connections.school.database', 'eschool_saas_2_school');
        // DB::purge('school');
        // DB::connection('school')->reconnect();
        // DB::setDefaultConnection('school');
        return Auth::user();
     }
  
    public function emailVerify()
    {
        try {
            $user = Auth::user();
            if (!$user->hasVerifiedEmail()) {
                $now = Carbon::now();
                if ($now->diffInHours($user->updated_at) >= 2) {
                    // Send the verification email
                    $user->sendEmailVerificationNotification();

                    // Update the `updated_at` timestamp to the current time
                    $user->touch(); // This will update the `updated_at` timestamp
                    Auth::logout();
                    return redirect()->route('login')->with('emailSuccess', 'A verification email has been sent to your email address. Please check your inbox.');
                } else {
                    Auth::logout();
                    return redirect()->route('login')->with('emailError', 'You have already requested a verification email recently. Please try again later.');
                }
            }

            if ($user->email_verified_at) {
                DB::connection('mysql')->table('users')->where('id',$user->id)->update(['email_verified_at' => $user->email_verified_at]);
            }        
            return redirect()->route('home');
        } catch (\Throwable $th) {
            Auth::logout();
            return redirect()->route('login')->with('error',trans('An error occurred Please try again later'));
        }
        
    }

    public function cacheFlush()
    {
        $school_database_name = Session::get('school_database_name');
        if ($school_database_name) {
            DB::setDefaultConnection('school');
            Config::set('database.connections.school.database', $school_database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');
            if (Auth::user()) {
                $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));
            }
        } else {
            DB::purge('school');
            DB::connection('mysql')->reconnect();
            DB::setDefaultConnection('mysql');

            $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.SETTINGS'));
        }

        // return DB::getDatabaseName();

        Cache::flush();
        Session::put('landing_locale', null);
        Session::save();

        return redirect()->back();
    }
}
