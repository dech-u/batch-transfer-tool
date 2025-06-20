<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\SchoolDataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed:school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $schools = School::withTrashed()->get();
        $schoolService = app(SchoolDataService::class);
        foreach ($schools as $key => $school) {
            if ($school->database_name) {
                Config::set('database.connections.school.database', $school->database_name);
                DB::purge('school');
                DB::connection('school')->reconnect();
                DB::setDefaultConnection('school');
                /*
                Permission
                Sync permission to roles
                    School admin
                    Teacher
                    Student
                    Guardian
                */
                $schoolService->createPermissions();
                $schoolService->createSchoolAdminRole($school);
                $schoolService->createTeacherRole($school);
                $schoolService->defaultRoles($school);

                
            }
            
        }
    }
}
