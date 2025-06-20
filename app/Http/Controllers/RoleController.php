<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RoleController extends Controller {

    /**
     * @var array|string[]
     */
    private array $reserveRole;

    public function __construct() {
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);

        $this->reserveRole = [
            'Super Admin',
            'School Admin',
            'Teacher',
            'Guardian',
            'Student'
        ];
    }


    public function index() {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noAnyPermissionThenRedirect(['role-list', 'role-create', 'role-edit', 'role-delete']);
        $roles = Role::orderBy('id', 'DESC')->get();
        return view('roles.index', compact('roles'));
    }

    public function list(Request $request) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('role-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $sql = Role::where('editable', 1)->whereNot('name','Teacher');

        if (!empty($request->search)) {
            $search = $request->search;
            $sql->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%");
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
            $operate = BootstrapTableService::button('fa fa-eye', route('roles.show', $row->id), ['btn-gradient-success'], ['title' => 'View']);
            if (Auth::user()->can('role-edit')) {
                $operate .= BootstrapTableService::editButton(route('roles.edit', $row->id), false);
            }
            if ($row->custom_role != 0 && Auth::user()->can('role-delete')) {
                $operate .= BootstrapTableService::deleteButton(route('roles.destroy', $row->id));
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function create() {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('role-create');
        $permission = Permission::whereHas('roles', static function ($q) {
            $q->where('name', '!=', 'Teacher');
        })->orderBy('name')->get();
        return view('roles.create', compact('permission'));
    }

    public function store(Request $request) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('role-create');
        try {
            $this->validate($request, [
                'name'       => [
                    'required',
                    new uniqueForSchool('roles', 'name', null, Auth::user()->school_id)
                ],
                'permission' => 'required'
            ]);

            if (in_array($request->name, $this->reserveRole)) {
                return redirect()->back()->with('error', $request->name . " " . trans("is not a valid Role name Because it's Reserved Role"));
            }
            DB::beginTransaction();
            $role = Role::create(['name' => $request->input('name'), 'school_id' => Auth::user()->school_id]);
            $role->syncPermissions($request->input('permission'));
            DB::commit();
            return redirect()->route('roles.index')->with('success', trans('Data Stored Successfully'));
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function show($id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('role-list');
        $role = Role::findOrFail($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")->where("role_has_permissions.role_id", $id)->orderBy('name')->get();

        return view('roles.show', compact('role', 'rolePermissions'));

    }


    public function edit($id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('role-edit');
        $role = Role::findOrFail($id);

        if ($role->name == "Teacher") {
            $permission = Permission::orderBy('name')->get();
        } else {            
            $permission = Permission::whereHas('roles', static function ($q) {
                $q->where('name', '!=', 'Teacher');
            })->orderBy('name')->get();
        }

        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }


    public function update(Request $request, $id) {
        ResponseService::noFeatureThenRedirect('Staff Management');
        ResponseService::noPermissionThenRedirect('role-edit');
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'permission' => 'required'
    
            ],[
                'permission.required' => 'Please select at least one permission.'
            ]);
            if ($validator->fails()) {
                ResponseService::validationError($validator->errors()->first());
            }

            if (in_array($request->name, $this->reserveRole)) {
                return redirect()->back()->with('error', $request->name . " " . trans("is not a valid Role name Because it's Reserved Role"));
            }
            $role = Role::findOrFail($id);
            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permission'));
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            ResponseService::noFeatureThenRedirect('Staff Management');
            ResponseService::noPermissionThenSendJson('role-delete');
            $role = Role::withCount('users')->findOrFail($id);
            if($role->users_count)
            {
                ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
            } else {
                Role::findOrFail($id)->delete();
                ResponseService::successResponse('Data Deleted Successfully');
            }
            
            
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
