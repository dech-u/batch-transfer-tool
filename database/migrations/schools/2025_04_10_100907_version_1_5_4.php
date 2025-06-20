<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // Drop all tables that are not needed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('academic_calendars');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('addon_subscriptions');
        Schema::dropIfExists('database_backups');
        Schema::dropIfExists('features');
        Schema::dropIfExists('feature_sections');
        Schema::dropIfExists('feature_section_lists');
        Schema::dropIfExists('guidances');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('staff_support_schools');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_bills');
        Schema::dropIfExists('subscription_features');
        Schema::dropIfExists('system_settings');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Add class_subject_id to lesson_commons, assignment_commons, online_exam_commons, online_exam_question_commons, topic_commons
        Schema::table('lesson_commons', function (Blueprint $table) {
            $table->foreignId('class_subject_id')->after('class_section_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });

        Schema::table('assignment_commons', function (Blueprint $table) {
            $table->foreignId('class_subject_id')->after('class_section_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });

        Schema::table('online_exam_commons', function (Blueprint $table) {
            $table->foreignId('class_subject_id')->after('class_section_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });

        Schema::table('online_exam_question_commons', function (Blueprint $table) {
            $table->foreignId('class_subject_id')->after('class_section_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });

        Schema::table('topic_commons', function (Blueprint $table) {
            $table->foreignId('class_subject_id')->after('class_section_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });

        // Update compulsory_fees, fees, fees_installments, fees_class_types, optional_fees, fees_paids, fees_advance, staffs, expenses, staff_payrolls, payment_transactions, addons, subscription_bills, fees_paids
        Schema::table('compulsory_fees', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
            $table->double('due_charges', 64, 2)->change();
        });
        
        Schema::table('fees', function (Blueprint $table) {
            $table->double('due_charges', 64, 2)->change();
            $table->double('due_charges_amount', 64, 2)->change();
        });
        
        Schema::table('fees_installments', function (Blueprint $table) {
            $table->double('due_charges', 64, 2)->change();
        });
        
        Schema::table('fees_class_types', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });
        
        Schema::table('optional_fees', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });
        
        Schema::table('fees_paids', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });
        
        Schema::table('fees_advance', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });
        
        // Staff salary
        Schema::table('staffs', function (Blueprint $table) {
            $table->double('salary', 64, 2)->change();
        });
        
        // Expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });
        
        // Payroll tables
        Schema::table('staff_payrolls', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });
        
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });

        Schema::table('fees_paids', function (Blueprint $table) {
            $table->double('amount', 64, 2)->change();
        });

        // Clear the cache to ensure changes are reflected
        Cache::flush();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove class_subject_id from lesson_commons, assignment_commons, online_exam_commons, online_exam_question_commons, topic_commons
        Schema::table('lesson_commons', function (Blueprint $table) {
            $table->dropColumn('class_subject_id');
        });

        Schema::table('assignment_commons', function (Blueprint $table) {
            $table->dropColumn('class_subject_id');
        });

        Schema::table('online_exam_commons', function (Blueprint $table) {
            $table->dropColumn('class_subject_id');
        });

        Schema::table('online_exam_question_commons', function (Blueprint $table) {
            $table->dropColumn('class_subject_id');
        });

        Schema::table('topic_commons', function (Blueprint $table) {
            $table->dropColumn('class_subject_id');
        });
    }
};
