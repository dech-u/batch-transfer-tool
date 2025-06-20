<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Students;
use App\Models\User;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\FormField\FormFieldsInterface;
use App\Repositories\School\SchoolInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\Student\StudentInterface;
use App\Services\CachingService;
use App\Services\ResponseService;
use Auth;
use Carbon\Carbon;
use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Response;
use Storage;
use Throwable;
use ZipArchive;
use App\Services\SchoolDataService;
use App\Models\School;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SchoolSettingsController extends Controller {
    // Initializing the Settings Repository
    private SchoolSettingInterface $schoolSettings;
    private CachingService $cache;
    private ClassSectionInterface $classSection;
    private StudentInterface $student;
    private SchoolInterface $school;
    private FormFieldsInterface $formField;

    public function __construct(SchoolSettingInterface $schoolSettings, CachingService $cachingService, ClassSectionInterface $classSection, StudentInterface $student, SchoolInterface $school, FormFieldsInterface $formField) {
        $this->schoolSettings = $schoolSettings;
        $this->cache = $cachingService;
        $this->classSection = $classSection;
        $this->student = $student;
        $this->school = $school;
        $this->formField = $formField;

    }

    public function index() {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $settings = $this->cache->getSchoolSettings();
        $getDateFormat = getDateFormat();
        $getTimeFormat = getTimeFormat();
        $baseUrl = url('/');
        // Remove the scheme (http:// or https://)
        $baseUrlWithoutScheme = preg_replace("(^https?://)", "", $baseUrl);
        $baseUrlWithoutScheme = str_replace("www.", "", $baseUrlWithoutScheme);
		$baseUrlParts = parse_url($baseUrl);
		$host = $baseUrlParts['host'];
		$host = str_replace("www.", "", $host);
		$hostParts = explode('.', $host);
		
		 if (count($hostParts) > 2) {
              if (strpos($baseUrlWithoutScheme, '.') !== false) {
					$baseUrlWithoutScheme = substr($baseUrlWithoutScheme, strpos($baseUrlWithoutScheme, '.') + 1);
				}
         }
		
        $systemSettings = $this->cache->getSystemSettings();
        $schoolService = app(SchoolDataService::class);
        DB::setDefaultConnection('mysql');
        $domain_type = School::where('id',Auth::user()->school_id)->pluck('domain_type')->first();
        $schoolService->switchToSchoolDatabase(Auth::user()->school_id);
        
        return view('school-settings.general-settings', compact('settings','getDateFormat','getTimeFormat','baseUrlWithoutScheme','systemSettings','domain_type'));
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $settings = [
            'school_name'             => 'required|max:255',
            'school_email'            => 'required|email',
            'school_phone'            => 'required',
            'school_address'          => 'required',
            'favicon'                 => 'nullable|image|max:2048',
            'horizontal_logo'         => 'nullable|image|max:2048',
            'vertical_logo'           => 'nullable|image|max:2048',
            'roll_number_sort_column' => 'nullable|in:first_name,last_name',
            'roll_number_sort_order'  => 'nullable|in:asc,desc',
            'change_roll_number'      => 'nullable',
            'school_tagline'          => 'required',
            'date_format'             => 'required',
            'time_format'             => 'required',
            'domain'                  => 'nullable|unique:schools,domain,'.Auth::user()->school_id,
            'google_map_link'         => 'nullable',
            'fees_remainder_duration' => 'required'

        ];
        $validator = Validator::make($request->all(), $settings);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {

            $school_database = School::where('id',Auth::user()->school_id)->pluck('database_name')->first();

            $data = array();
            foreach ($settings as $key => $rule) {
                if ($key == 'horizontal_logo' || $key == 'vertical_logo' || $key == 'favicon') {
                    if ($request->hasFile($key)) {
                        // TODO : Remove the old files from server
                        $data[] = [
                            "name" => $key,
                            "data" => $request->file($key),
                            "type" => "file"
                        ];
                    }
                }  else {
                    $data[] = [
                        "name" => $key,
                        "data" => $request->$key,
                        "type" => "string"
                    ];
                }
            }
            $this->schoolSettings->upsert($data, ["name"], ["data"]);

            DB::setDefaultConnection('mysql');
            Session::forget('school_database_name');
            Session::flush();
            Session::put('school_database_name', null);

            // Update school master table
            $school_data = [
                'name' => $request->school_name,
                'address' => $request->school_address,
                'support_phone' => $request->school_phone,
                'support_email' => $request->school_email,
                'tagline' => $request->school_tagline,
                'domain' => $request->domain,
                'domain_type' => $request->domain_type,
                'fees_remainder_duration' => $request->fees_remainder_duration
            ];
            if ($request->hasFile('vertical_logo') && Auth::user()->school_id) {
                $school = $this->school->findById(Auth::user()->school_id);
                if (Storage::disk('public')->exists($school->getRawOriginal('logo'))) {
                    Storage::disk('public')->delete($school->getRawOriginal('logo'));
                }
                $school_data['logo'] = $request->file('vertical_logo')->store('school','public');
            }
            if (Auth::user()->school_id) {
                $this->school->update(Auth::user()->school_id,$school_data);
            }
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));

            DB::setDefaultConnection('school');
            Config::set('database.connections.school.database', $school_database);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            if ($request->change_roll_number) {
                // Get Sort And Order
                $sort = $request->roll_number_sort_column;
                $order = $request->roll_number_sort_order;

                //Get Class Section's Data With Student Sorted By There Names
                $classSections = $this->classSection->builder()
                    ->with(['students' => function ($query) use ($sort, $order) {
                        $query->join('users', 'students.user_id', '=', 'users.id')
                            ->select('students.*', 'users.first_name', 'users.last_name')
                            ->orderBy('users.' . $sort, $order);
                    }])
                    ->get();

                // Loop towards Class Section Data And make Array To get Student's id and Count Roll Number
                $studentArray = array();
                foreach ($classSections as $classSection) {
                    if (isset($classSection->students) && $classSection->students->isNotEmpty()) {
                        foreach ($classSection->students as $key => $student) {
                            $studentArray[] = array(
                                'id'               => $student->id,
                                'class_section_id' => $student->class_section_id,
                                'roll_number'      => (int)$key + 1
                            );
                        }
                    }
                }

                // Update Roll Number Of Students
                $this->student->upsert($studentArray, ['id'], ['roll_number']);

            }
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "SchoolSettings Controller -> Store method");
            ResponseService::errorResponse();
        }
    }

    public function onlineExamIndex() {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $onlineExamTermsConditions = $this->schoolSettings->getSpecificData('online_exam_terms_condition');
        $name = 'online_exam_terms_condition';
        return response(view('online_exam.terms_conditions', compact('onlineExamTermsConditions', 'name')));
    }

    public function onlineExamStore(Request $request) {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        try {
            DB::beginTransaction();
            $this->schoolSettings->updateOrCreate(["name" => $request->name], ["data" => $request->data, "type" => "string"]);
            DB::commit();
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "SchoolSettings Controller -> storeOnlineExamTermsCondition method");
            ResponseService::errorResponse();
        }
    }

    public function id_card_index()
    {
        ResponseService::noFeatureThenRedirect('ID Card - Certificate Generation');
        ResponseService::noPermissionThenRedirect('id-card-settings');
        $settings = $this->cache->getSchoolSettings();
        $settings['student_id_card_fields'] = explode(",",$settings['student_id_card_fields'] ?? '');
        $settings['staff_id_card_fields'] = explode(",",$settings['staff_id_card_fields'] ?? '');

        $formFields = $this->formField->builder()->whereNot('type','file')->get();

        return view('school-settings.id_card_settings',compact('settings','formFields'));
    }

    public function id_card_store(Request $request)
    {
        ResponseService::noFeatureThenSendJson('ID Card - Certificate Generation');
        ResponseService::noAnyPermissionThenSendJson(['id-card-settings']);

        if ($request->type == 'Student') {
            $settings = [
                'header_color'              => 'required',
                'footer_color'              => 'required',
                'header_footer_text_color'  => 'required',
                'layout_type'               => 'required',
                'background_image'          => 'nullable|image|max:2048',
                'profile_image_style'       => 'required',
                'page_width'                => 'required',
                'page_height'               => 'required',
                'student_id_card_fields'    => 'nullable',
    
                'signature'               => 'nullable|image|max:2048',
            ];
        } else {
            // Staff
            $settings = [
                'staff_header_color'              => 'required',
                'staff_footer_color'              => 'required',
                'staff_header_footer_text_color'  => 'required',
                'staff_layout_type'               => 'required',
                'staff_background_image'          => 'nullable|image|max:2048',
                'staff_profile_image_style'       => 'required',
                'staff_page_width'                => 'required',
                'staff_page_height'               => 'required',
                'staff_id_card_fields'    => 'nullable',
                'signature'               => 'nullable|image|max:2048',
            ];
        }
    

        $validator = Validator::make($request->all(), $settings);
        
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        // $request->validate([
        //     'student_id_card_fields' => 'required',
        //     'staff_id_card_fields' => 'required_if:type,Staff'
        // ],[
        //     'student_id_card_fields.required' => 'Please select at least one field.',
        //     'staff_id_card_fields.required' => 'Please select at least one field.'
        // ]);

        $request->validate([
            'staff_id_card_fields' => 'required_if:type,Staff'
        ],[
            'staff_id_card_fields.required_if' => 'Please select at least one field.'
        ]);

        if (!$request->student_id_card_fields && !$request->extra_form_fields) {
            ResponseService::errorResponse('Please select at least one field');
        }

        try {
            DB::beginTransaction();
            $data = array();
            foreach ($settings as $key => $rule) {
                if ($key == 'background_image' || $key == 'staff_background_image' || $key == 'signature') {
                    if ($request->hasFile($key)) {
                        // TODO : Remove the old files from server
                        $data[] = [
                            "name" => $key,
                            "data" => $request->file($key),
                            "type" => "file"
                        ];
                    }
                } else if($key == 'student_id_card_fields') {
                    $key_value = implode(",",$request->student_id_card_fields ?? []);
                    $data[] = [
                        "name" => $key,
                        "data" => $key_value,
                        "type" => "string"
                    ];

                } else if($key == 'staff_id_card_fields') {
                    $key_value = implode(",",$request->staff_id_card_fields ?? []);
                    $data[] = [
                        "name" => $key,
                        "data" => $key_value,
                        "type" => "string"
                    ];

                } else {
                    if ($request->$key) {
                        $data[] = [
                            "name" => $key,
                            "data" => $request->$key,
                            "type" => "string"
                        ];    
                    }
                    
                }
            }

            // Student
            if ($request->type == 'Student') {
                $this->formField->builder()->update(['display_on_id' => 0]);
                if ($request->extra_form_fields) {
                    $this->formField->builder()->whereIn('id', $request->extra_form_fields)->update(['display_on_id' => 1]);
                }
            }

            $this->schoolSettings->upsert($data, ["name"], ["data"]);
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function remove_image_from_id_card($type)
    {
        ResponseService::noFeatureThenSendJson('ID Card - Certificate Generation');
        ResponseService::noAnyPermissionThenRedirect(['id-card-settings']);
        try {
            DB::beginTransaction();
            $settings = $this->cache->getSchoolSettings();
            if ($type == 'background') {
                $data = explode("storage/", $settings['background_image'] ?? '');
                if (Storage::disk('public')->exists(end($data))) {
                    Storage::disk('public')->delete(end($data));
                }
                $this->schoolSettings->builder()->where('name','background_image')->delete();
            } else if($type == 'staff_background'){
                $data = explode("storage/", $settings['staff_background_image'] ?? '');
                if (Storage::disk('public')->exists(end($data))) {
                    Storage::disk('public')->delete(end($data));
                }
                $this->schoolSettings->builder()->where('name','staff_background_image')->delete();
            } else if($type == 'signature'){
                $data = explode("storage/", $settings['signature'] ?? '');
                if (Storage::disk('public')->exists(end($data))) {
                    Storage::disk('public')->delete(end($data));
                }
                $this->schoolSettings->builder()->where('name','signature')->delete();
            }
            DB::commit();
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function terms_condition()
    {
        $name = 'terms_condition';
        $data = htmlspecialchars_decode($this->cache->getSchoolSettings($name));
        return view('school-settings.terms_condition', compact('name', 'data'));
    }

    public function public_terms_condition($id)
    {
        $schoolSettings = $this->cache->getSchoolSettings('*',$id);
        return htmlspecialchars_decode($schoolSettings['terms_condition'] ?? '');
    }

    public function public_privacy_policy($id)
    {
        $schoolSettings = $this->cache->getSchoolSettings('*',$id);
        return htmlspecialchars_decode($schoolSettings['privacy_policy'] ?? '');
    }
    
    public function public_refund_cancellation($id)
    {
        $schoolSettings = $this->cache->getSchoolSettings('*',$id);
        return htmlspecialchars_decode($schoolSettings['refund_cancellation'] ?? '');
    }

    public function privacy_policy()
    {
        $name = 'privacy_policy';
        $data = htmlspecialchars_decode($this->cache->getSchoolSettings($name));
        return view('school-settings.terms_condition', compact('name', 'data'));
    }

    public function update(Request $request) {
        $request->validate([
            'name' => 'required',
            'data' => 'required'
        ]);
        try {
            $OtherSettingsData[] = array(
                'name' => $request->name,
                'data' => htmlspecialchars($request->data),
                'type' => 'string'
            );
            $this->schoolSettings->upsert($OtherSettingsData, ["name"], ["data"]);
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));
            ResponseService::successResponse("Data Stored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "School Settings Controller -> otherSystemSettings method");
            ResponseService::errorResponse();
        }
    }

    public function emailTemplate()
    {
        ResponseService::noAnyPermissionThenRedirect(['email-template']);
        $data = htmlspecialchars_decode($this->cache->getSchoolSettings());
        $settings = $this->cache->getSchoolSettings();
        return view('school-settings.email_template', compact('settings'));
    }

    public function emailTemplateUpdate(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['email-template']);
        $validator = Validator::make($request->all(), [
            'staff_data' => 'required',
            'parent_data' => 'required',
            'reject_email_data' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $OtherSettingsData = array([
                'name' => 'email-template-staff',
                'data' => htmlspecialchars($request->staff_data),
                'type' => 'string',
            ],
            [
                'name' => 'email-template-parent',
                'data' => htmlspecialchars($request->parent_data),
                'type' => 'string'
            ],
            [
                'name' => 'email-template-application-reject',
                'data' => htmlspecialchars($request->reject_email_data),
                'type' => 'string'
            ]
        );
            $this->schoolSettings->upsert($OtherSettingsData, ["name"], ["data"]);
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));
            ResponseService::successResponse("Data Stored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "School Settings Controller -> otherSystemSettings method");
            ResponseService::errorResponse();
        }
    }

    public function refund_cancellation()
    {
        $name = 'refund_cancellation';
        $data = htmlspecialchars_decode($this->cache->getSchoolSettings($name));
        return view('school-settings.terms_condition', compact('name', 'data'));
    }

    public function thirdPartyApiIndex()
    {
        ResponseService::noFeatureThenRedirect('Website Management');
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $schoolSettings = $this->cache->getSchoolSettings();
        return view('school-settings.third-party-apis',compact('schoolSettings'));
    }

    public function thirdPartyApiUpdate(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Website Management');
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        // $request->validate([
        //     'SCHOOL_RECAPTCHA_SITE_KEY' => 'required',
        //     'SCHOOL_RECAPTCHA_SECRET_KEY' => 'required',
        //     // "SCHOOL_RECAPTCHA_SITE" => 'required'
        // ]);

        try {

            $data = array([
                "name" => 'SCHOOL_RECAPTCHA_SITE_KEY',
                "data" => $request->input('SCHOOL_RECAPTCHA_SITE_KEY'),
                "type" => "text"
            ],
            [
                "name" => 'SCHOOL_RECAPTCHA_SECRET_KEY',
                "data" => $request->input('SCHOOL_RECAPTCHA_SECRET_KEY'),
                "type" => "text"
            ]);

            $this->schoolSettings->upsert($data, ["name"], ["data"]);
            $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'));

            ResponseService::successResponse("Data Stored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "System Settings Controller -> Third Party Api method");
            ResponseService::errorResponse();
        }
    }

}
