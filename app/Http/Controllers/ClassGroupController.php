<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Repositories\ClassGroup\ClassGroupInterface;
use App\Repositories\ClassSchool\ClassSchoolInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private ClassSchoolInterface $class;
    private ClassGroupInterface $classGroup;
    private CachingService $cache;

    public function __construct(ClassSchoolInterface $class, ClassGroupInterface $classGroup, CachingService $cache) {
        $this->class = $class;
        $this->classGroup = $classGroup;
        $this->cache = $cache;
    }

    public function index()
    {
        //
        ResponseService::noAnyPermissionThenRedirect(['class-group-list','class-group-create']);
        $classes = $this->class->builder()->groupBy('name')->pluck('name','id');
        return view('class-group.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        ResponseService::noPermissionThenRedirect('class-group-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        ResponseService::noPermissionThenRedirect('class-group-create');

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,svg,gif,webp',
            'class_ids' => 'required',
        ],[
            'image.mimes' => 'The selected file must be a file of type: jpeg, png, jpg, svg, gif or webp.'
        ]);

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'image' => $request->file('image'),
                'class_ids' => implode(",",$request->class_ids),
            ];
            $this->classGroup->create($data);
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            ResponseService::logErrorResponse($th, "Class Group Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        ResponseService::noPermissionThenRedirect('class-group-list');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');

        $sql = $this->classGroup->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                });
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
            $operate = '';
            $operate .= BootstrapTableService::editButton(route('class-group.update', $row->id));
            $operate .= BootstrapTableService::deleteButton(route('class-group.destroy', $row->id));

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['classes'] = $row->class_name->pluck('name');
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        ResponseService::noPermissionThenRedirect('class-group-edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        ResponseService::noPermissionThenRedirect('class-group-edit');
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'class_ids' => 'required',
        ]);
        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'class_ids' => implode(",",$request->class_ids),
            ];
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }
            $this->classGroup->update($id,$data);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            ResponseService::logErrorResponse($th, "Class Group Controller -> Update Method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        ResponseService::noPermissionThenRedirect('class-group-delete');
        try {
            DB::beginTransaction();
            $this->classGroup->deleteById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Class Group Controller -> Destroy Method");
            ResponseService::errorResponse();
        }

    }
}
