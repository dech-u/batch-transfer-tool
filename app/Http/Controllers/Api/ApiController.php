<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ClassTeacher;
use App\Models\Role;
use App\Models\Students;
use App\Models\SubjectTeacher;
use App\Models\User;
use App\Repositories\Attachment\AttachmentInterface;
use App\Repositories\Chat\ChatInterface;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ExamResult\ExamResultInterface;
use App\Repositories\Gallery\GalleryInterface;
use App\Repositories\Grades\GradesInterface;
use App\Repositories\Holiday\HolidayInterface;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveDetail\LeaveDetailInterface;
use App\Repositories\LeaveMaster\LeaveMasterInterface;
use App\Repositories\Medium\MediumInterface;
use App\Repositories\PaymentConfiguration\PaymentConfigurationInterface;
use App\Repositories\PaymentTransaction\PaymentTransactionInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\User\UserInterface;
use App\Services\CachingService;
use App\Services\Payment\PaymentService;
use App\Services\ResponseService;
use App\Repositories\Fees\FeesInterface;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;
use Stripe\Exception\ApiErrorException;
use Throwable;
use App\Repositories\Files\FilesInterface;
use App\Repositories\Message\MessageInterface;
use App\Rules\MaxFileSize;
use App\Services\GeneralFunctionService;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\School;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class ApiController extends Controller
{
    private CachingService $cache;
    private HolidayInterface $holiday;
    private StudentInterface $student;
    private PaymentConfigurationInterface $paymentConfiguration;
    private PaymentTransactionInterface $paymentTransaction;
    private GalleryInterface $gallery;
    private SessionYearInterface $sessionYear;
    private LeaveDetailInterface $leaveDetail;
    private LeaveMasterInterface $leaveMaster;
    private LeaveInterface $leave;
    private UserInterface $user;
    private MediumInterface $medium;
    private ClassSectionInterface $classSection;
    private FilesInterface $files;
    private ExamResultInterface $examResult;
    private GradesInterface $grade;
    private ChatInterface $chat;
    private MessageInterface $message;
    private AttachmentInterface $attachment;
    private SchoolSettingInterface $schoolSettings;
    private FeesInterface $fees;
    

    public function __construct(CachingService $cache, HolidayInterface $holiday, StudentInterface $student, PaymentConfigurationInterface $paymentConfiguration, PaymentTransactionInterface $paymentTransaction, GalleryInterface $gallery, SessionYearInterface $sessionYear, LeaveDetailInterface $leaveDetail, LeaveMasterInterface $leaveMaster, LeaveInterface $leave, UserInterface $user, MediumInterface $medium, ClassSectionInterface $classSection, ExamResultInterface $examResult, GradesInterface $grade, FilesInterface $files, ChatInterface $chat, MessageInterface $message, AttachmentInterface $attachment, SchoolSettingInterface $schoolSettings, FeesInterface $fees)

    {
        $this->cache = $cache;
        $this->holiday = $holiday;
        $this->student = $student;
        $this->paymentConfiguration = $paymentConfiguration;
        $this->paymentTransaction = $paymentTransaction;
        $this->gallery = $gallery;
        $this->sessionYear = $sessionYear;
        $this->leaveDetail = $leaveDetail;
        $this->leaveMaster = $leaveMaster;
        $this->leave = $leave;
        $this->user = $user;
        $this->medium = $medium;
        $this->classSection = $classSection;
        $this->files = $files;
        $this->examResult = $examResult;
        $this->grade = $grade;
        $this->chat = $chat;
        $this->message = $message;
        $this->attachment = $attachment;
        $this->schoolSettings = $schoolSettings;
        $this->fees = $fees;
    }

    public function logout(Request $request)
    {
        try {
            
            $user = $request->user();
            $user->fcm_id = '';
            $user->save();
            // $user->currentAccessToken()->delete();
            $token = $request->bearerToken();

            if ($token) {
                // Find the token in the personal_access_tokens table
                $accessToken = PersonalAccessToken::findToken($token);
    
                if ($accessToken) {
                    // Delete the token to revoke access
                    $accessToken->delete();
                }
            }

            ResponseService::successResponse('Logout Successfully done');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getHolidays(Request $request)
    {
        try {
            // $query->whereDate('date', '>=',$sessionYear->start_date)
            //     ->whereDate('date', '<=',$sessionYear->end_date);

            $sessionYear = $this->cache->getDefaultSessionYear();
            if ($request->child_id) {
                $child = $this->student->findById($request->child_id);
                $data = $this->holiday->builder()->where('school_id', $child->user->school_id);
            } else {
                $data = $this->holiday->builder();
            }

            $data = $data->whereDate('date', '>=',$sessionYear->start_date)
                ->whereDate('date', '<=',$sessionYear->end_date)->get();
            
            ResponseService::successResponse("Holidays Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:student_privacy_policy,teacher_privacy_policy,contact_us,about_us,student_terms_condition,teacher_terms_condition,app_settings,fees_settings,terms_condition,privacy_policy'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $systemSettings = $this->cache->getSystemSettings();
            if ($request->type == "app_settings") {
                $data = array(
                    'app_link'                 => $systemSettings['app_link'] ?? "",
                    'ios_app_link'             => $systemSettings['ios_app_link'] ?? "",
                    'app_version'              => $systemSettings['app_version'] ?? "",
                    'ios_app_version'          => $systemSettings['ios_app_version'] ?? "",
                    'force_app_update'         => $systemSettings['force_app_update'] ?? "",
                    'app_maintenance'          => $systemSettings['app_maintenance'] ?? "",
                    'teacher_app_link'         => $systemSettings['teacher_app_link'] ?? "",
                    'teacher_ios_app_link'     => $systemSettings['teacher_ios_app_link'] ?? "",
                    'teacher_app_version'      => $systemSettings['teacher_app_version'] ?? "",
                    'teacher_ios_app_version'  => $systemSettings['teacher_ios_app_version'] ?? "",
                    'teacher_force_app_update' => $systemSettings['teacher_force_app_update'] ?? "",
                    'teacher_app_maintenance'  => $systemSettings['teacher_app_maintenance'] ?? "",
                    'tagline'                  => $systemSettings['tag_line'] ?? "",
                );
            } else {
                $data = isset($systemSettings[$request->type]) ? htmlspecialchars_decode($systemSettings[$request->type]) : "";
            }
            ResponseService::successResponse("Data Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    protected function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => "required|email",
            'school_code' => 'required'
        ]);
        try {

            $schoolCode = $request->school_code;

            if ($schoolCode) {
                $school = School::on('mysql')->where('code',$schoolCode)->first();
                if ($school) {
                    DB::setDefaultConnection('school');
                    Config::set('database.connections.school.database', $school->database_name);
                    DB::purge('school');
                    DB::connection('school')->reconnect();
                    DB::setDefaultConnection('school');
                    
                    $response = Password::sendResetLink(['email' => $request->email]);
                   
                    if ($response == Password::RESET_LINK_SENT) {
                        ResponseService::successResponse("Forgot Password email send successfully");
                    } else {
                        ResponseService::errorResponse("Cannot send Reset Password Link.Try again later", null, config('constants.RESPONSE_CODE.RESET_PASSWORD_FAILED'));
                    }

                } else {
                    return response()->json(['message' => 'Invalid school code'], 400);
                }
            } else {
                return response()->json(['message' => 'Unauthenticated'], 400);
            }

           
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    protected function changePassword(Request $request)
    {
        $request->validate([
            'current_password'     => 'required',
            'new_password'         => 'required|min:6',
            'new_confirm_password' => 'same:new_password',
        ]);

        try {
            $user = $request->user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->update(['password' => Hash::make($request->new_password)]);
                ResponseService::successResponse("Password Changed successfully.");
            } else {
                ResponseService::errorResponse("Invalid Password", null, config('constants.RESPONSE_CODE.INVALID_PASSWORD'));
            }
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getPaymentMethod(Request $request)
    {
        if (Auth::user()->hasRole('Guardian')) {
            $validator = Validator::make($request->all(), [
                'child_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                ResponseService::validationError($validator->errors()->first());
            }
        }
        try {

            $response = $this->paymentConfiguration->builder()->select('payment_method', 'status')->pluck('status', 'payment_method');
            ResponseService::successResponse("Payment Details Fetched", $response);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getPaymentConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $paymentTransaction = app(PaymentTransactionInterface::class)->builder()->where('id', $request->id)->first();
            if (empty($paymentTransaction)) {
                ResponseService::errorResponse("No Data Found");
            }
            $data = PaymentService::create($paymentTransaction->payment_gateway, $paymentTransaction->school_id)->retrievePaymentIntent($paymentTransaction->order_id);

            $data = PaymentService::formatPaymentIntent($paymentTransaction->payment_gateway, $data);

            // Success
            ResponseService::successResponse("Payment Details Fetched", $data, ['payment_transaction' => $paymentTransaction]);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getPaymentTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latest_only' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $paymentTransactions = app(PaymentTransactionInterface::class)->builder();
            if ($request->latest_only) {
                $paymentTransactions->where('created_at', '>', Carbon::now()->subMinutes(30)->toDateTimeString());
            }
            $paymentTransactions = $paymentTransactions->with('school')->orderBy('id', 'DESC')->get();

            $schoolSettings = app(SchoolSettingInterface::class)->builder()
                ->where(function ($q) {
                    $q->where('name', 'currency_code')->orWhere('name', 'currency_symbol');
                })->whereIn('school_id', $paymentTransactions->pluck('school_id'))->get();

            $paymentTransactions = $paymentTransactions->map(function ($data) use ($schoolSettings) {
                $getSchoolSettings = $schoolSettings->filter(function ($settings) use ($data) {
                    return $settings->school_id == $data->school_id;
                })->where('status', 1)->pluck('data', 'name');
                $data->currency_code = $getSchoolSettings['currency_code'] ?? '';
                $data->currency_symbol = $getSchoolSettings['currency_symbol'] ?? '';
                if ($data->payment_status == "pending") {
                    try {
                        if ($data->order_id) {
                            // For Flutterwave, use tx_ref for verification
                            if ($data->payment_gateway == "Flutterwave") {
                                $paymentService = PaymentService::create($data->payment_gateway, $data->school_id);
                                // $paymentIntent = $paymentService->verifyPayment($data->order_id);
                                
                                // Update transaction status based on verification
                                if (isset($paymentIntent['status'])) {
                                    $status = match (strtolower($paymentIntent['status'])) {
                                        'successful', 'completed' => 'succeed',
                                        'failed', 'cancelled' => 'failed',
                                        default => 'pending'
                                    };
                                    
                                    if ($status !== 'pending') {
                                        $this->paymentTransaction->update($data->id, [
                                            'payment_status' => $status,
                                            'payment_id' => $paymentIntent['transaction_id'] ?? null,
                                            'school_id' => $data->school_id
                                        ]);
                                        $data->payment_status = $status;
                                    }
                                }
                            } else {
                                // For other payment gateways
                                $paymentIntent = PaymentService::create($data->payment_gateway, $data->school_id)
                                    ->retrievePaymentIntent($data->order_id);
                                $paymentIntent = PaymentService::formatPaymentIntent($data->payment_gateway, $paymentIntent);
                                
                                if ($paymentIntent['status'] != "pending") {
                                    $this->paymentTransaction->update($data->id, [
                                        'payment_status' => $paymentIntent['status'],
                                        'school_id' => $data->school_id
                                    ]);
                                    $data->payment_status = $paymentIntent['status'];
                                }
                            }
                        }
                    } catch (Exception $e) {
                        Log::error('Payment verification error:', [
                            'payment_id' => $data->id,
                            'order_id' => $data->order_id,
                            'error' => $e->getMessage()
                        ]);
                        // Don't update status on verification error
                    }
                }
                return $data;
            });

            ResponseService::successResponse("Payment Transactions Fetched", $paymentTransactions);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getGallery(Request $request)
    {
        try {
            if ($request->gallery_id) {
                $data = $this->gallery->builder()->with('file')->where('id', $request->gallery_id)->first();
            } else {
                if ($request->child_id) {
                    $child = $this->student->findById($request->child_id);
                    $data = $this->gallery->builder()->with('file')->where('school_id', $child->user->school_id);
                    if ($request->session_year_id) {
                        $data = $data->where('session_year_id', $request->session_year_id);
                    }
                    $data = $data->get();
                } else {
                    if ($request->session_year_id) {
                        $data = $this->gallery->builder()->with('file')->where('session_year_id', $request->session_year_id);
                    } else {
                        $data = $this->gallery->builder()->with('file');
                    }
                    $data = $data->get();
                }
            }
            ResponseService::successResponse("Gallery Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getSessionYear(Request $request)
    {
        try {
            if ($request->child_id) {
                $child = $this->student->findById($request->child_id);
                $data = $this->sessionYear->builder()->where('school_id', $child->user->school_id)->get();
            } else {
                $data = $this->sessionYear->builder()->get();
            }
            ResponseService::successResponse("Session Year Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getLeaves(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        try {
            $leave = $this->leaveDetail->builder()->with('leave:id,user_id', 'leave.user:id,first_name,last_name,image', 'leave.user.roles')
                ->whereHas('leave', function ($q) {
                    $q->where('status', 1);
                });
            if ($request->type == 0 || $request->type == null) {
                $leave->whereDate('date', '<=', Carbon::now()->format('Y-m-d'))->whereDate('date', '>=', Carbon::now()->format('Y-m-d'));
            }
            if ($request->type == 1) {
                $tomorrow_date = Carbon::now()->addDay()->format('Y-m-d');
                $leave->whereDate('date', '<=', $tomorrow_date)->whereDate('date', '>=', $tomorrow_date);
            }
            if ($request->type == 2) {
                $upcoming_date = Carbon::now()->addDays(1)->format('Y-m-d');
                $leave->whereDate('date', '>', $upcoming_date);
            }
            $leave = $leave->orderBy('date', 'ASC')->get()->append(['leave_date']);
            ResponseService::successResponse("Data Fetched Successfully", $leave);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function applyLeaves(Request $request)
    {
        $file_upload_size_limit = $this->cache->getSystemSettings('file_upload_size_limit');
        $validator = Validator::make($request->all(), [
            'reason'  => 'required',
            'files'   => 'nullable|array',
            'files.*' => ['nullable','file',new MaxFileSize($file_upload_size_limit)]
        ],[
            'files.*' => trans('The file Uploaded must be less than :file_upload_size_limit MB.', [
                'file_upload_size_limit' => $file_upload_size_limit,  
            ]),
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            $leaveMaster = $this->leaveMaster->builder()->where('session_year_id', $sessionYear->id)->first();
            
            if (!$leaveMaster) {
                ResponseService::errorResponse("Kindly contact the school admin to update settings for continued access.");
            }

            $public_holiday = $this->holiday->builder()->whereDate('date', '>=', $sessionYear->start_date)->whereDate('date', '<=', $sessionYear->end_date)->get()->pluck('date')->toArray();

            $dates = array_column($request->leave_details, 'date');
            $from_date = min($dates);
            $to_date = max($dates);

            $leave_data = [
                'user_id' => Auth::user()->id,
                'reason' => $request->reason,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'status' => 0,
                'leave_master_id' => $leaveMaster->id
            ];

            $holidays = explode(',', $leaveMaster->holiday);

            $leave = $this->leave->create($leave_data);
            foreach ($request->leave_details as $key => $leaves) {
                $day = date('l', strtotime($leaves['date']));
                if (!in_array($day, $holidays) && !in_array($leaves['date'], $public_holiday)) {
                    $data[] = [
                        'leave_id' => $leave->id,
                        'date' => $leaves['date'],
                        'type' => $leaves['type']
                    ];
                } else {
                    ResponseService::errorResponse("please choose a valid date that is not a holiday or a public holiday");
                }
            }
            if ($request->hasFile('files')) {
                $fileData = []; // Empty FileData Array
                // Create A File Model Instance
                $leaveModelAssociate = $this->files->model()->modal()->associate($leave);
                foreach ($request->file('files') as $file_upload) {
                    // Create Temp File Data Array
                    $tempFileData = [
                        'modal_type' => $leaveModelAssociate->modal_type,
                        'modal_id'   => $leaveModelAssociate->modal_id,
                        'file_name'  => $file_upload->getClientOriginalName(),
                        'type'       => 1,
                        'file_url'   => $file_upload->store('files', 'public') // Store file and get the file path
                    ];
                    $fileData[] = $tempFileData; // Store Temp File Data in Multi-Dimensional File Data Array
                }
                $this->files->createBulk($fileData); // Store File Data
            }

            $this->leaveDetail->createBulk($data);

            $user = $this->user->builder()->whereHas('roles.permissions', function ($q) {
                $q->where('name', 'approve-leave');
            })->pluck('id');

            $type = "Leave";
            $title = Auth::user()->full_name . ' has submitted a new leave request.';
            $body = $request->reason;
            send_notification($user, $title, $body, $type);

            DB::commit();
            ResponseService::successResponse("Data Stored Successfully");
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                ResponseService::logErrorResponse($e);
                ResponseService::errorResponse();
            }
        }
    }

    public function getMyLeaves(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        $validator = Validator::make($request->all(), [
            'month'  => 'in:1,2,3,4,5,6,7,8,9,10,11,12',
            'status' => 'in:0,1,2'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $sessionYear = $this->cache->getDefaultSessionYear();
            $leaveMaster = $this->leaveMaster->builder();
            $sql = $this->leave->builder()->with('leave_detail')->where('user_id', Auth::user()->id)->withCount(['leave_detail as full_leave' => function ($q) {
                $q->where('type', 'Full');
            }])->withCount(['leave_detail as half_leave' => function ($q) {
                $q->whereNot('type', 'Full');
            }]);

            if ($request->session_year_id) {
                $sql->whereHas('leave_master', function ($q) use ($request) {
                    $q->where('session_year_id', $request->session_year_id);
                });
                $leaveMaster->where('session_year_id', $request->session_year_id);
            } else {
                $sql->whereHas('leave_master', function ($q) use ($sessionYear) {
                    $q->where('session_year_id', $sessionYear->id);
                });
                $leaveMaster->where('session_year_id', $sessionYear->id);
            }
            if (isset($request->status)) {
                $sql->where('status', $request->status);
            }
            if ($request->month) {
                $sql->whereHas('leave_detail', function ($q) use ($request) {
                    $q->whereMonth('date', $request->month);
                });
            }
            $leaveMaster = $leaveMaster->first();
            $sql = $sql->get();
            $sql = $sql->map(function ($sql) {
                $total_leaves = ($sql->half_leave / 2) + $sql->full_leave;
                $sql->days = $total_leaves;
                return $sql;
            });
            $data = [
                'monthly_allowed_leaves' => $leaveMaster->leaves,
                'taken_leaves' => $sql->sum('days'),
                'leave_details' => $sql
            ];

            ResponseService::successResponse("Data Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function deleteLeaves(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        $validator = Validator::make($request->all(), [
            'leave_id'  => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $leave = $this->leave->findById($request->leave_id);
            if ($leave->status != 0) {
                ResponseService::errorResponse("You cannot delete this leave");
            }
            $this->leave->deleteById($request->leave_id);
            DB::commit();
            ResponseService::successResponse("Data Deleted Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getStaffLeaveDetail(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'month'  => 'in:1,2,3,4,5,6,7,8,9,10,11,12',
            'status' => 'in:0,1,2'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $sessionYear = $this->cache->getDefaultSessionYear();
            $leaveMaster = $this->leaveMaster->builder();
            $sql = $this->leave->builder()->with('leave_detail','file')->withCount(['leave_detail as full_leave' => function ($q) {
                $q->where('type', 'Full');
            }])->withCount(['leave_detail as half_leave' => function ($q) {
                $q->whereNot('type', 'Full');
            }])->where('user_id', $request->staff_id);

            if ($request->session_year_id) {
                $sql->whereHas('leave_master', function ($q) use ($request) {
                    $q->where('session_year_id', $request->session_year_id);
                });
                $leaveMaster->where('session_year_id', $request->session_year_id);
            } else {
                $sql->whereHas('leave_master', function ($q) use ($sessionYear) {
                    $q->where('session_year_id', $sessionYear->id);
                });
                $leaveMaster->where('session_year_id', $sessionYear->id);
            }
            if (isset($request->status)) {
                $sql->where('status', $request->status);
            }
            if ($request->month) {
                $sql->whereHas('leave_detail', function ($q) use ($request) {
                    $q->whereMonth('date', $request->month);
                });
            }
            $leaveMaster = $leaveMaster->first();
            if (!$leaveMaster) {
                ResponseService::errorResponse("Leave settings not found");
            }
            $sql = $sql->get();
            $sql = $sql->map(function ($sql) {
                $total_leaves = ($sql->half_leave / 2) + $sql->full_leave;
                $sql->days = $total_leaves;
                if ($sql->status == 1) {
                    $sql->taken_leaves = $total_leaves;
                }
                return $sql;
            });
            $data = [
                'monthly_allowed_leaves' => $leaveMaster->leaves,
                'total_leaves' => $sql->sum('days'),
                'taken_leaves' => $sql->sum('taken_leaves'),
                'leave_details' => $sql
            ];

            ResponseService::successResponse("Data Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getMedium()
    {
        try {
            $sql = $this->medium->builder()->get();
            ResponseService::successResponse("Data Fetched Successfully", $sql);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getClass(Request $request)
    {
        try {
            $currentSemester = $this->cache->getDefaultSemesterData();

            // Teacher
            if (Auth::user()->hasRole('Teacher')) {
                $user = $request->user()->teacher;
                //Find the class in which teacher is assigns as Class Teacher
                $class_section_ids = [];
                if ($user->class_teacher) {
                    $class_teacher = $user->class_teacher;
                    $class_section_ids = $class_teacher->pluck('class_section_id');
                }
                //Find the Classes in which teacher is taking subjects
                $class_section = $this->classSection->builder()->with('class.stream', 'section', 'medium')->whereNotIn('id', $class_section_ids);

                $class_section = $class_section->whereHas('subject_teachers.class_subject', function ($q) use ($currentSemester) {
                    (!empty($currentSemester)) ? $q->where('semester_id', $currentSemester->id)->orWhereNull('semester_id') : $q->orWhereNull('semester_id');
                })->get();

                // $class_section = $class_section->get();
            } else {
                // Staff
                $class_section = $this->classSection->builder()->with('class', 'section', 'medium', 'class.stream')->get();
            }
            ResponseService::successResponse('Data Fetched Successfully', null, [
                'class_teacher' => $class_teacher ?? [],
                'other'         => $class_section,
                'semester'      => $currentSemester ?? null
            ]);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'dob' => 'required',
            'current_address' => 'required',
            'permanent_address' => 'required',
            'gender' => 'required|in:male,female',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,svg|max:5120',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $user_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'dob' => $request->dob,
                'current_address' => $request->current_address,
                'permanent_address' => $request->permanent_address,
                'gender' => $request->gender,
            ];

            if ($request->hasFile('image')) {
                $user_data['image'] = $request->file('image');
            }

            $user = $this->user->update(Auth::user()->id, $user_data);

            ResponseService::successResponse('Data Updated Successfully', $user);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getExamResultPdf(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Exam Management');

        
        try {

            $validator = Validator::make($request->all(), [
                'exam_id'  => 'required',
                'student_id' => 'required',
            ]);
            if ($validator->fails()) {
                ResponseService::validationError($validator->errors()->first());
            }

            $exam_id = $request->exam_id;
            $student_id = $request->student_id;

            $results = $this->examResult->builder()
            ->with([
                'exam',
                'session_year',
                'user' => function($q) use($exam_id) {
                    $q->with([
                        'student' => function($q) {
                            $q->with([
                                'guardian',
                                'class_section.class.stream',
                                'class_section.section',
                                'class_section.medium'
                            ]);
                        },
                        'exam_marks' => function($q) use($exam_id) {
                            $q->whereHas('timetable', function($q) use($exam_id) {
                                $q->where('exam_id', $exam_id);
                            })
                            ->with([
                                'class_subject' => function($q) {
                                    $q->withTrashed()->with('subject:id,name,type');
                                },
                                'timetable'
                            ]);
                        }
                    ]);
                }
            ])
            ->where('exam_id', $exam_id)
            ->select('exam_results.*')
            ->get();

            // Convert the results to a collection
            $results = collect($results);

            // Add rank calculation to each item in the collection
            $results = $results->map(function($result) {
                $rank = DB::table('exam_results as er2')
                    ->where('er2.class_section_id', $result->class_section_id)
                    ->where('er2.obtained_marks', '>', $result->obtained_marks)
                    ->where('er2.exam_id', $result->exam_id)
                    ->where('er2.status', 1)
                    ->distinct('er2.obtained_marks')
                    ->count() + 1;

                $result->rank = $rank;
                return $result;
            });

            // Filter the collection based on student ID
            $result = $results->where('student_id', $student_id)->first();

            if (!$result) {
                return redirect()->back()->with('error', trans('no_records_found'));
            }

            $grades = $this->grade->builder()->orderBy('starting_range', 'ASC')->get();

            $settings = $this->cache->getSchoolSettings('*',$result->school_id);
            $data = explode("storage/", $settings['horizontal_logo'] ?? '');
            $settings['horizontal_logo'] = end($data);

            $pdf = PDF::loadView('exams.exam_result_pdf', compact('result', 'settings', 'grades'))->output();

            return $response = array(
                'error' => false,
                'pdf'   => base64_encode($pdf),
            );
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function leaveSettings(Request $request)
    {
        try {

            if ($request->session_year_id) {
                $session_year_id = $request->session_year_id;
            } else {
                $sessionYear = $this->cache->getDefaultSessionYear();
                $session_year_id = $sessionYear->id;
            }
            $sql = $this->leaveMaster->builder()->where('session_year_id',$session_year_id)->get();
            ResponseService::successResponse("Data Fetched Successfully", $sql);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getSchoolSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:privacy_policy,terms_condition,refund_cancellation'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {

            if ($request->child_id) {
                $child = $this->student->builder()->where('id',$request->child_id)->first();
                if (!$child) {
                    ResponseService::errorResponse("no_data_found");
                }
                $schoolSettings = $this->cache->getSchoolSettings('*', $child->school_id);
            } else {
                $schoolSettings = $this->cache->getSchoolSettings();
            }

            $data = isset($schoolSettings[$request->type]) ? htmlspecialchars_decode($schoolSettings[$request->type]) : "";
            ResponseService::successResponse("Data Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function sendMessage(Request $request)
    {
         
        $validator = Validator::make($request->all(), [
            'to' => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        $message = '';

        try {
            DB::beginTransaction();
            $chat = $this->chat->builder()
            ->where(function($q) use($request){
                $q->where('sender_id', Auth::user()->id)->where('receiver_id',$request->to);
            })
            ->orWhere(function($q) use($request){
                $q->where('sender_id', $request->to)->where('receiver_id',Auth::user()->id);
            })
            ->first();

            if (!$chat) {
                $chat = $this->chat->create(['sender_id' => Auth::user()->id, 'receiver_id' => $request->to]);
            }

            $chat = $chat->load(['receiver']);

            $message = $this->message->create(['chat_id' => $chat->id, 'sender_id' => Auth::user()->id, 'message' => $request->message]);

            $data = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $file_path = $file->store('chat_file','public');
                    $data[] = [
                        'message_id' => $message->id,
                        'file' => $file_path,
                        'file_type' => $file->getClientOriginalExtension()
                    ];
                }
                $this->attachment->createBulk($data);
            }
            
            // set attachment
            $message['attachment'] = $message->load(['attachment'])->toArray();
            
            $user[] = $request->to;
            $title = 'New Message from ' .$chat->receiver->full_name;
            $body = $request->message;
            $type = 'Message';

            send_notification($user, $title, $body, $type);
            DB::commit();
            ResponseService::successResponse("Data Stored Successfully", $message);

        } catch (\Throwable $th) {
            $notificationStatus = app(GeneralFunctionService::class)->wrongNotificationSetup($th);
            if ($notificationStatus) {
                DB::rollBack();
                ResponseService::logErrorResponse($th);
                ResponseService::errorResponse();
            } else {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.", $message);
            }
        }
    }

    public function getMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $data = [];
            $data = $this->message->builder()->whereHas('chat',function($q) use($request) {
                $q->where(function($q) use($request){
                    $q->where('sender_id', Auth::user()->id)->where('receiver_id',$request->receiver_id);
                })
                ->orWhere(function($q) use($request){
                    $q->where('sender_id', $request->receiver_id)->where('receiver_id',Auth::user()->id);
                });
            })
            ->with('attachment')->orderBy('id','DESC')
            ->paginate(10);
            
            ResponseService::successResponse("data_fetch_successfully",$data);

        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function deleteMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $this->message->builder()->whereIn('id',$request->id)->delete();
            ResponseService::successResponse("Data Deleted Successfully");

        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function readMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message_id' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $read_at = Carbon::now();
            $this->message->builder()->whereIn('id',$request->message_id)->update(['read_at' => $read_at]);
            ResponseService::successResponse("Data Updated Successfully");

        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getUsers(Request $request)
    {
        if (Auth::user()->hasRole('Teacher')) {
            $validator = Validator::make($request->all(), [
                'role' => 'required|in:Guardian,Staff,Student,Teacher',
            ]);
            if ($validator->fails()) {
                ResponseService::validationError($validator->errors()->first());
            }    
        }
        
        try {
            
            $users = [];
            if (Auth::user()) {
                $searchTerm = $request->search;
                DB::enableQueryLog();
                $users = User::select('id','first_name','last_name','image','school_id')->whereNot('id',Auth::user()->id)->where('status',1);

                if (Auth::user()->hasRole('Student')) { // Student login
                    $class_subject_ids = Auth::user()->student->selectedStudentSubjects()->pluck('class_subject_id')->toArray();
                    // Get teacher list
                    $class_teachers = ClassTeacher::where('class_section_id',Auth::user()->student->class_section_id)->pluck('teacher_id')->toArray();

                    $users = $users->where(function($query) use($class_teachers, $class_subject_ids, $searchTerm) {
                    // Filter by subject teachers
                    $query->whereHas('subjectTeachers', function($q) use($class_teachers, $class_subject_ids) {
                            $q->whereIn('class_subject_id', $class_subject_ids)
                            ->whereNotIn('teacher_id', $class_teachers);
                        })
                        // OR condition to also include class teachers
                        ->orWhereHas('class_teacher', function($q) use($class_teachers) {
                            $q->whereIn('teacher_id', $class_teachers);
                        })
                        // Add search condition for first name or last name
                        ->where(function($q) use ($searchTerm) {
                            $q->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                        });
                    })
                    // Eager load subject teachers and class teachers
                    ->with([
                        'subjectTeachers' => function($q) use($class_teachers, $class_subject_ids) {
                            $q->whereIn('class_subject_id', $class_subject_ids)
                            ->with('subject:id,name')
                            ->whereNotIn('teacher_id', $class_teachers);
                        },
                        'class_teacher.class_section.class.stream',
                        'class_teacher.class_section.section',
                        'class_teacher.class_section.medium'
                    ]);
                    // ->orWhereIn('id', $class_teachers);
                } else if(Auth::user()->hasRole('Teacher')) { // Teacher login
                    // Get guardian list
                    if ($request->role == 'Guardian') {
                        $validator = Validator::make($request->all(), [
                            'class_section_id' => 'required',
                        ]);
                        if ($validator->fails()) {
                            ResponseService::validationError($validator->errors()->first());
                        } 
                        $users = $users->role(['Guardian'])->whereHas('child',function($q) use($request) {
                            $q->where('school_id',Auth::user()->school_id)
                            ->where('class_section_id', $request->class_section_id);
                        })->with('child:id,user_id,guardian_id,class_section_id','child.user:id,first_name,last_name,image');

                    } else if($request->role == 'Staff') {
                        // Get staff list
                        $users = $users->where('school_id',Auth::user()->school_id)->has('staff');
                    }
                } else if(Auth::user()->hasRole('Guardian')) { // Guardian login
                    
                    $users = $users->where('school_id', Auth::user()->school_id)->has('staff');
                    
                    if ($request->child_id) {
                        $class_sections = Students::where('id',$request->child_id)->pluck('class_section_id')->toArray();
                    } else {
                        $child_ids = Auth::user()->load('child.user')->child->pluck('id')->toArray();
                        $class_sections = Students::whereIn('id',$child_ids)->pluck('class_section_id')->toArray();    
                    }
                    
                    $class_sections = array_filter($class_sections);

                    $class_teachers = ClassTeacher::whereIn('class_section_id',$class_sections)->pluck('teacher_id')->toArray();
                    $class_teachers = array_unique($class_teachers);
                    $class_teachers = array_values($class_teachers);

                    $subject_teachers = SubjectTeacher::whereIn('class_section_id',$class_sections)->pluck('teacher_id')->toArray();
                    $subject_teachers = array_unique($subject_teachers);
                    $subject_teachers = array_values($subject_teachers);

                    $teacher_ids = array_merge($class_teachers, $subject_teachers);
                    $teacher_ids = array_unique($teacher_ids);
                    $teacher_ids = array_values($teacher_ids);

                    $users = $users->whereIn('id',$teacher_ids)->with(['subjectTeachers' => function($q) use($class_sections){
                        $q->whereIn('class_section_id', $class_sections)
                        ->with('subject:id,name');
                    }])
                    ->where(function($query) use ($searchTerm) {
                        $query->where('first_name', 'like', '%' . $searchTerm . '%')
                              ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->with(['class_teacher' => function($q) use($class_sections) {
                        $q->whereIn('class_section_id',$class_sections)
                        ->with('class_section.class.stream','class_section.section','class_section.medium');
                    }])
                    ->has('staff');


                    // =====================
                    // $users = $users->whereHas('subjectTeachers', function($q) use($class_sections) {
                    //     $q->whereIn('class_section_id', $class_sections);
                    // })
                    // ->with(['subjectTeachers' => function($q) use($class_sections){
                    //     $q->whereIn('class_section_id', $class_sections)
                    //     ->with('subject:id,name');
                    // }])
                    // ->where(function($query) use ($searchTerm) {
                    //     $query->where('first_name', 'like', '%' . $searchTerm . '%')
                    //           ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                    // })
                    // ->orWhereHas('class_teacher',function($q) use($class_sections) {
                    //     $q->whereIn('class_section_id', $class_sections);
                    // })
                    // ->with(['class_teacher' => function($q) use($class_sections) {
                    //     $q->whereIn('class_section_id',$class_sections)
                    //     ->with('class_section.class.stream','class_section.section','class_section.medium');
                    // }])
                    // ->has('staff');


                } else { // Staff login
                    $users = $users->where('school_id',Auth::user()->school_id);
                }
                if ($request->search) {
                    $search = $request->search;
                    $users->where(function($query) use ($search) {
                        $query->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%");
                    });
                }
                $users = $users->whereHas('roles',function($q) use($request){
                    $q->where('name',$request->role);
                })->orderBy('first_name','ASC')->with('roles')->paginate(10);
            }
            
            ResponseService::successResponse("Data Fetched Successfully",$users);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function usersChatHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:Guardian,Staff,Student,Teacher',
        ], [
            'role.required' => 'The role field is mandatory. Please select a role.',
            'role.in' => 'The selected role is invalid. Valid roles are: Guardian, Staff, Student, Teacher.',
        ]);
        
        if ($validator->fails()) {
            return ResponseService::validationError($validator->errors()->first());
        }
        
        try {
            $data = [];
            if (Auth::user()) {
                $search = $request->search;
                $roles_Staff = Role::where('name',$request->role)->first();

                if(!$roles_Staff) {
                    return ResponseService::errorResponse('Please create a role first before creating a chat with that role name as '.$request->role);
                }
                
                if ($roles_Staff && $request->role == 'Staff' || $request->role == 'Teacher') {
                    // Staff
                    $data = Chat::where(function($q) {
                        $q->where('sender_id',Auth::user()->id)->orWhere('receiver_id',Auth::user()->id);
                    })
                    ->where(function($q) use($request) {
                        $q->whereHas('receiver', function($q) {
                            $q->whereHas('roles', function ($q) {
                                $q->whereNotIn('name', ['Student','Guardian']);
                            });
                        });
                    })
                    ->withCount(['message as unread_count' => function($q) {
                        $q->where('read_at',null)->whereNot('sender_id',Auth::user()->id);
                    }])
                    ->when($search, function ($q) use ($search) {
                        $q->whereHas('receiver', function($query) use($search) {
                            $query->where('first_name', 'LIKE', "%$search%")
                            ->orwhere('last_name', 'LIKE', "%$search%")
                            ->orWhereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'");
                        });
                    })
                    ->paginate(10);
                } else if ($request->role == 'Student' || $request->role == 'Guardian') {
                    // Guardian, Student
                    $data = Chat::where(function($q) {
                        $q->where('sender_id',Auth::user()->id)->orWhere('receiver_id',Auth::user()->id);
                    })
                    ->where(function($q) use($request) {
                        $q->whereHas('receiver', function($q) use($request) {
                            $q->role([$request->role]);
                        })
                        ->orWhereHas('sender', function($q) use($request) {
                            $q->role([$request->role]);
                        });
                    })
                    ->withCount(['message as unread_count' => function($q) {
                        $q->where('read_at',null)->whereNot('sender_id',Auth::user()->id);
                    }])
                    ->when($search, function ($q) use ($search) {
                        $q->whereHas('receiver', function($query) use($search) {
                            $query->where('first_name', 'LIKE', "%$search%")
                            ->orwhere('last_name', 'LIKE', "%$search%")
                            ->orWhereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'");
                        });
                    })
                    ->paginate(10);

                    // Apply filter after paginating
                    // $data->getCollection()->filter(function($chat) {
                    //     // Skip the chat row if the user attribute is null
                    //     return $chat->user !== null;
                    // });

                    // Return the filtered paginated results
                    $filteredData = $data->setCollection($data->getCollection()->filter(function($chat) {
                        return $chat->user !== null;
                    }));
                    $data->setCollection($filteredData->values());
                    // return $data;
                }
                
                
            }
            
            ResponseService::successResponse("Data Fetched Successfully",$data);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function classSectionTeachers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            
            $users = $this->user->builder()->role(['Teacher'])->select('id','first_name','last_name','image')
            ->whereHas('class_teacher',function($q) use($request) {
                $q->where('class_section_id', $request->class_section_id);
            })
            ->orWhereHas('subjectTeachers',function($q) use($request) {
                $q->where('class_section_id', $request->class_section_id);
            })
            // ->with(['class_teacher' => function($q) use($request) {
            //     $q->where('class_section_id', $request->class_section_id);
            // }])
            // ->with(['subjectTeachers' => function($q) use($request) {
            //     $q->where('class_section_id', $request->class_section_id);
            // }])
            ->get();
            
            ResponseService::successResponse("Data Fetched Successfully", $users);

        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function schoolDetails(Request $request)
    {
        try {
                $gallery_images = [];
                $school_code = $request->header('school-code');

                if ($school_code) {
                    $school = School::on('mysql')->where('code', $school_code)->first();
                    
                    if ($school) {
                        DB::setDefaultConnection('school');
                        Config::set('database.connections.school.database', $school->database_name);
                        DB::purge('school');
                        DB::connection('school')->reconnect();
                        DB::setDefaultConnection('school');
                         
                        $names = array('school_name', 'school_tagline','horizontal_logo');

                        $settings = $this->schoolSettings->getBulkData($names); 

                        $gallery = $this->gallery->builder()->with('file')->first();
                        if($gallery)
                        {
                            $gallery_images = $gallery->file->where('file_name', '!=' , 'YouTube Link')->pluck('file_url')->toArray();
                        }
                       
                        $schoolDetails = array(
                               'school_name' => $settings['school_name'],
                               'school_tagline' => $settings['school_tagline'],
                               'school_logo' => $settings['horizontal_logo'],
                               'school_images' => $gallery_images ?? []
                        );
                       
                    } else {
                        return response()->json(['message' => 'Invalid school code'], 400);
                    }

                } else {
                    return response()->json(['message' => 'Unauthenticated'], 400);
                }
               

            ResponseService::successResponse("Data Fetched Successfully", $schoolDetails);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function sendFeeNotification(Request $request)
    {
        try {

            $school_code = $request->header('school-code');
              
            if ($school_code) {
                $school = School::on('mysql')->where('code', $school_code)->first();
                
                if ($school) {
                    DB::setDefaultConnection('school');
                    Config::set('database.connections.school.database', $school->database_name);
                    DB::purge('school');
                    DB::connection('school')->reconnect();
                    DB::setDefaultConnection('school');
                     
                    $feesRemainderDuration = $this->schoolSettings->builder()->where('name','fees_remainder_duration')->value('data') ?? 2;
            
                    $feesRemainderDuration = (int) $feesRemainderDuration;
        
                    if (!$feesRemainderDuration) {
                        return response()->json(['message' => 'Remainder duration not found in settings'], 400);
                    }
                   
                    $classesWithDueDates = $this->fees->builder()->with('installments')->get();
                   
                    $today = Carbon::now();
                  
                    foreach ($classesWithDueDates as $classFee) {

                        $dueDate = Carbon::parse($classFee->due_date);
                        $class_section_id = $this->classSection->builder()->where('class_id',$classFee->class_id)->first();
                        $daysUntilDue = $today->diffInDays($dueDate, false); 
                      
                        if ($daysUntilDue <= $feesRemainderDuration && $daysUntilDue >= 0) {
                    
                            $user = $this->student->builder()->whereIn('class_section_id', $class_section_id)->pluck('guardian_id')->toArray();
                            $title = 'Fees Due Reminder';
                            $body = "Pay fees if you didn't paid !!";
                            $type = "fee-reminder"; 
                            send_notification($user, $title, $body, $type);
                        
                        }
                    }
                   
                } else {
                    return response()->json(['message' => 'Invalid school code'], 400);
                }

            } else {
                return response()->json(['message' => 'Unauthenticated'], 400);
            }

            
            ResponseService::successResponse("Notification Sent Successfully",);
    
        } catch (\Throwable $e) {
            ResponseService::logErrorResponse($e);
            return ResponseService::errorResponse();
        }
    }

    

    public function paymentStatus(Request $request)
    {
        Log::info('Payment Status Callback:', $request->all());
        ResponseService::successResponse("Payment Status Callback.", $request->all());
    }

    public function flutterwaveFeesWebhook(Request $request)
    {
        Log::info('Flutterwave Fees Webhook:', $request->all());
        ResponseService::successResponse("Flutterwave Fees Webhook received.");
    }

    public function flutterwaveSuccessCallback() {
        Log::info('Flutterwave Successfully.');
        ResponseService::successResponse("Flutterwave Successfully.");
    }
    
    public function flutterwaveCancelCallback() {
        Log::info('Flutterwave Payment Canceled.');
        ResponseService::successResponse("Flutterwave Payment Canceled.");
    }
    
    
}
