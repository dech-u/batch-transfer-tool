<?php

namespace App\Imports;

use App\Repositories\User\UserInterface;
use App\Services\CachingService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Staff\StaffInterface;
use App\Repositories\StaffSupportSchool\StaffSupportSchoolInterface;
use App\Services\SubscriptionService;
use App\Services\UserService;
use Str;
use Throwable;
use TypeError;

class StaffImport implements ToCollection, WithHeadingRow
{
    private $roleID;
    private $is_send_notification;

    public function __construct($roleID, $is_send_notification)
    {
        $this->roleID = $roleID;
        $this->is_send_notification = $is_send_notification;
    }

    public function collection(Collection $collection)
    {
        $subscription = app(SubscriptionInterface::class);
        $user = app(UserInterface::class);
        $cache = app(CachingService::class);
        $staff = app(StaffInterface::class);
        $staffSupportSchool = app(StaffSupportSchoolInterface::class);


        $school_id = Auth::user()->school_id ;

        $validator = Validator::make($collection->toArray(), [
            '*.first_name'     => 'required',
            '*.last_name'      => 'required',
            '*.mobile'         => 'required|numeric|digits_between:1,16',
            '*.email'      => 'required|email',
            '*.dob'            => 'required|date',
            '*.salary'            => 'required|numeric',
        ],[
            '*.dob.date' => 'Please ensure that the dob date format you use is either DD-MM-YYYY or MM/DD/YYYY.'
        ]);

        $validator->validate();

        if (Auth::user()->school_id) {
            $today_date = Carbon::now()->format('Y-m-d');
            $subscription = $subscription->builder()->doesntHave('subscription_bill')->whereDate('start_date', '<=', $today_date)->where('end_date', '>=', $today_date)->whereHas('package', function ($q) {
                $q->where('is_trial', 1);
            })->first();

            if ($subscription) {
                $systemSettings = $cache->getSystemSettings();
                $staff_count = $user->builder()->role('Teacher')->withTrashed()->orWhereHas('roles', function ($q) {
                    $q->where('custom_role', 1)->whereNot('name', 'Teacher');
                })->whereNotNull('school_id')->Owner()->count();
                if ($staff_count >= $systemSettings['staff_limit']) {
                    $message = "The free trial allows only " . $systemSettings['staff_limit'] . " staff.";
                    ResponseService::errorResponse($message);
                }
            }
        }

        DB::beginTransaction();
        foreach($collection as $row)
        {
            try {
                $role = Role::findOrFail($this->roleID);
                
                $existingUser = $user->builder()->where('email', $row['email'])->first();
                $id = $existingUser ? $existingUser->id : null;

                $users = $user->updateOrCreate(['id' => $id ],[
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'mobile' => $row['mobile'],
                    'email' => $row['email'],
                    'dob' => $row['dob'],
                    'password'   => Hash::make($row['mobile']),
                    'status'     => 0,
                    'deleted_at' => '1970-01-01 01:00:00'
                ]);
                
                $users->assignRole($role);

                if ($users->school_id) {
                    $leave_permission = [
                        'leave-list',
                        'leave-create',
                        'leave-edit',
                        'leave-delete',
                    ];
                    $users->givePermissionTo($leave_permission);
                }
    
                $staff->updateOrCreate( ['user_id' => $users->id] ,[
                    'user_id'       => $users->id,
                    'qualification' => null,
                    'salary'        => $row['salary'] ?? 0,
                    'joining_date'  => date('Y-m-d',strtotime($row['joining_date']))
                ]);
    
    
                if ($school_id) {
                    $data[] = array(
                        'user_id'   => $users->id,
                        'school_id' => $school_id
                    );   
                    // $staffSupportSchool->upsert($data, ['user_id', 'school_id'], ['user_id', 'school_id']);
                }

                if ($users->school_id) {
                    $sendEmail = app(UserService::class);
                    if ($this->is_send_notification) {
                        $sendEmail->sendStaffRegistrationEmail($users, $users->mobile);
                    }
                }

            } catch (Throwable $e) {
                // IF Exception is TypeError and message contains Mail keywords then email is not sent successfully
                if (Str::contains($e->getMessage(), ['Failed', 'Mail', 'Mailer', 'MailManager'])) {
                    continue;
                }
                DB::rollBack();
                throw $e;
            }
        }
        DB::commit();
        return true;
    }
}


