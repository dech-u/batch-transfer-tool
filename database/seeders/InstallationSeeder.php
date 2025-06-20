<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InstallationSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        /**** Create All the Permission ****/
        $this->createPermissions();

        $this->createSuperAdminRole();

        // System Features
        $this->systemFeatures();

        //Change system version here
        Language::updateOrCreate(['id' => 1], ['name' => 'English', 'code' => 'en', 'file' => 'en.json', 'status' => 1, 'is_rtl' => 0]);

        Artisan::call('migrate:school');
        Artisan::call('db:seed --class=SchoolInstallationSeeder');
        //clear cache
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }


    public function createPermissions() {

        $permissions = [
            ...self::permission('role'),
            ...self::permission('language'),
            ...self::permission('schools'),
            ...self::permission('package'),
            ...self::permission('addons'),
            ...self::permission('guidance'),
            ['name' => 'system-setting-manage'],
            ['name' => 'fcm-setting-create'],
            ['name' => 'email-setting-create'],
            ['name' => 'privacy-policy'],
            ['name' => 'contact-us'],
            ['name' => 'about-us'],
            ['name' => 'terms-condition'],
            ['name' => 'app-settings'],
            ['name' => 'subscription-view'],
            ...self::permission('staff'),
            ...self::permission('faqs'),
            ['name' => 'fcm-setting-manage'],
            ['name' => 'front-site-setting'],
            ['name' => 'payment-settings'],

            ['name' => 'subscription-settings'],
            ['name' => 'subscription-change-bills'],
            ['name' => 'school-terms-condition'],
            ['name' => 'subscription-bill-payment'],
            ['name' => 'web-settings'],
            ['name' => 'email-template'],            
            ['name' => 'custom-school-email'],
            ['name' => 'database-backup'],
            ...self::permission('school-custom-field'),


            

        ];
        $permissions = array_map(static function ($data) {
            $data['guard_name'] = 'web';
            return $data;
        }, $permissions);
        Permission::upsert($permissions, ['name'], ['name']);
    }


    public function createSuperAdminRole() {
        $role = Role::withoutGlobalScope('school')->updateOrCreate(['name' => 'Super Admin', 'custom_role' => 0, 'editable' => 0]);
        $superAdminHasAccessTo = [
            'schools-list',
            'schools-create',
            'schools-edit',
            'schools-delete',

            'package-list',
            'package-create',
            'package-edit',
            'package-delete',

            'email-setting-create',
            'privacy-policy',
            'terms-condition',
            'contact-us',
            'about-us',
            'fcm-setting-create',
            'language-list',
            'language-create',
            'language-edit',
            'language-delete',
            'system-setting-manage',
            'app-settings',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'staff-list',
            'staff-create',
            'staff-edit',
            'staff-delete',

            'addons-list',
            'addons-create',
            'addons-edit',
            'addons-delete',

            'subscription-view',

            'faqs-list',
            'faqs-create',
            'faqs-edit',
            'faqs-delete',

            'fcm-setting-manage',

            // 'front-site-setting',

            // 'update-admin-profile',
            'subscription-settings',
            'subscription-change-bills',
            'school-terms-condition',

            'guidance-list',
            'guidance-create',
            'guidance-edit',
            'guidance-delete',

            'subscription-bill-payment',
            'web-settings',
            'custom-school-email',
            
            'database-backup',

            'school-custom-field-list',
            'school-custom-field-create',
            'school-custom-field-edit',
            'school-custom-field-delete'

        ];
        $role->syncPermissions($superAdminHasAccessTo);
    }


    /**
     * Generate List , Create , Edit , Delete Permissions
     * @param $prefix
     * @param array $customPermissions - Prefix will be set Automatically
     * @return string[]
     */
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

    // System Features
    public function systemFeatures() {
        $features = [
            ['name' => 'Student Management', 'is_default' => 1, 'status' => 1],
            ['name' => 'Academics Management', 'is_default' => 1, 'status' => 1],
            ['name' => 'Slider Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Teacher Management', 'is_default' => 1, 'status' => 1],
            ['name' => 'Session Year Management', 'is_default' => 1, 'status' => 1],
            ['name' => 'Holiday Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Timetable Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Attendance Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Exam Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Lesson Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Assignment Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Announcement Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Staff Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Expense Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Staff Leave Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'Fees Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'School Gallery Management', 'is_default' => 0, 'status' => 1],
            ['name' => 'ID Card - Certificate Generation', 'is_default' => 0, 'status' => 1],
            ['name' => 'Website Management', 'is_default' => 0, 'status' => 1, 'required_vps' => 0],
            ['name' => 'Chat Module', 'is_default' => 0, 'status' => 1, 'required_vps' => 0],
        ];

        foreach ($features as $key => $feature) {
            Feature::updateOrCreate(['id' => ($key + 1)], $feature);
        }
    }
}
