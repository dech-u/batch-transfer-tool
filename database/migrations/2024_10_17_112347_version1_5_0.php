<?php

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
        Schema::table('packages', static function (Blueprint $table) {
            $table->float('charges', 64, 2)->change();
        });

        if (!Schema::hasColumn('schools', 'type') && !Schema::hasColumn('schools', 'domain_type')) {
            Schema::table('schools', static function (Blueprint $table) {
                $table->string('type')->after('code')->nullable()->default('custom');
                $table->string('domain_type')->after('code')->nullable()->default('default');
            });
        }

        Schema::create('school_inquiries', static function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('school_address');
            $table->string('school_phone');
            $table->string('school_email');
            $table->string('school_tagline');
            $table->date('date');
            $table->integer('status')->default(0);
            $table->timestamps();
        });

        Schema::create('extra_school_datas', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_inquiry_id')->nullable()->references('id')->on('school_inquiries')->onDelete('cascade');
            $table->foreignId('school_id')->nullable()->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('form_field_id')->references('id')->on('form_fields')->onDelete('cascade');
            $table->text('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('form_fields', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->unsignedBigInteger('school_id')->nullable()->change();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        Schema::table('packages', static function (Blueprint $table) {
            $table->double('charges',64,4)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('two_factor_enabled')->default(1)->after('email_verified_at');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->string('two_factor_expires_at')->nullable()->after('two_factor_secret');
        });

        $systemSettings = [
            [
                'name' => 'email_template_two_factor_authentication_code',
                'data' => '&lt;p&gt;Dear {school_admin_name},&lt;/p&gt; &lt;p&gt;Welcome to {system_name}!&lt;/p&gt; &lt;p&gt;We are excited to have you as part of our educational community. To enhance the security of your account, we have enabled Two-Factor Authentication (2FA) for your login.&lt;/p&gt; &lt;p&gt;&lt;strong&gt;Your Verification Code:&lt;/strong&gt;&lt;/p&gt; &lt;p&gt;&lt;strong&gt;{verification_code}&lt;/strong&gt;&lt;/p&gt; &lt;p&gt;This verification code is required to complete your login process. Please enter the code within the next {expiration_time} minutes. If the code expires, you can request a new one by following the same process.&lt;/p&gt; &lt;hr&gt; &lt;p&gt;&lt;strong&gt;Important:&lt;/strong&gt;&lt;/p&gt; &lt;ul&gt; &lt;li&gt;If you did not request this verification code, please contact our support team immediately at {support_email} or call {support_contact} to secure your account.&lt;/li&gt; &lt;li&gt;For additional security, ensure that no one else has access to your email or device when retrieving your verification code.&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;If you have any issues with the 2FA process or need assistance, our support team is ready to help at {support_email} or {support_contact}.&lt;/p&gt; &lt;p&gt;Thank you for taking extra steps to secure your account. We appreciate your commitment to keeping your information safe.&lt;/p&gt; &lt;p&gt;Best regards,&lt;/p&gt; &lt;p&gt;{super_admin_name}&lt;br&gt;{system_name}&lt;br&gt;{support_email}&lt;br&gt;{url}&lt;/p&gt; &lt;br&gt; &lt;p&gt;&lt;strong&gt;This email was auto-generated, so please do not reply.&lt;/strong&gt;&lt;/p&gt;',
                'type' => 'text'
            ],  
            [
                'name' => 'school_inquiry',
                'data' => '0',
                'type' => 'string'
            ]          
        ];

        SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);
        Cache::flush();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', static function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::dropIfExists('extra_school_datas');
        Schema::dropIfExists('school_inquiries');
       
        Schema::table('form_fields', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
       
        // Remove columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_enabled', 'two_factor_secret', 'two_factor_expires_at']);
        });
    }
};
