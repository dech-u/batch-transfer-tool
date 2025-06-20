<?php

namespace App\Http\Controllers;

use App\Repositories\Semester\SemesterInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Throwable;

class SemesterController extends Controller {
    private SemesterInterface $semester;
    private CachingService $cache;

    public function __construct(SemesterInterface $semester, CachingService $cachingService) {
        $this->semester = $semester;
        $this->cache = $cachingService;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('semester-list');
        return view('semester.index');
    }

    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('semester-create');
        $request->validate([
            'name' => [
                'required',
                new uniqueForSchool('semesters', 'name')
            ],
            'start_month' => 'required|min:1,max:12',
            'end_month'   => 'required|min:1,max:12|different:start_month',
        ]);

        try {
            $checkSemester = $this->checkIfMonthAlreadyExists($request->start_month, $request->end_month);
            if ($checkSemester['error']) {
                ResponseService::validationError($checkSemester['message'], $checkSemester['data']);
            }
            $this->semester->create($request->all());
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Session Year Controller -> Store method");
            ResponseService::errorResponse();
        }
    }


    public function update($id, Request $request) {
        ResponseService::noPermissionThenSendJson('semester-edit');
        $request->validate([
            'name' => ['required',
                       new uniqueForSchool('semesters', 'name', $id)
            ],
            'start_month' => 'required|min:1,max:12',
            'end_month'   => 'required|min:1,max:12|different:start_month',
        ]);

        try {
            $checkSemester = $this->checkIfMonthAlreadyExists($request->start_month, $request->end_month, $id);
            if ($checkSemester['error']) {
                ResponseService::validationError($checkSemester['message']);
            }

            $this->semester->update($id, $request->all());
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Semester Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    public function show() {
        ResponseService::noPermissionThenRedirect('semester-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');
        $showDeleted = request('show_deleted');

        $sql = $this->semester->builder()
            ->where(function($q) use($search){
                $q->when($search, function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")
                        ->orwhere('name', 'LIKE', "%$search%")
                        ->orwhere('start_month', 'LIKE', "%$search%")
                        ->orwhere('end_month', 'LIKE', "%$search%");
                });
            })
            ->when(!empty($showDeleted), function ($query) {
                $query->onlyTrashed();
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
            if ($showDeleted) {
                //Show Restore and Hard Delete Buttons
                $operate .= BootstrapTableService::restoreButton(route('semester.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('semester.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate .= BootstrapTableService::editButton(route('semester.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('semester.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('semester-delete');
        try {
            $this->semester->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Semester Controller -> Delete method");
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('semester-delete');
        try {
            $this->semester->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('semester-delete');
        try {

            $semester = $this->semester->findOnlyTrashedById($id);
            if ($semester->current) {
                $this->cache->removeSchoolCache(config('constants.CACHE.SCHOOL.SEMESTER'));
            }
            $semester->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Semester Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    /**
     * @param int $startMonth
     * @param int $endMonth
     * @param int|null $ignoreID - Optional
     * @return array
     */
    private function checkIfMonthAlreadyExists(int $startMonth, int $endMonth, int $ignoreID = null) {
        $months = [
            trans("January"),
            trans("February"),
            trans("March"),
            trans("April"),
            trans("May"),
            trans("June"),
            trans("July"),
            trans("August"),
            trans("September"),
            trans("October"),
            trans("November"),
            trans("December")
        ];

//        $semester = $this->semester->builder()->where(function ($q) use ($month) {
//            $q->where(function ($q) use ($month) {
//                $q->where('start_month', '>=', $month)->where('end_month', '>=', $month);
//            })->orWhere(function ($q) use ($month) {
//                $q->where('start_month', '<=', $month)->where('end_month', '<=', $month);
//            })->orWhere(function ($q) use ($month) {
//                $q->where('start_month', '>=', $month)->where('end_month', '<=', $month);
//            })->orWhere(function ($q) use ($month) {
//                $q->where('start_month', '<=', $month)->where('end_month', '>=', $month);
//            });
//        });

        $semesters = $this->semester->builder()->withTrashed();

        if ($ignoreID !== null) {
            $semesters = $semesters->where('id', '!=', $ignoreID);
        }
        $semesters = $semesters->get();
        $occupiedMonths = [];
        foreach ($semesters as $semester) {
            if ($semester->start_month < $semester->end_month) {
                for ($i = $semester->start_month; $i <= $semester->end_month; $i++) {
                    $occupiedMonths[] = $i;
                }
            } else {
                for ($i = $semester->start_month; $i <= 12; $i++) {
                    $occupiedMonths[] = $i;
                }

                for ($i = 1; $i <= $semester->end_month; $i++) {
                    $occupiedMonths[] = $i;
                }
            }
        }


        $currentMonthRange = [];
        if ($startMonth < $endMonth) {
            for ($i = $startMonth; $i <= $endMonth; $i++) {
                $currentMonthRange[] = $i;
            }
        } else {
            for ($i = $startMonth; $i <= 12; $i++) {
                $currentMonthRange[] = $i;
            }

            for ($i = 1; $i <= $endMonth; $i++) {
                $currentMonthRange[] = $i;
            }
        }
        $commonMonths = array_intersect($currentMonthRange, $occupiedMonths);
        if (count($commonMonths)) {
            $commonMonths = array_values($commonMonths);
            return [
                'error'   => true,
                'message' => $months[$commonMonths[0] - 1] . " " . trans("Month is already Occupied"),
                'data'    => [
                ]
            ];
        }

        return [
            'error'   => false,
            'message' => 'success'
        ];
    }
}
