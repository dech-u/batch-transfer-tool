<?php

namespace App\Http\Controllers;

use App\Repositories\Medium\MediumInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Throwable;

class MediumController extends Controller {
    private MediumInterface $medium;

    public function __construct(MediumInterface $medium) {
        $this->medium = $medium;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('medium-list');
        return view('medium.index');
    }

    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('medium-create');
        $request->validate([
            'name' => ['required', new uniqueForSchool('mediums', 'name')]
        ]);
        try {
            $this->medium->create($request->except('_token'));
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function edit($id) {
        $medium = $this->medium->findById($id);
        return response($medium);
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('medium-edit');
        $request->validate([
            'name' => ['required', new uniqueForSchool('mediums', 'name', $id)]
        ]);
        try {
            $this->medium->update($id, $request->except(['_token', 'id']));
            $response = ['error' => false, 'message' => trans('Data Updated Successfully'),];
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Medium Controller -> Update method");
            ResponseService::errorResponse();
        }
        return response()->json($response);
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('medium-delete');
        try {
            $this->medium->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Medium Controller -> Delete Method");
            ResponseService::errorResponse();
        }
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('medium-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $request->search;
        $showDeleted = $request->show_deleted;

        $sql = $this->medium->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->Owner();
                });
            })
            ->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed()->Owner();
            });
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = "";
            if ($request->show_deleted) {
                //Show Restore and Hard Delete Buttons
                $operate .= BootstrapTableService::restoreButton(route('mediums.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('mediums.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate .= BootstrapTableService::editButton(route('mediums.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('mediums.destroy', $row->id));
            }
//            $operate .= BootstrapTableService::viewRelatedDataButton(route('related-data.index', ['mediums', $row->id]));
            // Copy Data From Row to Temp Row
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('medium-delete');
        try {
            $this->medium->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('medium-delete');
        try {
            $this->medium->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Medium Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
        }
    }
}
