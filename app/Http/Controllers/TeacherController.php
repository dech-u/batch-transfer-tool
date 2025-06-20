<?php

namespace App\Http\Controllers;

use App\Repositories\Staff\StaffInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Repositories\User\UserInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use TypeError;
use App\Exports\TeacherDataExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeacherImport;
use App\Repositories\ExtraFormField\ExtraFormFieldsInterface;
use App\Repositories\FormField\FormFieldsInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\PayrollSetting\PayrollSettingInterface;
use App\Repositories\StaffSalary\StaffSalaryInterface;
use App\Services\UserService;
use Illuminate\Http\UploadedFile;

class TeacherController extends Controller {
    private UserInterface $user;
    private StaffInterface $staff;
    private SubscriptionInterface $subscription;
    private CachingService $cache;
    private SubscriptionService $subscriptionService;
    private PayrollSettingInterface $payrollSetting;
    private StaffSalaryInterface $staffSalary;
    private FormFieldsInterface $formFields;
    private ExtraFormFieldsInterface $extraFormFields;



    public function __construct(StaffInterface $staff, UserInterface $user, SubscriptionInterface $subscription, CachingService $cache, SubscriptionService $subscriptionService, PayrollSettingInterface $payrollSetting, StaffSalaryInterface $staffSalary, FormFieldsInterface $formFields, ExtraFormFieldsInterface $extraFormFields) {
        $this->user = $user;
        $this->staff = $staff;
        $this->subscription = $subscription;
        $this->cache = $cache;
        $this->subscriptionService = $subscriptionService;
        $this->payrollSetting = $payrollSetting;
        $this->staffSalary = $staffSalary;
        $this->formFields = $formFields;
        $this->extraFormFields = $extraFormFields;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('teacher-list');

        $allowances = $this->payrollSetting->builder()->where('type', 'allowance')->get();
        $deductions = $this->payrollSetting->builder()->where('type', 'deduction')->get();

        if(Auth::user()->school_id) {
            $extraFields = $this->formFields->defaultModel()->where('user_type', 2)->orderBy('rank')->get();    
        } else {
            $extraFields = $this->formFields->defaultModel()->orderBy('rank')->get();
        }
        return view('teacher.index',compact('allowances', 'deductions','extraFields'));
    }

    public function store(Request $request) {
        ResponseService::noAnyPermissionThenSendJson(['teacher-create', 'teacher-edit']);
        $request->validate([
            'first_name'        => 'required',
            'last_name'         => 'required',
            'gender'            => 'required',
            'email'             => 'required|email|unique:users,email',
            'mobile'            => 'required|numeric|digits_between:1,16',
            'dob'               => 'required|date|unique:users,email',
            'qualification'     => 'required',
            'current_address'   => 'required',
            'permanent_address' => 'required',
            'status'            => 'nullable|in:0,1',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,svg,gif,webp',
        ]);
        try {
            DB::beginTransaction();

            // Check free trial package
            $today_date = Carbon::now()->format('Y-m-d');
            $subscription = $this->subscription->builder()->doesntHave('subscription_bill')->whereDate('start_date','<=',$today_date)->where('end_date','>=',$today_date)->whereHas('package',function($q){
                $q->where('is_trial',1);
            })->first();
            
            if ($subscription) {
                $systemSettings = $this->cache->getSystemSettings();
                $staff = $this->user->builder()->role('Teacher')->withTrashed()->orWhereHas('roles', function ($q) {
                    $q->where('custom_role', 1)->whereNotIn('name', ['Teacher','Guardian']);
                })->whereNotNull('school_id')->Owner()->count();
                if ($staff >= $systemSettings['staff_limit']) {
                    $message = "The free trial allows only ".$systemSettings['staff_limit']." staff.";
                    ResponseService::errorResponse($message);
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

            $user_data = array(
                ...$request->all(),
                'password'          => Hash::make($request->mobile),
                'image'             => $request->file('image'),
                'status'            => $request->status ?? 0,
                'deleted_at'        => $request->status == 1 ? null : '1970-01-01 01:00:00'
            );

            //Call store function of User Repository and get the User Data
            $user = $this->user->create($user_data);

            $user->assignRole('Teacher');

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

            $staff = $this->staff->create([
                'user_id'       => $user->id,
                'qualification' => $request->qualification,
                'salary'        => $request->salary,
                'joining_date'   => date('Y-m-d',strtotime($request->joining_date))
            ]);

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
            $sendEmail = app(UserService::class);
            $sendEmail->sendStaffRegistrationEmail($user, $request->mobile);
            
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Teacher Registered successfully. But Email not sent.");
            } else {
                DB::rollback();
                ResponseService::logErrorResponse($e, "Teacher Controller -> Store method");
                ResponseService::errorResponse();
            }
        }
    }

    public function show() {
        ResponseService::noPermissionThenRedirect('teacher-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $showDeleted = request('show_deactive');
        $sql = $this->user->builder()->role('Teacher')->with('staff','staff.staffSalary','extra_student_details.form_field')
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                    ->orwhere('first_name', 'LIKE', "%$search%")
                    ->orwhere('last_name', 'LIKE', "%$search%")
                    ->orwhere('gender', 'LIKE', "%$search%")
                    ->orwhere('email', 'LIKE', "%$search%")
                    ->orwhere('current_address', 'LIKE', "%$search%")
                    ->orwhere('permanent_address', 'LIKE', "%$search%")
                    ->whereHas('staff', function ($q) use ($search) {
                        $q->orwhere('staffs.qualification', 'LIKE', "%$search%");
                    });
                });
            })
            ->when(!empty($showDeleted), function ($query) {
                $query->where('status',0)->onlyTrashed();
            });
        $total = $sql->count();
        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            if ($showDeleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::menuButton('active',route('teachers.change-status', $row->id),['activate-teacher'],[]);
                $operate .= BootstrapTableService::menuTrashButton('delete',route('teachers.trash', $row->id));
                
            } else {
                //Show Edit and Soft Delete Buttons
                
                $operate = BootstrapTableService::menuEditButton('edit',route('teachers.update', $row->id));
                $operate .= BootstrapTableService::menuButton('View Timetable',route('timetable.teacher.show', $row->id),[],[]);
                $operate .= BootstrapTableService::menuButton('inactive',route('teachers.change-status', $row->id),['deactivate-teacher'],[]);
                $operate .= BootstrapTableService::menuButton('salary_structure',route('staff.payroll-structure', $row->id),[],[]);
                $operate .= BootstrapTableService::menuTrashButton('delete',route('teachers.trash', $row->id));
                
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            // $tempRow['dob'] = format_date($row->dob);
            $tempRow['operate'] = BootstrapTableService::menuItem($operate);
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function edit($id) {
        $teacher = $this->staff->findById($id);
        return response($teacher);
    }


    public function update(Request $request, $id) {
        // ResponseService::noFeatureThenSendJson('Teacher Management');
        ResponseService::noPermissionThenSendJson('teacher-edit');
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'gender'            => 'required',
            'email'             => 'required|email|unique:users,email,' . $id,
            'mobile'            => 'required|numeric|digits_between:1,16',
            'dob'               => 'required|date',
            'qualification'     => 'required',
            'current_address'   => 'required',
            'permanent_address' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $user_data = array(
                ...$request->all(),
            );
            if ($request->file('image')) {
                $user_data['image'] = $request->file('image');
            }

            if ($request->reset_password) {
                $user_data['password'] = Hash::make($request->mobile);
            }

            //Call store function of User Repository and get the User Data
            $user = $this->user->update($id, $user_data);

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

            //Call store function of User Repository and get the User Data
            $this->staff->update($user->staff->id, array('qualification' => $request->qualification, 'salary' => $request->salary,'joining_date'   => date('Y-m-d',strtotime($request->joining_date))));

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            if ($e instanceof TypeError && Str::contains($e->getMessage(), ['Mail', 'Mailer', 'MailManager'])) {
                ResponseService::warningResponse("Teacher Registered successfully. But Email not sent.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "Teacher Controller -> Update method");
                ResponseService::errorResponse();
            }
        }
    }


    public function trash($id) {
        ResponseService::noPermissionThenSendJson('teacher-delete');
        try {
            DB::beginTransaction();
            $this->user->findTrashedById($id)->forceDelete();
            DB::commit();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Teacher Controller ->trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function changeStatus($id) {
        // ResponseService::noFeatureThenSendJson('Teacher Management');
        ResponseService::noPermissionThenRedirect('teacher-delete');
        try {
            DB::beginTransaction();
            $teacher = $this->user->findTrashedById($id);

            if ($teacher->status == 0) {
                // If prepaid plan check student limit
                $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
                if ($subscription && $subscription->package_type == 0) {
                    $status = $this->subscriptionService->check_user_limit($subscription, "Staffs");
                    
                    if (!$status) {
                        ResponseService::errorResponse('You reach out limits');
                    }
                }
            }

            $this->user->builder()->where('id',$id)->withTrashed()->update(['status' => $teacher->status == 0 ? 1 : 0,'deleted_at' => $teacher->status == 1 ? now() : null]);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'Status methods -> Teacher controller');
            ResponseService::errorResponse();
        }
    }
    public function changeStatusBulk(Request $request){
        // ResponseService::noFeatureThenSendJson('Teacher Management');
        ResponseService::noPermissionThenRedirect('teacher-delete');
        try {
            DB::beginTransaction();
            $userIds = json_decode($request->ids);
            foreach ($userIds as $userId) {
                $teacher = $this->user->findTrashedById($userId);
                if ($teacher->status == 0) {
                    // If prepaid plan check student limit
                    $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);
                    if ($subscription && $subscription->package_type == 0) {
                        $status = $this->subscriptionService->check_user_limit($subscription, "Staffs");
                        
                        if (!$status) {
                            ResponseService::errorResponse('You reach out limits');
                        }
                    }
                }
                $this->user->builder()->where('id',$userId)->withTrashed()->update(['status' => $teacher->status == 0 ? 1 : 0,'deleted_at' => $teacher->status == 1 ? now() : null]);
            }
            DB::commit();
            ResponseService::successResponse("Status Updated Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
    public function bulkUploadIndex()
    {
        ResponseService::noAnyPermissionThenSendJson(['teacher-create', 'teacher-edit']);
        return view('teacher.bulk_upload');
       
    }
    public function storeBulkUpload(Request $request)
    {
        ResponseService::noAnyPermissionThenSendJson(['teacher-create', 'teacher-edit']);
        $validator = Validator::make($request->all(), [
            'file'             => 'required|mimes:csv,txt'
        ]);
        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            Excel::import(new TeacherImport($request->is_send_notification), $request->file('file'));
            ResponseService::successResponse('Data Stored Successfully');
        } catch (ValidationException $e) {
            ResponseService::errorResponse($e->getMessage());
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Teacher Controller -> Store Bulk method");
            ResponseService::errorResponse();
        }                                                                                                                               
    }

    public function downloadSampleFile() {
        try {
            return Excel::download(new TeacherDataExport(), 'teachers.xlsx');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'Teacher Controller ---> Download Sample File');
            ResponseService::errorResponse();
        }
    }

}
