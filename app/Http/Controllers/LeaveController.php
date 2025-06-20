<?php

namespace App\Http\Controllers;

use App\Models\LeaveDetail;
use App\Repositories\Expense\ExpenseInterface;
use App\Repositories\Holiday\HolidayInterface;
use App\Repositories\Leave\LeaveInterface;
use App\Repositories\LeaveDetail\LeaveDetailInterface;
use App\Repositories\LeaveMaster\LeaveMasterInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Staff\StaffInterface;
use App\Repositories\User\UserInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use App\Repositories\Files\FilesInterface;
use Illuminate\Support\Facades\Validator;
use Storage;

class LeaveController extends Controller
{

    private LeaveInterface $leave;
    private SessionYearInterface $sessionYear;
    private LeaveDetailInterface $leaveDetail;
    private CachingService $cache;
    private LeaveMasterInterface $leaveMaster;
    private ExpenseInterface $expense;
    private UserInterface $user;
    private HolidayInterface $holiday;
    private StaffInterface $staff;
    private FilesInterface $files;

    public function __construct(LeaveInterface $leave, SessionYearInterface $sessionYear, LeaveDetailInterface $leaveDetail, CachingService $cache, LeaveMasterInterface $leaveMaster, ExpenseInterface $expense, UserInterface $user, HolidayInterface $holiday, StaffInterface $staff, FilesInterface $files)
    {
        $this->leave = $leave;
        $this->sessionYear = $sessionYear;
        $this->leaveDetail = $leaveDetail;
        $this->cache = $cache;
        $this->leaveMaster = $leaveMaster;
        $this->expense = $expense;
        $this->user = $user;
        $this->holiday = $holiday;
        $this->staff = $staff;
        $this->files = $files;
    }

    public function index()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('leave-list');

        $sessionYear = $this->sessionYear->builder()->pluck('name', 'id');
        $current_session_year = app(CachingService::class)->getDefaultSessionYear();
        $leaveMaster = $this->leaveMaster->builder()->where('session_year_id', $current_session_year->id)->first();
        $months = sessionYearWiseMonth();
        $holiday = $this->holiday->builder()->whereDate('date', '>=', $current_session_year->start_date)->whereDate('date', '<=', $current_session_year->end_date)->get()->pluck('default_date_format')->toArray();
        $holiday = implode(',', $holiday);
        return view('leave.index', compact('sessionYear', 'current_session_year', 'leaveMaster', 'months', 'holiday'));
    }

    public function create()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('leave-create');
    }

    public function store(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('leave-create');

        $request->validate([
            'reason'  => 'required',
            'from_date' => 'required',
            'to_date' => 'required|after_or_equal:from_date',
            'leave_master_id' => 'required',

            'type' => 'required',
            'files.*' => 'nullable',
        ],[

            'leave_master_id.required' => 'Kindly contact the school admin to update settings for continued access.',
            'type.required' => 'Kindly select different dates as the ones mentioned are already allocated as holidays.'
        ]);

        try {
            DB::beginTransaction();
            
            $data = [
                'user_id' => Auth::user()->id,
                'reason' => $request->reason,
                'from_date' => date('Y-m-d', strtotime($request->from_date)),
                'to_date' => date('Y-m-d', strtotime($request->to_date)),
                'leave_master_id' => $request->leave_master_id
            ];
            $leave = $this->leave->create($data);
            $data = array();
            foreach ($request->type as $key => $type) {
                $data[] = [
                    'leave_id' => $leave->id,
                    'date' => date('Y-m-d', strtotime($key)),
                    'type' => $type[0]
                ];
            }

            
            if ($request->hasFile('files')) {
                $fileData = []; // Empty FileData Array
                // Create A File Model Instance
                $leaveModelAssociate = $this->files->model()->modal()->associate($leave); // Get the Association Values of File with Assignment
            
                foreach ($request->file('files') as $file_upload) {
                    // Create Temp File Data Array
                    $tempFileData = [
                        'modal_type' => $leaveModelAssociate->modal_type,
                        'modal_id'   => $leaveModelAssociate->modal_id,
                        'file_name'  => $file_upload->getClientOriginalName(),
                        'type'       => 1,
                        'file_url'   => $file_upload // Store file and get the file path
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
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "Leave Controller -> Store Method");
                ResponseService::errorResponse();
            }
        }
    }

    public function show()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('leave-list');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $session_year_id = request('session_year_id');
        $filter_upcoming = request('filter_upcoming');
        $month_id = request('month_id');


        $sql = $this->leave->builder()->with('leave_detail','file')->where('user_id',Auth::user()->id)
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")->orwhere('reason', 'LIKE', "%$search%")->orwhere('from_date', 'LIKE', "%$search%")->orwhere('to_date', 'LIKE', "%$search%");
                    });
                });
            });

        if ($session_year_id) {
            $sql->whereHas('leave_master', function ($q) use ($session_year_id) {
                $q->where('session_year_id', $session_year_id);
            });
        }

        $sql = $sql->withCount(['leave_detail as full_leave' => function ($q) {
            $q->where('type', 'Full');
        }]);

        $sql = $sql->withCount(['leave_detail as half_leave' => function ($q) {
            $q->whereNot('type', 'Full');
        }]);

        if ($filter_upcoming) {
            if ($filter_upcoming == 'Today') {
                $sql->whereDate('from_date', '<=', Carbon::now()->format('Y-m-d'))->whereDate('to_date', '>=', Carbon::now()->format('Y-m-d'));
            }
            if ($filter_upcoming == 'Tomorrow') {
                $tomorrow_date = Carbon::now()->addDay()->format('Y-m-d');
                $sql->whereHas('leave_detail', function ($q) use ($tomorrow_date) {
                    $q->whereDate('date', '<=', $tomorrow_date)->whereDate('date', '>=', $tomorrow_date);
                });
            }
            if ($filter_upcoming == 'Upcoming') {
                $upcoming_date = Carbon::now()->addDays(1)->format('Y-m-d');
                $sql->whereHas('leave_detail', function ($q) use ($upcoming_date) {
                    $q->whereDate('date', '>', $upcoming_date);
                });
            }
        }

        if ($month_id) {
            $sql->whereHas('leave_detail', function ($q) use ($month_id) {
                $q->whereMonth('date', $month_id);
            });
        }

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '';
            $operate = BootstrapTableService::button('fa fa-eye', '#', ['edit-data', 'btn-gradient-info'], ['title' => trans("view"), "data-toggle" => "modal", "data-target" => "#editModal"]);
            if ($row->status == 0) {
                // $operate .= BootstrapTableService::editButton(route('leave.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('leave.destroy', $row->id));
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['days'] = $row->full_leave + ($row->half_leave / 2);
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        // dd($bulkData);
        return response()->json($bulkData);
    }

    public function update(Request $request, $id)
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('leave-edit');

        $request->validate([
            'reason'  => 'required',
            'from_date' => 'required',
            'to_date' => 'required|after_or_equal:from_date',
        ]);
        try {
            DB::beginTransaction();
            $data = [
                'reason' => $request->reason,
                'from_date' => date('Y-m-d', strtotime($request->from_date)),
                'to_date' => date('Y-m-d', strtotime($request->to_date)),
            ];
            $this->leave->update($id, $data);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Leave Controller -> Update Method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id)
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noAnyPermissionThenRedirect(['leave-delete', 'approve-leave']);
        try {
            DB::beginTransaction();
            // $this->leave->deleteById($id);
            $leave = $this->leave->findById($id);
            foreach ($leave->file as $key => $file) {
                if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                    Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                }
            }
            $leave->file()->delete();
            $leave->delete();
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Leave Controller -> Destroy Method");
            ResponseService::errorResponse();
        }
    }

    public function leave_request()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('approve-leave');

        $sessionYear = $this->sessionYear->builder()->pluck('name', 'id');
        $current_session_year = app(CachingService::class)->getDefaultSessionYear();
        $leaveMaster = $this->leaveMaster->builder()->where('session_year_id', $current_session_year->id)->first();
        $holiday_days = '';
        if ($leaveMaster) {
            $holiday_days = $leaveMaster->holiday;
        }
        $users = $this->user->builder()->has('staff')->get()->pluck('full_name', 'id');
        $months = sessionYearWiseMonth();

        $holiday = $this->holiday->builder()->whereDate('date', '>=', $current_session_year->start_date)->whereDate('date', '<=', $current_session_year->end_date)->get()->pluck('default_date_format')->toArray();
        $public_holiday = implode(',', $holiday);

        return view('leave.leave_request', compact('sessionYear', 'current_session_year', 'holiday_days', 'users', 'months', 'public_holiday'));
    }

    public function leave_request_show()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('approve-leave');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $session_year_id = request('session_year_id');
        $filter_upcoming = request('filter_upcoming');
        $month_id = request('month_id');
        $user_id = request('user_id');

        $sql = $this->leave->builder()->with('leave_detail','file','user')
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")->orwhere('reason', 'LIKE', "%$search%")->orwhere('from_date', 'LIKE', "%$search%")->orwhere('to_date', 'LIKE', "%$search%")->orwhereHas('user', function ($q) use ($search) {
                            $q->whereRaw('concat(first_name," ",last_name) like ?', "%$search%");
                        });
                    });
                });
            });

        if ($session_year_id) {
            $sql->whereHas('leave_master', function ($q) use ($session_year_id) {
                $q->where('session_year_id', $session_year_id);
            });
        }

        if ($filter_upcoming != 'All') {
            if ($filter_upcoming == 'Today') {
                $sql->whereDate('from_date', '<=', Carbon::now()->format('Y-m-d'))->whereDate('to_date', '>=', Carbon::now()->format('Y-m-d'));
            }
            if ($filter_upcoming == 'Tomorrow') {
                $tomorrow_date = Carbon::now()->addDay()->format('Y-m-d');
                $sql->whereHas('leave_detail', function ($q) use ($tomorrow_date) {
                    $q->whereDate('date', '<=', $tomorrow_date)->whereDate('date', '>=', $tomorrow_date);
                });
            }
            if ($filter_upcoming == 'Upcoming') {
                $upcoming_date = Carbon::now()->addDays(1)->format('Y-m-d');
                $sql->whereHas('leave_detail', function ($q) use ($upcoming_date) {
                    $q->whereDate('date', '>', $upcoming_date);
                });
            }
        }

        if ($month_id) {
            $sql->whereHas('leave_detail', function ($q) use ($month_id) {
                $q->whereMonth('date', $month_id);
            });
        }

        if ($user_id) {
            $sql->where('user_id', $user_id);
        }

        $sql = $sql->withCount(['leave_detail as full_leave' => function ($q) {
            $q->where('type', 'Full');
        }]);

        $sql = $sql->withCount(['leave_detail as half_leave' => function ($q) {
            $q->whereNot('type', 'Full');
        }]);
        $total = $sql->count();

        $sql->orderBy('created_at', 'DESC')->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '';
            $operate .= BootstrapTableService::editButton(route('leave.status.update', $row->id));
            $operate .= BootstrapTableService::deleteButton(route('leave.destroy', $row->id));

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['days'] = $row->full_leave + ($row->half_leave / 2);
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function leave_status_update(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noPermissionThenRedirect('approve-leave');
        try {
            DB::beginTransaction();
            $leave = $this->leave->update($request->id, ['status' => $request->status]);
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
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "Leave Controller -> Leave Status Method");
                ResponseService::errorResponse();
            }
        }
    }

    public function filter_leave(Request $request)
    {
        ResponseService::noFeatureThenSendJson('Staff Leave Management');
        try {
            DB::beginTransaction();
            $leave = $this->leaveDetail->builder()->with('leave:id,user_id', 'leave.user:id,first_name,last_name')
                ->whereHas('leave', function ($q) {
                    $q->where('status', 1);
                });
            if ($request->filter_leave == 'Today') {
                $leave->whereDate('date', '<=', Carbon::now()->format('Y-m-d'))->whereDate('date', '>=', Carbon::now()->format('Y-m-d'));
            }
            if ($request->filter_leave == 'Tomorrow') {
                $tomorrow_date = Carbon::now()->addDay()->format('Y-m-d');
                $leave->whereDate('date', '<=', $tomorrow_date)->whereDate('date', '>=', $tomorrow_date);
            }
            if ($request->filter_leave == 'Upcoming') {
                $upcoming_date = Carbon::now()->addDays(1)->format('Y-m-d');
                $leave->whereDate('date', '>', $upcoming_date);
            }


            $response = [
                'error' => false,
                'data' => $leave->orderBy('date', 'ASC')->get()->append(['leave_date']),
                'message' => trans('data_fetch_successfully')
            ];

            return response()->json($response);
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Leave Controller -> Filter Leave Method");
            ResponseService::errorResponse();
        }
    }

    public function report()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noAnyPermissionThenRedirect(['leave-create', 'approve-leave']);
        $sessionYear = $this->sessionYear->builder()->pluck('name', 'id');
        $current_session_year = $this->cache->getDefaultSessionYear();

        $staffs = null;
        if (Auth::user()->can('approve-leave')) {
            $staffs = $this->user->builder()->has('staff')->select('id', 'first_name', 'last_name')->get()->pluck('full_name', 'id');
        }
        return view('leave.detail', compact('sessionYear', 'current_session_year', 'staffs'));
    }

    public function detail()
    {
        ResponseService::noFeatureThenRedirect('Staff Leave Management');
        ResponseService::noAnyPermissionThenRedirect(['leave-create', 'approve-leave']);

        $session_year_id = request('session_year_id');
        $staff_id = request('staff_id');

        if (!$staff_id) {
            $staff_id = Auth::user()->id;
        }

        $leaveMaster = $this->leaveMaster->builder()->with('session_year')->where('session_year_id', $session_year_id)->first();
        // Get months starting from session year
        $months = sessionYearWiseMonth();

        $bulkData = array();
        $bulkData['total'] = count($months);
        $rows = array();
        $no = 1;

        $expenses = $this->expense->builder()->whereHas('staff', function ($q) use ($staff_id) {
            $q->where('user_id', $staff_id);
        })->where('session_year_id', $session_year_id)->get();

        foreach ($months as $key => $month) {
            $expense = null;
            foreach ($expenses as $index => $expense_data) {
                if ($expense_data->month == $key) {
                    $expense = $expense_data;
                    break;
                }
            }
            $leaves = $this->leaveDetail->builder()->whereMonth('date', $key)
                ->whereHas('leave', function ($q) use ($session_year_id, $staff_id) {
                    $q->where('user_id', $staff_id)->where('status', 1)
                        ->whereHas('leave_master', function ($q) use ($session_year_id) {
                            $q->where('session_year_id', $session_year_id);
                        });
                });

            $allocated = 0;
            $total_used_leaves = 0;

            if ($leaveMaster) {
                $tempRow['allocated'] = $leaveMaster->leaves;
                $allocated = $leaveMaster->leaves;
            }
            if ($expense) {
                $tempRow['allocated'] = $expense->paid_leaves;
                $allocated = $expense->paid_leaves;
            }
            $tempRow['lwp'] = '-';
            $lwp = 0;
            $total_leaves = $leaves->count();
            $total_used_leaves = $total_leaves - ($leaves->whereNot('type', 'Full')->count() / 2);
            if ($allocated < $total_used_leaves) {
                $lwp = $total_used_leaves - $allocated;;
                $tempRow['lwp'] = $lwp;
                $tempRow['used_cl'] = $total_used_leaves - $lwp;
            } else {
                $tempRow['used_cl'] = '-';
                if ($total_used_leaves) {
                    $tempRow['used_cl'] = $total_used_leaves;
                }
            }
            $tempRow['total'] = '-';
            if ($total_used_leaves) {
                $tempRow['total'] = $total_used_leaves;
            }

            if ($total_used_leaves >= $allocated) {
                $tempRow['remaining_cl'] = '-';
                $tempRow['remaining_total'] = '-';
            } else {
                $tempRow['remaining_cl'] = $total_used_leaves != 0 ? $allocated - $total_used_leaves : '-';
                $tempRow['remaining_total'] = $total_used_leaves != 0 ? $allocated - $total_used_leaves : '-';
            }

            $tempRow['no'] = $no++;
            $tempRow['month'] = $month;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}
