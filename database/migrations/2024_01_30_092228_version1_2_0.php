<?php

use App\Models\AddonSubscription;
use App\Models\Fee;
use App\Models\Package;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\CachingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('packages', static function (Blueprint $table) {
            $table->integer('days')->default(1)->after('staff_charge');
        });
        $systemSettings = app(CachingService::class)->getSystemSettings();
        $days = $systemSettings['billing_cycle_in_days'] ?? 0;
        if ($days == null || empty($days)) {
            $subscription = Subscription::latest()->first();
            $days = $subscription->billing_cycle ?? 0;
        }
        foreach (Package::get() as $key => $package) {
            $package->days = $days;
            $package->save();
        }

        Schema::create('subscription_bill_payments', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_bill_id')->references('id')->on('subscription_bills')->onDelete('cascade');
            $table->date('date');
            $table->float('amount');
            $table->enum('payment_type', ['Cash', 'Cheque']);
            $table->string('cheque_number')->nullable(true);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->foreignId('subscription_id')->after('id')->nullable(true)->references('id')->on('subscriptions')->onDelete('cascade');
        });

        foreach (Subscription::get() as $key => $subscription) {
            AddonSubscription::where('school_id', $subscription->school_id)->whereDate('end_date', $subscription->end_date)->withTrashed()->update(['subscription_id' => $subscription->id]);
        }

        Schema::table('fees', static function (Blueprint $table) {
            $table->float('due_charges_amount')->after('due_charges');
        });

        /* Calculate Due charges amount */
        $fees = Fee::with(['fees_class_type' => function ($q) {
            $q->where('optional', 0);
        }])->select(['id', 'due_charges', 'due_charges_amount'])->get();
        collect($fees)->map(function ($fee) {
            $fee->due_charges_amount = $fee->fees_class_type->sum('amount') * $fee->due_charges / 100;
            $fee->setAppends([]);
            unset($fee->fees_class_type,);
        });
        Fee::upsert($fees->toArray(), ['id'], ['due_charges_amount']);

        Schema::table('fees_installments', static function (Blueprint $table) {
            $table->enum('due_charges_type', ['fixed', 'percentage'])->default('percentage')->after('due_date');
            $table->integer('due_charges')->comment('')->change();
        });

        Schema::create('galleries', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable(true);
            $table->string('thumbnail')->nullable(true);
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('notifications', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('message')->nullable(true);
            $table->string('image')->nullable(true);
            $table->enum('send_to',['All users','Students','Guardian','Specific users','Over Due Fees']);
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('feature_sections', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('heading')->nullable(true);
            $table->integer('rank')->default(0);
            $table->timestamps();
        });

        Schema::create('feature_section_lists', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_section_id')->references('id')->on('feature_sections')->onDelete('cascade');
            $table->string('feature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('image')->nullable(true);
            $table->timestamps();
        });

        // Add date format in school settings
        $data = [];
        $schools = School::get();
        foreach ($schools as $key => $school) {
            $data[] = [
                'name'      => 'date_format',
                'data'      => 'd-m-Y',
                'type'      => 'string',
                'school_id' => $school->id,
            ];
            $data[] = [
                'name'      => 'time_format',
                'data'      => 'h:i A',
                'type'      => 'string',
                'school_id' => $school->id,
            ];
        }
        SchoolSetting::upsert($data,['name','school_id'],['data','type']);

        // System settings
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
        ];

        SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);
        Cache::flush();

        Schema::table('subscription_bills', static function (Blueprint $table) {
            $table->double('amount',64,4)->change();
        });

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->double('price',64,4)->change();
        });

        Schema::table('expenses', static function (Blueprint $table) {
            $table->double('amount',64,2)->change();
        });

        Schema::table('fees_class_types', static function (Blueprint $table) {
            $table->double('amount',64,2)->change();
        });

        // Soft delete school admin if school_id null, Because of super admin has change school admin softdelete old school admin due to many issue has occur like login, forgot password, etc...  
        $schools = School::onlyTrashed()->pluck('admin_id')->toArray();
        User::whereIn('id',$schools)->delete();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('packages', static function (Blueprint $table) {
            $table->dropColumn('days');
        });

        Schema::dropIfExists('subscription_bill_payments');

        Schema::table('addon_subscriptions', static function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
        });
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('notifications');

        Schema::table('fees', static function (Blueprint $table) {
            $table->renameColumn('due_charges_percentage', 'due_charges');
            $table->dropColumn('due_charges_amount');
        });

        Schema::table('fees', static function (Blueprint $table) {
            $table->dropColumn('due_charges_amount');
        });

        Schema::table('fees_installments', static function (Blueprint $table) {
            $table->dropColumn('due_charges_type');
            $table->integer('due_charges')->comment('in percentage (%)')->change();
        });
        Schema::dropIfExists('feature_sections');
        Schema::dropIfExists('feature_section_lists');
    }
};
