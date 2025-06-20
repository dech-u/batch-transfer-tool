<?php

namespace App\Http\Controllers;

use App\Repositories\Stream\StreamInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;


class StreamController extends Controller {

    private StreamInterface $stream;

    public function __construct(StreamInterface $stream) {
        $this->stream = $stream;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('stream-list');
        $streams = $this->stream->builder()->orderBy('id', 'DESC')->get();
        return response(view('stream.index', compact('streams')));
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('stream-create');

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                new uniqueForSchool('streams', 'name')
            ],
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->stream->create($request->all());
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('stream-list');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = $this->stream->builder()->where('id', '!=', 0);
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
                $operate = BootstrapTableService::restoreButton(route('stream.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('stream.trash', $row->id));
            } else {
                $operate = BootstrapTableService::editButton(route('stream.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('stream.destroy', $row->id));
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
        ResponseService::noPermissionThenSendJson('stream-edit');
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                new uniqueForSchool('streams', 'name', $id)
            ],
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            $this->stream->update($id, $request->all());
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('stream-delete');
        try {
            $this->stream->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('stream-delete');
        try {
            $this->stream->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('stream-delete');
        try {
            $this->stream->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Stream Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }
}
