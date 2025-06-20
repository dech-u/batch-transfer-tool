<?php

namespace App\Http\Controllers;

use App\Repositories\Expense\ExpenseInterface;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveMaster\LeaveMasterInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Staff\StaffInterface;
use App\Repositories\StaffPayroll\StaffPayrollInterface;
use App\Repositories\StaffSalary\StaffSalaryInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDF;
use Throwable;

class PayrollController extends Controller {
    private SessionYearInterface $sessionYear;
    private StaffInterface $staff;
    private ExpenseInterface $expense;
    private LeaveMasterInterface $leaveMaster;
    private CachingService $cache;
    private SchoolSettingInterface $schoolSetting;
    private LeaveInterface $leave;
    private SessionYearInterface $sessionYearInterface;
    private StaffSalaryInterface $staffSalary;
    private StaffPayrollInterface $staffPayroll;

    public function __construct(SessionYearInterface $sessionYear, StaffInterface $staff, ExpenseInterface $expense, LeaveMasterInterface $leaveMaster, CachingService $cache, SchoolSettingInterface $schoolSetting, LeaveInterface $leave, SessionYearInterface $sessionYearInterface, StaffSalaryInterface $staffSalary, StaffPayrollInterface $staffPayroll) {
        $this->sessionYear = $sessionYear;
        $this->staff = $staff;
        $this->expense = $expense;
        $this->leaveMaster = $leaveMaster;
        $this->cache = $cache;
        $this->schoolSetting = $schoolSetting;
        $this->leave = $leave;
        $this->sessionYearInterface = $sessionYearInterface;
        $this->staffSalary = $staffSalary;
        $this->staffPayroll = $staffPayroll;
    }

    public function index() {
        //
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-list');

        $sessionYear = $this->sessionYear->builder()->orderBy('start_date', 'ASC')->first();
        $sessionYear = date('Y', strtotime($sessionYear->start_date));
        // Get months starting from session year
        $months = sessionYearWiseMonth();
        

        return view('payroll.index', compact('sessionYear', 'months'));
    }

    public function create() {
        //
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-create');
    }

    public function store(Request $request) {
   
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-create');

        $request->validate([
            'net_salary' => 'required',
            'date'       => 'required',
            'user_id' => 'required'
        ], [
            'net_salary.required' => trans('no_records_found'),
            'user_id.required' => trans('Please select at least one record')
        ]);

        try {
            DB::beginTransaction();
            $user_ids = explode(",",$request->user_id);
            
            $selectedMonth = $request->month;
            $selectedYear = $request->year;
            // Define the start and end dates
            $startDate = Carbon::createFromFormat('Y-m', "$selectedYear-$selectedMonth")->startOfMonth();
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

            $data = array();
            $staff_payroll_data  = array();
            foreach ($user_ids as $key => $user_id) {
                $data = [
                    'title'           => Carbon::create()->month($request->month)->format('F') . ' - ' . $request->year,
                    'description'     => 'Salary',
                    'month'           => $request->month,
                    'year'            => $request->year,
                    'staff_id'        => $user_id,
                    'basic_salary'    => $request->basic_salary[$user_id],
                    'paid_leaves'     => $request->paid_leave[$user_id],
                    'amount'          => $request->net_salary[$user_id],
                    'session_year_id' => $sessionYearInterface->id,
                    'date'            => date('Y-m-d', strtotime($request->date)),
                ];

                $expense = $this->expense->updateOrCreate(['staff_id' => $data['staff_id'], 'month' => $data['month'], 'year' => $data['year']], ['amount' => $data['amount'], 'session_year_id' => $data['session_year_id'],'basic_salary' => $data['basic_salary'],'date' => $data['date'],'title' => $data['title'],'paid_leaves' => $data['paid_leaves'],'description' => $data['description']]);

                $staffSalary = $this->staffSalary->builder()->where('staff_id',$user_id)->get();
                if (count($staffSalary)) {
                    foreach ($staffSalary as $key => $payroll) {
                        $staff_payroll_data[] = [
                            'expense_id' => $expense->id,
                            'payroll_setting_id' => $payroll->payroll_setting_id,
                            'amount' => $payroll->amount,
                            'percentage' => $payroll->percentage,
                        ];   
                    }
                }
            }
            
            $this->staffPayroll->upsert($staff_payroll_data, ['staff_id', 'payroll_setting_id'], ['amount', 'percentage']);
            $user = $this->staff->builder()->whereIn('id', $user_ids)->pluck('user_id');
          
            $title = 'Payroll Update !!!' ;
            $body = "Your Payroll has been Updated.";
            $type = "assignment";

            send_notification($user, $title, $body, $type);

            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), ['does not exist', 'file_get_contents'])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not sent.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, 'Payroll Controller -> Store method');
                ResponseService::errorResponse();
            }
        }
    }

    public function show() {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-list');

        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');
        $month = request('month');
        $year = request('year');

        

        $leaveMaster = $this->leaveMaster->builder()->whereHas('session_year', function ($q) use ($month, $year) {
            $q->where(function ($q) use ($month, $year) {
                $q->whereMonth('start_date', '<=', $month)->whereYear('start_date', $year);
            })->orWhere(function ($q) use ($month, $year) {
                $q->whereMonth('start_date', '>=', $month)->whereYear('end_date', '<=', $year);
            });
        })->first();

        $sql = $this->staff->builder()->with(['user','staffSalary.payrollSetting', 'expense.staff_payroll.payroll_setting', 'leave' => function ($q) use ($month,$year) {
            $q->where('status', 1)->withCount(['leave_detail as full_leave' => function ($q) use ($month,$year) {
                $q->whereMonth('date', $month)->whereYear('date',$year)->where('type', 'Full');
            }])->withCount(['leave_detail as half_leave' => function ($q) use ($month,$year) {
                $q->whereMonth('date', $month)->whereYear('date',$year)->whereNot('type', 'Full');
            }]);
        }])->whereHas('user', function ($q) {
            $q->whereNull('deleted_at')->Owner();
        })->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->orwhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%");
                });
            });
        });

        $total = $sql->count();

        $sql->orderBy($sort, $order);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $salary_deduction = 0;
            $salary = $row->salary;
            $full_leave = isset($row->leave) ? $row->leave->sum('full_leave') : 0;
            $half_leave = isset($row->leave) ? ($row->leave->sum('half_leave') / 2) : 0;
            $total_leave = $full_leave + $half_leave;
            $tempRow['total_leaves'] = $total_leave;
            $tempRow['salary_deduction'] = number_format($salary_deduction, 2);
            $allowanceAmount = [];
            $deductionAmount = [];
            foreach ($row->staffSalary as $salaryItem) {
                $payrollSetting = $salaryItem->payrollSetting;
        
                if (!$payrollSetting) {
                    continue;
                }
        
                if ($payrollSetting->type === 'allowance') {
               
                    if (isset($salaryItem->percentage)) {
                        $allowanceAmount[] = ($salaryItem->percentage / 100) * $salary;
                    } elseif (isset($salaryItem->amount)) {
                        $allowanceAmount[] = $salaryItem->amount;
                    }
                  
                } elseif ($payrollSetting->type === 'deduction') {
                 
                    if (isset($salaryItem->percentage)) {
                        $deductionAmount[] = ($salaryItem->percentage / 100) * $salary;
                    } elseif (isset($salaryItem->amount)) {
                        $deductionAmount[] = $salaryItem->amount;
                    }
                 
                }
            }
            $totalAllowanceAmount = array_sum($allowanceAmount);
            $totalDeductionAmount = array_sum($deductionAmount);

            $tempRow['allowances'] = isset($totalAllowanceAmount) ? number_format($totalAllowanceAmount,2) : 0;
            $tempRow['deductions'] = isset($totalDeductionAmount) ? number_format($totalDeductionAmount,2) : 0;

            if (isset($row->expense)) {
                // TODO : this line can be converted into filter searching instead of searching from query
                $expense = $row->expense()->where('month', $month)->where('year', $year)->first();
                if ($expense) {
                    $operate = BootstrapTableService::button('fa fa-file-o', url('payroll/slip/'.$expense->id), ['btn-gradient-info'], ['title' => trans("slip"),'target' => '_blank']);
                    $status = 1;
                    $tempRow['salary'] = $expense->basic_salary;
                    $salary = $expense->getRawOriginal('basic_salary');

                    $tempRow['status'] = $status;
                    $tempRow['paid_leaves'] = $expense->paid_leaves;
                    if ($expense->paid_leaves < $total_leave && $expense->paid_leaves) {
                        $unpaid_leave = $total_leave - $expense->paid_leaves;
                        $per_day_salary = $salary / 30;
                        $salary_deduction = $unpaid_leave * $per_day_salary;
                        $tempRow['salary_deduction'] = number_format($salary_deduction,2);
                    }
                    $tempRow['net_salary'] = $expense->amount;
                    $tempRow['operate'] = $operate;

                    // Calculate allowances & deductions
                    if (count($expense->staff_payroll)) {
                        $allowance = $deduction = 0;
                        foreach ($expense->staff_payroll as $key => $staff_payroll) {
                            if ($staff_payroll->payroll_setting->type == 'allowance') {
                                if ($staff_payroll->amount) {
                                    $allowance += $staff_payroll->amount;
                                } else {
                                    $allowance += ($expense->basic_salary * $staff_payroll->percentage) / 100;
                                }
                            } else if($staff_payroll->payroll_setting->type == 'deduction') {
                                if ($staff_payroll->amount) {
                                    $deduction += $staff_payroll->amount;
                                } else {
                                    $deduction += ($expense->basic_salary * $staff_payroll->percentage) / 100;
                                }
                            }
                        }
                        $tempRow['allowances'] = number_format($allowance ,2);
                        $tempRow['deductions'] = number_format($deduction ,2);
                    }

                } else if ($leaveMaster) {
                    $salary = $row->salary;
                    $tempRow['paid_leaves'] = $leaveMaster->leaves;
                    if ($leaveMaster->leaves < $total_leave) {
                        if ($leaveMaster->leaves) {
                            $unpaid_leave = $total_leave - $leaveMaster->leaves;
                            $per_day_salary = $salary / 30;
                            $salary_deduction = $unpaid_leave * $per_day_salary;
                        }
                        $tempRow['salary_deduction'] = number_format($salary_deduction,2);
                    }
                    $tempRow['net_salary'] = $salary - $salary_deduction + $totalAllowanceAmount - $totalDeductionAmount;
                } else {
                    $tempRow['net_salary'] = $salary + $totalAllowanceAmount - $totalDeductionAmount;
                }
            }
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function slip_index()
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        try {
            $sessionYear = $this->sessionYear->builder()->pluck('name','id');
            $currentSessionYear = $this->cache->getDefaultSessionYear();

            $FirstsessionYear = $this->sessionYear->builder()->orderBy('start_date', 'ASC')->first();
            $FirstsessionYear = date('Y', strtotime($FirstsessionYear->start_date));
            
            return view('payroll.list',compact('sessionYear','currentSessionYear','FirstsessionYear'));
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th, 'Payroll Controller -> Slip Index method');
            ResponseService::errorResponse();
        }
    }

    public function slip_list()
    {
        ResponseService::noFeatureThenRedirect('Expense Management');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');
        $year = request('year');

        $sql = $this->expense->builder()->where('staff_id',Auth::user()->staff->id)
        ->where(function($q) use($search){
            $q->when($search, function($q) use($search) {
                $q->where('title','LIKE',"%$search%")
                ->orWhere('basic_salary','LIKE',"%$search%")
                ->orWhere('amount','LIKE',"%$search%")
                ->where('staff_id',Auth::user()->staff->id);
            });
        })
        
        ->when($year, function($q) use($year) {
            $q->whereYear('date',$year);
        })
        ->where('staff_id',Auth::user()->staff->id);

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            $operate = BootstrapTableService::button('fa fa-file-o', url('payroll/slip/'.$row->id), ['btn-gradient-info'], ['title' => trans("slip"),'target' => '_blank']);
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function slip($id = null)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        try {
            $schoolSetting = $this->cache->getSchoolSettings();
            $data = explode("storage/", $schoolSetting['horizontal_logo'] ?? '');
            $schoolSetting['horizontal_logo'] = end($data);

            if ($schoolSetting['horizontal_logo'] == null) {
                $systemSettings = $this->cache->getSystemSettings();
                $data = explode("storage/", $systemSettings['horizontal_logo'] ?? '');
                $schoolSetting['horizontal_logo'] = end($data);
            }

            // Salary
            $salary = $this->expense->builder()->with('staff.user:id,first_name,last_name','staff_payroll.payroll_setting')->where('id',$id)->first();
            if (!$salary) {
                return redirect()->back()->with('error',trans('no_data_found'));
            }
            // Get total leaves
            $leaves = $this->leave->builder()->where('status',1)->where('user_id',$salary->staff->user_id)->withCount(['leave_detail as full_leave' => function ($q) use ($salary) {
                $q->whereMonth('date', $salary->month)->whereYear('date',$salary->year)->where('type', 'Full');
            }])->withCount(['leave_detail as half_leave' => function ($q) use ($salary) {
                $q->whereMonth('date', $salary->month)->whereYear('date',$salary->year)->whereNot('type', 'Full');
            }])->get();

            $allow_leaves = 0;
            if ($salary) {
                $allow_leaves = $salary->paid_leaves;
            }

            $total_leaves = $leaves->sum('full_leave') + ($leaves->sum('half_leave') / 2);
            // Total days
            $days = Carbon::now()->year($salary->year)->month($salary->month)->daysInMonth;

            $pdf = PDF::loadView('payroll.slip',compact('schoolSetting','salary','total_leaves','days','allow_leaves'));
            return $pdf->stream($salary->title.'-'.$salary->staff->user->full_name.'.pdf');
        } catch (\Throwable $th) {
            ResponseService::logErrorResponse($th);
            ResponseService::errorResponse();
        }
    }

}
