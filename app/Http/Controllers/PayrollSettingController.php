<?php

namespace App\Http\Controllers;

use App\Repositories\PayrollSetting\PayrollSettingInterface;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Auth;
use DB;
use Illuminate\Http\Request;
use Throwable;

class PayrollSettingController extends Controller
{
    //

    private PayrollSettingInterface $payrollSetting;


    public function __construct(PayrollSettingInterface $payrollSetting) {
        $this->payrollSetting = $payrollSetting;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-settings-list');

        return view('payroll.payroll-settings');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-settings-create');

        $request->validate([
            'name' => 'required',
            'amount' => 'nullable|required_without_all:percentage',
            'percentage' => 'nullable|required_without_all:amount'
        ]);

        try {
            DB::beginTransaction();
            $data = [ 
                'user_id' => Auth::user()->id, 
                'name' => $request->name, 
                'amount' => $request->amount ?? null , 
                'percentage' => !isset($request->amount) ? $request->percentage : null, 
                'type' => $request->type
            ];

            $this->payrollSetting->create($data);
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Allowance Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-settings-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $_GET['search'];
        $showDeleted = $request->show_deleted;

        $sql = $this->payrollSetting->builder()->where('type',$request->type)
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
            
            //Show Edit and Soft Delete Buttons
            if ($showDeleted) {
                $operate = BootstrapTableService::restoreButton(route('payroll-setting.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('payroll-setting.trash', $row->id));
            } else {
                $operate = BootstrapTableService::editButton(route('payroll-setting.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('payroll-setting.destroy', $row->id));
            }
            
            
           
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
       
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('payroll-settings-edit');
        $request->validate([
            'name' => 'required',
            'amount' => 'nullable|required_without_all:percentage',
            'percentage' => 'nullable|required_without_all:amount'
        ]);
        try {
            DB::beginTransaction();
            $data = [ 
                'user_id' => Auth::user()->id, 
                'name' => $request->name, 
                'amount' =>  $request->amount ?? null , 
                'percentage' => $request->percentage ?? null, 
                'type' => $request->type
            ];

            $this->payrollSetting->update($id, $data);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Allowance Controller -> Update Method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('payroll-settings-delete');
        try {
            $this->payrollSetting->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('payroll-settings-delete');
        try {
            $this->payrollSetting->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('payroll-settings-delete');
        try {
            $this->payrollSetting->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Online Exam Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }
}
