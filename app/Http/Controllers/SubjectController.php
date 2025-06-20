<?php

namespace App\Http\Controllers;

use App\Repositories\Medium\MediumInterface;
use App\Repositories\Subject\SubjectInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SubjectController extends Controller {
    private MediumInterface $medium;
    private SubjectInterface $subject;

    public function __construct(MediumInterface $medium, SubjectInterface $subject) {
        $this->medium = $medium;
        $this->subject = $subject;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('subject-list');
        $mediums = $this->medium->builder()->orderBy('id', 'DESC')->get();
        return response(view('subjects.index', compact('mediums')));
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('subject-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $_GET['search'];
        $showDeleted = $request->show_deleted;

        $sql = $this->subject->builder()->with('medium')
            ->where(function ($query) use ($search) {
                $query->when($search, function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")
                        ->orwhere('name', 'LIKE', "%$search%")
                        ->orwhere('code', 'LIKE', "%$search%")
                        ->orwhere('type', 'LIKE', "%$search%")->Owner();
                });
            })
            ->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed()->Owner();
            });
        if (!empty($_GET['medium_id'])) {
            $sql = $sql->where('medium_id', $_GET['medium_id']);
        }

        $total = $sql->count();

        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            if ($request->show_deleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::restoreButton(route('subjects.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('subjects.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate = BootstrapTableService::editButton(route('subjects.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('subjects.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['type'] = trans($row->type);
            $tempRow['eng_type'] = $row->type;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('subject-create');
        $validator = Validator::make($request->all(), [
            'medium_id' => 'required|numeric',
            'type'      => 'required|in:Practical,Theory',
            'name'      => [
                'required',
                new uniqueForSchool('subjects', ['name' => $request->name, 'medium_id' => $request->medium_id, 'type' => $request->type])
            ],
            'bg_color'  => 'required|not_in:transparent',
            //            'code'      => 'nullable|unique:subjects,code',
            'code'      => [
                'nullable',
                new uniqueForSchool('subjects', ['code' => $request->code, 'medium_id' => $request->medium_id, 'type' => $request->type])
            ],
            'image'     => 'required|max:2048|mimes:jpg,jpeg,png,svg',
        ])->setAttributeNames(['bg_color' => 'Background Color']);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->subject->create($request->all());
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('subject-edit');
        $validator = Validator::make($request->all(), [
            'medium_id' => 'required|numeric',
            'name'      => [
                'required',
                new uniqueForSchool('subjects', ['name' => $request->name, 'medium_id' => $request->medium_id, 'type' => $request->type], $id)
            ],
            'code' => [
                'nullable',
                new uniqueForSchool('subjects', ['code' => $request->code, 'medium_id' => $request->medium_id, 'type' => $request->type], $id)
            ],
            'type'      => 'required|in:Practical,Theory',
            'bg_color'  => 'required|not_in:transparent',
            'image'     => 'mimes:jpg,jpeg,png,svg|max:2048|nullable',
        ])->setAttributeNames(['bg_color' => 'Background Color']);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {
            $this->subject->update($id, $request->all());
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('subject-delete');
        try {
            $this->subject->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('subject-delete');
        try {
            $this->subject->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('subject-delete');
        try {
            $this->subject->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Subject Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }
}
