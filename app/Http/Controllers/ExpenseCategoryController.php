<?php

namespace App\Http\Controllers;

use App\Repositories\ExpenseCategory\ExpenseCategoryInterface;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExpenseCategoryController extends Controller
{

    private ExpenseCategoryInterface $expenseCategory;

    public function __construct(ExpenseCategoryInterface $expenseCategory)
    {
        $this->expenseCategory = $expenseCategory;
    }

    public function index()
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noAnyPermissionThenRedirect(['expense-category-create','expense-category-list']);

        return view('expense.category');
    }

    public function create()
    {
        //
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('expense-category-create');
    }


    public function store(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('expense-category-create');
        $request->validate([
            'name' => 'required'
        ]);
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request->name,
                'description' => $request->description
            ];
            $this->expenseCategory->create($data);
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Expense Category Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }


    public function show()
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('expense-category-list');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');
        $showDeleted = request('show_deleted');

        $sql = $this->expenseCategory->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                });
                });
            })->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed();
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
            if (empty($showDeleted)) {
                $operate .= BootstrapTableService::editButton(route('expense-category.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('expense-category.destroy', $row->id));
            } else {
                $operate .= BootstrapTableService::restoreButton(route('expense-category.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('expense-category.trash', $row->id));
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function edit()
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenRedirect('expense-category-edit');
    }

    public function update(Request $request, $id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('expense-category-edit');
        try {
            DB::beginTransaction();
            $data = [
                'name' => $request->name,
                'description' => $request->description
            ];
            $this->expenseCategory->update($id, $data);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Expense Category Controller -> Update Method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('expense-category-delete');

        try {
            DB::beginTransaction();
            $this->expenseCategory->deleteById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Expense Category Controller -> Delete Method");
            ResponseService::errorResponse();
        }
    }

    public function restore($id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('expense-category-delete');

        try {
            DB::beginTransaction();
            $this->expenseCategory->restoreById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Expense Category Controller -> Restore Method");
            ResponseService::errorResponse();
        }
    }

    public function trash($id)
    {
        ResponseService::noFeatureThenRedirect('Expense Management');
        ResponseService::noPermissionThenSendJson('expense-category-delete');

        try {
            DB::beginTransaction();
            $category = $this->expenseCategory->findOnlyTrashedById($id);
            if (count($category->expense)) {
                ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
            } else {
                $this->expenseCategory->permanentlyDeleteById($id);
            }
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Expense Category Controller -> Restore Method");
            ResponseService::errorResponse();
        }
    }
}
