<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\SessionYear;
use App\Models\User;
use Artisan as GlobalArtisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;

class SchoolDataService {

    public function preSettingsSetup($schoolData) {

        DB::setDefaultConnection('school');
        Config::set('database.connections.school.database', $schoolData->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        $school = New School();
        $school->id = $schoolData->id;
        $school->name = $schoolData->name;
        $school->address = $schoolData->address;
        $school->support_phone = $schoolData->support_phone;
        $school->support_email = $schoolData->support_email;
        $school->tagline = $schoolData->tagline;
        $school->logo = $schoolData->logo;
        $school->status = $schoolData->type == "demo" ? 1 : $schoolData->status;
        $school->domain = $schoolData->domain;
        $school->database_name = $schoolData->database_name;
        $school->code = $schoolData->code;
        $school->created_at = $schoolData->created_at;
        $school->updated_at = $schoolData->updated_at;
        $school->save();

        $mainUser = DB::connection('mysql')->table('users')->where('id',$schoolData->admin_id)->first();        

        $userRow[] = [
            'id' => $mainUser->id,
            'first_name' => $mainUser->first_name,
            'last_name' => $mainUser->last_name,
            'mobile' => $mainUser->mobile,
            'email' => $mainUser->email,
            'password' => $mainUser->password,
            'school_id' => $mainUser->school_id,
            'email_verified_at' => $schoolData->type == "demo" ? Carbon::now() : null,
            'created_at' => $mainUser->created_at,
            'updated_at' => $mainUser->updated_at,
        ];

        DB::connection('school')->table('users')->insert($userRow);

        $school = School::find($schoolData->id);
        $school->admin_id = $schoolData->admin_id;
        $school->save();



        $this->createPreSetupRole($schoolData);
        $sessionYear = SessionYear::updateOrCreate([
            'name'      => Carbon::now()->format('Y'),
            'school_id' => $schoolData->id
        ],
            ['default'    => 1,
             'start_date' => Carbon::now()->startOfYear()->format('Y-m-d'),
             'end_date'   => Carbon::now()->endOfYear()->format('Y-m-d'),
            ]);
        // Add School Setting Data
        $schoolSettingData = array(
            [
                'name'      => 'school_name',
                'data'      => $schoolData->name,
                'type'      => 'string',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'school_email',
                'data'      => $schoolData->support_email,
                'type'      => 'string',
                'school_id' => $schoolData->id
            ],
            [
                'name'      => 'school_phone',
                'data'      => $schoolData->support_phone,
                'type'      => 'number',
                'school_id' => $schoolData->id
            ],
            [
                'name'      => 'school_tagline',
                'data'      => $schoolData->tagline,
                'type'      => 'string',
                'school_id' => $schoolData->id
            ],
            [
                'name'      => 'school_address',
                'data'      => $schoolData->address,
                'type'      => 'string',
                'school_id' => $schoolData->id
            ],
            [
                'name'      => 'session_year',
                'data'      => $sessionYear->id,
                'type'      => 'number',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'horizontal_logo',
                'data'      => '',
                'type'      => 'file',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'vertical_logo',
                'data'      => '',
                'type'      => 'file',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'timetable_start_time',
                'data'      => '09:00:00',
                'type'      => 'time',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'timetable_end_time',
                'data'      => '18:00:00',
                'type'      => 'time',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'timetable_duration',
                'data'      => '01:00:00',
                'type'      => 'time',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'auto_renewal_plan',
                'data'      => '1',
                'type'      => 'integer',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'currency_code',
                'data'      => 'INR',
                'type'      => 'string',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'currency_symbol',
                'data'      => '₹',
                'type'      => 'string',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'date_format',
                'data'      => 'd-m-Y',
                'type'      => 'string',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'time_format',
                'data'      => 'h:i A',
                'type'      => 'string',
                'school_id' => $schoolData->id,
            ],
            [
                'name'      => 'domain',
                'data'      => $schoolData->domain ?? '',
                'type'      => 'string',
                'school_id' => $schoolData->id,
            ],

            [
                'name' => 'email-template-staff',
                'data' => '&lt;p&gt;Dear {full_name},&lt;/p&gt; &lt;p&gt;Welcome to {school_name}!&lt;/p&gt; &lt;p&gt;We are excited to have you join our team. Below are your registration details to access the {school_name}:&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Your Registration Details:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Registration URL:&lt;/strong&gt; {url}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Code:&lt;/strong&gt; {code}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Email:&lt;/strong&gt; {email}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Steps to Complete Your Registration:&lt;/strong&gt;&lt;/p&gt; &lt;ol&gt; &lt;li&gt;Click on the registration URL provided above.&lt;/li&gt; &lt;li&gt;Enter your email and password.&lt;/li&gt; &lt;li&gt;Follow the on-screen instructions to set up your profile.&lt;/li&gt; &lt;/ol&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;For security reasons, please change your password upon your first login.&lt;/li&gt; &lt;li&gt;If you have any questions or need assistance during the registration process, please contact our support team at {support_email} or call {support_contact}.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;&lt;strong&gt;App Download Links:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Android:&lt;/strong&gt; {android_app}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;iOS:&lt;/strong&gt; {ios_app}&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;We look forward to a successful academic year with you on our team. Thank you for your commitment to excellence in education.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{school_name}&lt;br&gt;{support_email}&lt;br&gt;{support_contact}&lt;br&gt;{url}&lt;/p&gt;',
                'type' => 'text',
                'school_id' => $schoolData->id
            ],
            [
                'name' => 'email-template-parent',
                'data' => '&lt;p&gt;Dear {parent_name},&lt;/p&gt; &lt;p&gt;We are delighted to welcome {child_name} to {school_name}!&lt;/p&gt; &lt;p&gt;As part of our registration process, we have created accounts for both you and your child in our {school_name}. Below are the registration details you will need to access the system, along with links to download our mobile app for your convenience.&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Student Credential Details:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Name:&lt;/strong&gt; {child_name}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Admission No.: &lt;/strong&gt;{admission_no}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Code:&lt;/strong&gt; {code}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;GR No.:&lt;/strong&gt; {grno}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {child_password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Parent Credential Details:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Name:&lt;/strong&gt; {parent_name}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Code:&lt;/strong&gt; {code}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Email:&lt;/strong&gt; {email}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;App Download Links:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Android:&lt;/strong&gt; {android_app}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;iOS:&lt;/strong&gt; {ios_app}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Steps to Complete the Registration:&lt;/strong&gt;&lt;/p&gt; &lt;ol&gt; &lt;li&gt;Download the school management app using the links above for easier access on your mobile devices.&lt;/li&gt; &lt;li&gt;Enter the email and password for either the student or parent account.&lt;/li&gt; &lt;li&gt;Follow the on-screen instructions to complete the profile setup.&lt;/li&gt; &lt;/ol&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;For security reasons, please ensure that both the student and parent passwords are changed upon first login.&lt;/li&gt; &lt;li&gt;If you encounter any issues during the registration process, please do not hesitate to contact our support team at {support_email} or call {support_contact}.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;We look forward to an enriching educational experience for {child_name} at {school_name}. Thank you for entrusting us with your child&#039;s education.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{school_name}&lt;br&gt;{support_email}&lt;/p&gt;',
                'type' => 'text',
                'school_id' => $schoolData->id
            ],
            [
                'name' => 'email-template-application-reject',
                'data' => '&lt;p&gt;Dear {child_name},&lt;/p&gt; &lt;p&gt;We regret to inform you that your application for admission to {school_name} has been rejected. After a thorough review, it was found that your application did not meet certain criteria required for enrollment. Please note that this decision was made based on valid reasons, Unfortunately, all available seats for the requested grade have already been filled.&lt;/p&gt; &lt;p&gt;We encourage you to reach out to the admissions office if you have any questions or require further clarification.&lt;/p&gt; &lt;p&gt;Thank you for your interest in our school.&lt;/p&gt; &lt;p&gt;Sincerely,&lt;br&gt;{school_name}&lt;/p&gt; &lt;p&gt;Admissions Team&lt;/p&gt;',
                'type' => 'text',
                'school_id' => $schoolData->id
            ],


        );
        SchoolSetting::upsert($schoolSettingData, ["name", "school_id"], ["data", "type"]);
    }
    
    public function createPreSetupRole($school) {

        DB::setDefaultConnection('school');
        Config::set('database.connections.school.database', $school->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        $this->createPermissions();

        $this->createSchoolAdminRole($school);

        $schoolAdminUser = User::on('school')->where('id', $school->admin_id)->first();
        $user = $schoolAdminUser->setConnection('school');
        $user->assignRole('School Admin');

        $this->defaultRoles($school);

        // Create teacher role
        $this->createTeacherRole($school);
    }

    public function defaultRoles($school)
    {
        Role::updateOrCreate(['name' => 'Guardian', 'school_id' => $school->id, 'custom_role' => 0, 'editable' => 0]);
        Role::updateOrCreate(['name' => 'Student', 'school_id' => $school->id, 'custom_role' => 0, 'editable' => 0]);
    }

    public function createDatabaseMigration($schoolData)
    {
        $school_name = str_replace('.','_',$schoolData->name);
        // $database_name = 'eschool_saas_'.$schoolData->id.'_'.strtolower(strtok($school_name," "));
        
        // Check if $school_name is an array and convert it to a string
        if (is_array($school_name)) {
            $school_name = implode(" ", $school_name); // Join the array elements into a string
        }
        
        // Now apply strtok and strtolower
        $database_name = 'eschool_saas_'.$schoolData->id.'_'.strtolower(strtok($school_name, " "));

            
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";

        $db = DB::select($query, [$database_name]);
        
        if (empty($db)) {
            DB::statement("CREATE DATABASE {$database_name}");
        }

        $schoolData->database_name = $database_name;
        $schoolData->save();
        
        // Artisan::call('migrate:school');
        Config::set('database.connections.school.database', $schoolData->database_name);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');
        Artisan::call('migrate', [
            '--database' => 'school',
            '--path' => 'database/migrations/schools',
            '--force' => true,
        ]);
        
    }

    public function createPermissions() {

        $permissions = [
            ...self::permission('role'),
            ...self::permission('medium'),
            ...self::permission('section'),
            ...self::permission('class'),
            ...self::permission('class-section'),
            ...self::permission('subject'),
            ...self::permission('teacher'),
            ...self::permission('guardian'),
            ...self::permission('session-year'),
            ...self::permission('student'),
            ...self::permission('timetable'),
            ...self::permission('attendance'),
            ...self::permission('holiday'),
            ...self::permission('announcement'),
            ...self::permission('slider'),
            ...self::permission('promote-student'),
            ...self::permission('language'),
            ...self::permission('lesson'),
            ...self::permission('topic'),
            ...self::permission('schools'),
            ...self::permission('form-fields'),
            ...self::permission('grade'),
            ...self::permission('package'),
            ...self::permission('addons'),
            ...self::permission('guidance'),


            ...self::permission('assignment'),
            ['name' => 'assignment-submission'],

            ...self::permission('exam'),
            ...self::permission('exam-timetable'),
            ['name' => 'exam-upload-marks'],
            ['name' => 'exam-result'],
            ['name' => 'exam-result-edit'],

            ['name' => 'system-setting-manage'],
            ['name' => 'fcm-setting-create'],
            ['name' => 'email-setting-create'],
            ['name' => 'privacy-policy'],
            ['name' => 'contact-us'],
            ['name' => 'about-us'],
            ['name' => 'terms-condition'],

            ['name' => 'class-teacher'],
            ['name' => 'student-reset-password'],
            ['name' => 'reset-password-list'],
            ['name' => 'student-change-password'],

            ['name' => 'fees-classes'],
            ['name' => 'fees-paid'],
            ['name' => 'fees-config'],

            ['name' => 'school-setting-manage'],
            ['name' => 'app-settings'],
            ['name' => 'subscription-view'],

            ...self::permission('online-exam'),
            ...self::permission('online-exam-questions'),
            ['name' => 'online-exam-result-list'],
            ...self::permission('fees-type'),
            ...self::permission('fees-class'),
            ...self::permission('role'),
            ...self::permission('staff'),
            ...self::permission('expense-category'),
            ...self::permission('expense'),
            ...self::permission('semester'),
            ...self::permission('payroll'),
            ...self::permission('stream'),
            ...self::permission('shift'),
            ...self::permission('leave'),
            ['name' => 'approve-leave'],
            ...self::permission('faqs'),

            ['name' => 'fcm-setting-manage'],

            ...self::permission('fees'),
            ...self::permission('transfer-student'),
            ...self::permission('gallery'),
            ...self::permission('notification'),

            ['name' => 'payment-settings'],

            ['name' => 'subscription-settings'],
            ['name' => 'subscription-change-bills'],
            ['name' => 'school-terms-condition'],

            ['name' => 'id-card-settings'],

            ['name' => 'subscription-bill-payment'],
            ['name' => 'web-settings'],

            ...self::permission('certificate'),

            ...self::permission('payroll-settings'),

            ['name' => 'school-web-settings' ],
            ...self::permission('class-group'),

            ['name' => 'email-template' ],
            ['name' => 'database-backup' ],
            ['name' => 'view-exam-marks']

            

        ];
        $permissions = array_map(static function ($data) {
            $data['guard_name'] = 'web';
            return $data;
        }, $permissions);
        Permission::upsert($permissions, ['name'], ['name']);
    }

    public static function permission($prefix, array $customPermissions = []) {

        $list = [["name" => $prefix . '-list']];
        $create = [["name" => $prefix . '-create']];
        $edit = [["name" => $prefix . '-edit']];
        $delete = [["name" => $prefix . '-delete']];

        $finalArray = array_merge($list, $create, $edit, $delete);
        foreach ($customPermissions as $customPermission) {
            $finalArray[] = ["name" => $prefix . "-" . $customPermission];
        }
        return $finalArray;
    }

    public function createSchoolAdminRole($school) {
        $role = Role::withoutGlobalScope('school')->updateOrCreate(['name' => 'School Admin', 'custom_role' => 0, 'editable' => 0, 'school_id' => $school->id]);
        $SchoolAdminHasAccessTo = [
            'medium-list',
            'medium-create',
            'medium-edit',
            'medium-delete',

            'section-list',
            'section-create',
            'section-edit',
            'section-delete',

            'class-list',
            'class-create',
            'class-edit',
            'class-delete',

            'class-section-list',
            'class-section-create',
            'class-section-edit',
            'class-section-delete',

            'subject-list',
            'subject-create',
            'subject-edit',
            'subject-delete',

            'teacher-list',
            'teacher-create',
            'teacher-edit',
            'teacher-delete',

            'guardian-list',
            'guardian-create',
            'guardian-edit',
            'guardian-delete',

            'session-year-list',
            'session-year-create',
            'session-year-edit',
            'session-year-delete',

            'student-list',
            'student-create',
            'student-edit',
            'student-delete',

            'timetable-list',
            'timetable-create',
            'timetable-edit',
            'timetable-delete',

            'attendance-list',

            'holiday-list',
            'holiday-create',
            'holiday-edit',
            'holiday-delete',

            'announcement-list',
            'announcement-create',
            'announcement-edit',
            'announcement-delete',

            'slider-list',
            'slider-create',
            'slider-edit',
            'slider-delete',

            'exam-create',
            'exam-list',
            'exam-edit',
            'exam-delete',

            'exam-timetable-create',
            'exam-timetable-list',
            'exam-timetable-delete',

            'exam-result',
            'exam-result-edit',

            'assignment-submission',

            'student-reset-password',
            'reset-password-list',
            'student-change-password',

            'promote-student-list',
            'promote-student-create',
            'promote-student-edit',
            'promote-student-delete',

            'transfer-student-list',
            'transfer-student-create',
            'transfer-student-edit',
            'transfer-student-delete',

            'fees-paid',
            'fees-config',

            'form-fields-list',
            'form-fields-create',
            'form-fields-edit',
            'form-fields-delete',

            'grade-create',
            'grade-list',
            'grade-edit',
            'grade-delete',

            'school-setting-manage',

            'fees-type-list',
            'fees-type-create',
            'fees-type-edit',
            'fees-type-delete',

            'fees-class-list',
            'fees-class-create',
            'fees-class-edit',
            'fees-class-delete',


            'online-exam-create',
            'online-exam-list',
            'online-exam-edit',
            'online-exam-delete',
            'online-exam-questions-create',
            'online-exam-questions-list',
            'online-exam-questions-edit',
            'online-exam-questions-delete',
            'online-exam-result-list',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'staff-list',
            'staff-create',
            'staff-edit',
            'staff-delete',

            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',

            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-delete',

            'fees-list',
            'fees-create',
            'fees-edit',
            'fees-delete',

            'semester-list',
            'semester-create',
            'semester-edit',
            'semester-delete',

            'payroll-list',
            'payroll-create',
            'payroll-edit',
            'payroll-delete',

            'stream-list',
            'stream-create',
            'stream-edit',
            'stream-delete',

            'shift-list',
            'shift-create',
            'shift-edit',
            'shift-delete',

            'approve-leave',
            'id-card-settings',

            'gallery-list',
            'gallery-create',
            'gallery-edit',
            'gallery-delete',

            'notification-list',
            'notification-create',
            'notification-delete',

            'certificate-list',
            'certificate-create', 
            'certificate-edit',
            'certificate-delete',

            'payroll-settings-list',
            'payroll-settings-create',
            'payroll-settings-edit',
            'payroll-settings-delete',

            'school-web-settings',

            'faqs-list',
            'faqs-create',
            'faqs-edit',
            'faqs-delete',

            'class-group-list',
            'class-group-create',
            'class-group-edit',
            'class-group-delete',

            'email-template',
            'database-backup',
            'view-exam-marks'

        ];
        
        $role->syncPermissions($SchoolAdminHasAccessTo);
    }

    public function createTeacherRole($school)
    {
        //Add Teacher Role
        $teacher_role = Role::updateOrCreate(['name' => 'Teacher', 'school_id' => $school->id, 'custom_role' => 0, 'editable' => 1]);
        $TeacherHasAccessTo = [
            'student-list',
            'timetable-list',
            'holiday-list',
            'announcement-list',
            'announcement-create',
            'announcement-edit',
            'announcement-delete',
            'assignment-create',
            'assignment-list',
            'assignment-edit',
            'assignment-delete',
            'assignment-submission',
            'lesson-list',
            'lesson-create',
            'lesson-edit',
            'lesson-delete',
            'topic-list',
            'topic-create',
            'topic-edit',
            'topic-delete',
            'class-section-list',
            'online-exam-create',
            'online-exam-list',
            'online-exam-edit',
            'online-exam-delete',
            'online-exam-questions-create',
            'online-exam-questions-list',
            'online-exam-questions-edit',
            'online-exam-questions-delete',
            'online-exam-result-list',
            
            'leave-list',
            'leave-create',
            'leave-edit',
            'leave-delete',

            'attendance-list',
        ];
        $teacher_role->syncPermissions($TeacherHasAccessTo);
    }
    
    public static  function switchToMainDatabase()
    {
        DB::setDefaultConnection('mysql');
        Session::forget('school_database_name');
        Session::flush();
        Session::put('school_database_name', null);

    }

    public static function switchToSchoolDatabase($school_id)
    {
        $school_database = School::where('id',$school_id)->pluck('database_name')->first();

        DB::setDefaultConnection('school');
        Config::set('database.connections.school.database', $school_database);
        DB::purge('school');
        DB::connection('school')->reconnect();
        DB::setDefaultConnection('school');

        Session::put('school_database_name', $school_database);
        
    }
    
}
