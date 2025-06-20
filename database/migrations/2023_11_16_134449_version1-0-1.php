<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grades', static function (Blueprint $table) {
            $table->float('starting_range')->change();
            $table->float('ending_range')->change();
        });

        Schema::table('school_settings', static function (Blueprint $table) {
            $table->text('data')->change();
        });

        Schema::table('system_settings', static function (Blueprint $table) {
            $table->text('data')->change();
        });

        /*
         * Remove on Delete restrict
         * And add On Delete Restrict
         */

        /*TODO : DOUBT 1*/
        Schema::table('schools', static function (Blueprint $table) {
            $table->dropForeign('schools_admin_id_foreign');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('restrict');
        });


        /* Master Table Started */
        Schema::table('subjects', static function (Blueprint $table) {
            $table->dropForeign('subjects_medium_id_foreign');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('restrict');
        });

        Schema::table('classes', static function (Blueprint $table) {
            $table->dropForeign('classes_medium_id_foreign');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('restrict');

            $table->dropForeign('classes_shift_id_foreign');
            $table->foreign('shift_id')->references('id')->on('shifts')->onUpdate('restrict')->onDelete('restrict');

            $table->dropForeign('classes_stream_id_foreign');
            $table->foreign('stream_id')->references('id')->on('streams')->onUpdate('restrict')->onDelete('restrict');
        });
        Schema::table('class_subjects', static function (Blueprint $table) {
            $table->dropForeign('class_subjects_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('class_subjects_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');

            $table->dropForeign('class_subjects_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('restrict');
        });
        Schema::table('class_sections', static function (Blueprint $table) {
            $table->dropForeign('class_sections_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('class_sections_section_id_foreign');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');

            $table->dropForeign('class_sections_medium_id_foreign');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('restrict');
        });
        Schema::table('students', static function (Blueprint $table) {
            $table->dropForeign('students_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('students_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('students_guardian_id_foreign');
            $table->foreign('guardian_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('students_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('staffs', static function (Blueprint $table) {
            $table->dropForeign('staffs_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
        /* Master Table End */

        Schema::table('elective_subject_groups', static function (Blueprint $table) {
            $table->dropForeign('elective_subject_groups_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('elective_subject_groups_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('restrict');
        });

        Schema::table('student_subjects', static function (Blueprint $table) {
            $table->dropForeign('student_subjects_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('student_subjects_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');

            $table->dropForeign('student_subjects_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('student_subjects_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');

        });

        Schema::table('subject_teachers', static function (Blueprint $table) {
            $table->dropForeign('subject_teachers_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('subject_teachers_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');

            $table->dropForeign('subject_teachers_teacher_id_foreign');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('subject_teachers_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');
        });


        /* Lesson Module Start */
        Schema::table('lessons', static function (Blueprint $table) {
            $table->dropForeign('lessons_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('lessons_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');
        });
        /* Lesson Module End */

        /* Assignment Module Start */
        Schema::table('assignments', static function (Blueprint $table) {
            $table->dropForeign('assignments_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('assignments_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');

            $table->dropForeign('assignments_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');

            $table->dropForeign('assignments_created_by_foreign');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('assignments_edited_by_foreign');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('restrict');
        });
        Schema::table('assignment_submissions', static function (Blueprint $table) {
            $table->dropForeign('assignment_submissions_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('assignment_submissions_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');

        });
        /* Assignment Module End */

        /* Exam Module Start */
        Schema::table('exams', static function (Blueprint $table) {
            $table->dropForeign('exams_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('exams_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('exam_timetables', static function (Blueprint $table) {
            $table->dropForeign('exam_timetables_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');

            $table->dropForeign('exam_timetables_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('exam_marks', static function (Blueprint $table) {
            $table->dropForeign('exam_marks_exam_timetable_id_foreign');
            $table->foreign('exam_timetable_id')->references('id')->on('exam_timetables')->onDelete('restrict');

            $table->dropForeign('exam_marks_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('exam_marks_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');

            $table->dropForeign('exam_marks_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('exam_results', static function (Blueprint $table) {
            $table->dropForeign('exam_results_exam_id_foreign');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('restrict');

            $table->dropForeign('exam_results_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('exam_results_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('exam_results_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        /*Exam module End*/

        Schema::table('timetables', static function (Blueprint $table) {
            $table->dropForeign('timetables_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('timetables_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');

            $table->dropForeign('timetables_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('restrict');
        });

        /* Announcement Module Start */
        Schema::table('announcements', static function (Blueprint $table) {
            $table->dropForeign('announcements_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('announcement_classes', static function (Blueprint $table) {
            $table->dropForeign('announcement_classes_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('announcement_classes_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');
        });
        /* Announcement Module End */

        Schema::table('academic_calendars', static function (Blueprint $table) {
            $table->dropForeign('academic_calendars_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });

        Schema::table('attendances', static function (Blueprint $table) {
            $table->dropForeign('attendances_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('attendances_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('attendances_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });


        Schema::table('promote_students', static function (Blueprint $table) {
            $table->dropForeign('promote_students_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('promote_students_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('promote_students_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });

        /* Online Exam Module Start */
        Schema::table('online_exams', static function (Blueprint $table) {
            $table->dropForeign('online_exams_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('online_exams_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');

            $table->dropForeign('online_exams_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('online_exam_questions', static function (Blueprint $table) {
            $table->dropForeign('online_exam_questions_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('online_exam_questions_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('restrict');

            $table->dropForeign('online_exam_questions_last_edited_by_foreign');
            $table->foreign('last_edited_by')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('online_exam_question_choices', static function (Blueprint $table) {
            $table->dropForeign('online_exam_question_choices_online_exam_id_foreign');
            $table->foreign('online_exam_id')->references('id')->on('online_exams')->onDelete('restrict');

            $table->dropForeign('online_exam_question_choices_question_id_foreign');
            $table->foreign('question_id')->references('id')->on('online_exam_questions')->onDelete('restrict');
        });
        Schema::table('student_online_exam_statuses', static function (Blueprint $table) {
            $table->dropForeign('student_online_exam_statuses_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('student_online_exam_statuses_online_exam_id_foreign');
            $table->foreign('online_exam_id')->references('id')->on('online_exams')->onDelete('restrict');
        });
        Schema::table('online_exam_student_answers', static function (Blueprint $table) {
            $table->dropForeign('online_exam_student_answers_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('online_exam_student_answers_online_exam_id_foreign');
            $table->foreign('online_exam_id')->references('id')->on('online_exams')->onDelete('restrict');

            $table->dropForeign('online_exam_student_answers_question_id_foreign');
            $table->foreign('question_id')->references('id')->on('online_exam_question_choices')->onDelete('restrict');

            $table->dropForeign('online_exam_student_answers_option_id_foreign');
            $table->foreign('option_id')->references('id')->on('online_exam_question_options')->onDelete('restrict');
        });
        /* Online Exam Module End */

        /* Form Field Module Start */
        Schema::table('extra_student_datas', static function (Blueprint $table) {
            $table->dropForeign('extra_student_datas_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');
        });
        /* Form Field Module End */

        Schema::table('class_teachers', static function (Blueprint $table) {
            $table->dropForeign('class_teachers_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('restrict');

            $table->dropForeign('class_teachers_teacher_id_foreign');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('restrict');
        });


        /* Fees Module */
        Schema::table('fees', static function (Blueprint $table) {
            $table->dropForeign('fees_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });

        Schema::table('installment_fees', static function (Blueprint $table) {
            $table->dropForeign('installment_fees_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('fees_classes', static function (Blueprint $table) {
            $table->dropForeign('fees_classes_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('fees_classes_fees_type_id_foreign');
            $table->foreign('fees_type_id')->references('id')->on('fees_types')->onDelete('restrict');
        });
        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->dropForeign('payment_transactions_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
        Schema::table('fees_paids', static function (Blueprint $table) {
            $table->dropForeign('fees_paids_fees_id_foreign');
            $table->foreign('fees_id')->references('id')->on('fees')->onDelete('restrict');

            $table->dropForeign('fees_paids_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('fees_paids_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('fees_paids_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        Schema::table('compulsory_fees', static function (Blueprint $table) {
            $table->dropForeign('compulsory_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('compulsory_fees_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('compulsory_fees_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('restrict');

            $table->dropForeign('compulsory_fees_installment_id_foreign');
            $table->foreign('installment_id')->references('id')->on('installment_fees')->onDelete('restrict');

            $table->dropForeign('compulsory_fees_fees_paid_id_foreign');
            $table->foreign('fees_paid_id')->references('id')->on('fees_paids')->onDelete('restrict');
        });
        Schema::table('optional_fees', static function (Blueprint $table) {
            $table->dropForeign('optional_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('optional_fees_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('restrict');

            $table->dropForeign('optional_fees_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('restrict');

            $table->dropForeign('optional_fees_fees_class_id_foreign');
            $table->foreign('fees_class_id')->references('id')->on('fees_classes')->onDelete('restrict');

            $table->dropForeign('optional_fees_fees_paid_id_foreign');
            $table->foreign('fees_paid_id')->references('id')->on('fees_paids')->onDelete('restrict');
        });
        /*Fees Module End*/

        /* Subscription Module Start*/
        Schema::table('subscriptions', static function (Blueprint $table) {

            $table->dropForeign('subscriptions_package_id_foreign');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('restrict');
        });
        Schema::table('addons', static function (Blueprint $table) {
            $table->dropForeign('addons_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('restrict');
        });
        Schema::table('addon_subscriptions', static function (Blueprint $table) {

            $table->dropForeign('addon_subscriptions_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('restrict');
        });
        Schema::table('subscription_bills', static function (Blueprint $table) {
            $table->dropForeign('subscription_bills_subscription_id_foreign');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('restrict');

            $table->dropForeign('subscription_bills_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('restrict');
        });
        Schema::table('subscription_features', static function (Blueprint $table) {
            $table->dropForeign('subscription_features_subscription_id_foreign');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('restrict');

            $table->dropForeign('subscription_features_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('restrict');
        });
        Schema::table('package_features', static function (Blueprint $table) {
            $table->dropForeign('package_features_package_id_foreign');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('restrict');

            $table->dropForeign('package_features_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('restrict');
        });
        /* Subscription Module End*/

        /*Expense Module Start*/
        Schema::table('expenses', static function (Blueprint $table) {
            $table->dropForeign('expenses_category_id_foreign');
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('restrict');

            $table->dropForeign('expenses_staff_id_foreign');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('restrict');

            $table->dropForeign('expenses_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });
        /*Expense Module End*/

        Schema::table('leaves', static function (Blueprint $table) {
            $table->dropForeign('leaves_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->dropForeign('leaves_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('restrict');
        });

        Schema::table('staff_support_schools', static function (Blueprint $table) {
            $table->dropForeign('staff_support_schools_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grades', static function (Blueprint $table) {
            $table->integer('starting_range')->change();
            $table->integer('ending_range')->change();
        });

        Schema::table('school_settings', static function (Blueprint $table) {
            $table->string('data')->change();
        });

        Schema::table('system_settings', static function (Blueprint $table) {
            $table->string('data')->change();
        });
    }
};
