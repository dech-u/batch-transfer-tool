<?php

use App\Models\Feature;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('certificate_templates', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('page_layout')->comment('A4 Portrait, A4 Landscape, Custom');
            $table->float('height')->nullable(true);
            $table->float('width')->nullable(true);
            $table->string('user_image_shape')->comment('Round, Square');
            $table->float('image_size')->nullable(true);
            $table->string('background_image')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('fields')->nullable(true);
            $table->json('style')->nullable(true);
            $table->string('type')->comment('Student, Staff')->nullable(true);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::table('features', static function (Blueprint $table) {
            $table->integer('status')->default(1)->after('is_default');
        });

        Feature::where('id',18)->update(['name' => 'ID Card - Certificate Generation']);
        Feature::updateOrCreate(['id' => 19], ['name' => 'Website Management', 'is_default' => 0, 'status' => 1]);
        Schema::table('sliders', static function (Blueprint $table) {
            $table->integer('type')->default(1)->comment('1 => App, 2 => web, 3 => Both')->default(1)->after('link');
        });
        Schema::table('faqs', static function (Blueprint $table) {
            $table->foreignId('school_id')->after('description')->nullable(true)->references('id')->on('schools')->onDelete('cascade');
        });

        Schema::table('schools', static function (Blueprint $table) {
            $table->dropForeign('schools_admin_id_foreign');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('schools', static function (Blueprint $table) {
            $table->string('domain')->nullable(true)->after('status');
        });

        Schema::create('class_groups', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('image')->nullable(true);
            $table->string('class_ids')->nullable(true);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('amount')->nullable();
            $table->float('percentage')->nullable();
            $table->string('type');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('staff_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->nullable()->references('id')->on('staffs')->onDelete('cascade');
            $table->foreignId('payroll_setting_id')->nullable()->references('id')->on('payroll_settings')->onDelete('cascade');
            $table->double('amount')->nullable();
            $table->float('percentage')->nullable();
            $table->unique(['staff_id', 'payroll_setting_id'], 'unique_ids');
            $table->timestamps();
        });

        Schema::create('staff_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->nullable()->references('id')->on('expenses')->onDelete('cascade');
            $table->foreignId('payroll_setting_id')->nullable()->references('id')->on('payroll_settings')->onDelete('cascade');
            $table->double('amount')->nullable();
            $table->float('percentage')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['expense_id', 'payroll_setting_id'], 'unique_ids');
            $table->timestamps();
        });



        // Add email templates
        $systemSettings = array(
            [
            'name' => 'email_template_school_registration',
            'data' => '&lt;p&gt;Dear {school_admin_name},&lt;/p&gt; &lt;p&gt;Welcome to {system_name}!&lt;/p&gt; &lt;p&gt;We are excited to have you as part of our educational community. Below are your registration details to access the system:&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;School Name:&lt;/strong&gt; {school_name}&lt;/p&gt; &lt;p&gt;&lt;strong&gt;System URL:&lt;/strong&gt; {url}&lt;/p&gt; &lt;p&gt;&lt;strong&gt;Your Login Credentials:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Email:&lt;/strong&gt; {email}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Please follow these steps to complete your registration:&lt;/strong&gt;&lt;/p&gt; &lt;ol&gt; &lt;li&gt;Click on the system URL provided above.&lt;/li&gt; &lt;li&gt;Enter your email and password.&lt;/li&gt; &lt;li&gt;Follow the instructions to complete your profile setup.&lt;/li&gt; &lt;/ol&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;For security reasons, please change your password after your first login.&lt;/li&gt; &lt;li&gt;If you encounter any issues during the registration process, please do not hesitate to contact our support team at {support_email} or call {contact}.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;Thank you for choosing {system_name}. We are committed to providing you with the best educational tools and resources.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{super_admin_name}&lt;br&gt;{system_name}&lt;br&gt;{support_email}&lt;br&gt;{url}&lt;/p&gt;',
            'type' => 'text'
            ],
            [
                'name' => 'system_version',
                'data' => '1.3.2',
                'type' => 'text'
            ]
        );
        
        SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);
        $schools = School::get();
        $email_template_staff = [];
        $email_template_parent = [];
        foreach ($schools as $key => $school) {
            $email_template_staff[] =
                [
                    'name' => 'email-template-staff',
                    'data' => '&lt;p&gt;Dear {full_name},&lt;/p&gt; &lt;p&gt;Welcome to {school_name}!&lt;/p&gt; &lt;p&gt;We are excited to have you join our team. Below are your registration details to access the {school_name}:&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Your Registration Details:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Registration URL:&lt;/strong&gt; {url}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Email:&lt;/strong&gt; {email}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Steps to Complete Your Registration:&lt;/strong&gt;&lt;/p&gt; &lt;ol&gt; &lt;li&gt;Click on the registration URL provided above.&lt;/li&gt; &lt;li&gt;Enter your email and password.&lt;/li&gt; &lt;li&gt;Follow the on-screen instructions to set up your profile.&lt;/li&gt; &lt;/ol&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;For security reasons, please change your password upon your first login.&lt;/li&gt; &lt;li&gt;If you have any questions or need assistance during the registration process, please contact our support team at {support_email} or call {support_contact}.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;&lt;strong&gt;App Download Links:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Android:&lt;/strong&gt; {android_app}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;iOS:&lt;/strong&gt; {ios_app}&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;We look forward to a successful academic year with you on our team. Thank you for your commitment to excellence in education.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{school_name}&lt;br&gt;{support_email}&lt;br&gt;{support_contact}&lt;br&gt;{url}&lt;/p&gt;',
                    'type' => 'text',
                    'school_id' => $school->id
                ];
            $email_template_parent[] = [
                'name' => 'email-template-parent',
                'data' => '&lt;p&gt;Dear {parent_name},&lt;/p&gt; &lt;p&gt;We are delighted to welcome {child_name} to {school_name}!&lt;/p&gt; &lt;p&gt;As part of our registration process, we have created accounts for both you and your child in our {school_name}. Below are the registration details you will need to access the system, along with links to download our mobile app for your convenience.&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Student Credential Details:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Name:&lt;/strong&gt; {child_name}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Admission No.: &lt;/strong&gt;{admission_no}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;GR No.:&lt;/strong&gt; {grno}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {child_password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Parent Credential Details:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Name:&lt;/strong&gt; {parent_name}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Email:&lt;/strong&gt; {email}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;Password:&lt;/strong&gt; {password}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;App Download Links:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;&lt;strong&gt;Android:&lt;/strong&gt; {android_app}&lt;/li&gt; &lt;li&gt;&lt;strong&gt;iOS:&lt;/strong&gt; {ios_app}&lt;/li&gt; &lt;/ul&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Steps to Complete the Registration:&lt;/strong&gt;&lt;/p&gt; &lt;ol&gt; &lt;li&gt;Download the school management app using the links above for easier access on your mobile devices.&lt;/li&gt; &lt;li&gt;Enter the email and password for either the student or parent account.&lt;/li&gt; &lt;li&gt;Follow the on-screen instructions to complete the profile setup.&lt;/li&gt; &lt;/ol&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;For security reasons, please ensure that both the student and parent passwords are changed upon first login.&lt;/li&gt; &lt;li&gt;If you encounter any issues during the registration process, please do not hesitate to contact our support team at {support_email} or call {support_contact}.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;We look forward to an enriching educational experience for {child_name} at {school_name}. Thank you for entrusting us with your child&#039;s education.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{school_name}&lt;br&gt;{support_email}&lt;/p&gt;',
                'type' => 'text',
                'school_id' => $school->id
            ];
            
        }
        $schoolSettings = array_merge($email_template_parent, $email_template_staff);
        SchoolSetting::upsert($schoolSettings, ["name","school_id"], ["data","type"]);
        Cache::flush();

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('certificate_templates');
        Schema::table('sliders', static function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('schools', static function (Blueprint $table) {
            $table->dropColumn('domain');
        });
        Schema::table('faqs', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
        Schema::dropIfExists('class_groups');

        Schema::dropIfExists('payroll_settings');
        Schema::dropIfExists('staff_salaries');
        Schema::dropIfExists('staff_payrolls');

        Schema::table('features', static function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
