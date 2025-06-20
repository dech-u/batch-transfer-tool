<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\User;
use App\Repositories\Faqs\FaqsInterface;
use App\Repositories\Guidance\GuidanceInterface;
use App\Repositories\Package\PackageInterface;
use App\Repositories\School\SchoolInterface;
use App\Repositories\SchoolInquiry\SchoolInquiryInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Repositories\User\UserInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\GeneralFunctionService;
use App\Services\ResponseService;
use App\Services\SchoolDataService;
use App\Services\SubscriptionService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\Review;
use Symfony\Component\HttpFoundation\IpUtils;
use Throwable;
use TypeError;
use Illuminate\Support\Facades\Session;
use App\Repositories\ExtraSchoolData\ExtraSchoolDataInterface;
use App\Repositories\FormField\FormFieldsInterface;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;

class SchoolController extends Controller {

    // Initializing the schools Repository
    private SchoolInterface $schoolsRepository;
    private UserInterface $userRepository;
    private PackageInterface $package;
    private CachingService $cache;
    private SubscriptionService $subscriptionService;
    private SchoolSettingInterface $schoolSettings;
    private GuidanceInterface $guidance;
    private FaqsInterface $faqs;
    private SystemSettingInterface $systemSettings;
    private SchoolInquiryInterface $schoolInquiry;
    private ExtraSchoolDataInterface $extraSchoolData;
    private FormFieldsInterface $formFields;

    public function __construct(SchoolInterface $school, UserInterface $user, PackageInterface $package, CachingService $cache, SubscriptionService $subscriptionService, SchoolSettingInterface $schoolSettings, GuidanceInterface $guidance, FaqsInterface $faqs, SystemSettingInterface $systemSettings, SchoolInquiryInterface $schoolInquiry, ExtraSchoolDataInterface $extraSchoolData, FormFieldsInterface $formFields) {
        $this->schoolsRepository = $school;
        $this->userRepository = $user;
        $this->package = $package;
        $this->cache = $cache;
        $this->subscriptionService = $subscriptionService;
        $this->schoolSettings = $schoolSettings;
        $this->guidance = $guidance;
        $this->faqs = $faqs;
        $this->systemSettings = $systemSettings;
        $this->schoolInquiry = $schoolInquiry;
        $this->extraSchoolData = $extraSchoolData;
        $this->formFields = $formFields;
    }


    public function index() {
        ResponseService::noPermissionThenRedirect('schools-list');
        $packages = $this->package->builder()->orderBy('rank')->get()->pluck('package_with_type','id')->toArray();

        $baseUrl = url('/');
        // Remove the scheme (http:// or https://)
        $baseUrlWithoutScheme = preg_replace("(^https?://)", "", $baseUrl);
        $baseUrlWithoutScheme = str_replace("www.", "", $baseUrlWithoutScheme);

        $schools = $this->schoolsRepository->builder()->latest()->first();
        try {
            $demoSchool = $this->schoolsRepository->builder()->where('type', 'demo')->withTrashed()->first() !== null ? 1 : 0;
        } catch (\Exception $e) {
            $demoSchool = 0;
        }
      
        $school_code = date('Y').(($schools->id ?? 0) + 1);
        $settings = $this->cache->getSystemSettings();

        $prefix = $settings['school_code_prefix'] ?? 'SCH';

        $email_verified = $settings['email_verified'] ? 1 : 0;

        $extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();

        return view('schools.index', compact('packages','baseUrlWithoutScheme','school_code','prefix', 'demoSchool','extraFields','email_verified'));
    }


    public function store(Request $request) {
        ResponseService::noAnyPermissionThenRedirect(['schools-create']);

        $fullDomain = $_SERVER['HTTP_HOST']; 
        $parts = explode('.', $fullDomain); 
        $subdomain = $parts[0]; 
        // if($request->domain_type == "default")
        if ($subdomain === $request->domain) {
            ResponseService::errorResponse("This Domain is already in use choose any other Domain name.");
        }

        $validator = Validator::make($request->all(), [
            'school_name'          => 'required',
            'school_support_email' => 'required|unique:schools,support_email',
            'school_support_phone' => 'required|numeric|digits_between:1,16',
            'school_tagline'       => 'required',
            'school_address'       => 'required',
            'school_image'         => 'required|mimes:jpg,jpeg,png,svg,svg+xml|max:2048',
            'domain'               => 'nullable|unique:schools,domain',
            'school_code_prefix'   => 'required'

        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $school_code = $request->school_code_prefix . $request->school_code;
            $settings = $this->cache->getSystemSettings();

            if(!$settings['email_verified']) {
                ResponseService::validationError('Please contact the administrator to activate the email verification.');
            }
            
            DB::beginTransaction();

            $school_data = array(
                'name'          => $request->school_name,
                'address'       => $request->school_address,
                'support_email' => $request->school_support_email,
                'support_phone' => $request->school_support_phone,
                'tagline'       => $request->school_tagline,
                'logo'          => $request->file('school_image'),
                'domain'        => $request->domain,
                'code'          => $school_code,
                'type'          => "custom",
                'domain_type'   => $request->domain_type,
            );
            // Call store function of Schools Repository
            $schoolData = $this->schoolsRepository->create($school_data);
            

            $school_name = str_replace('.','_',$request->school_name);
            $database_name = 'eschool_saas_'.$schoolData->id.'_'.strtolower(strtok($school_name," "));

            $admin_data = array(
                'first_name' => "School",
                'last_name'  => "Admin",
                'mobile'     => $request->school_support_phone,
                'email'      => $request->school_support_email,
                'password'   => Hash::make($request->school_support_phone),
                'school_id'  => $schoolData->id,
                'image'      => $request->file('school_image')
            );


            //Call store function of User Repository and get the admin data
            $user = $this->userRepository->create($admin_data);


            $schoolDataArray = [];

            $extraFields = $request->extra_fields;

            $extraDetails = array();
           
            if (isset($extraFields) && is_array($extraFields)) {
                foreach ($extraFields as $fields) {
                    $data = null;
                    
                    if (isset($fields['data'])) {
                        // If the data is an array, JSON encode it
                        if (is_array($fields['data'])) {
                            try {
                                $data = json_encode($fields['data'], JSON_THROW_ON_ERROR);
                            } catch (\JsonException $e) {
                                // Handle JSON encoding error if needed
                                $data = null;
                            }
                        } else {
                            
                            $data = $fields['data'];
                        }
                    }
            
                    if (isset($fields['data']) && $fields['data'] instanceof UploadedFile) {
                        $image = UploadService::upload($fields['data'], 'school');
                        $data = $image;
                    }
            
                    // Now add the data to the array
                    $extraDetails[] = array(
                        'school_id'         => $schoolData->id,
                        'school_inquiry_id' => null,
                        'form_field_id'     => $fields['form_field_id'],
                        'data'              => $data,
                    );
                }
            }

            
            if (!empty($extraDetails)) {
                $this->extraSchoolData->createBulk($extraDetails);
            }

            // Update Admin id to School Data
            $schoolData = $this->schoolsRepository->update($schoolData->id, ['admin_id' => $user->id,'database_name' => $database_name]);

            $schoolService = app(SchoolDataService::class);
            
            DB::statement("CREATE DATABASE {$database_name}");

            $schoolService->createDatabaseMigration($schoolData);

            // Add Pre School Settings By Default
            $schoolService->preSettingsSetup($schoolData);

            // Assign package
            if ($request->assign_package) {
                // Create subscription plan
                $this->subscriptionService->createSubscription($request->assign_package, $schoolData->id, null, 1);
                $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'),$schoolData->id);

            }

            // Update school code prefix
            if (($settings['school_prefix'] ?? '') != $request->school_code_prefix) {
                $settingsData[] = [
                    "name" => 'school_prefix',
                    "data" => $request->school_code_prefix,
                    "type" => "text"
                ];
                $this->systemSettings->upsert($settingsData, ["name"], ["data"]);
                $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.SETTINGS'));
            }

            DB::commit();
            $email_body = $this->replacePlaceholders($request, $user, $settings, $school_code);
            
            $data = [
                'subject'     => 'Welcome to ' . $settings['system_name'] ?? 'eSchool Saas',
                'email'       => $request->school_support_email,
                'email_body'  => $email_body
            ];

            Mail::send('schools.email', $data, static function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
            });

            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            ResponseService::successResponse('Data Stored Successfully');

        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("School Registered successfully. But Email not sent.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "School Controller -> Store method");
                ResponseService::errorResponse();
            }

        }
    }

    private function replacePlaceholders($request, $user, $settings, $school_code)
    {
        $templateContent = $settings['email_template_school_registration'] ?? '';
        // Define the placeholders and their replacements
        $placeholders = [
            '{school_admin_name}' => $user->full_name,
            '{code}' => $school_code,
            '{email}' => $user->email,
            '{password}' => $user->mobile,
            '{school_name}' => $request->school_name,

            '{super_admin_name}' => $settings['super_admin_name'] ?? 'Super Admin',
            '{support_email}' => $settings['mail_username'] ?? '',
            '{contact}' => $settings['mobile'] ?? '',
            '{system_name}' => $settings['system_name'] ?? 'eSchool Saas',
            '{url}' => url('/'),
            // Add more placeholders as needed
        ];

        // Replace the placeholders in the template content
        foreach ($placeholders as $placeholder => $replacement) {
            $templateContent = str_replace($placeholder, $replacement, $templateContent);
        }

        return $templateContent;
    }

    public function show() {
        ResponseService::noPermissionThenRedirect('schools-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');
        $package_id = request('package_id');
        $showDeleted = request('show_deleted');
        $today_date = Carbon::now()->format('Y-m-d');

        $sql = $this->schoolsRepository->builder()->with('user:id,first_name,last_name,email,image,mobile,email_verified_at,two_factor_enabled')->with(['subscription' => function($q) use($today_date){
            $q->whereDate('start_date','<=',$today_date)->whereDate('end_date','>=',$today_date);
        }])->with('subscription.package')
            //search query
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%")
                            ->orWhere('support_email', 'LIKE', "%$search%")
                            ->orWhere('support_phone', 'LIKE', "%$search%")
                            ->orWhere('tagline', 'LIKE', "%$search%")
                            ->orWhere('address', 'LIKE', "%$search%")
                            ->orWhere('domain', 'LIKE', "%$search%")
                            ->orWhere('code', 'LIKE', "%$search%")
                            ->orWhereHas('user', function ($query) use ($search) {
                                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", "%$search%");
                            });
                    });
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%")
                            ->orWhere('support_email', 'LIKE', "%$search%")
                            ->orWhere('support_phone', 'LIKE', "%$search%")
                            ->orWhere('tagline', 'LIKE', "%$search%")
                            ->orWhere('address', 'LIKE', "%$search%")
                            ->orWhere('domain', 'LIKE', "%$search%")
                            ->orWhere('code', 'LIKE', "%$search%")
                            ->orWhereHas('user', function ($query) use ($search) {
                                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", "%$search%");
                            });
                    });
                });
            })->when(!empty($showDeleted), function ($query) {
                $query->onlyTrashed();
            });

        if ($package_id) {
            $sql->whereHas('subscription',function($q) use($package_id, $today_date) {
                $q->where('package_id',$package_id)->whereDate('start_date','<=',$today_date)->whereDate('end_date','>=',$today_date);
            });
        }


        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        
        $baseUrl = url('/');
        $baseUrlWithoutScheme = preg_replace("(^https?://)", "", $baseUrl);
        $baseUrlWithoutScheme = str_replace("www.", "", $baseUrlWithoutScheme);

        foreach ($res as $row) {
            $operate = '';
            if ($showDeleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::menuRestoreButton('restore',route('schools.restore', $row->id));
                $operate .= BootstrapTableService::menuTrashButton('delete',route('schools.trash', $row->id));
            } else {
                $operate = BootstrapTableService::menuButton('change_admin',"#",['update-admin-data'],['data-toggle' => "modal", 'data-target' => "#editAdminModal"]);

                if ($row->status == 0) {
                    $operate .= BootstrapTableService::menuButton('activate_school',"#",["change-school-status"],['data-id' => $row->id]);
                } else {
                    $operate .= BootstrapTableService::menuButton('inactive_school',"#",["change-school-status"],['data-id' => $row->id]);
                }
                $operate .= BootstrapTableService::menuEditButton('edit',route('schools.update', $row->id));
                $operate .= BootstrapTableService::menuDeleteButton('delete',route('schools.destroy', $row->id));
            }


            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['active_plan'] = '-';
            
            // Generate school URL based on domain type
            if ($row->domain_type == 'default') {
                $tempRow['school_domain'] = $row->domain;
                $tempRow['school_url'] = 'https://' . $row->domain . '.' . $baseUrlWithoutScheme;
            } else {
                $tempRow['school_domain'] = $row->domain;
                $tempRow['school_url'] = 'https://' . $row->domain;
            }
            
            if (count($row->subscription)) {
                $package = $row->subscription()->whereDate('start_date','<=',$today_date)->whereDate('end_date','>=',$today_date)->latest()->first();
                if ($package) {
                    $tempRow['active_plan'] = $package->name;
                }
            } else {
                $tempRow['active_plan'] = '-';
            }
            
            $tempRow['extra_fields'] = $row->extra_school_details;
            foreach ($row->extra_school_details as $key => $field) {
                $data = '';
                if ($field->form_field->type == 'checkbox') {
                    $data = json_decode($field->data);
                } else if($field->form_field->type == 'file') {
                    $data = '<a href="'.Storage::url($field->data).'" target="_blank">DOC</a>';
                } else if($field->form_field->type == 'dropdown') {
                    $data = $field->form_field->default_values;
                    $data = $field->data ?? '';
                } else {
                    $data = $field->data;
                }
                $tempRow[$field->form_field->name] = $data;
            }

            // $tempRow['operate'] = $operate;
            $tempRow['operate'] = BootstrapTableService::menuItem($operate);
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson(['schools-edit']);
        $validator = Validator::make($request->all(), [
            'edit_school_name'          => 'required',
            'edit_school_support_email' => 'required|unique:schools,support_email,' . $id,
            'edit_school_support_phone' => 'required|numeric|digits_between:1,16',
            'edit_school_tagline'       => 'required',
            'edit_school_address'       => 'required',
            'edit_school_image'         => 'nullable|mimes:jpg,jpeg,png,svg,svg+xml|max:2048',
            'edit_domain'               => 'nullable|unique:schools,domain,'.$id
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $school_database = School::where('id',$id)->pluck('database_name')->first();
          
            $school_data = array(
                'name'          => $request->edit_school_name,
                'address'       => $request->edit_school_address,
                'support_email' => $request->edit_school_support_email,
                'support_phone' => $request->edit_school_support_phone,
                'tagline'       => $request->edit_school_tagline,
                'domain_type'   => $request->edit_domain_type,
                'domain'        => $request->edit_domain
            );

            if ($request->hasFile('edit_school_image')) {
                $school_data['logo'] = $request->file('edit_school_image');
            }

            $school = $this->schoolsRepository->update($request->edit_id, $school_data); // Call update function of Schools Repository
            // Update school settings

            $schoolDataArray = [];

            $extraFields = $request->edit_extra_fields;

            if (isset($extraFields) && is_array($extraFields))
            {
                foreach ($extraFields as $fields) {
                    if ($fields['input_type'] == 'file') {
                        if (isset($fields['data']) && $fields['data'] instanceof UploadedFile) {
                        
                            $image = UploadService::upload($fields['data'], 'school');                      
                            $schoolDataArray[] = array(
                                'id'                => $fields['id'] ?? null, // Handle nullable 'id'
                                'school_inquiry_id' => null,
                                'school_id'         => $school->id,
                                'form_field_id'     => $fields['form_field_id'],
                                'data'              => $image, // Store the filename or handle upload
                            );
                        }
                    } else {
                        $data = null;
                
                        // Ensure 'data' is properly formatted
                        if (isset($fields['data'])) {
                            $data = is_array($fields['data'])
                                ? json_encode($fields['data'], JSON_THROW_ON_ERROR) // Convert arrays to JSON
                                : $fields['data'];
                        }
                
                        $schoolDataArray[] = array(
                            'id'                => $fields['id'] ?? null, // Handle nullable 'id'
                            'school_inquiry_id' => null,
                            'school_id'         => $school->id,
                            'form_field_id'     => $fields['form_field_id'],
                            'data'              => $data, // Ensure data is a string or JSON
                        );
                    }
                }
                $this->extraSchoolData->upsert($schoolDataArray, ['id'], ['data', 'updated_at']);
               
            }
          
            DB::setDefaultConnection('school');
            Config::set('database.connections.school.database', $school_database);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            $schoolSettingData = array(
                [
                    'name'      => 'school_name',
                    'data'      => $request->edit_school_name,
                    'type'      => 'string',
                    'school_id' => $request->edit_id,
                ],
                [
                    'name'      => 'school_email',
                    'data'      => $request->edit_school_support_email,
                    'type'      => 'string',
                    'school_id' => $request->edit_id
                ],
                [
                    'name'      => 'school_phone',
                    'data'      => $request->edit_school_support_phone,
                    'type'      => 'number',
                    'school_id' => $request->edit_id
                ],
                [
                    'name'      => 'school_tagline',
                    'data'      => $request->edit_school_tagline,
                    'type'      => 'string',
                    'school_id' => $request->edit_id
                ],
                [
                    'name'      => 'school_address',
                    'data'      => $request->edit_school_address,
                    'type'      => 'string',
                    'school_id' => $request->edit_id
                ],
                [
                    'name'      => 'domain',
                    'data'      => $request->edit_domain,
                    'type'      => 'string',
                    'school_id' => $request->edit_id
                ]);

                if ($request->hasFile('edit_school_image')) {
                    $schoolSettingData[] = [
                        'name'      => 'vertical_logo',
                        'data'      => $request->file('edit_school_image')->store('school','public'),
                        'type'      => 'file',
                        'school_id' => $request->edit_id
                    ];
                }
                SchoolSetting::upsert($schoolSettingData,['name','school_id'],['data','school_id','type']);
                $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'),$request->edit_id);
                
                DB::setDefaultConnection('mysql');
                Session::forget('school_database_name');
                Session::flush();
                Session::put('school_database_name', null);

                // Assign package
                if ($request->assign_package) {
                    // Create subscription plan
                    $this->subscriptionService->createSubscription($request->assign_package, $request->edit_id, null, 1);
                    $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'),$request->edit_id);

                }

            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "School Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('schools-delete');
        try {
            $school = $this->schoolsRepository->update($id,['status' => 0]);
            User::withTrashed()->where('id',$school->admin_id)->delete();
            $this->schoolsRepository->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "School Controller -> Delete method");
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('schools-delete');
        try {
            $this->schoolsRepository->findOnlyTrashedById($id)->restore();
            $school = $this->schoolsRepository->findById($id);
            User::onlyTrashed()->where('id', $school->admin_id)->restore();

            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('schools-delete');
        try {
            $school = $this->schoolsRepository->builder()->withTrashed()->where('id',$id)->first();
            DB::statement("DROP DATABASE IF EXISTS `{$school->database_name}`");
            Storage::disk('public')->deleteDirectory($school->id);
            User::where('id',$school->admin_id)->withTrashed()->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e,'','cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function adminSearch(Request $request) {
        $adminData = $this->userRepository->getTrashedAdminData($request->email);
        if (!empty($adminData)) {
            $response = ['error' => false, 'data' => $adminData];
        } else {
            $response = ['error' => true, 'message' => trans('no_data_found')];
        }
        return response()->json($response);
    }

    public function updateAdmin(Request $request) {
        ResponseService::noAnyPermissionThenRedirect(['schools-edit']);
        $validator = Validator::make($request->all(), [
            "edit_id"               => 'required',
            "edit_admin_email"      => 'required|email|unique:users,email,' . $request->edit_admin_id,
            "edit_admin_first_name" => 'required',
            "edit_admin_last_name"  => 'required',
            "edit_admin_contact"    => 'required|digits_between:1,16',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();

            // If Email is not the ID then Check the following requirements
            // if (!is_numeric($request->edit_admin_email)) {
            //     $validator = Validator::make($request->all(), [
            //         "edit_admin_email" => 'required|email|unique:users,email',
            //     ],
            //         [
            //             "edit_admin_email.required" => trans('email_is_required'),
            //             "edit_admin_email.email"    => trans('enter_valid_email'),
            //             "edit_admin_email.unique"   => trans('email_already_in_use'),
            //         ]);
            //     if ($validator->fails()) {
            //         ResponseService::validationError($validator->errors()->first());
            //     }
            // }

            $admin_data = array(
                'school_id'  => $request->edit_id,
                'id'         => $request->edit_admin_id,
                'email'      => $request->edit_admin_email,
                'first_name' => $request->edit_admin_first_name,
                'last_name'  => $request->edit_admin_last_name,
                'contact'    => $request->edit_admin_contact,
                'reset_password'    => $request->reset_password,
            );
            $this->schoolsRepository->updateSchoolAdmin($admin_data, $request->edit_admin_image); // Call updateSchoolAdmin function of Schools Repository

            // Re-send Email by Super Admin
            if($request->resend_email) {
                $settings = $this->cache->getSystemSettings();
                $users = $this->schoolsRepository->builder()->with("user")->where('id',$request->edit_id)->first();

                $email_body = $this->replacePlaceholders($request, $users->user, $settings, $users->code);
            
                $data = [
                    'subject'     => 'Welcome to ' . $settings['system_name'] ?? 'eSchool Saas',
                    'email'       => $request->edit_admin_email,
                    'email_body'  => $email_body
                    ];

                Mail::send('schools.email', $data, static function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['subject']);
                });

                if (!$users->user->hasVerifiedEmail()) {
                    $users->user->sendEmailVerificationNotification();
                }
                
                ResponseService::successResponse('School Admin Re-send Email Successfully');
            }
            
            // Mark the School Admin Email as Verified
            if ($request->manually_verify_email) {
                
                $users = $this->schoolsRepository->builder()->with("user")->where('id', $request->edit_id)->first();

                // set school admin email as verified in Super Admin
                if (!$users->user->hasVerifiedEmail()) {
                    
                    DB::connection('mysql')->reconnect();
                    DB::setDefaultConnection('mysql');
                    DB::connection('mysql')->table('users')->where('id', $users->user->id)->update(['email_verified_at' => Carbon::now()]);
                    DB::commit();
                }
            
                // set school admin email as verified in School Admin
                $schoolCode = $users->code;
                
                if ($schoolCode) {
                    $school = School::on('mysql')->where('code', $schoolCode)->first();
            
                    if ($school) {
                        DB::setDefaultConnection('school');
                        Config::set('database.connections.school.database', $school->database_name);
                        DB::purge('school');
                        DB::connection('school')->reconnect();

                        DB::connection('school')->table('users')->where('id', $users->user->id)->update(['email_verified_at' => Carbon::now()]);

                        DB::commit();
                    }
                } else {
                    // Return a response if the school code is missing
                    return response()->json(['message' => 'Unauthenticated'], 400);
                }
            
                // Send a success response after the update
                ResponseService::successResponse('School Admin Email Manually Verified Successfully');
            }
            
            // Update Two Factor Authentication

            $users = $this->schoolsRepository->builder()->with("user")->where('id', $request->edit_id)->first();
            
            if ($request->two_factor_verification != $users->user->two_factor_enabled) {
              
                if ($users->code) {
                    $school = School::on('mysql')->where('code', $users->code)->first();
                    if ($school) {
                        
                        // Super Admin Database Connection
                        DB::setDefaultConnection('mysql');
                        DB::connection('mysql')->reconnect();
                        DB::setDefaultConnection('mysql');
                        DB::connection('mysql')->table('users')->where('id', $users->user->id)->update(['two_factor_enabled' => $request->two_factor_verification]);
                        DB::commit();

                        // School Admin Database Connection
                        DB::setDefaultConnection('school');
                        Config::set('database.connections.school.database', $school->database_name);
                        DB::purge('school');
                        DB::connection('school')->reconnect();

                        DB::connection('school')->table('users')->where('id', $users->user->id)->update(['two_factor_enabled' => $request->two_factor_verification]);

                        DB::commit();
                        
                        ResponseService::successResponse('Two Factor Authentication Updated Successfully');
                    }
                } else {
                    // Return a response if the school code is missing
                    return response()->json(['message' => 'Unauthenticated'], 400);
                }
            }
            
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Data Updated successfully. But Email not sent.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "School Controller -> Update Admin method");
                ResponseService::errorResponse();
            }
        }
    }

    public function changeStatus($id) {
        ResponseService::noAnyPermissionThenRedirect(['schools-edit']);
        try {
            DB::beginTransaction();
            $school = $this->schoolsRepository->findById($id);
            $status = ['status' => $school->status == 0 ? 1 : 0];
            $this->schoolsRepository->update($id, $status);
            DB::commit();
            DB::setDefaultConnection('school');
            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            DB::beginTransaction();
            $school = $this->schoolsRepository->findById($id);
            $this->schoolsRepository->update($id, $status);

            DB::commit();
            ResponseService::successResponse('Data updated successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "School Controller -> Change Status");
            ResponseService::errorResponse();
        }
    }

    public function searchAdmin(Request $request) {
        ResponseService::noAnyPermissionThenRedirect(['schools-create', 'schools-edit']);
        $parent = $this->userRepository->builder()->role('School Admin')->withTrashed()->where(function ($query) use ($request) {
            $query->where('email', 'like', '%' . $request->email . '%')
                ->orWhere('first_name', 'like', '%' . $request->email . '%')
                ->orWhere('last_name', 'like', '%' . $request->email . '%');
        })->get();

        if (!empty($parent)) {
            $response = [
                'error' => false,
                'data'  => $parent
            ];
        } else {
            $response = [
                'error'   => true,
                'message' => trans('no_data_found')
            ];
        }
        return response()->json($response);
    }

    public function registration(Request $request) {
        $validator = Validator::make($request->all(), [
            'school_name'          => 'required',
            'school_email'         => 'required',
            'school_phone'         => 'required|numeric|digits_between:1,16',
            'school_tagline'       => 'required',
            'school_address'       => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

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

        $school = $this->schoolsRepository->builder()->where('support_email', $request->school_email)->withTrashed()->first();
        $user = $this->userRepository->builder()->where('email', $request->school_email)->withTrashed()->first();

        $schoolInquiry = $this->schoolInquiry->builder()->where('school_email', $request->school_email)->first();

        if ($school || $user || $schoolInquiry) {
            ResponseService::errorResponse(trans('School or User email already exists'));
        }

        try {
            $settings = $this->cache->getSystemSettings();

            if(isset($settings['school_inquiry']) && ($settings['school_inquiry'] == 1) )
            {
                DB::beginTransaction();

                $school_data = array(
                    'school_name'       => $request->school_name,        
                    'school_email'      => $request->school_email,
                    'school_phone'      => $request->school_phone,
                    'school_tagline'    => $request->school_tagline,      
                    'school_address'    => $request->school_address,      
                    'date'              => Carbon::now()->format('Y-m-d'), 
                    'status'            => 0,
                );
            
                $schoolData = $this->schoolInquiry->create($school_data);
              
                $schoolDataArray = [];
    
                $extraFields = $request->extra_fields;
    
                $extraDetails = array();
           
                if (isset($request->extra_fields) && is_array($request->extra_fields)) {
                    foreach ($request->extra_fields as $fields) {
                        $data = null;
                        
                        if (isset($fields['data'])) {
                            // If the data is an array, JSON encode it
                            if (is_array($fields['data'])) {
                                try {
                                    $data = json_encode($fields['data'], JSON_THROW_ON_ERROR);
                                } catch (\JsonException $e) {
                                    // Handle JSON encoding error if needed
                                    $data = null;
                                }
                            } else {
                                
                                $data = $fields['data'];
                            }
                        }
                
                        if (isset($fields['data']) && $fields['data'] instanceof UploadedFile) {
                            $image = UploadService::upload($fields['data'], 'school');
                            $data = $image;
                        }
                
                        // Now add the data to the array
                        $extraDetails[] = array(
                            'school_inquiry_id' => $schoolData->id,
                            'school_id'         => null,
                            'form_field_id'     => $fields['form_field_id'],
                            'data'              => $data,
                        );
                    }
                }

                
                if (!empty($extraDetails)) {
                    $this->extraSchoolData->createBulk($extraDetails);
                }

                DB::commit();
        
                ResponseService::successResponse(trans('School Inquiry Sent to Admin, wait for Admin Approval to successfully registered.'));

            }else{
                DB::beginTransaction();
                $schools = $this->schoolsRepository->builder()->latest()->first();
                $school_code = date('Y').(($schools->id ?? 0) + 1);
                $settings = $this->cache->getSystemSettings();
                $prefix = $settings['school_code_prefix'] ?? 'SCH';
                $school_code = $prefix . $school_code;
                $school_data = array(
                    'name'          => $request->school_name,
                    'address'       => $request->school_address,
                    'support_email' => $request->school_email,
                    'support_phone' => $request->school_phone,
                    'tagline'       => $request->school_tagline,
                    'logo'          => 'no_image_available.jpg',
                    'status'        => 1,
                    'code'          => $school_code
                );

                if($settings['email_verified'] == 0) {
                    ResponseService::errorResponse(trans('Please contact the super admin to configure the email.'));
                }

                // Call store function of Schools Repository
                $schoolData = $this->schoolsRepository->create($school_data);
                $admin_data = array(
                    'first_name' => 'School',
                    'last_name'  => 'Admin',
                    'mobile'     => $request->school_phone,
                    'email'      => $request->school_email,
                    'password'   => Hash::make($request->school_phone),
                    'school_id'  => $schoolData->id,
                    'image'      => 'dummy_logo.jpg'
                );
               
                $user = $this->userRepository->create($admin_data);
               
                $schoolDataArray = [];

                $extraFields = $request->extra_fields;
                $extraDetails = [];
                if (isset($request->extra_fields) && is_array($request->extra_fields)) {
                    foreach ($request->extra_fields as $fields) {
                        $data = null;
                        
                        if (isset($fields['data'])) {
                            // If the data is an array, JSON encode it
                            if (is_array($fields['data'])) {
                                try {
                                    $data = json_encode($fields['data'], JSON_THROW_ON_ERROR);
                                } catch (\JsonException $e) {
                                    // Handle JSON encoding error if needed
                                    $data = null;
                                }
                            } else {
                                
                                $data = $fields['data'];
                            }
                        }
                
                        if (isset($fields['data']) && $fields['data'] instanceof UploadedFile) {
                            // If the data is an uploaded file, store it as the file's path or name
                            $data = $fields['data']->getClientOriginalName(); // or you can save the file and store its path
                        }
                
                        // Now add the data to the array
                        $extraDetails[] = array(
                            'school_inquiry_id' => null,
                            'school_id'         => $schoolData->id,
                            'form_field_id'     => $fields['form_field_id'],
                            'data'              => $data,
                        );
                    }
                }

                
                if (!empty($extraDetails)) {
                    $this->extraSchoolData->createBulk($extraDetails);
                }

                $school_name = str_replace('.','_',$request->school_name);
                $database_name = 'eschool_saas_'.$schoolData->id.'_'.strtolower(strtok($school_name," "));
              
                $schoolData = $this->schoolsRepository->update($schoolData->id, ['admin_id' => $user->id, 'database_name' => $database_name]);
                $schoolService = app(SchoolDataService::class);
               
                DB::statement("CREATE DATABASE {$database_name}");
                $schoolService->createDatabaseMigration($schoolData);
                $schoolService->preSettingsSetup($schoolData);
               
                if ($request->trial_package) {
                   
                    $this->subscriptionService->createSubscription($request->trial_package, $schoolData->id, null, 1);
                    $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'),$schoolData->id);
                }
               
                $settings = $this->cache->getSystemSettings();
                $email_body = $this->replacePlaceholders($request, $user, $settings, $school_code);
                $data = [
                    'subject'     => 'Welcome to ' . $settings['system_name'] ?? 'eSchool Saas',
                    'email'       => $request->school_email,
                    'email_body'  => $email_body
                ];

                Mail::send('schools.email', $data, static function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['subject']);
                });
                if (!$user->hasVerifiedEmail()) {
                    $user->sendEmailVerificationNotification();
                }
                ResponseService::successResponse(trans('School Registration Successful'));

            }

        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("School Registration successfully. But Email not sent.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "School Controller -> Registration method");
                ResponseService::errorResponse();
            }
        }
    }

    public function sendMailIndex()
    {
        ResponseService::noAnyPermissionThenRedirect(['custom-school-email']);
        $schools = $this->schoolsRepository->builder()->pluck('name','id');


        return view('settings.custom_email',compact('schools'));

    }

    public function sendMail(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['custom-school-email']);
        
        $request->validate([
            'subject' => 'required',
            'school_id' => 'required',
            'description' => 'required'
        ]);

        try {
            
            foreach ($request->school_id as $key => $school_id) {
                $this->sendCustomEmail($school_id, $request->description, $request->subject);
            }

            ResponseService::successResponse(trans('Email send Successfully'));
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th, "School Controller -> Send mail method");
            ResponseService::errorResponse();
        }
    }

    public function sendCustomEmail($school_id, $data, $subject)
    {
        try {
            $school = $this->schoolsRepository->builder()->where('id',$school_id)->with('user')->first();
            $systemSettings = $this->cache->getSystemSettings();
            $placeholders = [
                '{school_name}' => $school->name,
                '{school_admin_name}' => $school->user->full_name,
                '{school_email}' => $school->support_email,
                '{school_admin_email}' => $school->user->email,
                '{code}' => $school->code,
                '{school_admin_mobile}' => $school->user->mobile,
                '{system_name}' => $systemSettings['system_name'] ?? 'eSchool-SaaS',
                '{support_email}' => $systemSettings['mail_send_from'] ?? 'example@gmail.com',
                '{support_contact}' => $systemSettings['mobile'] ?? '[+xx xxxxxxxxxx]',
                '{website}' => url('/'),
            ];

            foreach ($placeholders as $placeholder => $replacement) {
                $data = str_replace($placeholder, $replacement, $data);
            }

            $emailBody = [
                'subject'     => $subject,
                'email'       => $school->user->email,
                'email_body'  => $data
            ];

            Mail::send('students.email', $emailBody, static function ($message) use ($emailBody) {
                $message->to($emailBody['email'])->subject($emailBody['subject']);
            });
        } catch (\Throwable $th) {
            if (Str::contains($th->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Message send successfully. But Email not sent.");
            } else {
                ResponseService::errorResponse(trans('error_occur'));
            }
        }
        
    }

    public function createDemoSchool()
    {
        try {
            DB::beginTransaction();

            $schools = $this->schoolsRepository->builder()->latest()->first();        
            $school_code = date('Y').(($schools->id ?? 0) + 1);
            $settings = $this->cache->getSystemSettings();
            $prefix = $settings['school_prefix'] ?? 'SCH';
            $school_code = $prefix . $school_code;

            $school_data = array(
                'name'          => 'Demo School',
                'address'       => '123 Demo Street',
                'support_email' => 'demo@school.com',
                'support_phone' => '1234567890',
                'tagline'       => 'Demo Tagline',
                'domain'        => 'demo',
                'code'          =>  $school_code,
                'type'          => 'demo',
                'domain_type'   => 'default',
                'status'        => 1,
            );
            // Call store function of Schools Repository
            $schoolData = $this->schoolsRepository->create($school_data);

            $admin_data = array(
                'first_name' => 'Demo',
                'last_name'  => 'Admin',
                'mobile'     => '1234567890',
                'email'      => 'demo@school.com',
                'password'   => Hash::make('1234567890'),
                'school_id'  => $schoolData->id,
                'image'      => 'dummy_logo.jpg',
                'email_verified_at' => $schoolData->type == 'demo' ? Carbon::now() : null,
                'two_factor_enabled' => 0,
            );

            //Call store function of User Repository and get the admin data
            $user = $this->userRepository->create($admin_data);
            // $user->assignRole('School Admin');

            $school_name = str_replace('.','_',$schoolData->name);
            $database_name = 'eschool_saas_'.$schoolData->id.'_'.strtolower(strtok($school_name," "));

            // Update Admin id to School Data
            $schoolData = $this->schoolsRepository->update($schoolData->id, ['admin_id' => $user->id, 'database_name' => $database_name]);

            $schoolService = app(SchoolDataService::class);
            // Add Pre School Settings By Default

            DB::statement("CREATE DATABASE {$database_name}");

            $schoolService->createDatabaseMigration($schoolData);

            $schoolService->preSettingsSetup($schoolData);

            ResponseService::successResponse(trans('School Registration Successful'));

        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::successResponse(trans('School Registration Successfully. But Email not sent'));
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "School Controller -> Registration method");
                ResponseService::errorResponse();
            }
        }

    }

    public function schoolInquiryIndex() {
        ResponseService::noPermissionThenRedirect('schools-list');
        
        $baseUrl = url('/');
        // Remove the scheme (http:// or https://)
        $baseUrlWithoutScheme = preg_replace("(^https?://)", "", $baseUrl);
        $baseUrlWithoutScheme = str_replace("www.", "", $baseUrlWithoutScheme);

        $schools = $this->schoolsRepository->builder()->latest()->first();
        $school_code = date('Y').(($schools->id ?? 0) + 1);
        $settings = $this->cache->getSystemSettings();
        $prefix = $settings['school_prefix'] ?? 'SCH';
        $extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();

        return view('schools.school_inquiry', compact('baseUrlWithoutScheme','school_code','prefix','extraFields'));
    }

    public function schoolInquiryList() {
        ResponseService::noPermissionThenRedirect('schools-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');
        $status = request('status');
        $date = request('date');

        $sql = $this->schoolInquiry->builder()->with('extra_school_details')->where(function ($query) use ($search) {
            $query->where('school_name', 'LIKE', "%$search%")
                ->orWhere('school_address', 'LIKE', "%$search%")
                ->orWhere('school_phone', 'LIKE', "%$search%")
                ->orWhere('school_email', 'LIKE', "%$search%")   
                ->orWhere('school_tagline', 'LIKE', "%$search%")
                ->orWhere('date', 'LIKE', "%$search%")
                ->orWhere('status', 'LIKE', "%$search%");
        });

        if ($status == '0' || $status == '2') { // 0 = Pending, 2 = Reject
            $sql->where('status', $status);
        }

        if ($date) {
            $dateRange = explode(' - ', $date);
            $startDate = Carbon::parse($dateRange[0])->startOfDay();
            $endDate = Carbon::parse($dateRange[1])->endOfDay();
            
            $sql->whereBetween('date', [$startDate, $endDate]);
        }
        
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        // dd($res->toArray());
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            $operate = BootstrapTableService::viewRelatedDataButton(route('schools.update', $row->id));
            $operate .= BootstrapTableService::deleteButton(route('school-inquiry.delete', $row->id));

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['active_plan'] = '-';

            $tempRow['extra_fields'] = $row->extra_school_details;
            foreach ($row->extra_school_details as $key => $field) {
                $data = '';
                if ($field->form_field->type == 'checkbox') {
                    $data = json_decode($field->data);
                } else if($field->form_field->type == 'file') {
                    $data = '<a href="'.Storage::url($field->data).'" target="_blank">DOC</a>';
                } else if($field->form_field->type == 'dropdown') {
                    $data = $field->form_field->default_values;
                    $data = $field->data ?? '';
                } else {
                    $data = $field->data;
                }
                $tempRow[$field->form_field->name] = $data;
            }

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function schoolInquiryUpdate(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['schools-create']);
        $validator = Validator::make($request->all(), [
            'school_name'          => 'required',
            'school_support_email' => 'required|unique:schools,support_email',
            'school_support_phone' => 'required|numeric|digits_between:1,16',
            'school_tagline'       => 'required',
            'school_address'       => 'required',
            'domain'               => 'nullable|unique:schools,domain',
            'school_code_prefix'   => 'required'

        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $settings = $this->cache->getSystemSettings();
            $schoolService = app(SchoolDataService::class);

            if($settings['email_verified'] == 0) {
                ResponseService::errorResponse(trans('Kindly first configure the email, then the school will be approved.'));
            }

            if($request->status == 1)
            {
                $school_code = $request->school_code_prefix . $request->school_code;
                // dd($school_code);
                DB::beginTransaction();

                $school_data = array(
                    'name'          => $request->school_name,
                    'address'       => $request->school_address,
                    'support_email' => $request->school_support_email,
                    'support_phone' => $request->school_support_phone,
                    'tagline'       => $request->school_tagline,
                    'domain'        => "demo",
                    'code'          => $school_code,
                    'type'          => "custom",
                    'domain_type'   => "default",
                );
                // Call store function of Schools Repository
                $schoolData = $this->schoolsRepository->create($school_data);
                

                $school_name = str_replace('.','_',$request->school_name);
                $database_name = 'eschool_saas_'.$schoolData->id.'_'.strtolower(strtok($school_name," "));

                $admin_data = array(
                    'first_name' => 'School',
                    'last_name'  => 'Admin',
                    'mobile'     => $request->school_support_phone,
                    'email'      => $request->school_support_email,
                    'password'   => Hash::make($request->school_support_phone),
                    'school_id'  => $schoolData->id,
                );


                //Call store function of User Repository and get the admin data
                $user = $this->userRepository->create($admin_data);

                // Update Admin id to School Data
                $schoolData = $this->schoolsRepository->update($schoolData->id, ['admin_id' => $user->id,'database_name' => $database_name]);

               
                $schoolDataArray = [];

                $extraFields = $request->extra_fields;

                
                if(isset($extraFields)){
                    foreach ($extraFields as $field) {
                        // Validate the required fields before saving
                        if (isset($field['form_field_id']) && isset($field['data'])) {
    
                            $schoolDataArray = array(
                                'school_inquiry_id' => null,
                                'school_id'         => $schoolData->id,
                                'form_field_id'     => $field['form_field_id'],
                                'data'              => $field['data']
                            );
        
                            $this->extraSchoolData->update($field['id'], $schoolDataArray);
                        
                        }
                    }
                }
              

                
                DB::statement("CREATE DATABASE {$database_name}");

                $schoolService->createDatabaseMigration($schoolData);

                // Add Pre School Settings By Default
                $schoolService->preSettingsSetup($schoolData);

                // Assign package
                if ($request->assign_package) {
                    // Create subscription plan
                    $this->subscriptionService->createSubscription($request->assign_package, $schoolData->id, null, 1);
                    $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SETTINGS'),$schoolData->id);

                }

                // Update school code prefix
                if (($settings['school_prefix'] ?? '') != $request->school_code_prefix) {
                    $settingsData[] = [
                        "name" => 'school_prefix',
                        "data" => $request->school_code_prefix,
                        "type" => "text"
                    ];
                    $this->systemSettings->upsert($settingsData, ["name"], ["data"]);
                    $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.SETTINGS'));
                }

                DB::commit();
                $email_body = $this->replacePlaceholders($request, $user, $settings, $school_code);
                
                $data = [
                    'subject'     => 'Welcome to ' . $settings['system_name'] ?? 'eSchool Saas',
                    'email'       => $request->school_support_email,
                    'email_body'  => $email_body
                ];

                Mail::send('schools.email', $data, static function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['subject']);
                });

                if (!$user->hasVerifiedEmail()) {
                    $user->sendEmailVerificationNotification();
                }
                 
                $schoolService->switchToMainDatabase();


                $schoolInquiry = $this->schoolInquiry->builder()->where('id', $request->edit_id)->delete();


                ResponseService::successResponse('School Registered Successfully');
            }elseif($request->status == 2){

                $email_body = $this->replaceEmailPlaceholders($request, $settings);
                
                $data = [
                    'subject'     => 'Welcome to ' . $settings['system_name'] ?? 'eSchool Saas',
                    'email'       => $request->school_support_email,
                    'email_body'  => $email_body
                ];

                Mail::send('schools.email', $data, static function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['subject']);
                });

                $schoolService->switchToMainDatabase();

                $this->schoolInquiry->update($request->edit_id, ['status' => 2]);

                ResponseService::successResponse('Email Sent Successfully');
            }else{
                ResponseService::successResponse('Data Stored Successfully');
            }
            


        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("School Registered successfully. But Email not sent.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "School Controller -> Store method");
                ResponseService::errorResponse();
            }

        }
    }

    private function replaceEmailPlaceholders($request, $settings)
    {
        $templateContent = $settings['school_reject_template'] ?? '';
        // Define the placeholders and their replacements
        $placeholders = [
            '{school_name}' => $request->school_name,
            '{super_admin_name}' => $settings['super_admin_name'] ?? 'Super Admin',
            '{support_email}' => $settings['mail_username'] ?? '',
            '{contact}' => $settings['mobile'] ?? '',
            '{system_name}' => $settings['system_name'] ?? 'eSchool Saas',
            '{url}' => url('/'),
        ];

        // Replace the placeholders in the template content
        foreach ($placeholders as $placeholder => $replacement) {
            $templateContent = str_replace($placeholder, $replacement, $templateContent);
        }

        return $templateContent;
    }

    public function schoolInquiryDelete($id)
    {
        ResponseService::noPermissionThenSendJson('schools-delete');
        try {
            $school_inquiry = $this->schoolInquiry->builder()->where('id',$id)->first();
            $school_inquiry->delete();
            ResponseService::successResponse("Data Deleted Successfully.");
        } catch (Throwable $e) {
            ResponseService::errorResponse();
        }
    }
}
