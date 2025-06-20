<?php

namespace App\Http\Controllers;

use App\Repositories\Section\SectionInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SectionController extends Controller {
    private SectionInterface $section;

    public function __construct(SectionInterface $section) {
        $this->section = $section;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('section-list');
        $sections = $this->section->builder()->orderBy('id', 'DESC')->get();
        return response(view('section.index', compact('sections')));
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('section-create');
        $validator = Validator::make($request->all(), ['name' => ['required', new uniqueForSchool('sections', 'name')]]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {

            $this->section->create($request->except('_token'));
            ResponseService::successResponse('Data Stored Successfully');

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('section-edit');
        $validator = Validator::make($request->all(), ['name' => ['required', new uniqueForSchool('sections', 'name', $id)],]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->section->update($id, $request->except('_token'));
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('section-delete');
        try {
            $this->section->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('section-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $_GET['search'];
        $showDeleted = $request->show_deleted;

        $sql = $this->section->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->Owner();
                });
            })->when(!empty($showDeleted), function ($q) {
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
            if ($request->show_deleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::restoreButton(route('section.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('section.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate = BootstrapTableService::editButton(route('section.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('section.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('section-delete');
        try {
            $this->section->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('section-delete');
        try {
            $this->section->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Section Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }
}
