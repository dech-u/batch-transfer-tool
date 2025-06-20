<?php

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        set_time_limit(0);
        //Remove the OLD Tables
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('fees');
        Schema::dropIfExists('installment_fees');
        Schema::dropIfExists('fees_types');
        Schema::dropIfExists('fees_classes');
        Schema::dropIfExists('fees_paids');
        Schema::dropIfExists('compulsory_fees');
        Schema::dropIfExists('optional_fees');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_configurations');
        Schema::dropIfExists('leaves');
        Schema::enableForeignKeyConstraints();


        /*---- START : Master Tables ----*/

        Schema::create('fees', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('due_date');
            $table->float('due_charges')->comment('in percentage (%)');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('restrict');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fees_types', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        /*---- END : Master Tables ----*/

        Schema::create('fees_installments', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('due_date');
            $table->integer('due_charges')->comment('in percentage (%)');
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('fees_class_types', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('fees_type_id')->references('id')->on('fees_types')->onDelete('cascade');
            $table->float('amount');
            $table->boolean('optional')->comment('0 - No, 1 - Yes');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'fees_type_id', 'school_id', 'fees_id'], 'unique_ids');
            $table->timestamps();
        });

        Schema::create('payment_transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount', 8, 2);
            $table->string('payment_gateway', 128);
            $table->string('order_id')->comment('order_id / payment_intent_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_signature')->nullable();
            $table->enum('payment_status', ['failed', 'succeed', 'pending']);
            $table->foreignId('school_id')->nullable()->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('fees_paids', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->boolean('is_fully_paid')->comment('0 - No, 1 - Yes');
            $table->boolean('is_used_installment')->comment('0 - No, 1 - Yes');
            $table->double('amount', 8, 2);
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            //$table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->unique(['student_id', 'fees_id', 'school_id'], 'unique_ids');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('compulsory_fees', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->nullable()->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->enum('type', ['Full Payment', 'Installment Payment']);
            $table->foreignId('installment_id')->nullable()->references('id')->on('fees_installments')->onDelete('restrict');
            $table->enum('mode', ['Cash', 'Cheque', 'Online']);
            $table->string('cheque_no')->nullable();
            $table->double('amount', 8, 2);
            $table->double('due_charges', 8, 2)->nullable();
            $table->foreignId('fees_paid_id')->nullable()->references('id')->on('fees_paids')->onDelete('cascade');
            $table->enum('status', ['Success', 'Pending', 'Failed']);
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('optional_fees', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->nullable()->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->foreignId('fees_class_id')->nullable()->references('id')->on('fees_class_types')->onDelete('restrict');
            $table->enum('mode', ['Cash', 'Cheque', 'Online']);
            $table->string('cheque_no')->nullable();
            $table->double('amount', 8, 2);
            $table->foreignId('fees_paid_id')->nullable()->references('id')->on('fees_paids')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->enum('status', ['Success', 'Pending', 'Failed']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_configurations', static function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->string('api_key');
            $table->string('secret_key');
            $table->string('webhook_secret_key');
            $table->string('currency_code', 128)->nullable();
            $table->boolean('status')->comment('0 - Disabled, 1 - Enabled')->default(1);
            $table->foreignId('school_id')->nullable()->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('expenses', static function (Blueprint $table) {
            $table->bigInteger('basic_salary')->default(0)->after('staff_id');
            $table->float('paid_leaves')->default(0)->after('basic_salary');
        });

        Schema::table('packages', static function (Blueprint $table) {
            $table->integer('is_trial')->default(0)->after('status');
        });

        Schema::create('leave_masters', static function (Blueprint $table) {
            $table->id();
            $table->float('leaves')->comment('Leaves per month');
            $table->string('holiday');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('leaves', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('reason');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('status')->default(0)->comment('0 => Pending, 1 => Approved, 2 => Rejected');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('leave_master_id')->references('id')->on('leave_masters')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('leave_details', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_id')->references('id')->on('leaves')->onDelete('cascade');
            $table->date('date');
            $table->string('type');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
        });

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->dropUnique('subscription');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['feature_id']);
        });

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->dropUnique('addon_subscription');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->softDeletes();
        });

        Schema::create('user_status_for_next_cycles', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('status');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique('user_id');
            $table->timestamps();
        });

        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->string('payment_gateway')->change();
            $table->enum('payment_status', [0, 1, 2, 'failed', 'succeed', 'pending'])->change();
        });


        // Update payment transaction table
        $transactions = PaymentTransaction::whereIn('payment_status', [0, 1, 2])->get();
        if (count($transactions) !== 0) {
            foreach ($transactions as $key => $transaction) {
                $transaction->payment_gateway = "Stripe";
                if ($transaction->payment_status == "0") {
                    $transaction->payment_status = "failed";
                }
                if ($transaction->payment_status == "1") {
                    $transaction->payment_status = "succeed";
                }
                if ($transaction->payment_status == "2") {
                    $transaction->payment_status = "pending";
                }
                $transaction->save();
            }
        }

        Schema::table('payment_transactions', static function (Blueprint $table) {
            $table->enum('payment_status', ['failed', 'succeed', 'pending'])->change();
        });


        // Subscription bill calculation based on days usage
        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->integer('billing_cycle')->default(0)->after('end_date');
        });

        $subscriptions = Subscription::get();
        if (count($subscriptions) !== 0) {
            foreach ($subscriptions as $key => $subscription) {
                $start_date = Carbon::parse($subscription->start_date);
                $end_date = Carbon::parse($subscription->end_date);
                $subscription->billing_cycle = $start_date->diffInDays($end_date) + 1;
                $subscription->save();
            }
        }


        Schema::create('guidances', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable(true);
            $table->text('link')->nullable(true);
        });

        Schema::create('fees_advance', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('compulsory_fee_id')->references('id')->on('compulsory_fees')->onDelete('cascade');
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('parent_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('fees');
        Schema::dropIfExists('fees_types');
        Schema::dropIfExists('fees_installments');
        Schema::dropIfExists('fees_class_types');
        Schema::dropIfExists('fees_paids');
        Schema::dropIfExists('compulsory_fees');
        Schema::dropIfExists('optional_fees');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_configurations');

        Schema::dropIfExists('leave_details');
        Schema::table('expenses', static function (Blueprint $table) {
            $table->dropColumn('basic_salary');
            $table->dropColumn('paid_leaves');
        });
        Schema::table('packages', static function (Blueprint $table) {
            $table->dropColumn('is_trial');
        });
        Schema::dropIfExists('leave_masters');

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->unique(['school_id', 'start_date'], 'subscription');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['feature_id']);
            $table->unique(['school_id', 'feature_id', 'end_date'], 'addon_subscription');

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->dropColumn('deleted_at');
        });

        Schema::dropIfExists('user_status_for_next_cycles');

        Schema::table('subscriptions', static function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });

        Schema::dropIfExists('fees_advance');
        Schema::dropIfExists('guidances');

        Schema::enableForeignKeyConstraints();

    }
};
