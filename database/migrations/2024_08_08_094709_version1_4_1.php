<?php

use App\Models\FeesAdvance;
use App\Models\School;
use App\Models\Staff;
use App\Models\StaffSalary;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\SchoolDataService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vesion 1.4.0
        $migrate = app(SchoolDataService::class);

        $schools = School::withTrashed()->get();
        foreach ($schools as $key => $school) {
            DB::setDefaultConnection('mysql');
            $school_name = str_replace('.','_',$school->name);
            $database_name = 'eschool_saas_'.$school->id.'_'.strtolower(strtok($school_name," "));
            
            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";

            $db = DB::select($query, [$database_name]);
            if (empty($db)) {
                DB::statement("CREATE DATABASE {$database_name}");
            }
            $school->database_name = $database_name;
            $school->save();
            // Migrate school database
            $migrate->createDatabaseMigration($school);

            Config::set('database.connections.school.database', $database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');

            // Get data
            // $schoolData = DB::table('students')->where('school_id', 1)->get();

            // Insert
            // DB::connection('school')->table('students')->insert($schoolData->toArray());
            try {
                DB::beginTransaction();
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::connection('school')->statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::connection('school')->statement("SET SESSION sql_mode = ''");

                // Add default school role
                DB::connection('school')->table('roles')->upsert(['id' => 2, 'name' => 'School Admin','guard_name' => 'web','school_id' => $school->id,'custom_role' => 0,'editable' => 0], ['id']);

                DB::connection('school')->table('roles')->upsert(['id' => 5, 'name' => 'Teacher','guard_name' => 'web','school_id' => null,'custom_role' => 0,'editable' => 0], ['id']);

                // Add school database name in school settings table
                DB::connection('school')->table('school_settings')->upsert(['name' => 'database_name', 'data' => $database_name,'type' => 'text','school_id' => $school->id], ['name','school_id']);


                // Teacher
                $teacher_ids = User::on('mysql')->where('school_id',$school->id)->pluck('id')->toArray();

                // Guradian
                $students = DB::connection('mysql')->table('students')->where('school_id', $school->id)->get();
                $student_ids = $students->pluck('user_id')->toArray();
                $guardian_ids = $students->pluck('guardian_id')->toArray();
                $guardian_users = DB::connection('mysql')->table('users')->whereIn('id', $guardian_ids)->get();

                $rowsArray = $guardian_users->map(function ($item) {
                    $array = (array) $item;
                    return $array;
                })->toArray();

                DB::connection('school')->table('users')->upsert($rowsArray, ['id']);
                

                // With school_id tables
                $withSchoolIdTables = ["academic_calendars", "announcements", "announcement_classes", "assignments", "assignment_submissions", "attendances", "categories", "certificate_templates", "classes", "class_groups", "class_sections", "class_subjects", "class_teachers", "compulsory_fees", "database_backups", "elective_subject_groups", "exams", "exam_marks", "exam_results", "exam_timetables", "expenses", "expense_categories", "extra_student_datas", "faqs", "fees", "fees_class_types", "fees_installments", "fees_paids", "fees_types", "files", "form_fields", "galleries", "grades", "holidays", "leaves", "leave_details", "leave_masters", "lessons", "lesson_topics", "mediums", "notifications", "online_exams", "online_exam_questions", "online_exam_question_choices", "online_exam_question_options", "online_exam_student_answers", "optional_fees", "payment_configurations", "payment_transactions", "payroll_settings", "promote_students", "roles", "school_settings", "sections", "semesters", "session_years", "shifts", "sliders", "staff_payrolls", "staff_support_schools", "streams", "students", "student_online_exam_statuses", "student_subjects", "subjects", "subject_teachers", "timetables", "users", "user_status_for_next_cycles"];

                foreach ($withSchoolIdTables as $key => $table) {
                    $rows = DB::connection('mysql')->table($table)->where('school_id', $school->id)->get();

                    // if ($table == 'roles') {
                    //     $rows = DB::connection('mysql')->table($table)->where('school_id', $school->id)->where('name','!=','Teacher')->get();
                    // } else {
                    //     $rows = DB::connection('mysql')->table($table)->where('school_id', $school->id)->get();
                    // }
                    

                    $rowsArray = $rows->map(function ($item) use ($table) {
                        $array = (array) $item;
                
                        // Exclude the generated column for the 'class_subjects' table
                        if ($table === 'class_subjects') {
                            unset($array['virtual_semester_id']);
                        }
                
                        return $array;
                    })->toArray();

                    DB::connection('school')->table($table)->upsert($rowsArray, ['id']);
                }

                // Without school_id tables
                $withoutSchoolIdTables = ["model_has_permissions", "model_has_roles", "permissions", "role_has_permissions"];
                
                foreach ($withoutSchoolIdTables as $key => $table) {
                    $rows = DB::connection('mysql')->table($table)->get();
                    $rowsArray = $rows->map(function ($item) {
                        return (array) $item;
                    })->toArray();
                    DB::connection('school')->table($table)->insert($rowsArray);
                }
                // fees_advance, schools, staffs, staff_salaries
                $fees_advances = FeesAdvance::on('mysql')->whereHas('user',function($q) use($school) {
                    $q->where('school_id',$school->id);
                })->get();
                DB::connection('school')->table('fees_advance')->upsert($fees_advances->toArray(), ['id']);

                // $schoolRow = School::where('id',$school->id)->get();
                DB::connection('school')->table('schools')->upsert($school->toArray(), ['id']);

                $staffs = Staff::on('mysql')->whereHas('user',function($q) use($school) {
                    $q->where('school_id',$school->id);
                })->get();
                DB::connection('school')->table('staffs')->upsert($staffs->toArray(), ['id']);

                $staffIds = $staffs->pluck('id')->toArray();
                $staffSalaries = StaffSalary::on('mysql')->whereIn('staff_id',$staffIds)->get();
                DB::connection('school')->table('staff_salaries')->upsert($staffSalaries->toArray(), ['id']);

                DB::setDefaultConnection('school');
                // Assign guardian roles
                $guardians = User::on('school')->whereIn('id',$guardian_ids)->get();
                foreach ($guardians as $key => $guardian) {
                    $guardian->school_id = $school->id;
                    $guardian->save();
                    $guardian->assignRole('Guardian');
                }

                // Assign student roles
                $students = User::on('school')->whereIn('id',$student_ids)->get();
                foreach ($students as $key => $student) {
                    $student->assignRole('Student');
                }

                // Assign teacher roles
                $teachers = User::on('school')->whereIn('id',$teacher_ids)->get();
                foreach ($teachers as $key => $teacher) {
                    $teacher->assignRole('Teacher');
                }

                $updateSchool = School::on('school')->find($school->id);
                $updateSchool->database_name = $database_name;
                $updateSchool->save();

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                DB::connection('school')->statement('SET FOREIGN_KEY_CHECKS=1;');
                
                DB::connection('school')->statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'");

                DB::commit();
            } catch (\Throwable $th) {
                
            }
            
        }

        $systemSettings[] = [
            'name' => 'system_version',
            'data' => '1.4.0',
            'type' => 'text'
        ];
        SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);

        Cache::flush();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schools = School::withTrashed()->get();
        foreach ($schools as $key => $school) {
            DB::statement("DROP DATABASE `{$school->database_name}`");
        }
    }
};
