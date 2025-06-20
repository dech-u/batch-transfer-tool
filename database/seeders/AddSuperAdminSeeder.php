<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AddSuperAdminSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //Add Super Admin User
        $super_admin_role = Role::where('name', 'Super Admin')->first();
        $user = User::updateOrCreate(['id' => 1], [
            'first_name' => 'super',
            'last_name'  => 'admin',
            'email'      => 'superadmin@gmail.com',
            'password'   => Hash::make('superadmin'),
            'gender'     => 'male',
            'image'      => 'logo.svg',
            'mobile'     => "",
            'email_verified_at' => Carbon::now(),
            'two_factor_enabled' => 0,
        ]);
        $user->assignRole([$super_admin_role->id]);

//        SessionYear::updateOrCreate(['id' => 1], [
//            'name'       => '2022-23',
//            'default'    => 1,
//            'start_date' => '2022-06-01',
//            'end_date'   => '2023-04-30',
//        ]);

        SystemSetting::upsert([
            ["name" => "time_zone", "data" => "Asia/Kolkata", "type" => "string"],
            ["name" => "date_format", "data" => "d-m-Y", "type" => "date"],
            ["name" => "time_format", "data" => "h:i A", "type" => "time"],
            ["name" => "theme_color", "data" => "#22577A", "type" => "string"],
            ["name" => "session_year", "data" => 1, "type" => "string"],
            ["name" => "system_version", "data" => "1.5.4", "type" => "string"],
            ["name" => "email_verified", "data" => 0, "type" => "boolean"],
            ["name" => "subscription_alert", "data" => 7, "type" => "integer"],
            ["name" => "currency_code", "data" => "USD", "type" => "string"],
            ["name" => "currency_symbol", "data" => "$", "type" => "string"],
            ["name" => "additional_billing_days", "data" => "5", "type" => "integer"],
            ["name" => "system_name", "data" => "eSchool Saas - School Management System", "type" => "string"],
            ["name" => "address", "data" => "#262-263, Time Square Empire, SH 42 Mirjapar highway, Bhuj - Kutch 370001 Gujarat India.", "type" => "string"],
            ["name" => "billing_cycle_in_days", "data" => "30", "type" => "integer"],
            ["name" => "current_plan_expiry_warning_days", "data" => "7", "type" => "integer"],
            ["name" => "front_site_theme_color", "data" => "#e9f9f3", "type" => "text"],
            ["name" => "primary_color", "data" => "#3ccb9b", "type" => "text"],
            ["name" => "secondary_color", "data" => "#245a7f", "type" => "text"],
            ["name" => "short_description", "data" => "eSchool-Saas - Manage Your School", "type" => "text"],
            ["name" => "facebook", "data" => "https://www.facebook.com/wrteam.in/", "type" => "text"],
            ["name" => "instagram", "data" => "https://www.instagram.com/wrteam.in/", "type" => "text"],
            ["name" => "linkedin", "data" => "https://in.linkedin.com/company/wrteam", "type" => "text"],
            ["name" => "footer_text", "data" => "<p>&copy;&nbsp;<strong><a href='https://wrteam.in/' target='_blank' rel='noopener noreferrer'>WRTeam</a></strong>. All Rights Reserved</p>", "type" => "text"],
            ["name" => "tagline", "data" => "We Provide the best Education", "type" => "text"],
            ["name" => "super_admin_name", "data" => "Super Admin", "type" => "text"],

        ], ['name'], ['data', 'type']);

        $systemSettings = [
            [
                'name' => 'hero_title_1',
                'data' => 'Opt for eSchool Saas 14+ robust features for an enhanced educational experience.',
                'type' => 'text'
            ],
            [
                'name' => 'hero_title_2',
                'data' => 'Top Rated Instructors',
                'type' => 'text'
            ],
            [
                'name' => 'about_us_title',
                'data' => 'A modern and unique style',
                'type' => 'text'
            ],
            [
                'name' => 'about_us_heading',
                'data' => 'Why it is best?',
                'type' => 'text'
            ],
            [
                'name' => 'about_us_description',
                'data' => 'eSchool is the pinnacle of school management, offering advanced technology, user-friendly features, and personalized solutions. It simplifies communication, streamlines administrative tasks, and elevates the educational experience for all stakeholders. With eSchool, excellence in education management is guaranteed.',
                'type' => 'text'
            ],
            [
                'name' => 'about_us_points',
                'data' => 'Affordable price,Easy to manage admin panel,Data Security',
                'type' => 'text'
            ],
            [
                'name' => 'custom_package_status',
                'data' => '1',
                'type' => 'text'
            ],
            [
                'name' => 'custom_package_description',
                'data' => 'Tailor your experience with our custom package options. From personalized services to bespoke solutions, we offer flexibility to meet your unique needs.',
                'type' => 'text'
            ],
            [
                'name' => 'download_our_app_description',
                'data' => 'Join the ranks of true trivia champions and quench your thirst for knowledge with Masters of Trivia - the ultimate quiz app designed to test your wits and unlock a world of fun facts. Challenge your brain, compete with friends, and discover fascinating tidbits from diverse categories. Don\'t miss out on the exhilarating experience that awaits you - get started now!Join the ranks of true trivia champions and quench your thirst for knowledge with Masters of Trivia - the ultimate quiz app designed to test your wits and unlock a world of fun facts.',
                'type' => 'text'
            ],
            [
                'name' => 'theme_primary_color',
                'data' => '#56cc99',
                'type' => 'text'
            ],
            [
                'name' => 'theme_secondary_color',
                'data' => '#215679',
                'type' => 'text'
            ],
            [
                'name' => 'theme_secondary_color_1',
                'data' => '#38a3a5',
                'type' => 'text'
            ],
            [
                'name' => 'theme_primary_background_color',
                'data' => '#f2f5f7',
                'type' => 'text'
            ],
            [
                'name' => 'theme_text_secondary_color',
                'data' => '#5c788c',
                'type' => 'text'
            ],
            [
                'name' => 'tag_line',
                'data' => 'Transform School Management With eSchool SaaS',
                'type' => 'text'
            ],
            [
                'name' => 'mobile',
                'data' => 'xxxxxxxxxx',
                'type' => 'text'
            ],
            [
                'name' => 'hero_description',
                'data' => 'Experience the future of education with our eSchool SaaS platform. Streamline attendance, assignments, exams, and more. Elevate your school\'s efficiency and engagement.',
                'type' => 'text'
            ],
            [
                'name' => 'display_school_logos',
                'data' => '1',
                'type' => 'text'
            ],
            [
                'name' => 'display_counters',
                'data' => '1',
                'type' => 'text'
            ],

            [
                'name' => 'email_template_school_registration',
                'data' => '&lt;p&gt;Dear {school_admin_name},&lt;/p&gt; &lt;p&gt;Welcome to {system_name}!&lt;/p&gt; &lt;p&gt;We are excited to have you as part of our educational community. Below are your registration details to access the system:&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;School Name:&lt;/strong&gt; {school_name}&lt;/p&gt; &lt;p&gt;&lt;strong&gt;System URL:&lt;/strong&gt; {url}&lt;/p&gt; &lt;p&gt;&lt;strong&gt;Your Login Credentials:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Email:&lt;/strong&gt; {email}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {password}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;School Code:&lt;/strong&gt; {code}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Please follow these steps to complete your registration:&lt;/strong&gt;&lt;/p&gt; &lt;ol&gt; &lt;li&gt;Click on the system URL provided above.&lt;/li&gt; &lt;li&gt;Enter your email and password.&lt;/li&gt; &lt;li&gt;Follow the instructions to complete your profile setup.&lt;/li&gt; &lt;/ol&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;For security reasons, please change your password after your first login.&lt;/li&gt; &lt;li&gt;If you encounter any issues during the registration process, please do not hesitate to contact our support team at {support_email} or call {contact}.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;Thank you for choosing {system_name}. We are committed to providing you with the best educational tools and resources.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{super_admin_name}&lt;br&gt;{system_name}&lt;br&gt;{support_email}&lt;br&gt;{url}&lt;/p&gt; &lt;br&gt; &lt;p&gt;&lt;strong&gt;This email was auto-generated, so don\'t reply.&lt;/strong&gt;&lt;/p&gt;',
                'type' => 'text'
            ],

            [
                'name' => 'web_maintenance',
                'data' => '0',
                'type' => 'string'
            ],

            [
                'name' => 'file_upload_size_limit',
                'data' => '2',
                'type' => 'string'
            ],

            [
                'name' => 'email_template_two_factor_authentication_code',
                'data' => '&lt;p&gt;Dear {school_admin_name},&lt;/p&gt; &lt;p&gt;Welcome to {system_name}!&lt;/p&gt; &lt;p&gt;We are excited to have you as part of our educational community. To enhance the security of your account, we have enabled Two-Factor Authentication (2FA) for your login.&lt;/p&gt; &lt;p&gt;&lt;strong&gt;Your Verification Code:&lt;/strong&gt;&lt;/p&gt; &lt;p&gt;&lt;strong&gt;{verification_code}&lt;/strong&gt;&lt;/p&gt; &lt;p&gt;This verification code is required to complete your login process. Please enter the code within the next {expiration_time} minutes. If the code expires, you can request a new one by following the same process.&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;If you did not request this verification code, please contact our support team immediately at {support_email} or call {support_contact} to secure your account.&lt;/li&gt; &lt;li&gt;For additional security, ensure that no one else has access to your email or device when retrieving your verification code.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;If you have any issues with the 2FA process or need assistance, our support team is ready to help at {support_email} or {support_contact}.&lt;/p&gt; &lt;p&gt;Thank you for taking extra steps to secure your account. We appreciate your commitment to keeping your information safe.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{super_admin_name}&lt;br&gt;{system_name}&lt;br&gt;{support_email}&lt;br&gt;{url}&lt;/p&gt; &lt;br&gt; &lt;p&gt;&lt;strong&gt;This email was auto-generated, so please do not reply.&lt;/strong&gt;&lt;/p&gt;',
                'type' => 'text'
            ],

            [
                'name' => 'wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],

            [
                'name' => 'system_settings_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'notification_settings_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'email_settings_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'verify_email_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'email_template_settings_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'payment_settings_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ],
            [
                'name' => 'third_party_api_settings_wizard_checkMark',
                'data' => 0,
                'type' => 'integer'
            ]
            
        ];

        SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);
        Cache::flush();
    }
}
