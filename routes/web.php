<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\ClassSchoolController;
use App\Http\Controllers\ClassSectionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\Exam\ExamController;
use App\Http\Controllers\Exam\ExamTimetableController;
use App\Http\Controllers\Exam\GradeController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeesController;
use App\Http\Controllers\FeesTypeController;
use App\Http\Controllers\FormFieldsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveMasterController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonTopicController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnlineExamController;
use App\Http\Controllers\OnlineExamQuestionController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollSettingController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\PromoteStudentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolSettingsController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SessionYearController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubscriptionBillPaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionWebhookController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WebSettingsController;
use App\Http\Controllers\WizardSettingsController;
use App\Http\Controllers\PaymentController;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionBill;
use App\Models\User;
use App\Services\CachingService;
use Carbon\Carbon;
use Illuminate\Queue\Connectors\DatabaseConnector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Auth::routes(['verify' => true]);

// global login
Route::get('/login', [AuthController::class, 'login']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/', [Controller::class, 'index'])->middleware(['CheckForMaintenanceMode','2fa']);

// 2fa code verification
Route::get('/2fa', [AuthController::class, 'twoFactorAuthentication'])->name('auth.2fa');
Route::post('/2fa-code', [AuthController::class, 'twoFactorAuthenticationCode'])->name('auth.2fa.code');

Route::post('schools/registration', [SchoolController::class, 'registration']);
Route::post('contact', [Controller::class, 'contact']);
Route::get('subscription/cron-job', [Controller::class, 'cron_job']);
Route::get('set-language/{lang}', [LanguageController::class, 'set_language']);

Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('students/admission-form', [StudentController::class, 'admissionForm'])->name('admission.form');
Route::get('email/verify', [Controller::class, 'emailVerify']);

// wizard settings
Route::get('wizard-settings/', [WizardSettingsController::class, 'index'])->name('wizard-settings.index');
Route::post('wizard-settings/store', [WizardSettingsController::class, 'store'])->name('wizard-settings.store');
Route::post('/update-wizard-session', [WizardSettingsController::class, 'updateWizardSystemSettings'])->name('wizard-settings.update-wizard-session');
Route::get('wizard-settings/show', [WizardSettingsController::class, 'show'])->name('wizard-settings.show');


Route::group(['prefix' => 'school'], static function () {
    Route::get('about-us', [Controller::class, 'about_us']);
    Route::get('contact-us', [Controller::class, 'contact_us']);
    Route::post('contact-us', [Controller::class, 'contact_form']);
    Route::get('photos', [Controller::class, 'photo']);
    Route::get('photos/{id}', [Controller::class, 'photo_file']);
    Route::get('videos', [Controller::class, 'video']);
    Route::get('videos/{id}', [Controller::class, 'video_file']);
    Route::get('terms-conditions', [Controller::class, 'terms_conditions']);
    Route::get('privacy-policy', [Controller::class, 'privacy_policy']);
    Route::get('refund-cancellation-policy', [Controller::class, 'refund_cancellation']);
    Route::get('online-admission',[Controller::class, 'admission'])->name('online-admission.index');
    Route::post('online-admission',[Controller::class, 'registerStudent'])->name('online-admission.store');
});

Route::group(['prefix' => 'page/type'], static function () {
    Route::get('/{type?}', [Controller::class, 'systemLinks']);
});

Route::group(['prefix' => 'install'], static function () {
    Route::get('purchase-code', [InstallerController::class, 'purchaseCodeIndex'])->name('install.purchase-code.index');
    Route::post('purchase-code', [InstallerController::class, 'checkPurchaseCode'])->name('install.purchase-code.post');
    Route::get('php-function', [InstallerController::class, 'phpFunctionIndex'])->name('install.php-function.index');
});

// auth
Route::group(['middleware' => ['Role', 'checkSchoolStatus', 'status','SwitchDatabase','verifiedEmail','CheckForMaintenanceMode','2fa','wizardSettings']], static function () {

    Route::group(['middleware' => 'language'], static function () {

        // Super admin routes
        /*** School ***/

        Route::group(['prefix' => 'school-custom-fields'], static function () {
            Route::get('/',[FormFieldsController::class, 'schoolIndex'])->name('school-custom-fields.index');
            Route::post('/store',[FormFieldsController::class, 'schoolStore'])->name('school-custom.store');
            Route::put('/{id}',[FormFieldsController::class, 'schoolUpdate'])->name('school-custom-field.update');
            Route::get('/list', [FormFieldsController::class, 'schoolShow'])->name('school-custom-field.list');
            Route::delete('/delete/{id}', [FormFieldsController::class, 'schoolDestroy'])->name('school-custom-field.destroy');

            Route::post('/update-rank', [FormFieldsController::class, 'schoolUpdateRankOfFields']);
            Route::put("/{id}/restore", [FormFieldsController::class, 'schoolRestore'])->name('school-custom-field.restore');
            Route::delete("/{id}/deleted", [FormFieldsController::class, 'schoolTrash'])->name('school-custom-field.trash');

        });
        
        Route::group(['prefix' => 'schools'], static function () {
            Route::put("/{id}/restore", [SchoolController::class, 'restore'])->name('schools.restore');
            Route::delete("/{id}/deleted", [SchoolController::class, 'trash'])->name('schools.trash');
            // Route::get('/admin/search', [SchoolController::class, 'adminSearch']);
            Route::post('/admin/update', [SchoolController::class, 'updateAdmin']);
            Route::PUT('/change/status/{id}', [SchoolController::class, 'changeStatus']);
            Route::get('/admin/search', [SchoolController::class, 'searchAdmin']);

            Route::get('/send-mail', [SchoolController::class, 'sendMailIndex'])->name('schools.send.mail');
            Route::post('/send-mail', [SchoolController::class, 'sendMail']);
            Route::post('/create-demo-school', [SchoolController::class, 'createDemoSchool'])->name('create.demo.school');
            Route::get('/school-inquiry-index',[SchoolController::class, 'schoolInquiryIndex'])->name('school-inquiry.index');
            Route::get('/school-inquiry-list', [SchoolController::class, 'schoolInquiryList'])->name('school-inquiry.list');
            Route::post('/school-inquiry-update', [SchoolController::class, 'schoolInquiryUpdate'])->name('school-inquiry.update');
            Route::delete("/{id}/school-inquiry-delete", [SchoolController::class, 'schoolInquiryDelete'])->name('school-inquiry.delete');
            
        });
        Route::resource('schools', SchoolController::class);

        /*** Package ***/
        Route::group(['prefix' => 'package'], static function () {
            Route::get('status/{id}', [PackageController::class, 'status']);
            Route::put('restore/{id}', [PackageController::class, 'restore'])->name('package.restore');
            Route::delete('trash/{id}', [PackageController::class, 'trash'])->name('package.trash');
            Route::PATCH('change/rank', [PackageController::class, 'change_rank']);

        });
        Route::resource('package', PackageController::class);

        // Addons
        Route::group(['prefix' => 'addons'], static function () {
            Route::put('restore/{id}', [AddonController::class, 'restore'])->name('addons.restore');
            Route::delete('trash/{id}', [AddonController::class, 'trash'])->name('addons.trash');
            Route::put('status/{id}', [AddonController::class, 'status'])->name('addons.status');
            Route::get('plan', [AddonController::class, 'plan'])->name('addons.plan');
            Route::get('subscribe/{id}/package-type/{type}', [AddonController::class, 'subscribe'])->name('addons.subscribe');
            Route::get('discontinue/{id}', [AddonController::class, 'discontinue'])->name('addons.discontinue');

            Route::get('prepaid-package/{id}', [AddonController::class, 'prepaid_package_addon'])->name('prepaid_package_addon');

            // Stripe
            Route::get('payment/success/{checkout_session_id?}/{id}', [AddonController::class, 'payment_success']);
            Route::get('payment/cancel', [AddonController::class, 'payment_cancel']);


            // Razorpay, Paystack, Flutterwave
            Route::get('payment/success', [AddonController::class, 'payment_success_callback'])->name('addons.payment.success');
            Route::get('payment/cancel_callback', [AddonController::class, 'payment_cancel_callback'])->name('addons.payment.cancel');

        });
        Route::resource('addons', AddonController::class);

        // Subscription
        Route::group(['prefix' => 'subscriptions'], static function () {
            Route::get('plan/{id}/type/{type}/current-plan/{isCurrentPlan?}', [SubscriptionController::class, 'plan']);
            Route::get('prepaid/package/{package_id}/{type?}/{isCurrentPlan?}', [SubscriptionController::class, 'prepaid_plan']);
            
            Route::get('history', [SubscriptionController::class, 'history'])->name('subscriptions.history');
            Route::get('cancel-upcoming/{id?}', [SubscriptionController::class, 'cancel_upcoming'])->name('subscriptions.cancel.upcoming');
            Route::get('confirm-upcoming-plan/{id}', [SubscriptionController::class, 'confirm_upcoming_plan']);

            Route::get('payment/success/{checkout_session_id}/{subscriptionBill_id?}/{package_id?}/{type?}/{subscription_id?}/{isCurrentPlan?}', [SubscriptionController::class, 'payment_success']);
            Route::get('payment/cancel/{subscriptionBillId?}', [SubscriptionController::class, 'payment_cancel']);

            Route::get('bill/receipt/{id}', [SubscriptionController::class, 'bill_receipt']);
            Route::get('report', [SubscriptionController::class, 'subscription_report']);
            Route::get('report/show/{status?}', [SubscriptionController::class, 'subscription_report_show']);
            Route::put('update-expiry', [SubscriptionController::class, 'update_expiry'])->name('subscription.update.expiry');
            Route::put('change-bill-date', [SubscriptionController::class, 'change_bill_date'])->name('subscription.change.bill.date');
            Route::get('start-immediate-plan/{id?}/type/{type?}', [SubscriptionController::class, 'start_immediate_plan']);
            Route::put('update-current-plan', [SubscriptionController::class, 'update_current_plan'])->name('subscription.update-current-plan');
            Route::get('generate-bill/{id?}', [SubscriptionController::class, 'generate_bill']);
            Route::get('transactions', [SubscriptionController::class, 'transactions_log']);
            Route::get('transactions/list', [SubscriptionController::class, 'subscription_transaction_list']);

            Route::get('bill-payment/{id}', [SubscriptionController::class, 'bill_payment']);
            Route::put('bill-payment/store{id?}', [SubscriptionController::class, 'bill_payment_store'])->name('subscriptions-bill-payment.update');

            Route::delete('bill-payment/destroy/{id}', [SubscriptionController::class, 'delete_bill_payment']);

            Route::get('pay-prepaid-upcoming-plan/{package_id}/type/{type}/subscription/{subscription_id}', [SubscriptionController::class, 'pay_prepaid_upcoming_plan']);

            // Super admin graph
            Route::get('transaction/{year}', [SubscriptionController::class, 'transaction']);

            // Razorpay
            Route::post('create/razorpay/order-id', [SubscriptionController::class, 'razorpay_order_id']);
            Route::post('razorpay', [SubscriptionController::class, 'razorpay']);

        });
        Route::resource('subscriptions', SubscriptionController::class);


        Route::group(['prefix' => 'web-settings'], static function () {
            Route::get('feature-section', [WebSettingsController::class, 'feature_section_index'])->name('web-settings.feature.sections');
            Route::post('feature-section', [WebSettingsController::class, 'feature_section_store'])->name('web-settings.feature.sections.store');
            Route::get('section/show', [WebSettingsController::class, 'web_settings_show'])->name('web-settings-section.show');
            Route::get('section/{id}/edit', [WebSettingsController::class, 'web_settings_edit'])->name('web-settings-section.edit');
            Route::put('section/update/{id}', [WebSettingsController::class, 'web_settings_update'])->name('web-settings-section.update');
            Route::delete('section/delete/{id}', [WebSettingsController::class, 'feature_section_delete'])->name('web-settings-section.destroy');

            Route::PATCH('feature-section/change/rank', [WebSettingsController::class, 'feature_section_rank'])->name('feature_section_rank');
            
        });

        /*** System Settings ***/
        Route::group(['prefix' => 'system-settings'], static function () {
            Route::get('fcm', [SystemSettingsController::class, 'fcmIndex'])->name('system-settings.fcm');

            // privacy policy
            Route::get('privacy-policy', [SystemSettingsController::class, 'privacyPolicy'])->name('system-settings.privacy-policy');

            // terms & conditions
            Route::get('terms-condition', [SystemSettingsController::class, 'termsConditions'])->name('system-settings.terms-condition');

            Route::get('student-privacy-policy', [SystemSettingsController::class, 'privacyPolicy'])->name('system-settings.student-privacy-policy');
            Route::get('student-terms-condition', [SystemSettingsController::class, 'termsConditions'])->name('system-settings.student-terms-condition');
            Route::get('contact-us', [SystemSettingsController::class, 'contactUs'])->name('system-settings.contact-us');
            Route::get('about-us', [SystemSettingsController::class, 'aboutUs'])->name('system-settings.about-us');
            Route::put('notification-settings', [SystemSettingsController::class, 'notificationSettingUpdate'])->name('notification-setting.update');

            /*** Email Settings ***/
            Route::get('email', [SystemSettingsController::class, 'emailIndex'])->name('system-settings.email.index');
            Route::post('email', [SystemSettingsController::class, 'emailUpdate'])->name('system-settings.email.update');
            Route::post('email/verify', [SystemSettingsController::class, 'verifyEmailConfiguration'])->name('system-settings.email.verify');

            Route::get('email-template', [SystemSettingsController::class, 'emailTemplate'])->name('system-settings.email.template');


            /*** App Settings ***/
            Route::get('app', [SystemSettingsController::class, 'appSettingsIndex'])->name('system-settings.app');
            Route::post('app', [SystemSettingsController::class, 'appSettingsUpdate'])->name('system-settings.app.update');

            /*** Payment Settings ***/
            Route::get('payment', [SystemSettingsController::class, 'paymentIndex'])->name('system-settings.payment.index');
            Route::post('payment', [SystemSettingsController::class, 'paymentUpdate'])->name('system-settings.payment.update');

            Route::get('third-party-apis', [SystemSettingsController::class, 'thirdPartyApiIndex'])->name('system-settings.third-party');
            Route::post('third-party-apis', [SystemSettingsController::class, 'thirdPartyApiUpdate'])->name('system-settings.third-party.update');

            Route::get('subscription-settings', [SystemSettingsController::class, 'subscription_settings'])->name('system-settings.subscription-settings');
            Route::post('subscription-settings', [SystemSettingsController::class, 'subscription_settings_update'])->name('system-settings.subscription-settings-store');

            Route::get('school-terms-conditions', [SystemSettingsController::class, 'school_terms_condition'])->name('system-settings.school-terms-condition');

            Route::get('refund-cancellation', [SystemSettingsController::class, 'refund_cancellation'])->name('system-settings.refund-cancellation');

            Route::get('teacher-privacy-policy', [SystemSettingsController::class, 'teacherPrivacyPolicy'])->name('system-settings.teacher-privacy-policy');
            Route::get('teacher-terms-condition', [SystemSettingsController::class, 'teacherTermsConditions'])->name('system-settings.teacher-terms-condition');

            Route::put('email-template', [SystemSettingsController::class, 'emailTemplateUpdate'])->name('system-settings.email-template.update');

        });

        Route::resource('system-settings', SystemSettingsController::class);


        Route::get('system-update', [SystemUpdateController::class, 'index'])->name('system-update.index');
        Route::post('system-update', [SystemUpdateController::class, 'update'])->name('system-update.update');

        // Features
        Route::get('features', [PackageController::class, 'features_list']);
        Route::get('features/show', [PackageController::class, 'features_show'])->name('features.show');
        Route::post('features/enable', [PackageController::class, 'features_enable']);

        Route::resource('guidances', GuidanceController::class);

        // End super admin routes
        // =================================================================

        /*** Dashboard ***/
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('home', [DashboardController::class, 'index'])->name('home');

        /*** Auth ***/
        Route::group(['prefix' => 'auth'], static function () {
            Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('check-password', [AuthController::class, 'checkPassword'])->name('auth.check-password');
            Route::get('change-password', [AuthController::class, 'changePasswordIndex'])->name('auth.change-password.index');
            Route::post('change-password', [AuthController::class, 'changePasswordStore'])->name('auth.change-password.update');
            Route::get('profile', [AuthController::class, 'profileEdit'])->name('auth.profile.edit');
            Route::put('profile', [AuthController::class, 'profileUpdate'])->name('auth.profile.update');
        });

        /*** Role & Staff management ***/
        Route::get('staff/support', [StaffController::class, 'support']);
        Route::get("/roles-list", [RoleController::class, 'list'])->name('roles.list');
        Route::resource('roles', RoleController::class);

        Route::group(['prefix' => 'staff'], static function () {
            Route::get('id-card', [StaffController::class, 'staff_id_card'])->name('staff.id-card');
            Route::get('id-card-list', [StaffController::class, 'staff_id_card_list'])->name('staff.show.all');
            Route::post('generate-id-card', [StaffController::class, 'generate_staff_id_card']);
            Route::get('download-dummy-file', [StaffController::class, 'downloadSampleFile'])->name('staff.bulk-data-sample');
            Route::get("create-bulk-upload",[StaffController::class,'bulkUploadIndex'])->name('staff.create-bulk-upload');
            Route::post("store-bulk-upload",[StaffController::class,'storeBulkUpload'])->name('staff.store-bulk-upload');
            Route::get('payroll-structure/{id}',[StaffController::class,'viewSalaryStructure'])->name('staff.payroll-structure');

            Route::delete('payroll-setting/{id}',[StaffController::class,'deletePayrollSetting']);
            Route::put('payroll-setting/{id}',[StaffController::class,'updatePayrollSetting']);
            

        });
        
        Route::resource('staff', StaffController::class);
        Route::put("staff/{id}/change-status", [StaffController::class, 'restore'])->name('staff.restore');
        Route::delete("staff/{id}/deleted", [StaffController::class, 'trash'])->name('staff.trash');
        Route::post("staff/change-status-bulk", [StaffController::class, 'changeStatusBulk']);
       

        /*** Medium ***/
        Route::group(['prefix' => 'mediums'], static function () {
            Route::put("/{id}/restore", [MediumController::class, 'restore'])->name('mediums.restore');
            Route::delete("/{id}/deleted", [MediumController::class, 'trash'])->name('mediums.trash');
        });
        Route::resource('mediums', MediumController::class);


        /*** Section ***/
        Route::group(['prefix' => 'section'], static function () {
            Route::put("/{id}/restore", [SectionController::class, 'restore'])->name('section.restore');
            Route::delete("/{id}/deleted", [SectionController::class, 'trash'])->name('section.trash');
        });
        Route::resource('section', SectionController::class);


        /*** Subject ***/
        Route::group(['prefix' => 'subjects'], static function () {
            Route::put("/{id}/restore", [SubjectController::class, 'restore'])->name('subjects.restore');
            Route::delete("/{id}/deleted", [SubjectController::class, 'trash'])->name('subjects.trash');
        });
        Route::resource('subjects', SubjectController::class);

        /*** Class ***/
        Route::group(['prefix' => 'class'], static function () {
            Route::put("/{id}/restore", [ClassSchoolController::class, 'restore'])->name('class.restore');
            Route::delete("/{id}/deleted", [ClassSchoolController::class, 'trash'])->name('class.trash');
            Route::get('/subject', [ClassSchoolController::class, 'classSubjectIndex'])->name('class.subject.index');
            Route::get('/subject/{id}/edit', [ClassSchoolController::class, 'classSubjectEdit'])->name('class.subject.edit');
            Route::put('/subject/{id}/edit', [ClassSchoolController::class, 'classSubjectUpdate'])->name('class.subject.update');
            Route::get('/subject/list', [ClassSchoolController::class, 'classSubjectList'])->name('class.subject.list');
            Route::delete('/subject/{class_subject_id}', [ClassSchoolController::class, 'deleteClassSubject'])->name('class.subject.destroy');
            Route::delete('/subject-group/{group_id}', [ClassSchoolController::class, 'deleteClassSubjectGroup'])->name('class.subject-group.destroy');

            Route::get('/attendance/{id?}', [ClassSchoolController::class, 'classAttendance']);

        });
        Route::resource('class', ClassSchoolController::class);

        /*** Class Section ***/
        Route::group(['prefix' => 'class-section'], static function () {
            Route::delete('class-teacher/remove/{id}/{class_section_id}', [ClassSectionController::class, 'removeClassTeacher']);
            Route::delete('subject-teacher/remove/{class_section_id}/{teacher_id}/{subject_id}', [ClassSectionController::class, 'removeSubjectTeacher']);
            Route::put("/{id}/restore", [ClassSectionController::class, 'restore'])->name('class-section.restore');
            Route::delete("/{id}/trash", [ClassSectionController::class, 'trash'])->name('class-section.trash');
        });
        Route::resource('class-section', ClassSectionController::class);

        /*** Teachers ***/
        Route::group(['prefix' => 'teachers'], static function () {
            Route::put("/{id}/restore", [TeacherController::class, 'restore'])->name('teachers.restore');
            Route::delete("/{id}/deleted", [TeacherController::class, 'trash'])->name('teachers.trash');
            Route::put("change/status/{id}", [TeacherController::class, 'changeStatus'])->name('teachers.change-status');
            Route::post("/change-status-bulk", [TeacherController::class, 'changeStatusBulk'])->name('staff.change-status-bulk');
            Route::get('download-dummy-file', [TeacherController::class, 'downloadSampleFile'])->name('teachers.bulk-data-sample');
            Route::get("create-bulk-upload",[TeacherController::class,'bulkUploadIndex'])->name('teachers.create-bulk-upload');
            Route::post("store-bulk-upload",[TeacherController::class,'storeBulkUpload'])->name('teachers.store-bulk-upload');
        });
        Route::resource('teachers', TeacherController::class);


        /*** Parents ***/
        Route::get('/guardian/search', [GuardianController::class, 'search']);
        Route::resource('guardian', GuardianController::class);

        /*** Students ***/
        Route::group(['prefix' => 'students'], static function () {
            Route::get('create-bulk', [StudentController::class, 'createBulkData'])->name('students.create-bulk-data');
            Route::post('store-bulk', [StudentController::class, 'storeBulkData'])->name('students.store-bulk-data');
            
            // Update bulk profile student & guardian
            Route::get('update-profile', [StudentController::class, 'update_profile'])->name('students.upload-profile');
            Route::get('list/{id?}', [StudentController::class, 'list'])->name('students.list');
            Route::post('update-profile', [StudentController::class, 'store_update_profile'])->name('students.update-profile');
            

            Route::get('download-file', [StudentController::class, 'downloadSampleFile'])->name('student.bulk-data-sample');
            Route::delete('change-status/{id}', [StudentController::class, 'changeStatus'])->name('student.change-status');
            /*** Reset Password ***/
            Route::get('reset-password', [StudentController::class, 'resetPasswordIndex'])->name('students.reset-password.index');
            Route::post('reset-password', [StudentController::class, 'resetPasswordUpdate'])->name('student.reset-password.update');
            Route::get('reset-password-list', [StudentController::class, 'resetPasswordShow'])->name('student.reset-password.show');

            /*** Roll Number ***/
            Route::get('roll-number', [StudentController::class, 'rollNumberIndex'])->name('students.roll-number.index');
            Route::post('roll-number', [StudentController::class, 'rollNumberUpdate'])->name('students.roll-number.update');
            Route::get('roll-number-list', [StudentController::class, 'rollNumberShow'])->name('students.roll-number.show');
            Route::post("change-status-bulk", [StudentController::class, 'changeStatusBulk'])->name('students.change-status-bulk');

            Route::delete("/{id}/deleted", [StudentController::class, 'trash'])->name('student.trash');

            Route::get('generate-id-card', [StudentController::class, 'generate_id_card_index'])->name('students.generate-id-card-index');
            Route::post('generate-id-card', [StudentController::class, 'generate_id_card'])->name('students.generate-id-card');
            Route::get('online-registration-index',[StudentController::class, 'onlineRegistrationIndex'])->name('online-registration.index');
            Route::get('online-registration-list',[StudentController::class, 'onlineRegistrationList'])->name('students.online-registration');
            Route::post('update-bulk-application-status',[StudentController::class, 'updateBulkApplicationStatus'])->name('update-bulk-application-status');
            Route::post('update-application-status',[StudentController::class, 'updateApplicationStatus'])->name('update-application-status');
            Route::get('get-class-section-by-class/{class_id}',[StudentController::class, 'getclassSectionByClass']);
        });
        Route::resource('students', StudentController::class);

        Route::get('id-card-settings', [SchoolSettingsController::class, 'id_card_index'])->name('id-card-settings');
        Route::post('id-card-settings', [SchoolSettingsController::class, 'id_card_store']);

        /*** Timetable ***/
        Route::group(['prefix' => 'timetable'], static function () {
            Route::put('/settings', [TimetableController::class, 'updateTimetableSettings'])->name('timetable.settings');
            Route::group(['prefix' => '/teacher'], static function () {
                Route::get('/', [TimetableController::class, 'teacherIndex'])->name('timetable.teacher.index');
                Route::get('/list', [TimetableController::class, 'teacherList'])->name('timetable.teacher.list');
                Route::get('/show/{teacher_id}', [TimetableController::class, 'teacherShow'])->name('timetable.teacher.show');
            });
            Route::delete('/delete/{id}', [TimetableController::class, 'deleteClassTimetable']);
        });
        Route::resource('timetable', TimetableController::class);

        /*** Attendance ***/
        // TODO : Improve this
        Route::group(['prefix' => 'attendance'], static function () {
            Route::get('view-attendance', [AttendanceController::class, 'view'])->name("attendance.view");
            Route::get('student-attendance-list', [AttendanceController::class, 'attendance_show'])->name('attendance.list.show');
            Route::get('getAttendanceData', [AttendanceController::class, 'getAttendanceData']);

            Route::get('month-wise', [AttendanceController::class, 'monthWiseIndex'])->name('attendance.month');
            Route::get('month-wise/list', [AttendanceController::class, 'monthWiseShow']);
        });

        Route::resource('attendance', AttendanceController::class);

        /*** Lesson ***/
        Route::group(['prefix' => 'lesson'], static function () {
            Route::get('/search', [LessonController::class, 'search'])->name('lesson.search');
            Route::put("/{id}/restore", [LessonController::class, 'restore'])->name('lesson.restore');
            Route::delete("/{id}/deleted", [LessonController::class, 'trash'])->name('lesson.trash');

        });
        Route::resource('lesson', LessonController::class);
        Route::delete('file/delete/{id}', [LessonController::class, 'deleteFile'])->name('file.delete');

        /*** Lesson Topic ***/
        Route::group(['prefix' => 'lesson-topic'], static function () {
            Route::put("/{id}/restore", [LessonTopicController::class, 'restore'])->name('lesson-topic.restore');
            Route::delete("/{id}/deleted", [LessonTopicController::class, 'trash'])->name('lesson-topic.trash');
        });
        Route::resource('lesson-topic', LessonTopicController::class);


        /*** Announcement ***/
        Route::group(['prefix' => 'announcement'], static function () {
            Route::put("/{id}/restore", [AnnouncementController::class, 'restore'])->name('announcement.restore');
            Route::delete("/{id}/deleted", [AnnouncementController::class, 'trash'])->name('announcement.trash');
            Route::delete("file/delete/{id}", [AnnouncementController::class, 'fileDelete'])->name('announcement.fileDelete');

        });
        Route::resource('announcement', AnnouncementController::class);

        /*** Holiday ***/
        Route::resource('holiday', HolidayController::class);

        /*** Assignment ***/
        // TODO : Improve this
        Route::get('assignment-submission', [AssignmentController::class, 'viewAssignmentSubmission'])->name('assignment.submission');
        Route::put('assignment-submission/{id}', [AssignmentController::class, 'updateAssignmentSubmission'])->name('assignment.submission.update');
        Route::get('assignment-submission-list', [AssignmentController::class, 'assignmentSubmissionList'])->name('assignment.submission.list');
        Route::put("assignment/{id}/restore", [AssignmentController::class, 'restore'])->name('assignment.restore');
        Route::delete("assignment/{id}/deleted", [AssignmentController::class, 'trash'])->name('assignment.trash');
        Route::resource('assignment', AssignmentController::class);

        /*** Sliders ***/
        Route::resource('sliders', SliderController::class);

        /*** Session Years ***/
        Route::group(['prefix' => 'session-year'], static function () {
            Route::put("/{id}/restore", [SessionYearController::class, 'restore'])->name('session-year.restore');
            Route::delete("/{id}/deleted", [SessionYearController::class, 'trash'])->name('session-year.trash');
            Route::put("/{id}/default", [SessionYearController::class, 'default'])->name('session-year.default');
        });
        Route::resource('session-year', SessionYearController::class);


        /*** Exams ***/

        // Grades
        Route::resource('exam/grade', GradeController::class, ['as' => 'exam']);

        // TODO : Improve this
        // Exam Timetables
        Route::resource('exam/timetable', ExamTimetableController::class, ['as' => 'exam']);
        Route::post('exams/update-timetable', [ExamController::class, 'updateExamTimetable'])->name('exams.update-timetable');
        Route::delete('exams/delete-timetable/{id}', [ExamController::class, 'deleteExamTimetable'])->name('exams.delete-timetable');

        //Exam Marks
        Route::post('exams/submit-marks', [ExamController::class, 'submitMarks'])->name('exams.submit-marks');
        Route::get('exams/upload-marks', [ExamController::class, 'uploadMarks'])->name('exams.upload-marks');
        Route::get('exams/marks-list', [ExamController::class, 'marksList'])->name('exams.marks-list');

        // Exam Result
        Route::get('exams/exam-result', [ExamController::class, 'getExamResultIndex'])->name('exams.get-result');
        Route::get('exams/show-result', [ExamController::class, 'showExamResult'])->name('exams.show-result');
        Route::post('exams/update-result-marks', [ExamController::class, 'updateExamResultMarks'])->name('exams.update-result-marks');
        Route::get('exams/result/student/{student_id}/exam/{exam_id}', [ExamController::class, 'examResultPdf']);

        // Exams
        Route::get('exams/get-subjects/{exam_id}', [ExamController::class, 'getSubjectByExam'])->name('exams.subject');
        Route::post('exams/publish/{id}', [ExamController::class, 'publishExamResult'])->name('exams.publish');
        Route::put("exams/{id}/restore", [ExamController::class, 'restore'])->name('exams.restore');
        Route::delete("exams/{id}/deleted", [ExamController::class, 'trash'])->name('exams.trash');

        Route::get('exams/result-report/{session_year_id}/{exam_name}', [ExamController::class, 'resultReport']);
        Route::get('exams/timetable', [ExamController::class, 'examTimetableIndex'])->name('exams.timetable');
        Route::get('exams/timetable/{id?}', [ExamController::class, 'examTimetableShow'])->name('exams.timetable.show');
        Route::get('exams/bulk-upload-marks', [ExamController::class, 'bulkUploadIndex'])->name('exam.bulk-upload-marks');
        Route::get('exams/download-sample-file', [ExamController::class, 'downloadSampleFile'])->name('exam.download-sample-file');
        Route::post('exams/store-bulk-data', [ExamController::class, 'storeBulkData'])->name('exam.store-bulk-data');
        Route::get('exams/view-marks', [ExamController::class, 'viewMarksindex'])->name('exam.view-marks');
        Route::get('exams/view-marks-list', [ExamController::class, 'viewMarksShow'])->name('exam.view-marks-list');
        Route::get('exams/get-exams/{class_section_id}', [ExamController::class, 'getExamByClassId'])->name('exams.classes');
    
        Route::resource('exams', ExamController::class);

        // TODO make two groups promote student and transfer student and classify the routes related to their group
        Route::resource('promote-student', PromoteStudentController::class);
        Route::get('getPromoteData', [PromoteStudentController::class, 'getPromoteData']);
        Route::post('transfer-student-store', [PromoteStudentController::class, 'storeTransferStudent'])->name('transfer-student.store');
        Route::get('transfer-student-list', [PromoteStudentController::class, 'showTransferStudent'])->name('transfer-student.show');

        // TODO : Improve this
        /*** Language ***/
        Route::get('language-sample', [LanguageController::class, 'language_sample']);
        Route::get('language-json-file/{code?}', [LanguageController::class, 'language_file'])->name('language.json.file');

        
        Route::get('language-list', [LanguageController::class, 'show']);
        Route::resource('language', LanguageController::class);

        Route::group(['prefix' => 'fees-type'], static function () {
            Route::put("/{id}/restore", [FeesTypeController::class, 'restore'])->name('fees-type.restore');
            Route::delete("/{id}/deleted", [FeesTypeController::class, 'trash'])->name('fees-type.trash');
        });
        Route::resource('fees-type', FeesTypeController::class);

        Route::group(['prefix' => 'fees'], static function () {
            // Fees
            Route::put("/{id}/restore", [FeesController::class, 'restore'])->name('fees.restore');
            Route::delete("/{id}/delete", [FeesController::class, 'trash'])->name('fees.trash');
            Route::delete("/installment/{id}", [FeesController::class, 'deleteInstallment'])->name('fees.installment.delete');
            Route::delete("/class-type/{id}", [FeesController::class, 'deleteClassType'])->name('fees.class-type.delete');
            Route::get("/search", [FeesController::class, 'search'])->name('fees.search');


            // Fees Paid
            Route::get('/paid', [FeesController::class, 'feesPaidListIndex'])->name('fees.paid.index');
            Route::get('/paid/list', [FeesController::class, 'feesPaidList'])->name('fees.paid.list');

            Route::get('/pay/compulsory/{feesID}/{studentID}', [FeesController::class, 'payCompulsoryFeesIndex'])->name('fees.compulsory.index');
            Route::post('pay/compulsory', [FeesController::class, 'payCompulsoryFeesStore'])->name('fees.compulsory.store');

            // Optional Fees Payment Offline
            Route::get('/pay/optional/{feesID}/{studentID}', [FeesController::class, 'payOptionalFeesIndex'])->name('fees.optional.index');
            Route::post('pay/optional', [FeesController::class, 'payOptionalFeesStore'])->name('fees.optional.store');

            Route::post('/paid/store', [FeesController::class, 'feesPaidStore'])->name('fees.paid.store');
            Route::put('/paid/update/{id}', [FeesController::class, 'feesPaidUpdate'])->name('fees.paid.update');
            Route::delete('/paid/remove-optional-fee/{id}', [FeesController::class, 'removeOptionalFees'])->name('fees.paid.remove.optional.fees');
            Route::delete('/paid/remove-installment-fees/{id}', [FeesController::class, 'removeInstallmentFees'])->name('fees.paid.remove.installment.fees');
            // Fees Config
            Route::get('/config', [FeesController::class, 'feesConfigIndex'])->name('fees.config.index');
            Route::post('/config/update', [FeesController::class, 'feesConfigUpdate'])->name('fees.config.update');

            Route::post('/optional-paid/store', [FeesController::class, 'optionalFeesPaidStore'])->name('fees.optional-paid.store');


            // Transaction list
            Route::get('/transaction-logs', [FeesController::class, 'feesTransactionsLogsIndex'])->name('fees.transactions.log.index');
            Route::get('/transaction-logs/list', [FeesController::class, 'feesTransactionsLogsList'])->name('fees.transactions.log.list');

            // Receipt
            Route::get('/paid/receipt-pdf/{id}', [FeesController::class, 'feesPaidReceiptPDF'])->name('fees.paid.receipt.pdf');

            // Fees Over Due Dashboard
            Route::get('/fees-over-due/{class_section_id}', [FeesController::class, 'feesOverDue']);
            Route::post('/student-account-deactivate', [FeesController::class, 'studentAccountDeactivate'])->name('deactivate-student-account');

           
        });
        Route::resource('fees', FeesController::class);


        // Online Exam
        Route::group(['prefix' => 'online-exam'], static function () {
            Route::put("/{id}/restore", [OnlineExamController::class, 'restore'])->name('online-exam.restore');
            Route::delete("/{id}/deleted", [OnlineExamController::class, 'trash'])->name('online-exam.trash');
            Route::get('/add-questions-index/{id}', [OnlineExamController::class, 'addQuestionIndex'])->name('online-exam.add.questions.index');
            Route::post('/add-new-question', [OnlineExamController::class, 'storeExamQuestionChoices'])->name('online-exam.add-new-question');
            Route::get('/get-class-questions/{id}', [OnlineExamController::class, 'getClassQuestions'])->name('online-exam-question.get-class-questions');
            Route::post('/store-questions-choices', [OnlineExamController::class, 'storeQuestionsChoices'])->name('online-exam.store-choice-question');
            Route::delete('/remove-choiced-question/{id}', [OnlineExamController::class, 'removeQuestionsChoices'])->name('online-exam.remove-choice-question');
            Route::get('/result/{id}', [OnlineExamController::class, 'onlineExamResultIndex'])->name('online-exam.result.index');
            Route::get('/result-show/{id}', [OnlineExamController::class, 'showOnlineExamResult'])->name('online-exam.result.show');
        });
        Route::resource('online-exam', OnlineExamController::class);

        Route::group(['prefix' => 'online-exam-question'], static function () {
            Route::delete('/remove-option/{id}', [OnlineExamQuestionController::class, 'removeOptions']);
        });
        Route::resource('online-exam-question', OnlineExamQuestionController::class);
        // End Online Exam Routes

        

        /*** School Settings ***/
        Route::group(['prefix' => 'school-settings'], static function () {
            Route::get('online-exam', [SchoolSettingsController::class, 'onlineExamIndex'])->name('school-settings.online-exam.index');
            Route::post('online-exam', [SchoolSettingsController::class, 'onlineExamStore'])->name('school-settings.online-exam.store');
            Route::get('id-card/remove/{type}', [SchoolSettingsController::class, 'remove_image_from_id_card']);
            Route::get('terms-condition', [SchoolSettingsController::class, 'terms_condition'])->name('school-settings.terms-condition');
            Route::get('privacy-policy', [SchoolSettingsController::class, 'privacy_policy'])->name('school-settings.privacy-policy');

            Route::get('email-template', [SchoolSettingsController::class, 'emailTemplate'])->name('school-settings.email.template');
            Route::put('email-template', [SchoolSettingsController::class, 'emailTemplateUpdate'])->name('school-settings.email-template.update');
            

            Route::get('refund-cancellation', [SchoolSettingsController::class, 'refund_cancellation'])->name('school-settings.refund-cancellation');

            Route::get('third-party-apis', [SchoolSettingsController::class, 'thirdPartyApiIndex'])->name('school-settings.third-party');
            Route::post('third-party-apis', [SchoolSettingsController::class, 'thirdPartyApiUpdate'])->name('school-settings.third-party.update');
        });
        Route::resource('school-settings', SchoolSettingsController::class);

        // Database backup
        Route::group(['prefix' => 'database-backup'], static function () {
            Route::get('/',[DatabaseBackupController::class, 'index'])->name('database-backup.index');
            Route::get('show',[DatabaseBackupController::class, 'show']);
            Route::get('store',[DatabaseBackupController::class, 'store']);
            Route::delete('/{id}',[DatabaseBackupController::class, 'destroy']);
            Route::post('restore/{id}',[DatabaseBackupController::class, 'restore'])->name('database-backup.restore');
            Route::get('download/{filename}', [DatabaseBackupController::class, 'download'])->name('database-backup.download');
        });


        

        /*** Form Fields ***/
        Route::group(['prefix' => 'form-fields'], static function () {
            Route::post('/update-rank', [FormFieldsController::class, 'updateRankOfFields']);
            Route::put("/{id}/restore", [FormFieldsController::class, 'restore'])->name('form-fields.restore');
            Route::delete("/{id}/deleted", [FormFieldsController::class, 'trash'])->name('form-fields.trash');
        });
        Route::resource('form-fields', FormFieldsController::class);

        // Expense Category
        Route::group(['prefix' => 'expense-category'], static function () {
            Route::put('restore/{id}', [ExpenseCategoryController::class, 'restore'])->name('expense-category.restore');
            Route::delete('trash/{id}', [ExpenseCategoryController::class, 'trash'])->name('expense-category.trash');

        });
        Route::resource('expense-category', ExpenseCategoryController::class);

        // Expense
        Route::get('expense/filter/{session_year_id?}',[ExpenseController::class,'filter_graph']);
        Route::resource('expense', ExpenseController::class);
        // Payroll
        Route::get('payroll/slip/{id?}',[PayrollController::class,'slip'])->name('payroll.slip');
        Route::get('payroll/slips',[PayrollController::class,'slip_index'])->name('payroll.slip.index');
        Route::get('payroll/slips/list',[PayrollController::class,'slip_list'])->name('payroll.slip.list');
        
        Route::resource('payroll', PayrollController::class)->only(['index', 'store', 'show']);

        // Leave
        Route::group(['prefix' => 'leave'], static function () {
            Route::get('request', [LeaveController::class, 'leave_request'])->name('leave.request');
            Route::get('request/show', [LeaveController::class, 'leave_request_show'])->name('leave.request.show');
            Route::put('status/update', [LeaveController::class, 'leave_status_update'])->name('leave.status.update');
            Route::get('filter', [LeaveController::class, 'filter_leave']);
            Route::get('report', [LeaveController::class, 'report'])->name('leave.report');
            Route::get('detail', [LeaveController::class, 'detail'])->name('leave.detail');

        });

        Route::resource('leave', LeaveController::class);
        Route::resource('leave-master', LeaveMasterController::class);

        // Semester
        Route::group(['prefix' => 'semester'], static function () {
            Route::put('restore/{id}', [SemesterController::class, 'restore'])->name('semester.restore');
            Route::delete('trash/{id}', [SemesterController::class, 'trash'])->name('semester.trash');
        });
        Route::resource('semester', SemesterController::class);

        Route::group(['prefix' => 'stream'], static function () {
            Route::put('restore/{id}', [StreamController::class, 'restore'])->name('stream.restore');
            Route::delete('trash/{id}', [StreamController::class, 'trash'])->name('stream.trash');
        });
        Route::resource('stream', StreamController::class);


        Route::group(['prefix' => 'shift'], static function () {
            Route::put('restore/{id}', [ShiftController::class, 'restore'])->name('shift.restore');
            Route::delete('trash/{id}', [ShiftController::class, 'trash'])->name('shift.trash');
        });
        Route::resource('shift', ShiftController::class);

        Route::resource('faqs', FaqController::class);

        Route::get('users/status', [UserController::class, 'status']);
        Route::get('users/show', [UserController::class, 'show'])->name('users.show');
        Route::post('users/status', [UserController::class, 'status_change']);
        Route::get('users/birthday/{type?}', [UserController::class, 'birthday']);

        Route::group(['prefix' => 'related-data'], static function () {
            Route::get('/{table}/{id}', [Controller::class, 'relatedDataIndex'])->name('related-data.index');
            Route::delete('delete/{table}/{id}', [Controller::class, 'relatedDataDestroy'])->name('related-data.trash');
        });

        
        Route::group(['prefix' => 'gallery'], static function () {
            Route::delete('file/delete/{id}', [GalleryController::class, 'deleteFile'])->name('gallery.delete');
        });
        Route::resource('gallery', GalleryController::class);


        Route::group(['prefix' => 'notifications'], static function () {
            Route::get('user/show', [NotificationController::class, 'userShow'])->name('notifications.user.show');
        });
        Route::resource('notifications', NotificationController::class);

        
        

        Route::group(['prefix' => 'school'], static function () {
            Route::group(['prefix' => 'web-settings'], static function () {
                Route::get('/', [WebSettingsController::class, 'school_index'])->name('school.web-settings.index');
                Route::post('/', [WebSettingsController::class, 'school_store'])->name('school.web-settings.store');

            });
            
        });
        Route::resource('web-settings', WebSettingsController::class);

        // Certificates
        Route::group(['prefix' => 'certificate-template'], static function () {
            Route::get('design/{id}', [CertificateTemplateController::class, 'design'])->name('certificate-template.design');
            Route::put('design/{id}', [CertificateTemplateController::class, 'design_store'])->name('certificate-template.design.store');
        });

        Route::group(['prefix' => 'certificate'], static function () {
            Route::get('/', [CertificateTemplateController::class, 'certificate']);
            Route::post('/', [CertificateTemplateController::class, 'certificate_generate']);
            Route::get('staff-certificate', [CertificateTemplateController::class, 'staff_certificate']);
            Route::post('staff-certificate', [CertificateTemplateController::class, 'staff_generate_certificate']);
        });
        Route::resource('certificate-template', CertificateTemplateController::class);
        Route::resource('class-group', ClassGroupController::class);

        Route::group(['prefix' => 'payroll-setting'], static function () {
            Route::put('restore/{id}', [PayrollSettingController::class, 'restore'])->name('payroll-setting.restore');
            Route::delete('trash/{id}', [PayrollSettingController::class, 'trash'])->name('payroll-setting.trash');
        });

        Route::resource('payroll-setting', PayrollSettingController::class);
    });
});

// webhooks
Route::post('webhook/razorpay', [WebhookController::class, 'razorpay']);
Route::post('webhook/stripe', [WebhookController::class, 'stripe']);
Route::post('webhook/paystack',[WebhookController::class,'paystack']);
Route::post('webhook/flutterwave',[WebhookController::class, 'flutterwave']);
// Route::get('response/paystack/success', [WebhookController::class,'paystackSuccessCallback'])->name('paystack.success');
// Route::get('response/flutterwave/success', [WebhookController::class,'flutterwaveSuccessCallback'])->name('flutterwave.success');

Route::post('subscription/webhook/stripe', [SubscriptionWebhookController::class, 'stripe']);
Route::post('subscription/webhook/razorpay', [SubscriptionWebhookController::class, 'razorpay']);
Route::post('subscription/webhook/paystack', [SubscriptionWebhookController::class, 'paystack']);
Route::post('subscription/webhook/flutterwave', [SubscriptionWebhookController::class, 'flutterwave']);

// Payment Routes for app
Route::prefix('payment')->group(function () {
    Route::get('/status', [PaymentController::class, 'status'])->name('payment.status');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});



// Super admin
Route::get('page/privacy-policy', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('privacy_policy'));
})->name('public.privacy-policy.privacy-policy');

Route::get('page/teacher-staff-privacy-policy', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('teacher_staff_privacy_policy'));
})->name('public.teacher-staff-privacy-policy');

Route::get('page/student-parent-privacy-policy', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('student_parent_privacy_policy'));
})->name('public.student-parent-privacy-policy');

Route::get('page/terms-conditions', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('terms_condition'));
})->name('public.terms-conditions');

Route::get('page/student-terms-conditions', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('student_terms_condition'));
})->name('public.student-terms-conditions');

Route::get('page/teacher-terms-conditions', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('teacher_terms_condition'));
})->name('public.teacher-terms-conditions');

Route::get('page/refund-cancellation', static function () {
    $cache = app(CachingService::class);
    echo htmlspecialchars_decode($cache->getSystemSettings('refund_cancellation'));
})->name('public.refund-cancellation');

// School terms & conditions
Route::get('school-settings/{id}/terms-condition', [SchoolSettingsController::class, 'public_terms_condition'])->name('school-settings.get-terms-condition');
Route::get('school-settings/{id}/privacy-policy', [SchoolSettingsController::class, 'public_privacy_policy'])->name('school-settings.get-privacy-policy');
Route::get('school-settings/{id}/refund-cancellation', [SchoolSettingsController::class, 'public_refund_cancellation'])->name('school-settings.get-refund-cancellation');
// End school terms & conditions

// Payment Gateway Apps Status 
Route::get('payment/status', [PaymentController::class, 'status'])->name('payment.status');



Route::get('clear', static function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    return redirect()->back();
});

Route::get('storage-link', static function () {
    try {
        Artisan::call('storage:link');
        echo "storage link created";
    } catch (Exception) {
        echo "Storage Link already exists";
    }
    return redirect()->back();
});


Route::get('migrate', static function () {
    Artisan::call('migrate');
//    return redirect()->back();
    echo "Done";
    return false;
});

Route::get('migrate-school', static function () {
    Artisan::call('migrate:school');
//    return redirect()->back();
    echo "Done";
    return false;
});

Route::get('seeder-school', static function () {
    Artisan::call('db:seed:school');
    echo "Done";
    return false;
});

Route::get('start-websocket', static function () {
    Artisan::call('websocket:init');
   return redirect()->back();
    return false;
});

Route::get('migrate-rollback', static function () {
    Artisan::call('migrate:rollback');
    echo "Done";
    return false;
});
Route::get('installation-seeder', static function () {
    Artisan::call('db:seed --class=InstallationSeeder');
    echo "Done";
    return false;
});

Route::get('dummy-seeder', static function () {
   Artisan::call('db:seed --class=DummyDataSeeder');
    // Artisan::call('db:seed');
    return redirect()->back();
    return false;
});

Route::get('AddSuperAdminSeeder-seeder', static function () {
    Artisan::call('db:seed --class=AddSuperAdminSeeder');
    echo "Done";
    return false;
});

Route::get('/js/lang', static function () {
    //    https://medium.com/@serhii.matrunchyk/using-laravel-localization-with-javascript-and-vuejs-23064d0c210e
    header('Content-Type: text/javascript');
    $labels = \Illuminate\Support\Facades\Cache::remember('lang.js', 3600, static function () {
        $lang = app()->getLocale();
        $files = resource_path('lang/' . $lang . '.json');
        return File::get($files);
    });
    echo('window.trans = ' . $labels);
    exit();
})->name('assets.lang');

Route::get('test-code', static function () {

});

// Route::get('cache-flush', [Controller::class, 'cacheFlush']);

Route::get('cache-flush', static function () {
    \Illuminate\Support\Facades\Cache::flush();
    Session::put('landing_locale', null);
    Session::save();
    return redirect()->back();
});


Route::get('demo-tokens', static function () {
    echo "<pre>";

    $guardian = User::where('email', 'guardian@gmail.com')->first();
    if (!empty($guardian)) {
        echo "Demo Guardian Token<br>";
        echo Cache::rememberForever('demoGuardianToken', static function () use ($guardian) {
            return $guardian->createToken($guardian->first_name)->plainTextToken;
        });
    }


    $student = User::where('email', 'student@gmail.com')->first();
    if (!empty($student)) {
        echo "<br><br>Demo Student Token<br>";
        echo Cache::rememberForever('demoStudentToken', static function () use ($student) {
            return $student->createToken($student->first_name)->plainTextToken;
        });
    }
});

