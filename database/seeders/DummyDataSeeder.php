<?php

namespace Database\Seeders;

use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Feature;
use App\Models\Mediums;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\School;
use App\Models\Section;
use App\Models\SessionYear;
use App\Models\Staff;
use App\Models\Students;
use App\Models\Subject;
use App\Models\User;
use App\Services\SchoolDataService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $this->createDummySchool();
    }


    public function createDummySchool()
    {
        $data = [
            [
                'image'             => 'users/school_admin.png',
                'password'          => Hash::make('school@123'),
                'first_name'        => 'School 1',
                'last_name'         => 'Demo 1',
                'email'             => 'school1@gmail.com',
                'mobile'            => 1234567890,
                'gender'            => 'male',
                'current_address'   => 'Bhuj',
                'permanent_address' => 'Bhuj',
            ],
            [
                'image'             => 'users/school_admin.png',
                'password'          => Hash::make('school@123'),
                'first_name'        => 'School 2',
                'last_name'         => 'Demo 2',
                'email'             => 'school2@gmail.com',
                'mobile'            => 1234567890,
                'gender'            => 'male',
                'current_address'   => 'Bhuj',
                'permanent_address' => 'Bhuj',
            ]
        ];

        User::upsert($data, ['email'], ['image', 'password', 'first_name', 'last_name', 'mobile', 'current_address', 'permanent_address']);
        $schoolAdmin1 = User::where('email', 'school1@gmail.com')->first();
        $schoolAdmin1->assignRole('School Admin');

        $schoolAdmin2 = User::where('email', 'school2@gmail.com')->first();
        $schoolAdmin2->assignRole('School Admin');

        $schoolData = [
            [
                'id'            => 1,
                'name'          => 'School 1',
                'address'       => 'Bhuj',
                'support_phone' => 1234567890,
                'support_email' => 'school1@gmail.com',
                'tagline'       => 'We Provide Best Education',
                'logo'          => 'school/logo.png',
                'admin_id'      => $schoolAdmin1->id,
                'status'        => 1
            ], [
                'id'            => 2,
                'name'          => 'School 2',
                'address'       => 'Bhuj',
                'support_phone' => 1234567890,
                'support_email' => 'school2@gmail.com',
                'tagline'       => 'We Provide Best Education',
                'logo'          => 'school/logo.png',
                'admin_id'      => $schoolAdmin2->id,
                'status'        => 1
            ]
        ];
        School::upsert($schoolData, ['id'], ['name', 'address', 'support_phone', 'support_email', 'tagline', 'logo', 'admin_id']);
        $schoolAdmin1->school_id = 1;
        $schoolAdmin1->save();

        $schoolAdmin2->school_id = 2;
        $schoolAdmin2->save();
        $schoolService = app(SchoolDataService::class);

        foreach ($schoolData as $value) {
            $value = (object)$value;
            $schoolService->preSettingsSetup($value);
        }

        $medium = [
            ['id' => 1, 'name' => 'English', 'school_id' => 1],
            ['id' => 2, 'name' => 'Hindi', 'school_id' => 1],
            ['id' => 3, 'name' => 'Gujarati', 'school_id' => 1],
        ];
        Mediums::upsert($medium, ['id'], ['name']);

        $sections = [
            ['id' => 1, 'name' => 'A', 'school_id' => 1],
            ['id' => 2, 'name' => 'B', 'school_id' => 1],
            ['id' => 3, 'name' => 'C', 'school_id' => 1],
        ];
        Section::upsert($sections, ['id'], ['name']);

        $classes = [
            ['id' => 1, 'name' => '9', 'medium_id' => 1, 'school_id' => 1],
            ['id' => 2, 'name' => '10', 'medium_id' => 1, 'school_id' => 1],
        ];
        ClassSchool::upsert($classes, ['id'], ['name', 'medium_id']);

        $class_sections = [
            ['id' => 1, 'medium_id' => 1, 'class_id' => 1, 'section_id' => 1, 'school_id' => 1],
            ['id' => 2, 'medium_id' => 1, 'class_id' => 1, 'section_id' => 2, 'school_id' => 1],
            ['id' => 3, 'medium_id' => 1, 'class_id' => 2, 'section_id' => 1, 'school_id' => 1],
            ['id' => 4, 'medium_id' => 1, 'class_id' => 2, 'section_id' => 2, 'school_id' => 1],
            ['id' => 5, 'medium_id' => 1, 'class_id' => 2, 'section_id' => 3, 'school_id' => 1],
        ];
        ClassSection::upsert($class_sections, ['id'], ['class_id', 'section_id']);

        $subjects = [
            ['id' => 1, 'name' => 'Maths', 'code' => 'MA', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Practical'],
            ['id' => 2, 'name' => 'Science', 'code' => 'SC', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Practical'],
            ['id' => 3, 'name' => 'English', 'code' => 'EN', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Theory'],
            ['id' => 4, 'name' => 'Gujarati', 'code' => 'GJ', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Theory'],
            ['id' => 5, 'name' => 'Sanskrit', 'code' => 'SN', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Theory'],
            ['id' => 6, 'name' => 'Hindi', 'code' => 'HN', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Theory'],
            ['id' => 7, 'name' => 'Computer', 'code' => 'CMP', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Practical'],
            ['id' => 8, 'name' => 'PT', 'code' => 'PT', 'bg_color' => '#5031f7', 'image' => 'subject.png', 'medium_id' => 1, 'school_id' => 1, 'type' => 'Practical'],

        ];
        Subject::upsert($subjects, ['id'], ['name', 'code', 'bg_color', 'image', 'medium_id', 'type']);

        $session_years = [
            ['name' => '2024-25', 'default' => 0, 'start_date' => Carbon::create('2024', '06', '01'), 'end_date' => Carbon::create('2025', '04', '30'), 'school_id' => 1],
            ['name' => '2025-26', 'default' => 0, 'start_date' => Carbon::create('2025', '06', '01'), 'end_date' => Carbon::create('2026', '04', '30'), 'school_id' => 1],
        ];
        SessionYear::upsert($session_years, ['name', 'school_id'], ['name', 'default', 'start_date', 'end_date']);

        //Users
        $user = [
            [
                'image'             => 'guardian/user.png',
                'password'          => Hash::make('guardian@123'),
                'first_name'        => 'Guardian',
                'last_name'         => 'Demo',
                'email'             => 'guardian@gmail.com',
                'mobile'            => 1234567890,
                'gender'            => 'female',
                'current_address'   => 'Bhuj',
                'permanent_address' => 'Bhuj',
                'school_id'         => null
            ],
            [
                'image'             => 'students/user.png',
                'password'          => Hash::make('student@123'),
                'first_name'        => 'Student',
                'last_name'         => 'Demo',
                'email'             => 'student@gmail.com',
                'mobile'            => 1234567890,
                'gender'            => 'male',
                'current_address'   => 'Bhuj',
                'permanent_address' => 'Bhuj',
                'school_id'         => 1
            ],
            [
                'image'             => 'teachers/user.png',
                'password'          => Hash::make('teacher@123'),
                'first_name'        => 'Teacher',
                'last_name'         => 'Demo',
                'email'             => 'teacher@gmail.com',
                'mobile'            => 1234567890,
                'gender'            => 'male',
                'current_address'   => 'Bhuj',
                'permanent_address' => 'Bhuj',
                'school_id'         => 1
            ]
        ];

        User::upsert($user, ['email'], ['image', 'password', 'first_name', 'last_name', 'mobile', 'gender', 'current_address', 'permanent_address', 'school_id']);
        $guardianUser = User::where('email', 'guardian@gmail.com')->first();
        $guardianUser->assignRole('Guardian');

        $studentUser = User::where('email', 'student@gmail.com')->first();
        $studentUser->assignRole('Student');

        //Student
        $student = [
            'id'               => 1,
            'user_id'          => $studentUser->id,
            'class_section_id' => 3,
            'admission_no'     => 12345667,
            'roll_number'      => 1,
            'guardian_id'      => $guardianUser->id,
            'admission_date'   => Carbon::create('2022', '04', '01'),
            'school_id'        => 1,
            'session_year_id'  => 1
        ];
        Students::upsert($student, ['id'], ['user_id', 'class_section_id', 'admission_no', 'roll_number', 'admission_date', 'guardian_id', 'school_id', 'session_year_id']);


        $teacherUser = User::where('email', 'teacher@gmail.com')->first();
        $teacherUser->assignRole('Teacher');
        $teacher = [
            'id'            => 1,
            'user_id'       => $teacherUser->id,
            'qualification' => 'MSC IT',
            'salary'        => 100000
        ];
        Staff::upsert($teacher, ['id'], ['user_id', 'qualification']);

        $package = [
            'id'               => 1,
            'name'             => 'Pro',
            'description'      => 'Unlimited Features',
            'tagline'          => 'Best plan for school',
            'student_charge'   => 0.025,
            'staff_charge'     => 0.025,
            'days'             => 90,
            'status'           => 1,
            'highlight'        => 0,
        ];

        $package = Package::upsert($package,['id'],['name','description','tagline','student_charge','staff_charge','days','status','highlight']);

        $features = Feature::get();
        $package_features = array();
        foreach ($features as $key => $feature) {
            $package_features[] = [
                'package_id' => 1,
                'feature_id' => $feature->id
            ];
        }
        PackageFeature::upsert($package_features,['package_id','feature_id'],['package_id','feature_id']);
    }
}
