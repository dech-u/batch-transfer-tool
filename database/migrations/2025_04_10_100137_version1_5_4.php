<?php

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Part 1: Update wizard settings (from drop_tables.php)
        $wizardSettings = [
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

        SystemSetting::upsert($wizardSettings, ["name"], ["data","type"]);
        
        // Part 2: Update monetary fields precision (from version1_5_4.php)
        // Add-on and Package monetary fields
        Schema::table('addons', function (Blueprint $table) {
            $table->double('price', 64, 4)->change();
        });

        // Subscription Bills
        Schema::table('subscription_bills', function (Blueprint $table) {
            $table->double('amount', 64, 4)->change();
        });
        
        Schema::table('addon_subscriptions', function (Blueprint $table) {
            $table->double('price', 64, 4)->change();
        });
        
        // Part 3: Drop tables (from drop_tables.php)
        // Disable foreign key checks to allow dropping tables with relationships
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Schema::dropIfExists('academic_calendars');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('announcement_classes');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('certificate_templates');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('class_groups');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('class_teachers');
        Schema::dropIfExists('compulsory_fees');
        Schema::dropIfExists('database_backups');
        Schema::dropIfExists('elective_subject_groups');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_timetables');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('extra_student_datas');
        Schema::dropIfExists('fees');
        Schema::dropIfExists('fees_advance');
        Schema::dropIfExists('fees_class_types');
        Schema::dropIfExists('fees_installments');
        Schema::dropIfExists('fees_paids');
        Schema::dropIfExists('fees_types');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('leave_details');
        Schema::dropIfExists('leave_masters');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('lesson_topics');
        Schema::dropIfExists('mediums');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('online_exams');
        Schema::dropIfExists('online_exam_questions');
        Schema::dropIfExists('online_exam_question_choices');
        Schema::dropIfExists('online_exam_question_options');
        Schema::dropIfExists('online_exam_student_answers');
        Schema::dropIfExists('optional_fees');
        Schema::dropIfExists('payroll_settings');
        Schema::dropIfExists('promote_students');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('session_years');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('staff_payrolls');
        Schema::dropIfExists('staff_salaries');
        Schema::dropIfExists('streams');
        Schema::dropIfExists('students');
        Schema::dropIfExists('student_online_exam_statuses');
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('subject_teachers');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('user_status_for_next_cycles');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Clear cache to apply changes
        Cache::flush();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       // 
    }
};
