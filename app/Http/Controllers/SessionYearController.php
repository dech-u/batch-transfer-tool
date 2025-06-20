<?php

namespace App\Http\Controllers;

use App\Repositories\Chat\ChatInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SessionYearController extends Controller {
    private SessionYearInterface $sessionYear;
    private CachingService $cache;
    private SchoolSettingInterface $schoolSettings;
    private ChatInterface $chat;

    public function __construct(SessionYearInterface $sessionYear, CachingService $cache, SchoolSettingInterface $schoolSettings, ChatInterface $chat) {
        $this->sessionYear = $sessionYear;
        $this->cache = $cache;
        $this->schoolSettings = $schoolSettings;
        $this->chat = $chat;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('session-year-list');
        return view('session_years.index');
    }

    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('session-year-create');
        $request->validate([
            'name' => ['required', new uniqueForSchool('session_years', 'name')],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $this->sessionYear->create($request->all());
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Session Year Controller -> Store method");
            ResponseService::errorResponse();
        }
    }


    public function update($id, Request $request) {
        ResponseService::noPermissionThenSendJson('session-year-edit');
        $request->validate([
            'name' => ['required', new uniqueForSchool('session_years', 'name', $id)],
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $this->sessionYear->update($id, $request->all());
            $this->cache->removeSchoolCache(config("constants.CACHE.SCHOOL.SESSION_YEAR"));
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Session Year Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    public function show() {
        ResponseService::noPermissionThenRedirect('session-year-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $showDeleted = request('show_deleted');

        $sql = $this->sessionYear->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                    ->orwhere('name', 'LIKE', "%$search%")
                    ->orwhere('start_date', 'LIKE', "%$search%")
                    ->orwhere('end_date', 'LIKE', "%$search%");
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
                $operate .= BootstrapTableService::restoreButton(route('session-year.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('session-year.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                if (!$row->default) {
                    $operate .= BootstrapTableService::button('fa fa-calendar-check-o', route('session-year.default', $row->id), ['btn-gradient-success', 'default-session-year'], ["title" => trans("Set Default Session Year")]);
                }
                $operate .= BootstrapTableService::editButton(route('session-year.update', $row->id));
                if (!$row->default) {
                    $operate .= BootstrapTableService::deleteButton(route('session-year.destroy', $row->id));
                }

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
        ResponseService::noPermissionThenSendJson('session-year-delete');
        try {
            DB::beginTransaction();
            $year = $this->sessionYear->findById($id);
            if ($year->default == 1) {
                $response = array(
                    'error'   => true,
                    'message' => trans('default_session_year_cannot_delete')
                );
            } else {
                $this->sessionYear->deleteById($id);
                DB::commit();
                ResponseService::successResponse('Data Deleted Successfully');
            }
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Session Year Controller -> Delete method");
            ResponseService::errorResponse();
        }
        return response()->json($response);
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('session-year-delete');
        try {
            $this->sessionYear->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('session-year-delete');
        try {
            $this->sessionYear->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Session Year Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function default($id) {
        ResponseService::noPermissionThenRedirect('session-year-delete');
        try {
            DB::beginTransaction();
            $defaultSessionYear = $this->cache->getDefaultSessionYear();
            $this->chat->builder()->whereDate('created_at','<=',$defaultSessionYear->end_date)->delete();
            
            // Change the Current Default Session Year to Non-Default Session Year
            $this->sessionYear->builder()->where(['default' => 1])->update(['default' => 0]);
            
            // Make new SessionYear as Default Session Year
            $this->sessionYear->builder()->where('id', $id)->update(['default' => 1]);
            $data[] = [
                "name" => 'session_year',
                "data" => $id,
                "type" => "number",
            ];
            $this->schoolSettings->upsert($data, ["name"], ["data"]);
            $this->cache->removeSchoolCache(config("constants.CACHE.SCHOOL.SESSION_YEAR"));
            DB::commit();
            ResponseService::successResponse("Default Session has been Changed SuccessFully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
