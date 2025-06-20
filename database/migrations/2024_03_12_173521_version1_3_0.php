<?php

use App\Models\ExamMarks;
use App\Models\ExamResult;
use App\Models\PaymentTransaction;
use App\Models\SubscriptionBill;
use App\Models\SubscriptionBillPayment;
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
        Schema::table('packages', static function (Blueprint $table) {
            $table->integer('type')->default(1)->comment('0 => Prepaid, 1 => Postpaid')->after('days');
            $table->integer('no_of_students')->default(0)->after('type');
            $table->integer('no_of_staffs')->default(0)->after('no_of_students');
            $table->double('charges',64,4)->default(0)->after('no_of_staffs');
        });

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->integer('package_type')->default(1)->comment('0 => Prepaid, 1 => Postpaid')->after('end_date');
            $table->integer('no_of_students')->default(0)->after('package_type');
            $table->integer('no_of_staffs')->default(0)->after('no_of_students');
            $table->double('charges',64,4)->default(0)->after('no_of_staffs');
        });

        
        $subscription_payments = SubscriptionBillPayment::with('school')->get();
        foreach ($subscription_payments as $key => $payment) {
            $transaction = New PaymentTransaction();
            $transaction->user_id = $payment->school->admin_id;
            $transaction->amount = $payment->amount;
            $transaction->payment_gateway = $payment->payment_type == 'Cash' ? 'Cash' : 'Cheque';
            $transaction->order_id = $payment->cheque_number;
            $transaction->payment_status = 'succeed';
            $transaction->school_id = $payment->school_id;
            $transaction->save();

            SubscriptionBill::find($payment->subscription_bill_id)->update(['payment_transaction_id' => $transaction->id]);
        }

        Schema::dropIfExists('subscription_bill_payments');

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->foreignId('payment_transaction_id')->nullable(true)->after('status')->references('id')->on('payment_transactions')->onDelete('cascade');
        });

        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->double('amount',64,2)->change();
        });

        // Exam Result Status
        Schema::table('exam_results', static function (Blueprint $table) {
            $table->integer('status')->default(1)->comment('0 -> Failed, 1 -> Pass')->after('grade');
        });

        $exam_marks = ExamMarks::whereHas('timetable.exam', function($q) {
            $q->where('publish',1);
        })->with('timetable.exam')->where('passing_status',0)->get();

        foreach ($exam_marks as $key => $mark) {
            $result = ExamResult::where('exam_id',$mark->timetable->exam_id)->where('student_id',$mark->student_id)->first();
            if ($result) {
                $result->status = 0;
                $result->save();
            }
        }
        // End Exam Result Status

        Schema::table('users', static function (Blueprint $table) {
            $table->string('language')->default('en')->after('school_id');
        });

        $systemSettings = [
            [
            'name' => 'display_school_logos',
            'data' => '1',
            'type' => 'text'
            ],
            [
                'name' => 'display_counters',
                'data' => '1',
                'type' => 'text'
            ]
        ];

        SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);
        Cache::flush();


        // Permanent delete option for students
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

        Schema::table('student_subjects', static function (Blueprint $table) {
            $table->dropForeign('student_subjects_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');

        });

        Schema::table('assignment_submissions', static function (Blueprint $table) {
            $table->dropForeign('assignment_submissions_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('exam_marks', static function (Blueprint $table) {
            $table->dropForeign('exam_marks_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('exam_results', static function (Blueprint $table) {
            $table->dropForeign('exam_results_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('attendances', static function (Blueprint $table) {
            $table->dropForeign('attendances_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });


        Schema::table('promote_students', static function (Blueprint $table) {
            $table->dropForeign('promote_students_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('student_online_exam_statuses', static function (Blueprint $table) {
            $table->dropForeign('student_online_exam_statuses_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('online_exam_student_answers', static function (Blueprint $table) {
            $table->dropForeign('online_exam_student_answers_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('extra_student_datas', static function (Blueprint $table) {
            $table->dropForeign('extra_student_datas_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('fees_paids', static function (Blueprint $table) {
            $table->dropForeign('fees_paids_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
        Schema::table('compulsory_fees', static function (Blueprint $table) {
            $table->dropForeign('compulsory_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('optional_fees', static function (Blueprint $table) {
            $table->dropForeign('optional_fees_student_id_foreign');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        // End permanent delete option for students

        Schema::table('notifications', static function (Blueprint $table) {
            $table->string('send_to')->change();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('packages', static function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('no_of_students');
            $table->dropColumn('no_of_staffs');
            $table->dropColumn('charges');
        });

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->dropColumn('package_type');
            $table->dropColumn('no_of_students');
            $table->dropColumn('no_of_staffs');
            $table->dropColumn('charges');
        });

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->dropForeign(['payment_transaction_id']);
            $table->dropColumn('payment_transaction_id');
        });

        Schema::table('exam_results', static function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('language');
        });

    }
};
