<?php

use App\Models\School;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('schools', static function (Blueprint $table) {
            $table->dropForeign('schools_admin_id_foreign');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });


        /* Master Table Started */
        Schema::table('subjects', static function (Blueprint $table) {
            $table->dropForeign('subjects_medium_id_foreign');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('cascade');
        });

        Schema::table('classes', static function (Blueprint $table) {
            $table->dropForeign('classes_medium_id_foreign');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('cascade');

            $table->dropForeign('classes_shift_id_foreign');
            $table->foreign('shift_id')->references('id')->on('shifts')->onUpdate('restrict')->onDelete('cascade');

            $table->dropForeign('classes_stream_id_foreign');
            $table->foreign('stream_id')->references('id')->on('streams')->onUpdate('restrict')->onDelete('cascade');
        });
        Schema::table('class_subjects', static function (Blueprint $table) {
            $table->dropForeign('class_subjects_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->dropForeign('class_subjects_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->dropForeign('class_subjects_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
        });
        Schema::table('class_sections', static function (Blueprint $table) {
            $table->dropForeign('class_sections_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->dropForeign('class_sections_section_id_foreign');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');

            $table->dropForeign('class_sections_medium_id_foreign');
            $table->foreign('medium_id')->references('id')->on('mediums')->onDelete('cascade');
        });
        Schema::table('students', static function (Blueprint $table) {
            $table->dropForeign('students_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('students_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('students_guardian_id_foreign');
            $table->foreign('guardian_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('students_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        Schema::table('staffs', static function (Blueprint $table) {
            $table->dropForeign('staffs_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        /* Master Table End */

        Schema::table('elective_subject_groups', static function (Blueprint $table) {
            $table->dropForeign('elective_subject_groups_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->dropForeign('elective_subject_groups_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
        });

        Schema::table('student_subjects', static function (Blueprint $table) {
            $table->dropForeign('student_subjects_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('student_subjects_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');

            $table->dropForeign('student_subjects_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('student_subjects_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');

        });

        Schema::table('subject_teachers', static function (Blueprint $table) {
            $table->dropForeign('subject_teachers_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('subject_teachers_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->dropForeign('subject_teachers_teacher_id_foreign');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('subject_teachers_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });


        /* Lesson Module Start */
        Schema::table('lessons', static function (Blueprint $table) {
            $table->dropForeign('lessons_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('lessons_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });
        /* Lesson Module End */

        /* Assignment Module Start */
        Schema::table('assignments', static function (Blueprint $table) {
            $table->dropForeign('assignments_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('assignments_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');

            $table->dropForeign('assignments_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');

            $table->dropForeign('assignments_created_by_foreign');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('assignments_edited_by_foreign');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('assignment_submissions', static function (Blueprint $table) {
            $table->dropForeign('assignment_submissions_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('assignment_submissions_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');

        });
        /* Assignment Module End */

        /* Exam Module Start */
        Schema::table('exams', static function (Blueprint $table) {
            $table->dropForeign('exams_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->dropForeign('exams_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        Schema::table('exam_timetables', static function (Blueprint $table) {
            $table->dropForeign('exam_timetables_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');

            $table->dropForeign('exam_timetables_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        Schema::table('exam_marks', static function (Blueprint $table) {
            $table->dropForeign('exam_marks_exam_timetable_id_foreign');
            $table->foreign('exam_timetable_id')->references('id')->on('exam_timetables')->onDelete('cascade');

            $table->dropForeign('exam_marks_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('exam_marks_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');

            $table->dropForeign('exam_marks_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        Schema::table('exam_results', static function (Blueprint $table) {
            $table->dropForeign('exam_results_exam_id_foreign');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');

            $table->dropForeign('exam_results_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('exam_results_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('exam_results_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        /*Exam module End*/

        Schema::table('timetables', static function (Blueprint $table) {
            $table->dropForeign('timetables_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('timetables_subject_id_foreign');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');

            $table->dropForeign('timetables_semester_id_foreign');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
        });

        /* Announcement Module Start */
        Schema::table('announcements', static function (Blueprint $table) {
            $table->dropForeign('announcements_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        Schema::table('announcement_classes', static function (Blueprint $table) {
            $table->dropForeign('announcement_classes_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('announcement_classes_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
        });
        /* Announcement Module End */

        Schema::table('academic_calendars', static function (Blueprint $table) {
            $table->dropForeign('academic_calendars_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });

        Schema::table('attendances', static function (Blueprint $table) {
            $table->dropForeign('attendances_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('attendances_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('attendances_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });


        Schema::table('promote_students', static function (Blueprint $table) {
            $table->dropForeign('promote_students_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('promote_students_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('promote_students_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });

        /* Online Exam Module Start */
        Schema::table('online_exams', static function (Blueprint $table) {
            $table->dropForeign('online_exams_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('online_exams_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');

            $table->dropForeign('online_exams_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        Schema::table('online_exam_questions', static function (Blueprint $table) {
            $table->dropForeign('online_exam_questions_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('online_exam_questions_class_subject_id_foreign');
            $table->foreign('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');

            $table->dropForeign('online_exam_questions_last_edited_by_foreign');
            $table->foreign('last_edited_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('online_exam_question_choices', static function (Blueprint $table) {
            $table->dropForeign('online_exam_question_choices_online_exam_id_foreign');
            $table->foreign('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');

            $table->dropForeign('online_exam_question_choices_question_id_foreign');
            $table->foreign('question_id')->references('id')->on('online_exam_questions')->onDelete('cascade');
        });
        Schema::table('student_online_exam_statuses', static function (Blueprint $table) {
            $table->dropForeign('student_online_exam_statuses_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('student_online_exam_statuses_online_exam_id_foreign');
            $table->foreign('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');
        });
        Schema::table('online_exam_student_answers', static function (Blueprint $table) {
            $table->dropForeign('online_exam_student_answers_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('online_exam_student_answers_online_exam_id_foreign');
            $table->foreign('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');

            $table->dropForeign('online_exam_student_answers_question_id_foreign');
            $table->foreign('question_id')->references('id')->on('online_exam_question_choices')->onDelete('cascade');

            $table->dropForeign('online_exam_student_answers_option_id_foreign');
            $table->foreign('option_id')->references('id')->on('online_exam_question_options')->onDelete('cascade');
        });
        /* Online Exam Module End */

        /* Form Field Module Start */
        Schema::table('extra_student_datas', static function (Blueprint $table) {
            $table->dropForeign('extra_student_datas_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
        /* Form Field Module End */

        Schema::table('class_teachers', static function (Blueprint $table) {
            $table->dropForeign('class_teachers_class_section_id_foreign');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');

            $table->dropForeign('class_teachers_teacher_id_foreign');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });


        /* Fees Module */
        Schema::table('fees', static function (Blueprint $table) {
            $table->dropForeign('fees_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });

        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->dropForeign('payment_transactions_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('fees_paids', static function (Blueprint $table) {
            $table->dropForeign('fees_paids_fees_id_foreign');
            $table->foreign('fees_id')->references('id')->on('fees')->onDelete('cascade');

            $table->dropForeign('fees_paids_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('compulsory_fees', static function (Blueprint $table) {
            $table->dropForeign('compulsory_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('compulsory_fees_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');

            $table->dropForeign('compulsory_fees_fees_paid_id_foreign');
            $table->foreign('fees_paid_id')->references('id')->on('fees_paids')->onDelete('cascade');
        });
        Schema::table('optional_fees', static function (Blueprint $table) {
            $table->dropForeign('optional_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('optional_fees_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->dropForeign('optional_fees_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');

            $table->dropForeign('optional_fees_fees_paid_id_foreign');
            $table->foreign('fees_paid_id')->references('id')->on('fees_paids')->onDelete('cascade');
        });
        /*Fees Module End*/

        /* Subscription Module Start*/
        Schema::table('subscriptions', static function (Blueprint $table) {

            $table->dropForeign('subscriptions_package_id_foreign');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });
        Schema::table('addons', static function (Blueprint $table) {
            $table->dropForeign('addons_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        });

        // Schema::table('addon_subscriptions', static function (Blueprint $table) {
        //     $table->dropForeign('addon_subscriptions_feature_id_foreign');
        //     $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        // });
        
        Schema::table('subscription_bills', static function (Blueprint $table) {
            $table->dropForeign('subscription_bills_subscription_id_foreign');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');

            $table->dropForeign('subscription_bills_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');
        });
        Schema::table('subscription_features', static function (Blueprint $table) {
            $table->dropForeign('subscription_features_subscription_id_foreign');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');

            $table->dropForeign('subscription_features_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        });
        Schema::table('package_features', static function (Blueprint $table) {
            $table->dropForeign('package_features_package_id_foreign');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

            $table->dropForeign('package_features_feature_id_foreign');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
        });
        /* Subscription Module End*/

        /*Expense Module Start*/
        Schema::table('expenses', static function (Blueprint $table) {
            $table->dropForeign('expenses_category_id_foreign');
            $table->foreign('category_id')->references('id')->on('expense_categories')->onDelete('cascade');

            $table->dropForeign('expenses_staff_id_foreign');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade');

            $table->dropForeign('expenses_session_year_id_foreign');
            $table->foreign('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
        });
        /*Expense Module End*/

        Schema::table('leaves', static function (Blueprint $table) {
            $table->dropForeign('leaves_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('staff_support_schools', static function (Blueprint $table) {
            $table->dropForeign('staff_support_schools_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('fees', static function (Blueprint $table) {
            $table->dropForeign('fees_school_id_foreign');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->dropForeign('fees_class_id_foreign');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
        });

        Schema::table('compulsory_fees', static function (Blueprint $table) {
            $table->dropForeign('compulsory_fees_installment_id_foreign');
            $table->foreign('installment_id')->references('id')->on('fees_installments')->onDelete('cascade');

            $table->dropForeign('compulsory_fees_school_id_foreign');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->dropForeign('compulsory_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropForeign('compulsory_fees_payment_transaction_id_foreign');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');

            $table->dropForeign('compulsory_fees_fees_paid_id_foreign');
            $table->foreign('fees_paid_id')->references('id')->on('fees_paids')->onDelete('cascade');
        });

        Schema::table('optional_fees', static function (Blueprint $table) {
            $table->dropForeign('optional_fees_fees_class_id_foreign');
            $table->foreign('fees_class_id')->references('id')->on('fees_class_types')->onDelete('cascade');
        });

        Schema::table('payment_configurations', static function (Blueprint $table) {
            $table->string('bank_name')->nullable(true)->after('webhook_secret_key');
            $table->string('account_name')->nullable(true)->after('bank_name');
            $table->string('account_no')->nullable(true)->after('account_name');
        });

        Schema::create('chats', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('messages', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreignId('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('message')->nullable(true);
            $table->timestamp('read_at')->nullable(true);
            $table->timestamps();
        });

        Schema::create('attachments', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->references('id')->on('messages')->onDelete('cascade');
            $table->string('file')->nullable(true);
            $table->string('file_type')->nullable(true);
            $table->timestamps();
        });

        Schema::table('packages', static function (Blueprint $table) {
            $table->float('student_charge', 8, 2)->change();
            $table->float('staff_charge', 8, 2)->change();
            $table->float('charges', 8, 2)->change();
        });

        Schema::table('students', static function (Blueprint $table) {
            $table->string('application_type')->nullable()->after('class_section_id')->default('offline');
            $table->integer('application_status')->nullable()->after('school_id')->comment('1- accepted, 0- rejected')->default('1');
            $table->foreignId('class_id')->nullable()->after('user_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('class_section_id')->nullable()->change();
         });

        Schema::create('database_backups', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('features', static function (Blueprint $table) {
            $table->integer('required_vps')->after('status')->default(0);
        });

        Schema::table('schools', static function (Blueprint $table) {
            $table->string('database_name')->nullable(true)->after('domain');
            $table->string('code')->nullable(true)->after('database_name');
        });

        $schools = School::withTrashed()->get();

        foreach ($schools as $key => $school) {
            $school->code = "SCH".date('Y').$school->id;
            $school->save();
        }
      
        Schema::table('staffs', static function (Blueprint $table) {
            $table->date('joining_date')->nullable(true)->after('salary');
        });

        try {
            // Old school admin email verification
            $schoolAdmins = User::role('School Admin')->withTrashed()->get();
            foreach ($schoolAdmins as $key => $admin) {
                $admin->email_verified_at = Carbon::now();
                $admin->save();
            }    
        } catch (\Throwable $th) {
            
        }
        Cache::flush();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('payment_configurations', static function (Blueprint $table) {
            $table->dropColumn('bank_name');
            $table->dropColumn('account_name');
            $table->dropColumn('account_no');
        });

        Schema::dropIfExists('chats');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('attachments');

        Schema::table('students', static function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
            $table->dropColumn('application_type');
            $table->dropColumn('application_status');
            $table->foreignId('class_section_id')->nullable(false)->change();
        });

        Schema::dropIfExists('database_backups');
        Schema::table('features', static function (Blueprint $table) {
            $table->dropColumn('required_vps');
        });

        Schema::table('schools', static function (Blueprint $table) {
            $table->dropColumn('database_name');
            $table->dropColumn('code');
        });
        Schema::table('staffs', static function (Blueprint $table) {
            $table->dropColumn('joining_date');
        });

    }
};
