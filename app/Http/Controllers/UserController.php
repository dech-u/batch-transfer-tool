<?php

namespace App\Http\Controllers;

use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\User\UserInterface;
use App\Repositories\UserStatusForNextCycle\UserStatusForNextCycleInterface;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    //
    private UserInterface $user;
    private ClassSectionInterface $classSection;
    private UserStatusForNextCycleInterface $userStatus; 

    public function __construct(UserInterface $user, ClassSectionInterface $classSection, UserStatusForNextCycleInterface $userStatus) {
        $this->user = $user;
        $this->classSection = $classSection;
        $this->userStatus = $userStatus;
    }


    public function status()
    {
        $classSection = $this->classSection->builder()->with('class','section','medium')->get()->pluck('full_name','id');
        return view('user_status',compact('classSection'));
    }

    public function show() {

        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $role = request('role');
        $class_section_id = request('class_section_id');

        $sql = $this->user->builder()->withTrashed()->with('user_status')->whereHas('roles',function($q) {
            $q->whereNot('name','Guardian');
        });
        if ($search) {
            $sql->where(function ($query) use ($search) {
                $query->whereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'");
            });
        }

        if ($role) {
            if ($role == 1) {
                $sql->role('Student');
                if ($class_section_id) {
                    $sql->whereHas('student',function($q) use($class_section_id) {
                        $q->where('class_section_id',$class_section_id);
                    });
                    $total = $sql->count();
                    $sql->orderBy($sort, $order);
                    $res = $sql->get();
                }
            }
            if ($role == 2) {
                $sql->whereHas('roles',function($q) {
                    $q->whereNotIn('name',['Student','Guardian','School Admin']);
                });
                $total = $sql->count();
                $sql->orderBy($sort, $order);
                $res = $sql->get();
            }
        }

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function status_change(Request $request)
    {
        $request->validate([
            'user_status' => 'required'
        ],[
            'user_status.required' => 'No records found'
        ]);
        $data = array();
        foreach ($request->user_status as $key => $status) {
            $data[] = [
                'user_id' => $key,
                'status' => $status['type']
            ];
        }

        $this->userStatus->upsert($data, ['user_id'],['user_id','status']);
        ResponseService::successResponse('Data Stored Successfully');
    }

    public function birthday($type = null)
    {
        try {
            $users = $this->user->builder()->select('id','first_name','last_name','dob','image')->has('student');
            if ($type == 'today' || $type == '') {
                $users = $users->whereMonth('dob',Carbon::now()->format('m'))->whereDay('dob',Carbon::now()->format('d'));
            } elseif ($type == 'this_month') {
                $users = $users->whereMonth('dob',Carbon::now()->format('m'));
            } else {
                $users = $users->whereMonth('dob',Carbon::now()->addMonth()->format('m'));
            }
            $users = $users->get()->append(['dob_date']);

            ResponseService::successResponse('Data Fetched Successfully',$users);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
