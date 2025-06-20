<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Repositories\School\SchoolInterface;
use App\Repositories\Staff\StaffInterface;
use App\Repositories\StaffSupportSchool\StaffSupportSchoolInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Repositories\User\UserInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\FeaturesService;
use App\Services\ResponseService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PDF;
use Throwable;
use App\Exports\StaffDataExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StaffImport;
use App\Repositories\ExtraFormField\ExtraFormFieldsInterface;
use App\Repositories\FormField\FormFieldsInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\PayrollSetting\PayrollSettingInterface;
use App\Repositories\StaffSalary\StaffSalaryInterface;
use App\Services\UserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class StaffController extends Controller {

    private UserInterface $user;
    private StaffInterface $staff;
    private SchoolInterface $school;
    private StaffSupportSchoolInterface $staffSupportSchool;
    private FeaturesService $features;
    private SubscriptionInterface $subscription;
    private CachingService $cache;
    private SubscriptionService $subscriptionService;
    private PayrollSettingInterface $payrollSetting;
    private StaffSalaryInterface $staffSalary;
    private FormFieldsInterface $formFields;
    private ExtraFormFieldsInterface $extraFormFields;

    public function __construct(UserInterface $user, StaffInterface $staff, SchoolInterface $school, StaffSupportSchoolInterface $staffSupportSchool, FeaturesService $features, SubscriptionInterface $subscription, CachingService $cache, SubscriptionService $subscriptionService, PayrollSettingInterface $payrollSetting, StaffSalaryInterface $staffSalary, FormFieldsInterface $formFields, ExtraFormFieldsInterface $extraFormFields) {
        $this->user = $user;
        $this->staff = $staff;
        $this->school = $school;
        $this->staffSupportSchool = $staffSupportSchool;
        $this->features = $features;
        $this->subscription = $subscription;
        $this->cache = $cache;
        $this->subscriptionService = $subscriptionService;
        $this->payrollSetting = $payrollSetting;
        $this->staffSalary = $staffSalary;
        $this->formFields = $formFields;
        $this->extraFormFields = $extraFormFields;

    }

    public function index() {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('staff-list');
        $roles = Role::where('custom_role', 1)->whereNot('name','Teacher')->get();
        $schools = array();
        if (!Auth::user()->school_id) {
            $schools = $this->school->active()->pluck('name', 'id');
        }
        $features = $this->features->getFeatures();
        // $features = array();

        $allowances = [];
        $deductions = [];

        if(Auth::user()->school_id) {
            $allowances = $this->payrollSetting->builder()->where('type', 'allowance')->get();
            $deductions = $this->payrollSetting->builder()->where('type', 'deduction')->get();
            $extraFields = $this->formFields->defaultModel()->where('user_type', 2)->orderBy('rank')->get();    
        } else {
            $extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();
        }
      
        return response(view('staff.index', compact('roles', 'schools', 'features', 'allowances', 'deductions', 'extraFields')));
    }

    public function store(Request $request) {
        // dd($request->all());
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('staff-create');

        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name'  => 'required',
                'mobile'     => 'required|digits_between:1,16',
                'email'      => 'required|unique:users,email',
                'role_id'    => 'required|numeric',
                'status'     => 'nullable|in:0,1',
                'dob'        => 'required',
            ]);
            if ($validator->fails()) {
                ResponseService::validationError($validator->errors()->first());
            }
            DB::beginTransaction();

            // Check free trial package
            if (Auth::user()->school_id) {
                $today_date = Carbon::now()->format('Y-m-d');
                $subscription = $this->subscription->builder()->doesntHave('subscription_bill')->whereDate('start_date', '<=', $today_date)->where('end_date', '>=', $today_date)->whereHas('package', function ($q) {
                    $q->where('is_trial', 1);
                })->first();

                if ($subscription) {
                    $systemSettings = $this->cache->getSystemSettings();
                    $staff = $this->user->builder()->role('Teacher')->withTrashed()->orWhereHas('roles', function ($q) {
                        $q->where('custom_role', 1)->whereNot('name', 'Teacher');
                    })->whereNotNull('school_id')->Owner()->count();
                    if ($staff >= $systemSettings['staff_limit']) {
                        $message = "The free trial allows only " . $systemSettings['staff_limit'] . " staff.";
                        ResponseService::errorResponse($message);
                    }
                }
            }

            // If prepaid plan check student limit
            $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
            if ($subscription && $subscription->package_type == 0) {
                $status = $this->subscriptionService->check_user_limit($subscription, "Staffs");
                
                if (!$status) {
                    ResponseService::errorResponse('You reach out limits');
                }
            }

            $role = Role::findOrFail($request->role_id);

            /*If Super admin creates the staff then make it active by default*/
            if (!empty(Auth::user()->school_id)) {
                $data = array(
                    ...$request->except('school_id'),
                    'password'   => Hash::make($request->mobile),
                    'image'      => $request->file('image'),
                    'status'     => $request->status ?? 0,
                    'deleted_at' => $request->status == 1 ? null : '1970-01-01 01:00:00'
                );
            } else {
                /*If School Admin creates the Staff then active/inactive staff based on status*/
                $data = array(
                    ...$request->except('school_id'),
                    'password' => Hash::make($request->mobile),
                    'image'    => $request->file('image'),
                    'status'   => 1,
                );
            }


            $user = $this->user->create($data);
            

            // Store Extra Details
            $extraDetails = array();
           
            if (isset($request->extra_fields) && is_array($request->extra_fields)) {
                foreach ($request->extra_fields as $fields) {
                    $data = null;
                    if (isset($fields['data'])) {
                        $data = (is_array($fields['data']) ? json_encode($fields['data'], JSON_THROW_ON_ERROR) : $fields['data']);
                    }
                    $extraDetails[] = array(
                        'user_id'       => $user->id,
                        'form_field_id' => $fields['form_field_id'],
                        'data'          => $data,
                    );
                }
            }

            if (!empty($extraDetails)) {
                $this->extraFormFields->createBulk($extraDetails);
            }

            $user->assignRole($role);
            if ($user->school_id) {
                $leave_permission = [
                    'leave-list',
                    'leave-create',
                    'leave-edit',
                    'leave-delete',
                ];
                $user->givePermissionTo($leave_permission);
            }

           $staff = $this->staff->create([
                'user_id'       => $user->id,
                'qualification' => null,
                'salary'        => $request->salary ?? 0,
                'joining_date' => date('Y-m-d',strtotime($request->joining_date))
            ]);


            if ($request->school_id) {
                $data = array();
                foreach ($request->school_id as $school) {
                    $data[] = [
                        'user_id'   => $user->id,
                        'school_id' => $school
                    ];
                }
                $this->staffSupportSchool->upsert($data, ['user_id', 'school_id'], ['user_id', 'school_id']);
            }
         
            $allowance_data = array();
            $allowance_status = 0;
            foreach ($request->allowance ?? [] as $allowance) 
            {
                if ($allowance['id']) {
                    $allowance_status = 1;
                    $allowance_data[] = [
                        'staff_id' => $staff->id,
                        'payroll_setting_id' =>  $allowance['id'],
                        'amount' => $allowance['amount'] ??  null,
                        'percentage' => $allowance['percentage'] ?? null
                    ];
                }
            }
            if ($allowance_status) {
                $this->staffSalary->upsert($allowance_data,['staff_id','payroll_setting_id'],['amount','percentage']);
            }

            $deduction_data = array();
            $deduction_status = 0;
            foreach ($request->deduction ?? [] as $deduction) {

                if ($deduction['id']) {
                    $deduction_status = 1;
                    $deduction_data[] = [
                        'staff_id' => $staff->id,
                        'payroll_setting_id' =>  $deduction['id'],
                        'amount' => $deduction['amount'] ??  null,
                        'percentage' => $deduction['percentage'] ?? null
                    ];
                }
            }
            if ($deduction_status) {
                $this->staffSalary->upsert($deduction_data,['staff_id','payroll_setting_id'],['amount','percentage']);
            }

            DB::commit();

            if ($user->school_id) {
                $sendEmail = app(UserService::class);
                $sendEmail->sendStaffRegistrationEmail($user, $user->mobile);
            }

            ResponseService::successResponse('Data Stored Successfully');

        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Staff Registered successfully. But Email not sent.");
            } else {
                DB::rollback();
                ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
            }
            
        }
    }

    public function show(Request $request) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('staff-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = $this->user->builder()->whereHas('roles', function ($q) {
            $q->where('custom_role', 1)->whereNot('name', 'Teacher');
        })->with('staff', 'roles', 'support_school.school');

        //        if (!empty(Auth::user()->school_id)) {
        //            //Code For School Panel
        //            $sql->whereHas('roles', function ($q) {
        //                $q->where('custom_role', 1)->whereNot('name', 'Teacher');
        //            });
        //        } else {
        //            //Code For Super Admin side Panel
        //        }

        if (!empty($request->show_deactive)) {
            $sql = $sql->where('status', 0)->onlyTrashed();
        }
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('first_name', 'LIKE', "%$search%")
                    ->orwhere('last_name', 'LIKE', "%$search%")
                    ->orwhere('email', 'LIKE', "%$search%")
                    ->orwhere('mobile', 'LIKE', "%$search%")
                    ->orWhereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'");
            })->Owner();
        }

        $total = $sql->count();

        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            if ($request->show_deactive) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::button('fa fa-check', route('staff.restore', $row->id), ['activate-staff', 'btn-gradient-success'], ['title' => __('active')]);
                $operate .= BootstrapTableService::trashButton(route('staff.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate = BootstrapTableService::editButton(route('staff.update', $row->id));
                if (app(FeaturesService::class)->hasFeature('Expense Management')) {
                    $operate .= BootstrapTableService::button('fa fa-eye', route('staff.payroll-structure', $row->id),['btn-gradient-warning'], ['title' => __('salary_structure')]);    
                }
                $operate .= BootstrapTableService::button('fa fa-exclamation-triangle', route('staff.destroy', $row->id), ['deactivate-staff', 'btn-gradient-info'], ['title' => __('inactive')]);

                
                
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['support_school_id'] = $row->support_school->pluck('school_id');
            $tempRow['operate'] = $operate;
            $tempRow['roles_name'] = $row->roles->pluck('name');   
            if(Auth::user()->school_id) {
                $tempRow['extra_fields'] = $row->extra_user_datas;
                foreach ($row->extra_user_datas as $key => $field) {
                    $data = '';
                    if ($field->form_field->type == 'checkbox') {
                        $data = json_decode($field->data);
                    } elseif ($field->form_field->type == 'file') {
                        $data = '<a href="'.Storage::url($field->data).'" target="_blank">DOC</a>';
                    } elseif ($field->form_field->type == 'dropdown') {
                        $defaultValues = $field->form_field->default_values;
                        $data = $defaultValues[$field->data] ?? '';
                    } else {
                        $data = $field->data;
                    }
                    $tempRow[$field->form_field->name] = $data;
                }   
            }
                $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update(Request $request, $id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenSendJson('staff-edit');
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name'  => 'required',
                'mobile'     => 'required|digits_between:1,16',
                'email'      => 'required|unique:users,email,' . $id,
                'role_id'    => 'required|numeric',
                'dob'        => 'required'
            ]);
            if ($validator->fails()) {
                ResponseService::validationError($validator->errors()->first());
            }
            DB::beginTransaction();
            $data = $request->except('school_id');
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            if ($request->reset_password) {
                $data['password'] = Hash::make($request->mobile);
            }

            $user = $this->user->update($id, $data);

            // Store Extra Details
            $extraDetails = [];
            foreach ($request->extra_fields ?? [] as $fields) {
                if ($fields['input_type'] == 'file') {
                    if (isset($fields['data']) && $fields['data'] instanceof UploadedFile) {
                        $extraDetails[] = array(
                            'id'            => $fields['id'],
                            'user_id'    => $user->id,
                            'form_field_id' => $fields['form_field_id'],
                            'data'          => $fields['data']
                        );
                    }
                } else {
                    $data = null;
                    if (isset($fields['data'])) {
                        $data = (is_array($fields['data']) ? json_encode($fields['data'], JSON_THROW_ON_ERROR) : $fields['data']);
                    }
                    $extraDetails[] = array(
                        'id'            => $fields['id'],
                        'user_id'    => $user->id,
                        'form_field_id' => $fields['form_field_id'],
                        'data'          => $data,
                    );
                }
            }
            $this->extraFormFields->upsert($extraDetails, ['id'], ['data']);

            $oldRole = $user->roles;
            if ($oldRole[0]->id !== $request->role_id) {
                $newRole = Role::findById($request->role_id);
                $user->removeRole($oldRole[0]);
                $user->assignRole($newRole);
            }
            $this->staff->update($user->staff->id, [
                'salary' => $request->salary,
                'joining_date' => date('Y-m-d',strtotime($request->joining_date))
            ]);

            if ($user->school_id) {
                $leave_permission = [
                    'leave-list',
                    'leave-create',
                    'leave-edit',
                    'leave-delete',
                ];
                $user->givePermissionTo($leave_permission);
            }

            $this->staffSupportSchool->builder()->where('user_id', $user->id)->delete();
            if ($request->school_id) {
                $data = array();
                foreach ($request->school_id as $key => $school) {
                    $data[] = [
                        'user_id'   => $user->id,
                        'school_id' => $school
                    ];
                }
                $this->staffSupportSchool->upsert($data, ['user_id', 'school_id'], ['user_id', 'school_id']);
            }

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenSendJson('staff-delete');
        try {
            DB::beginTransaction();
            $user = $this->user->findById($id);
            $this->user->builder()->where('id', $id)->withTrashed()->update(['status' => $user->status == 0 ? 1 : 0, 'deleted_at' => $user->status == 1 ? now() : null]);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenSendJson('staff-delete');
        try {
            DB::beginTransaction();
            $staff = $this->user->findTrashedById($id);

            if ($staff->status == 0) {
                // If prepaid plan check student limit
                $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
                if ($subscription && $subscription->package_type == 0) {
                    $status = $this->subscriptionService->check_user_limit($subscription, "Staffs");
                    
                    if (!$status) {
                        ResponseService::errorResponse('You reach out limits');
                    }
                }
            }

            $this->user->builder()->where('id', $id)->withTrashed()->update(['status' => $staff->status == 0 ? 1 : 0, 'deleted_at' => $staff->status == 1 ? now() : null]);
            DB::commit();
            ResponseService::successResponse("Status Updated Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenSendJson('staff-delete');
        try {
            $user = $this->user->findOnlyTrashedById($id);
            $user->staff->delete();
            $user->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function support() {
        ResponseService::noRoleThenRedirect('School Admin');
        DB::setDefaultConnection('mysql');
        $support_staffs = $this->staffSupportSchool->builder()->Owner()->with('user:id,first_name,last_name,mobile,email,image')->get();
        $super_admin = '';
        if (!count($support_staffs)) {
            $super_admin = $this->user->builder()->select('first_name', 'last_name', 'mobile', 'email', 'image')->orWhereNull('school_id')->role('Super Admin')->first();
        }
        $settings = [
            'mobile' => app(CachingService::class)->getSystemSettings()['mobile'] ?? '',
            'email'  => app(CachingService::class)->getSystemSettings()['mail_username'] ?? '',
        ];
        DB::setDefaultConnection('school');
        return view('staff.support', compact('support_staffs', 'settings', 'super_admin'));
    }

    public function changeStatusBulk(Request $request) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('staff-create');
        try {
            DB::beginTransaction();
            $userIds = json_decode($request->ids);
            foreach ($userIds as $userId) {
                $staff = $this->user->findTrashedById($userId);

                if ($staff->status == 0) {
                    // If prepaid plan check student limit
                    $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
                    if ($subscription && $subscription->package_type == 0) {
                        $status = $this->subscriptionService->check_user_limit($subscription, "Staffs");
                        
                        if (!$status) {
                            ResponseService::errorResponse('You reach out limits');
                        }
                    }
                }

                $this->user->builder()->where('id', $userId)->withTrashed()->update(['status' => $staff->status == 0 ? 1 : 0, 'deleted_at' => $staff->status == 1 ? now() : null]);
            }
            DB::commit();
            ResponseService::successResponse("Status Updated Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function staff_id_card()
    {
        ResponseService::noFeatureThenRedirect('ID Card - Certificate Generation');
        ResponseService::noPermissionThenRedirect('staff-list');

        return view('staff.id_card');
    }

    public function staff_id_card_list()
    {
        ResponseService::noFeatureThenRedirect('ID Card - Certificate Generation');
        ResponseService::noPermissionThenRedirect('staff-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = $this->user->builder()->whereHas('roles', function ($q) {
            $q->where('custom_role', 1);
        })->with('staff', 'roles', 'support_school.school');
        
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('first_name', 'LIKE', "%$search%")
                    ->orwhere('last_name', 'LIKE', "%$search%")
                    ->orwhere('email', 'LIKE', "%$search%")
                    ->orwhere('mobile', 'LIKE', "%$search%");
            })->Owner();
        }

        $total = $sql->count();

        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {


            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['roles_name'] = $row->roles->pluck('name');
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function generate_staff_id_card(Request $request)
    {
        ResponseService::noFeatureThenRedirect('ID Card - Certificate Generation');
        ResponseService::noPermissionThenRedirect('staff-list');

        $request->validate([
            'user_id' => 'required'
        ], [
            'user_id.required' => trans('Please select at least one record')
        ]);
        try {
            $user_ids = explode(",",$request->user_id);
            $settings = $this->cache->getSchoolSettings();
            if (!isset($settings['staff_id_card_fields'])) {
                return redirect()->route('id-card-settings')->with('error',trans('settings_not_found'));
            }

            $settings['staff_id_card_fields'] = explode(",",$settings['staff_id_card_fields']);

            $data = explode("storage/", $settings['signature'] ?? '');
            $settings['signature'] = end($data);

            $data = explode("storage/", $settings['staff_background_image'] ?? '');
            $settings['staff_background_image'] = end($data);

            $data = explode("storage/", $settings['horizontal_logo'] ?? '');
            $settings['horizontal_logo'] = end($data);

            $sessionYear = $this->cache->getDefaultSessionYear();
            $valid_until = date('F j, Y',strtotime($sessionYear->end_date));
            $height = $settings['staff_page_height'] * 2.8346456693;
            $width = $settings['staff_page_width'] * 2.8346456693;
            // $customPaper = array(0,0,360,200);
            $customPaper = array(0,0,$width,$height);
            $users = $this->user->builder()->select('id','first_name','last_name','image','school_id','gender','dob','mobile','email')->whereIn('id',$user_ids)->with(['roles','staff','extra_user_datas' => function ($query) {
                    $query->with(['form_field'])->where('deleted_at', null);
                },
            ])->get();

            $settings['staff_page_height'] = ($settings['staff_page_height'] * 3.7795275591).'px';

            $pdf = PDF::loadView('staff.staff_id_card',compact('users','sessionYear','valid_until','settings'));
            $pdf->setPaper($customPaper);

            
            return $pdf->stream();
            return view('students.id_card_pdf');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }


    }

    public function bulkUploadIndex()
    {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('staff-list');
        $roles = Role::where('custom_role', 1)->whereNot('name','Teacher')->get();
        $schools = array();
        if (!Auth::user()->school_id) {
            $schools = $this->school->active()->pluck('name', 'id');
        }
        $features = $this->features->getFeatures();
        // $features = array();
        return response(view('staff.bulk_upload', compact('roles', 'schools', 'features')));
       
    }
    public function storeBulkUpload(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('staff-create');
        $validator = Validator::make($request->all(), [
            'role_id'  => 'required|numeric',
            'file'             => 'required|mimes:csv,txt'
        ]);
        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            Excel::import(new StaffImport($request->role_id,$request->is_send_notification), $request->file('file'));
            ResponseService::successResponse('Data Stored Successfully');
        } catch (ValidationException $e) {
            ResponseService::errorResponse($e->getMessage());
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Staff Controller -> Store Bulk method");
            ResponseService::errorResponse();
        }
    }

    public function downloadSampleFile() {
        try {
            return Excel::download(new StaffDataExport(), 'Staff_import.xlsx');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'Staff Controller ---> Download Sample File');
            ResponseService::errorResponse();
        }
    }

    public function viewSalaryStructure($id)
    {
        ResponseService::noAnyPermissionThenRedirect(['staff-list','teacher-list']);

        $user = $this->user->builder()->with(['staff' => function($q) {
            $q->withCount(['staffSalary as allowanceCounter' => function($q) {
                $q->whereHas('payrollSetting',function($q) {
                    $q->where('type','allowance');
                });
            }])
            ->withCount(['staffSalary as deductionCounter' => function($q) {
                $q->whereHas('payrollSetting',function($q) {
                    $q->where('type','deduction');
                });
            }])
            ->with('staffSalary.payrollSetting');
        }])->findOrFail($id);
        $payrollSetting = $this->payrollSetting->builder()->get();
    
        if (!$user || !$user->staff) {
            return redirect('staff-list')->with('error', 'User not found or has no staff information');
        }

        $allowances = $this->payrollSetting->builder()->withTrashed()->where('type', 'allowance')->get();
        $deductions = $this->payrollSetting->builder()->withTrashed()->where('type', 'deduction')->get();

        return response(view('staff.view_salary_structure', compact(
            'allowances',
            'deductions',
            'user'
        )));

        $user = $this->user->builder()->with([
            'staff',
            'staff.staffSalary',
            'staff.staffSalary.payrollSetting'
        ])->whereHas('roles', function ($q) {
            $q->where('custom_role', 1)->whereNot('name', 'Teacher');
        })->findOrFail($id);
    
        if (!$user || !$user->staff) {
            return redirect('staff-list')->with('error', 'User not found or has no staff information');
        }
    
        $salary = $user->staff->salary;
    
        $allowanceAmount = [];
        $deductionAmount = [];
        $allowances = [];
        $deductions = [];
    
        foreach ($user->staff->staffSalary as $salaryItem) {
            $payrollSetting = $salaryItem->payrollSetting;
            if (!$payrollSetting) {
                continue;
            }
    
            if ($payrollSetting->type === 'allowance') {
                $allowanceType = $payrollSetting->name;
                if (isset($salaryItem->percentage)) {
                    $allowanceType .= ' (' . $salaryItem->percentage . '%)';
                    $allowanceAmount[] = ($salaryItem->percentage / 100) * $salary;
                } elseif (isset($salaryItem->amount)) {
                    $allowanceAmount[] = $salaryItem->amount;
                }
                $allowances[] =   $allowanceType;
            } elseif ($payrollSetting->type === 'deduction') {
                $deductionType= $payrollSetting->name;
                if (isset($salaryItem->percentage)) {
                    $deductionType .= ' (' . $salaryItem->percentage . '%)';
                    $deductionAmount[] = ($salaryItem->percentage / 100) * $salary;
                } elseif (isset($salaryItem->amount)) {
                    $deductionAmount[] = $salaryItem->amount;
                }
                $deductions[] = $deductionType;
            }
        }
    
        $totalAllowanceAmount = array_sum($allowanceAmount);
        $totalDeductionAmount = array_sum($deductionAmount);
        $netSalary = $salary + $totalAllowanceAmount - $totalDeductionAmount;
    
        return response(view('staff.view_salary_structure', compact(
            'salary',
            'allowances',
            'deductions',
            'allowanceAmount',
            'deductionAmount',
            'netSalary'
        )));
    }

    public function deletePayrollSetting($id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        try {
            $this->staffSalary->deleteById($id);
            ResponseService::successResponse("Data Deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function updatePayrollSetting(Request $request, $id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        try {
            DB::beginTransaction();
            // Store allowances
            $allowance_data = array();
            $allowance_status = 0;
            foreach ($request->allowance ?? [] as $allowance) 
            {
                if ($allowance['id'] ?? '') {
                    $allowance_status = 1;
                    $allowance_data[] = [
                        'staff_id' => $id,
                        'payroll_setting_id' =>  $allowance['id'],
                        'amount' => $allowance['amount'] ??  null,
                        'percentage' => $allowance['percentage'] ?? null
                    ];
                }
            }

            if ($allowance_status) {
                $this->staffSalary->upsert($allowance_data,['staff_id','payroll_setting_id'],['amount','percentage']);
            }
            // Store deductions
            $deduction_data = array();
            $deduction_status = 0;
            foreach ($request->deduction ?? [] as $deduction) {

                if ($deduction['id'] ?? '') {
                    $deduction_status = 1;
                    $deduction_data[] = [
                        'staff_id' => $id,
                        'payroll_setting_id' =>  $deduction['id'],
                        'amount' => $deduction['amount'] ??  null,
                        'percentage' => $deduction['percentage'] ?? null
                    ];
                }
            }
            if ($deduction_status) {
                $this->staffSalary->upsert($deduction_data,['staff_id','payroll_setting_id'],['amount','percentage']);
            }

            DB::commit();
            ResponseService::successResponse("Data Updated Successfully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
