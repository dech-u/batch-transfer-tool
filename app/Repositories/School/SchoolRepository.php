<?php

namespace App\Repositories\School;

use App\Models\School;
use App\Models\User;
use App\Repositories\Base\BaseRepository;
use App\Services\UploadService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolRepository extends BaseRepository implements SchoolInterface {
    public function __construct(School $model) {
        parent::__construct($model, 'school');
        $this->model = $model;
    }

    public function forceDelete(int $modelId): bool {
        $school_query = School::where('id', $modelId); // Query for School
        $admin_id = $school_query->pluck('admin_id')->first(); // Get the admin id
        $school_query->forceDelete(); // Delete School

        $user = User::findOrFail($admin_id);
        $user->forceDelete(); // Soft Delete the user
        return true;
    }


    public function updateSchoolAdmin($array, $image = null) {
        $data = (object)$array;
        DB::setDefaultConnection('mysql');
        
        $school = School::find($data->school_id);

        $user = User::on('mysql')->where('id',$data->id)->first();
        $folder = 'user';
        if ($user) {
            $image_path = '';
            if ($image) {
                $image_path = UploadService::upload($image, $folder);
                $image_path = $image_path;
            }

            $this->updateSchooldatabase($data, $school, $image_path);

        }

        // Delete old Admin
        // $admin_id = $this->all()->where('id', $data->school_id)->pluck('admin_id')->first();
        // $user = User::findOrFail($admin_id);
        // $user->school_id = null; // Update the school_id to null
        // $user->save(); // Save the Changes
        // $user->delete(); // Soft Delete the user
        

        // // Check that email is not ID
        // if (!is_numeric($data->email)) {
        //     // For image
        //     $school = School::findOrFail($data->school_id);
        //     $folder = 'user';
        //     if ($image) {
        //         $image_path = UploadService::upload($image, $folder);
        //         $array['image'] = $image_path;
        //     }

        //     // Add New Admin
        //     $admin = new User();
        //     $admin->password = Hash::make($data->contact);
        //     $admin->school_id = $data->school_id;
        //     $admin->mobile = $data->contact ?? null;
        //     $admin->fill($array);
        //     $admin->save();

        //     $school->admin_id = $admin->id;
        //     $school->save();

        //     Config::set('database.connections.school.database', $school->database_name);
        //     DB::purge('school');
        //     DB::connection('school')->reconnect();
        //     DB::setDefaultConnection('school');

        //     DB::connection('school')->table('users')->insert($admin->toArray());

        //     $schoolAdminUser = User::on('school')->where('id', $school->admin_id)->first();
        //     $user = $schoolAdminUser->setConnection('school');
        //     $user->assignRole('School Admin');

        //     //Add New Admin ID to School
        //     $school = School::findOrFail($data->school_id);
        //     $school->admin_id = $admin->id;
        //     $school->save();
            
        // } else {

        //     //Change Admin ID
        //     $school = School::findOrFail($data->school_id);
        //     $school->admin_id = $data->email;
        //     $school->save();

        //     // Add School ID to Respective Admins ID
        //     $user = User::withTrashed()->findOrFail($data->email);
        //     $user->school_id = $data->school_id; // Update the school_id
        //     $user->mobile = $data->contact ?? null;
        //     $user->deleted_at = null;
        //     if ($array['reset_password']) {
        //         $password = Hash::make($data->contact);
        //         $user->password = $password;
        //     }
        //     $user->save();

        //     Config::set('database.connections.school.database', $school->database_name);
        //     DB::purge('school');
        //     DB::connection('school')->reconnect();
        //     DB::setDefaultConnection('school');

        //     DB::connection('school')->table('users')->insert($user->toArray());

        //     $schoolAdminUser = User::on('school')->where('id', $school->admin_id)->first();
        //     $user = $schoolAdminUser->setConnection('school');
        //     $user->assignRole('School Admin');

        //     //Add New Admin ID to School
        //     $school = School::findOrFail($data->school_id);
        //     $school->admin_id = $user->id;
        //     $school->save();
        // }
    }

    public function updateSchooldatabase($user, $school, $image_path)
    {

        if ($user->reset_password) {
            $password = Hash::make($user->contact);
            $password = $password;

            $userRow[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile' => $user->contact,
                'email' => $user->email,
                'image' => $image_path,
                'password' => $password
            ];
        } else {
            $userRow[] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'mobile' => $user->contact,
                'email' => $user->email,
                'image' => $image_path,
            ];
        }
        

        Config::set('database.connections.school.database', $school->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        DB::connection('school')->table('users')->upsert($userRow,['id']);
        
        $schoolAdminUser = User::on('school')->where('id', $school->admin_id)->first();
        $user = $schoolAdminUser->setConnection('school');
        $user->assignRole('School Admin');

        DB::setDefaultConnection('mysql');
        DB::connection('mysql')->table('users')->upsert($userRow,['id']);
    }


    public function active() {
        return $this->defaultModel()->where('status', 1);
    }
}
