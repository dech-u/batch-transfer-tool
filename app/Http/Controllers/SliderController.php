<?php

namespace App\Http\Controllers;

use App\Repositories\Sliders\SlidersInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class SliderController extends Controller {
    private SlidersInterface $sliders;
    private CachingService $cache;

    public function __construct(SlidersInterface $sliders, CachingService $cache) {
        $this->sliders = $sliders;
        $this->cache = $cache;
    }

    public function index() {
        ResponseService::noFeatureThenRedirect('Slider Management');
        ResponseService::noAnyPermissionThenRedirect(['slider-list','slider-create']);

        $systemSettings = $this->cache->getSystemSettings();

        return response(view('sliders.index', compact('systemSettings')));
    }

    public function store(Request $request) {
        ResponseService::noFeatureThenRedirect('Slider Management');
        ResponseService::noPermissionThenRedirect('slider-create');
        $validator = Validator::make($request->all(),
            [
                'image' => 'required|mimes:jpeg,png,jpg,svg,svg+xml|image|max:2048',
                'link'  => 'nullable|url'
            ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {
            $this->sliders->create($request->except('_token'));
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Sliders Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }


    public function show() {
        ResponseService::noFeatureThenRedirect('Slider Management');
        ResponseService::noPermissionThenRedirect('slider-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');

        $sql = $this->sliders->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->orwhere('mobile', 'LIKE', "%$search%");
                });
            });
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = BootstrapTableService::editButton(route('sliders.update', $row->id));
            $operate .= BootstrapTableService::trashButton(route('sliders.destroy', $row->id));

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update($id, Request $request) {
        ResponseService::noFeatureThenRedirect('Slider Management');
        ResponseService::noPermissionThenSendJson('slider-edit');
        $validator = Validator::make($request->all(), [
            'image' => 'mimes:jpeg,png,jpg|image|max:2048',
            'link'  => 'nullable|url'
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {
            $this->sliders->update($id, $request->except('_token', 'edit_id'));
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Sliders Controller -> Update Method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noFeatureThenRedirect('Slider Management');
        ResponseService::noPermissionThenSendJson('slider-delete');
        try {
            $this->sliders->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Sliders Controller -> Delete Method");
            ResponseService::errorResponse();
        }
    }
}
