<?php

namespace App\Http\Controllers;

use App\Repositories\Holiday\HolidayInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class HolidayController extends Controller {

    private HolidayInterface $holiday;
    private SessionYearInterface $sessionYear;

    public function __construct(HolidayInterface $holiday, SessionYearInterface $sessionYear) {
        $this->holiday = $holiday;
        $this->sessionYear = $sessionYear;
    }

    public function index() {
        ResponseService::noFeatureThenRedirect('Holiday Management');
        ResponseService::noPermissionThenRedirect('holiday-list');
        $sessionYears = $this->sessionYear->all();
        $months = sessionYearWiseMonth();
        return view('holiday.index', compact('sessionYears','months'));
    }


    public function store(Request $request) {
        ResponseService::noFeatureThenRedirect('Holiday Management');
        ResponseService::noPermissionThenRedirect('holiday-create');
        $validator = Validator::make($request->all(), [
            'date'  => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->holiday->create($request->all());
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Holiday Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }

    public function update($id, Request $request) {
        ResponseService::noFeatureThenRedirect('Holiday Management');
        ResponseService::noPermissionThenSendJson('holiday-edit');
        $validator = Validator::make($request->all(), ['date' => 'required', 'title' => 'required',]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->holiday->update($id, $request->all());
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Holiday Controller -> Update Method");
            ResponseService::errorResponse();
        }
    }

    // TODO : Remove this if not necessary
    // public function holiday_view()
    // {
    //     return view('holiday.list');
    // }

    public function show(Request $request) {
        ResponseService::noFeatureThenRedirect('Holiday Management');
        ResponseService::noPermissionThenRedirect('holiday-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $session_year_id = request('session_year_id');
        $month = request('month');

        $sessionYear = $this->sessionYear->findById($session_year_id);

        $sql = $this->holiday->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")->orwhere('title', 'LIKE', "%$search%")->orwhere('description', 'LIKE', "%$search%")->orwhere('date', 'LIKE', "%$search%");
                });
                });
            })->when($session_year_id, function ($query) use ($sessionYear) {
                $query->whereDate('date', '>=',$sessionYear->start_date)
                ->whereDate('date', '<=',$sessionYear->end_date);
            })->when($month, function ($query) use ($month) {
                $query->whereMonth('date', $month);
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = BootstrapTableService::editButton(route('holiday.update', $row->id));
            $operate .= BootstrapTableService::deleteButton(route('holiday.destroy', $row->id));
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            // $tempRow['date'] = format_date($row->date);
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function destroy($id) {
        ResponseService::noFeatureThenRedirect('Holiday Management');
        ResponseService::noPermissionThenSendJson('holiday-delete');
        try {
            $this->holiday->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Holiday Controller -> Delete Method");
            ResponseService::errorResponse();
        }
    }
}
