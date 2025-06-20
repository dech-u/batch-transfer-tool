<?php

namespace App\Http\Controllers;

use App\Models\shift;
use App\Repositories\Shift\ShiftInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;


class ShiftController extends Controller {

    private ShiftInterface $shift;

    public function __construct(ShiftInterface $shift) {
        $this->shift = $shift;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('shift-list');
        $shifts = $this->shift->builder()->orderBy('id', 'DESC')->get();
        return response(view('shift.index', compact('shifts')));
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('shift-create');

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'regex:/^[A-Za-z0-9_ ]+$/', new uniqueForSchool('shifts', 'name')],
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->shift->create($request->all());
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('shift-list');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = $this->shift->builder()->where('id', '!=', 0);
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%");
            });
        }

        if ($request->show_deleted) {
            $sql->onlyTrashed();
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            if ($request->show_deleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::restoreButton(route('shift.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('shift.trash', $row->id));
            } else {
                $operate = BootstrapTableService::editButton(route('shift.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('shift.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('shift-edit');

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'regex:/^[A-Za-z0-9_ ]+$/', new uniqueForSchool('shifts', 'name', $id)],
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->shift->update($id, $request->all());
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('shift-delete');
        try {
            Shift::find($id)->delete();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('shift-delete');
        try {
            $this->shift->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('shift-delete');
        try {
            $this->shift->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Shift Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }
}
