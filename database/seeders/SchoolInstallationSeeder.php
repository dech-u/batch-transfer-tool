<?php

namespace Database\Seeders;

use App\Models\School;
use App\Services\SchoolDataService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SchoolInstallationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private SchoolDataService $schoolService;

    public function __construct(SchoolDataService $schoolService) {
        $this->schoolService = $schoolService;
    }

    public function run(): void
    {
        //
        $schools = School::on('mysql')->withTrashed()->get();
        foreach ($schools as $key => $school) {
            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            $this->schoolService->createPermissions();
            $this->schoolService->createSchoolAdminRole($school);
            $this->schoolService->createTeacherRole($school);
        }
        
    }
}
