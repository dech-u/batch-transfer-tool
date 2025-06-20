<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SystemSettingsController;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Announcement\AnnouncementInterface;
use App\Repositories\AnnouncementClass\AnnouncementClassInterface;
use App\Repositories\Attendance\AttendanceInterface;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ExamResult\ExamResultInterface;
use App\Repositories\Expense\ExpenseInterface;
use App\Repositories\Fees\FeesInterface;
use App\Repositories\FeesPaid\FeesPaidInterface;
use App\Repositories\Files\FilesInterface;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveMaster\LeaveMasterInterface;
use App\Repositories\Notification\NotificationInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Staff\StaffInterface;
use App\Repositories\StaffSalary\StaffSalaryInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Repositories\Timetable\TimetableInterface;
use App\Repositories\User\UserInterface;
use App\Services\CachingService;
use App\Services\FeaturesService;
use App\Services\ResponseService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;
use PHPUnit\Framework\Constraint\Count;

class StaffApiController extends Controller
{
    //

    private ExpenseInterface $expense;
    private SchoolSettingInterface $schoolSetting;
    private CachingService $cache;
    private LeaveInterface $leave;
    private UserInterface $user;
    private StudentInterface $student;
    private TimetableInterface $timetable;
    private ClassSectionInterface $classSection;
    private AnnouncementInterface $announcement;
    private AnnouncementClassInterface $announcementClass;
    private FilesInterface $files;
    private AttendanceInterface $attendance;
    private NotificationInterface $notification;
    private FeesInterface $fees;
    private LeaveMasterInterface $leaveMaster;
    private ExamResultInterface $examResult;
    private FeaturesService $featureService;
    private SessionYearInterface $sessionYearInterface;
    private StaffInterface $staff;
    private FeesPaidInterface $feesPaid;
    private SystemSettingInterface $systemSetting;
    private SchoolSettingInterface $schoolSettings;
    private StaffSalaryInterface $staffSalary;

    public function __construct(ExpenseInterface $expense, SchoolSettingInterface $schoolSetting, CachingService $cache, LeaveInterface $leave, UserInterface $user, StudentInterface $student, TimetableInterface $timetable, ClassSectionInterface $classSection, AnnouncementInterface $announcement, AnnouncementClassInterface $announcementClass, FilesInterface $files, AttendanceInterface $attendance, NotificationInterface $notification, FeesInterface $fees, LeaveMasterInterface $leaveMaster, ExamResultInterface $examResult, FeaturesService $featureService, SessionYearInterface $sessionYearInterface, StaffInterface $staff, FeesPaidInterface $feesPaid, SystemSettingInterface $systemSetting, SchoolSettingInterface $schoolSettings, StaffSalaryInterface $staffSalary)
    {
        $this->expense = $expense;
        $this->schoolSetting = $schoolSetting;
        $this->cache = $cache;
        $this->leave = $leave;
        $this->user = $user;
        $this->student = $student;
        $this->timetable = $timetable;
        $this->classSection = $classSection;
        $this->announcement = $announcement;
        $this->announcementClass = $announcementClass;
        $this->files = $files;
        $this->attendance = $attendance;
        $this->notification = $notification;
        $this->fees = $fees;
        $this->leaveMaster = $leaveMaster;
        $this->examResult = $examResult;
        $this->featureService = $featureService;
        $this->sessionYearInterface = $sessionYearInterface;
        $this->staff = $staff;
        $this->feesPaid = $feesPaid;
        $this->systemSetting = $systemSetting;
        $this->schoolSettings = $schoolSettings;
        $this->staffSalary = $staffSalary;
    }

    public function myPayroll(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Expense Management');
        try {

            $sql = $this->expense->builder()->select('id', 'staff_id', 'basic_salary', 'paid_leaves', 'month', 'year', 'title', 'amount', 'date', 'session_year_id')->where('staff_id', Auth::user()->staff->id)
                ->when($request->year, function ($q) use ($request) {
                    $q->whereYear('date', $request->year);
                })->with('staff', 'staff.staffSalary.payrollSetting',);


            $sql = $this->expense->builder()->select('id', 'staff_id', 'basic_salary', 'paid_leaves', 'month', 'year', 'title', 'amount', 'date', 'session_year_id')->where('staff_id', Auth::user()->staff->id)
                ->when($request->year, function ($q) use ($request) {
                    $q->whereYear('date', $request->year);
                })->with('staff');

            if ($request->session_year_id) {
                $sql = $sql->where('session_year_id', $request->session_year_id);
            }

            $sql = $sql->get();


            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function myPayrollSlip(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Expense Management');
        $validator = Validator::make($request->all(), [
            'slip_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $vertical_logo = $this->schoolSetting->builder()->where('name', 'vertical_logo')->first();
            $schoolSetting = $this->cache->getSchoolSettings();

            // Salary
            $salary = $this->expense->builder()->with('staff.user:id,first_name,last_name')->where('id', $request->slip_id)->first();
            if (!$salary) {
                ResponseService::successResponse('no_data_found');
            }
            // Get total leaves
            $leaves = $this->leave->builder()->where('status', 1)->where('user_id', $salary->staff->user_id)->withCount(['leave_detail as full_leave' => function ($q) use ($salary) {
                $q->whereMonth('date', $salary->month)->whereYear('date', $salary->year)->where('type', 'Full');
            }])->withCount(['leave_detail as half_leave' => function ($q) use ($salary) {
                $q->whereMonth('date', $salary->month)->whereYear('date', $salary->year)->whereNot('type', 'Full');
            }])->get();

            $total_leaves = $leaves->sum('full_leave') + ($leaves->sum('half_leave') / 2);
            // Total days
            $days = Carbon::now()->year($salary->year)->month($salary->month)->daysInMonth;

            $allow_leaves = 0;
            if ($leaves->first()) {
                $allow_leaves = $leaves->first()->leave_master->leaves;
            }

            $pdf = PDF::loadView('payroll.slip', compact('vertical_logo', 'schoolSetting', 'salary', 'total_leaves', 'days','allow_leaves'))->output();

            return $response = array(
                'error' => false,
                'pdf'   => base64_encode($pdf),
            );



            // return $pdf->stream($salary->title.'-'.$salary->staff->user->full_name.'.pdf');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function storePayroll(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Expense Management');
        $validator = Validator::make($request->all(), [
            'month' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'year' => 'required',
            'payroll' => 'required',
            "allowed_leaves" => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            DB::beginTransaction();
            $month = $request->month;
            $year = $request->year;
            $startDate = Carbon::createFromFormat('Y-m', "$year-$month")->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $sessionYearInterface = $this->sessionYearInterface->builder()->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $startDate);
                });
            })->first();

            if (!$sessionYearInterface) {
                ResponseService::errorResponse('Session year not found');
            }

            $date = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth()->format('Y-m-d');
            $title = Carbon::create()->month($request->month)->format('F') . ' - ' . $request->year;
            $data = array();
            foreach ($request->payroll as $key => $payroll) {
                $payroll = (object)$payroll;
                $data[] = [
                    'staff_id' => $payroll->staff_id,
                    'basic_salary' => $payroll->basic_salary,
                    'paid_leaves' => $request->allowed_leaves,
                    'month' => $request->month,
                    'year' => $request->year,
                    'title' => $title,
                    'description' => 'Salary',
                    'amount' => $payroll->amount,
                    'date' => $date,
                    'session_year_id' => $sessionYearInterface->id,
                ];
            }

            $this->expense->upsert($data, ['staff_id', 'month', 'year'], ['amount', 'session_year_id', 'basic_salary', 'date', 'title', 'description', 'paid_leaves']);
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function payrollYear()
    {
        ResponseService::noFeatureThenSendJson('Expense Management');
        try {
            $sessionYear = $this->sessionYearInterface->builder()->orderBy('start_date', 'ASC')->first();
            $sessionYear = date('Y', strtotime($sessionYear->start_date));

            $current_year = Carbon::now()->format('Y');
            $sql = range($sessionYear, $current_year);

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function staffPayrollList(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Expense Management');
        $validator = Validator::make($request->all(), [
            'month' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'year' => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $month = $request->month;
            $year = $request->year;
            $search = null;

            $leaveMaster = $this->leaveMaster->builder()->whereHas('session_year', function ($q) use ($month, $year) {
                $q->where(function ($q) use ($month, $year) {
                    $q->whereMonth('start_date', '<=', $month)->whereYear('start_date', $year);
                })->orWhere(function ($q) use ($month, $year) {
                    $q->whereMonth('start_date', '>=', $month)->whereYear('end_date', '<=', $year);
                });
            })->first();

    
            $sql = $this->staff->builder()->with(['user:id,first_name,last_name,image','staffSalary.payrollSetting', 'expense:id,staff_id,basic_salary,paid_leaves,month,year,title,amount,date', 'leave' => function ($q) use ($month,$year) {
                $q->where('status', 1)->withCount(['leave_detail as full_leave' => function ($q) use ($month,$year) {
                    $q->whereMonth('date', $month)->whereYear('date',$year)->where('type', 'Full');
                }])->withCount(['leave_detail as half_leave' => function ($q) use ($month,$year) {
                    $q->whereMonth('date', $month)->whereYear('date',$year)->whereNot('type', 'Full');

                }])->with(['leave_detail' => function ($q) use ($month, $year) {
                        $q->whereMonth('date', $month)->whereYear('date', $year);
                    }]);
            }, 'expense' => function ($q) use ($month, $year) {
                $q->where('month', $month)->where('year', $year)
                    ->with('staff_payroll.payroll_setting');
            }])

                ->whereHas('user', function ($q) {
                    $q->Owner();
                })->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->orwhereHas('user', function ($q) use ($search) {
                            $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%");
                        });
                    });
                })->get();

            ResponseService::successResponse('Data Fetched Successfully', $sql, ['leave_master' => $leaveMaster]);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function profile()
    {
        try {
            $sql = $this->user->findById(Auth::user()->id, ['*'], ['staff']);

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function counter()
    {
        try {
            $students = $this->student->builder()->whereHas('user', function ($q) {
                $q->withTrashed();
            })->withTrashed()->count();

            $teachers = $this->user->builder()->role('Teacher')->withTrashed()->count();

            $staffs = $this->user->builder()->whereHas('roles', function ($q) {
                $q->where('custom_role', 1)->whereNot('name', 'Teacher');
            })->withTrashed()->count();

            $leaves = $this->leave->builder()->where('status', 0)->count();
            $data = [
                'students' => $students,
                'teachers' => $teachers,
                'staffs' => $staffs,
                'leaves' => $leaves
            ];
            ResponseService::successResponse('Data Fetched Successfully', $data);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function teacher(Request $request)
    {
        ResponseService::noAnyPermissionThenSendJson(['teacher-list', 'staff-list']);
        try {
            if ($request->teacher_id) {
                $sql = $this->user->findById($request->teacher_id, ['*'], ['staff']);
            } else {
                $sql = $this->user->builder()->role('Teacher')->with('staff');
                if ($request->search) {
                    $sql->where(function ($q) use ($request) {
                        $q->where('first_name', 'LIKE', "%$request->search%")
                            ->orwhere('last_name', 'LIKE', "%$request->search%")
                            ->orwhere('mobile', 'LIKE', "%$request->search%")
                            ->orwhere('email', 'LIKE', "%$request->search%")
                            ->orwhere('gender', 'LIKE', "%$request->search%")
                            ->orWhereRaw('concat(first_name," ",last_name) like ?', "%$request->search%");
                    });
                }

                if ($request->status != 1) {
                    if ($request->status == 2) {
                        $sql->onlyTrashed();
                    } else if ($request->status == 0) {
                        $sql->withTrashed();
                    } else {
                        $sql->withTrashed();
                    }
                }


                $sql = $sql->get();
            }
            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function teacherTimetable(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Timetable Management');
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $timetable = $this->timetable->builder()
                ->whereHas('subject_teacher', function ($q) use ($request) {
                    $q->where('teacher_id', $request->teacher_id);
                })
                ->with('class_section.class.stream', 'class_section.section', 'subject')->orderBy('start_time', 'ASC')->get();

            ResponseService::successResponse('Data Fetched Successfully', $timetable);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function staff(Request $request)
    {
        ResponseService::noAnyPermissionThenSendJson(['teacher-list', 'staff-list']);
        try {
            if ($request->staff_id) {
                $sql = $this->user->builder()->whereHas('roles', function ($q) {
                    $q->where('custom_role', 1)->whereNot('name', 'Teacher');
                })->with('staff', 'roles')->where('id', $request->staff_id)->first();
            } else {
                $sql = $this->user->builder()->whereHas('roles', function ($q) {
                    $q->where('custom_role', 1)->whereNot('name', 'Teacher');
                })->with('staff', 'roles')->withTrashed();

                if ($request->status != 1) {
                    if ($request->status == 2) {
                        $sql->onlyTrashed();
                    } else if ($request->status == 0) {
                        $sql->withTrashed();
                    } else {
                        $sql->withTrashed();
                    }
                } else {
                    $sql->where('status', 1);
                }

                if ($request->search) {
                    $sql->where(function ($q) use ($request) {
                        $q->where('first_name', 'LIKE', "%$request->search%")
                            ->orwhere('last_name', 'LIKE', "%$request->search%")
                            ->orwhere('mobile', 'LIKE', "%$request->search%")
                            ->orwhere('email', 'LIKE', "%$request->search%")
                            ->orwhere('gender', 'LIKE', "%$request->search%")
                            ->orWhereRaw('concat(first_name," ",last_name) like ?', "%$request->search%");
                    });
                }

                $sql = $sql->get();
            }

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function leaveRequest(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        ResponseService::noPermissionThenSendJson('approve-leave');
        try {
            if ($request->leave_id) {
                $sql = $this->leave->findById($request->leave_id, ['*'], ['user:id,first_name,last_name,image,email,mobile', 'leave_detail', 'file'])->orderBy('created_at','DESC')->get();
            } else {
                $sql = $this->leave->builder()->where('status', 0)->with('user:id,first_name,last_name,image,email,mobile', 'leave_detail', 'file')->orderBy('created_at','DESC')->get();
            }
            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function leaveApprove(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        ResponseService::noPermissionThenSendJson('approve-leave');
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
            'status' => 'required|in:0,1,2',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $leave = $this->leave->update($request->leave_id, ['status' => $request->status]);

            $user[] = $leave->user_id;

            $type = "Leave";
            if ($request->status == 1) {
                $title = 'Approved';
                $body = 'Your Leave Request Has Been Approved!';
                send_notification($user, $title, $body, $type);
            }
            if ($request->status == 2) {
                $title = 'Rejcted';
                $body = 'Your Leave Request Has Been Rejcted!';
                send_notification($user, $title, $body, $type);
            }

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (\Throwable $e) {
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

    public function leaveDelete(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        ResponseService::noPermissionThenSendJson('approve-leave');
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $this->leave->deleteById($request->leave_id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getAnnouncement(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-list');
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            $sql = $this->announcement->builder()->whereHas('announcement_class', function ($q) use ($request) {
                $q->where('class_section_id', $request->class_section_id);
            })->with('announcement_class')->where('session_year_id', $sessionYear->id)->with('file')->paginate(10);
            DB::commit();
            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function sendAnnouncement(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-create');
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            $announcementData = array(
                'title'           => $request->title,
                'description'     => $request->description,
                'session_year_id' => $sessionYear->id,
            );

            $announcement = $this->announcement->create($announcementData); // Store Data
            $announcementClassData = array();

            $notifyUser = $this->student->builder()->select('user_id')->whereIn('class_section_id', $request->class_section_id)->get()->pluck('user_id'); // Get the Student's User ID of Specified Class for Notification

            // Set class sections
            foreach ($request->class_section_id as $class_section) {
                $announcementClassData[] = [
                    'announcement_id'  => $announcement->id,
                    'class_section_id' => $class_section
                ];
            }
            $title = trans('New announcement'); // Title for Notification
            $this->announcementClass->upsert($announcementClassData, ['announcement_id', 'class_section_id', 'school_id'], ['announcement_id', 'class_section_id', 'school_id', 'class_subject_id']);

            // If File Exists
            if ($request->hasFile('file')) {
                $fileData = array(); // Empty FileData Array
                $fileInstance = $this->files->model(); // Create A File Model Instance
                $announcementModelAssociate = $fileInstance->modal()->associate($announcement); // Get the Association Values of File with Announcement
                foreach ($request->file as $file_upload) {
                    // Create Temp File Data Array
                    $tempFileData = array(
                        'modal_type' => $announcementModelAssociate->modal_type,
                        'modal_id'   => $announcementModelAssociate->modal_id,
                        'file_name'  => $file_upload->getClientOriginalName(),
                        'type'       => 1,
                        'file_url'   => $file_upload
                    );
                    $fileData[] = $tempFileData; // Store Temp File Data in Multi-Dimensional File Data Array
                }
                $this->files->createBulk($fileData); // Store File Data
            }

            if ($notifyUser !== null && !empty($title)) {
                $type = trans('Class Section'); // Get The Type for Notification
                $body = $request->title; // Get The Body for Notification
                send_notification($notifyUser, $title, $body, $type); // Send Notification
            }

            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (\Throwable $e) {
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

    public function updateAnnouncement(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-edit');
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
            'title' => 'required',
            'announcement_id' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            $announcementData = array(
                'title'           => $request->title,
                'description'     => $request->description,
                'session_year_id' => $sessionYear->id,
            );

            $announcement = $this->announcement->update($request->announcement_id, $announcementData); // Store Data
            $announcementClassData = array();

            $oldClassSection = $this->announcement->findById($request->announcement_id)->announcement_class->pluck('class_section_id')->toArray();

            // When only Class Section is passed
            $notifyUser = $this->student->builder()->select('user_id')->whereIn('class_section_id', $request->class_section_id)->get()->pluck('user_id'); // Get the Student's User ID of Specified Class for Notification


            // Set class sections
            foreach ($request->class_section_id as $class_section) {
                $announcementClassData[] = [
                    'announcement_id'  => $announcement->id,
                    'class_section_id' => $class_section
                ];
                // Check class section
                $key = array_search($class_section, $oldClassSection);
                if ($key !== false) {
                    unset($oldClassSection[$key]);
                }
            }
            $title = trans('Updated announcement'); // Title for Notification

            $this->announcementClass->upsert($announcementClassData, ['announcement_id', 'class_section_id', 'school_id'], ['announcement_id', 'class_section_id', 'school_id', 'class_subject_id']);

            // Delete announcement class sections
            $this->announcementClass->builder()->where('announcement_id', $request->announcement_id)->whereIn('class_section_id', $oldClassSection)->delete();


            // If File Exists
            if ($request->hasFile('file')) {
                $fileData = array(); // Empty FileData Array
                $fileInstance = $this->files->model(); // Create A File Model Instance
                $announcementModelAssociate = $fileInstance->modal()->associate($announcement); // Get the Association Values of File with Announcement
                foreach ($request->file as $file_upload) {
                    // Create Temp File Data Array
                    $tempFileData = array(
                        'modal_type' => $announcementModelAssociate->modal_type,
                        'modal_id'   => $announcementModelAssociate->modal_id,
                        'file_name'  => $file_upload->getClientOriginalName(),
                        'type'       => 1,
                        'file_url'   => $file_upload
                    );
                    $fileData[] = $tempFileData; // Store Temp File Data in Multi-Dimensional File Data Array
                }
                $this->files->createBulk($fileData); // Store File Data
            }

            if ($notifyUser !== null && !empty($title)) {
                $type = $request->aissgn_to; // Get The Type for Notification
                $body = $request->title; // Get The Body for Notification
                // send_notification($notifyUser, $title, $body, $type); // Send Notification
            }

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (\Throwable $e) {
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

    public function deleteAnnouncement(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-delete');
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $this->announcement->deleteById($request->announcement_id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function studentAttendance(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Attendance Management');
        ResponseService::noPermissionThenSendJson('attendance-list');
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
            'date' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $sql = $this->attendance->builder()->where('class_section_id', $request->class_section_id)->whereDate('date', $request->date)->with('user:id,first_name,last_name,image', 'user.student:id,user_id,roll_number');

            if (isset($request->status)) {
                $sql = $sql->where('type', $request->status);
            }
            $sql = $sql->paginate(10);

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getRoles()
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-list');

        try {
            $reserveRole = [
                'Super Admin',
                'School Admin',
                'Teacher',
                'Guardian',
                'Student'
            ];
            $sql = Role::orderBy('id', 'DESC')->whereNotIn('name', $reserveRole)->get();

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getUsers(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-list');

        try {
            $search = $request->search;

            $roles = Role::whereNot('name', 'Guardian')->pluck('name');
            $user_ids = $this->user->guardian()->with('roles')->select('id', 'first_name', 'last_name', 'school_id')
                ->whereHas('child.user', function ($q) {
                    $q->owner();
                })->orWhere(function ($q) use ($roles) {
                    $q->where('school_id', Auth::user()->school_id)
                        ->whereHas('roles', function ($q) use ($roles) {
                            $q->whereIn('name', $roles);
                        });
                })
                ->pluck('id');

            $sql = User::whereIn('id', $user_ids)->with('roles')->select('id', 'first_name', 'last_name', 'school_id')
                ->when($search, function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")
                        ->orwhere('last_name', 'LIKE', "%$search%")
                        ->orWhereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'");
                })
                ->paginate(10);

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function storeNotification(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-create');
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'message' => 'required',
            'type' => 'required|in:All users,Specific users,Over Due Fees,Roles',
            'user_id.*' => 'required_if:type,Specific users',
            'roles.*' => 'required_if:type,Roles',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            $data = [
                'title' => $request->title,
                'message' => $request->message,
                'send_to' => $request->type,
                'image' => $request->hasFile('file') ? $request->file('file')->store('notification', 'public') : null,
                'session_year_id' => $sessionYear->id
            ];
            $notification = $this->notification->create($data);

            $notifyUser = [];

            // if ($request->send_to == 'All users') {
            //     $notifyUser = $this->user->builder()->role(['Student','Guardian'])->pluck('id');
            // } else if($request->send_to == 'Students') {
            //     $notifyUser = $this->user->builder()->role('Student')->pluck('id');
            // } else if($request->send_to == 'Guardian') {
            //     $notifyUser = $this->user->builder()->role('Guardian')->pluck('id');
            // } else if($request->send_to == 'Over Due Fees') {
            //     // Over due fees
            //     $today = Carbon::now()->format('Y-m-d');
            //     $student_ids = array();
            //     $guardian_ids = array();
            //     $fees = $this->fees->builder()->whereDate('due_date','<',$today)->get();

            //     foreach ($fees as $key => $fee) {
            //         $sql = $this->user->builder()->role('Student')->select('id', 'first_name', 'last_name')->with([
            //             'fees_paid'     => function ($q) use ($fee) {
            //                 $q->where('fees_id', $fee->id);
            //             },
            //             'student:id,guardian_id,user_id','student.guardian:id'])->whereHas('student.class_section', function ($q) use ($fee) {
            //             $q->where('class_id', $fee->class_id);
            //         })->whereDoesntHave('fees_paid', function ($q) use ($fee) {
            //             $q->where('fees_id', $fee->id);
            //         })->orWhereHas('fees_paid', function ($q) use ($fee) {
            //             $q->where(['fees_id' => $fee->id, 'is_fully_paid' => 0]);
            //         });
            //         $student_ids[] = $sql->pluck('id')->toArray();
            //         $guardian_ids[] = $sql->get()->pluck('student.guardian_id')->toArray();
            //     }

            //     $student_ids = array_merge(...$student_ids);
            //     $guardian_ids = array_merge(...$guardian_ids);
            //     $notifyUser = array_merge($student_ids, $guardian_ids);
            // } else {
            //     $notifyUser = $request->user_id;
            // }

            // ====================================================

            if ($request->type == 'All users') {
                // All
                $roles = Role::whereNot('name', 'Guardian')->pluck('name');
                $users = $this->user->guardian()->with('roles')->whereHas('child.user', function ($q) {
                    $q->owner();
                })->orWhere(function ($q) use ($roles) {
                    $q->where('school_id', Auth::user()->school_id)
                        ->whereHas('roles', function ($q) use ($roles) {
                            $q->whereIn('name', $roles);
                        });
                })->get();

                $notifyUser = $users->pluck('id')->toArray();
            } else if ($request->type == 'Specific users') {
                // Specific
                $notifyUser = $request->user_id;
            } else if ($request->type == 'Over Due Fees') {
                // Over due fees
                $today = Carbon::now()->format('Y-m-d');
                $student_ids = array();
                $guardian_ids = array();
                $fees = $this->fees->builder()->whereDate('due_date', '<', $today)->get();

                foreach ($fees as $key => $fee) {
                    $sql = $this->user->builder()->role('Student')->select('id', 'first_name', 'last_name')->with([
                        'fees_paid'     => function ($q) use ($fee) {
                            $q->where('fees_id', $fee->id);
                        },
                        'student:id,guardian_id,user_id', 'student.guardian:id'
                    ])->whereHas('student.class_section', function ($q) use ($fee) {
                        $q->where('class_id', $fee->class_id);
                    })->whereDoesntHave('fees_paid', function ($q) use ($fee) {
                        $q->where('fees_id', $fee->id);
                    })->orWhereHas('fees_paid', function ($q) use ($fee) {
                        $q->where(['fees_id' => $fee->id, 'is_fully_paid' => 0]);
                    });
                    $student_ids[] = $sql->pluck('id')->toArray();
                    $guardian_ids[] = $sql->get()->pluck('student.guardian_id')->toArray();
                }

                $student_ids = array_merge(...$student_ids);
                $guardian_ids = array_merge(...$guardian_ids);
                $notifyUser = array_merge($student_ids, $guardian_ids);
            } else if ($request->type == 'Roles') {
                $guardian_ids = [];
                if (in_array('Guardian', $request->roles)) {
                    $guardian_ids = $this->user->guardian()->with('roles')->whereHas('child.user', function ($q) {
                        $q->owner();
                    })->pluck('id')->toArray();
                    $roles = array_diff($request->roles, ["Guardian"]);
                    $notifyUser = $this->user->builder()->role($roles)->pluck('id')->toArray();
                } else {
                    $notifyUser = $this->user->builder()->role($request->roles)->pluck('id')->toArray();
                }
                $notifyUser = array_merge($guardian_ids, $notifyUser);
            }


            // ====================================================

            $customData = [];
            if ($notification->image) {
                $customData = [
                    'image' => $notification->image
                ];
            }
            $title = $request->title; // Title for Notification
            $body = $request->message;
            $type = 'Notification';

            DB::commit();
            send_notification($notifyUser, $title, $body, $type, $customData); // Send Notification

            ResponseService::successResponse('Notification Send Successfully');
        } catch (\Throwable $e) {
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

    public function getNotification()
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-list');
        try {
            $sessionYear = $this->cache->getDefaultSessionYear();
            $sql = $this->notification->builder()->where('session_year_id', $sessionYear->id)->orderBy('id', 'DESC')->paginate(10);

            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function deleteNotification(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Announcement Management');
        ResponseService::noPermissionThenSendJson('announcement-delete');
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $this->notification->deleteById($request->notification_id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getFees()
    {
        ResponseService::noFeatureThenSendJson('Fees Management');
        ResponseService::noPermissionThenSendJson('fees-list');
        try {
            $sql = $this->fees->builder()->select(['id', 'name'])->get();
            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getFeesPaidList(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Fees Management');
        ResponseService::noPermissionThenSendJson('fees-paid');
        $validator = Validator::make($request->all(), [
            'session_year_id' => 'required',
            'fees_id' => 'required'
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {

            // $fees = $this->fees->findById($request->fees_id, ['*'], ['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,fees_id','fees_paid' => function($q) {
            //     $q->withSum('compulsory_fee','amount')
            //     ->withSum('optional_fee','amount');
            // }]);

            $fees = $this->fees->builder()->where('id', $request->fees_id)->where('session_year_id', $request->session_year_id)->with(['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,fees_id', 'fees_paid' => function ($q) {
                $q->withSum('compulsory_fee', 'amount')
                    ->withSum('optional_fee', 'amount');
            }])->first();

            if (!$fees) {
                ResponseService::successResponse('No Data Found');
            }

            $sql = $this->user->builder()->role('Student')->select('id', 'first_name', 'last_name')->with([
                'student'          => function ($query) {
                    $query->select('id', 'class_section_id', 'user_id')->with(['class_section' => function ($query) {
                        $query->select('id', 'class_id', 'section_id', 'medium_id')->with('class:id,name', 'section:id,name', 'medium:id,name');
                    }]);
                }, 'optional_fees' => function ($query) {
                    $query->with('fees_class_type');
                }, 'fees_paid'     => function ($q) use ($fees) {
                    $q->where('fees_id', $fees->id);
                },
                'compulsory_fees'
            ])->whereHas('student.class_section', function ($q) use ($fees) {
                $q->where('class_id', $fees->class_id);
            });


            if ($request->status == 0) {
                $sql->whereDoesntHave('fees_paid', function ($q) use ($fees) {
                    $q->where('fees_id', $fees->id);
                })->orWhereHas('fees_paid', function ($q) use ($fees) {
                    $q->where(['fees_id' => $fees->id, 'is_fully_paid' => 0]);
                });
            } else {
                $sql->whereHas('fees_paid', function ($q) use ($fees) {
                    $q->where(['fees_id' => $fees->id, 'is_fully_paid' => 1]);
                });
            }



            $sql = $sql->paginate(10);

            ResponseService::successResponse('Data Fetched Successfully', $sql, [
                'compolsory_fees' => $fees->total_compulsory_fees,
                'optional_fees' => $fees->total_optional_fees,
            ]);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getOfflineExamResult(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Exam Management');
        ResponseService::noPermissionThenSendJson('exam-result');
        $validator = Validator::make($request->all(), [
            'session_year_id' => 'required',
            'exam_id' => 'required',
            'class_section_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {

            $sql = $this->examResult->builder()->with(['user:id,first_name,last_name,school_id', 'user.exam_marks' => function ($q) use ($request) {
                $q->whereHas('timetable', function ($q) use ($request) {
                    $q->where('exam_id', $request->exam_id);
                })->with('timetable', 'subject');
            }])
                ->where('exam_id', $request->exam_id)
                ->where('session_year_id', $request->session_year_id)
                ->where('class_section_id', $request->class_section_id)->with('exam:id,name,description,start_date,end_date');

            if ($request->student_id) {
                $sql = $sql->where('student_id', $request->student_id);
            }


            $sql = $sql->paginate(10);


            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getFeaturesPermissions()
    {
        try {
            if (Auth::user()) {
                $features = $this->featureService->getFeatures();
                if (count($features) == 0) {
                    $features = null;
                }
                $permissions = Auth::user()->getAllPermissions()->pluck('name');
                $data = [
                    'features' => $features,
                    'permissions' => $permissions
                ];

                ResponseService::successResponse('Data Fetched Successfully', $data);    
            } else {
                ResponseService::errorResponse(trans('your_account_has_been_deactivated_please_contact_admin'), null, config('constants.RESPONSE_CODE.INACTIVATED_USER'));
            }
            
            
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function getClassTimetable(Request $request)
    {

        ResponseService::noFeatureThenSendJson('Timetable Management');
        ResponseService::noPermissionThenSendJson('timetable-list');
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $sql = $this->timetable->builder()->where('class_section_id', $request->class_section_id)
                ->with('class_section.class.stream', 'class_section.section', 'class_section.medium', 'subject', 'subject_teacher.teacher')
                ->orderBy('start_time')->get();
            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function feesReceipt(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Fees Management');
        ResponseService::noPermissionThenSendJson('fees-paid');
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'fees_id' => 'required',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {


            $feesPaid = $this->feesPaid->builder()->where('student_id', $request->student_id)->where('fees_id', $request->fees_id)->with([
                'fees.fees_class_type.fees_type',
                'compulsory_fee.installment_fee:id,name',
                'optional_fee' => function ($q) {
                    $q->with([
                        'fees_class_type' => function ($q) {
                            $q->select('id', 'fees_type_id')->with('fees_type:id,name');
                        }
                    ]);
                }
            ])->firstOrFail();
            $student = $this->student->builder()->with('user:id,first_name,last_name')->whereHas('user', function ($q) use ($feesPaid) {
                $q->where('id', $feesPaid->student_id);
            })->firstOrFail();

            $systemVerticalLogo = $this->systemSetting->builder()->where('name', 'vertical_logo')->first();
            $schoolVerticalLogo = $this->schoolSettings->builder()->where('name', 'vertical_logo')->first();
            $school = $this->cache->getSchoolSettings();

            //            return view('fees.fees_receipt', compact('systemLogo', 'school', 'feesPaid', 'student'));
            $pdf = Pdf::loadView('fees.fees_receipt', compact('systemVerticalLogo', 'school', 'feesPaid', 'student', 'schoolVerticalLogo'))->output();

            return $response = array(
                'error' => false,
                'pdf'   => base64_encode($pdf),
            );
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

    public function allowancesDeductions()
    {

        try {
            $sql = Auth::user()->load('staff.staffSalary.payrollSetting');
            
            ResponseService::successResponse('Data Fetched Successfully', $sql);
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }
}
