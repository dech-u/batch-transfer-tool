<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\StaffApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\TeacherApiController;
use App\Http\Controllers\SubscriptionWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Webhook Routes
 **/
Route::post('subscription/webhook/stripe', [SubscriptionWebhookController::class, 'stripe']);
Route::post('subscription/webhook/razorpay', [SubscriptionWebhookController::class, 'razorpay']);

// Route::group(['middleware' => 'auth:sanctum'], static function () {
//     Route::post('logout', [ApiController::class, 'logout']);
// });

Route::group(['middleware' => 'APISwitchDatabase'], static function () {
    Route::post('logout', [ApiController::class, 'logout']);
   
});
Route::get('fees-due-notification',[ApiController::class, 'sendFeeNotification']);
/**
 * STUDENT APIs
 **/
Route::group(['prefix' => 'student'], static function () {

    //Non Authenticated APIs
    Route::post('login', [StudentApiController::class, 'login']);
    Route::post('forgot-password', [StudentApiController::class, 'forgotPassword']);

    //Authenticated APIs
    Route::group(['middleware' => ['APISwitchDatabase', 'checkSchoolStatus']], static function () {
        Route::get('class-subjects', [StudentApiController::class, 'classSubjects']);
        Route::get('subjects', [StudentApiController::class, 'subjects']);
        Route::post('select-subjects', [StudentApiController::class, 'selectSubjects']);
        Route::get('guradian-details', [StudentApiController::class, 'getGuardianDetails']);
        Route::get('timetable', [StudentApiController::class, 'getTimetable']);
        Route::get('lessons', [StudentApiController::class, 'getLessons']);
        Route::get('lesson-topics', [StudentApiController::class, 'getLessonTopics']);
        Route::get('assignments', [StudentApiController::class, 'getAssignments']);
        Route::post('submit-assignment', [StudentApiController::class, 'submitAssignment']);
        Route::post('delete-assignment-submission', [StudentApiController::class, 'deleteAssignmentSubmission']);
        Route::get('attendance', [StudentApiController::class, 'getAttendance']);
        Route::get('announcements', [StudentApiController::class, 'getAnnouncements']);
        Route::get('get-exam-list', [StudentApiController::class, 'getExamList']); // Exam list Route
        Route::get('get-exam-details', [StudentApiController::class, 'getExamDetails']); // Exam Details Route
        Route::get('exam-marks', [StudentApiController::class, 'getExamMarks']); // Exam Details Route
        Route::get('sliders', [StudentApiController::class, 'getSliders']); // Sliders

        // online exam routes
        Route::get('get-online-exam-list', [StudentApiController::class, 'getOnlineExamList']); // Get Online Exam List Route
        Route::get('get-online-exam-questions', [StudentApiController::class, 'getOnlineExamQuestions']); // Get Online Exam Questions Route
        Route::post('submit-online-exam-answers', [StudentApiController::class, 'submitOnlineExamAnswers']); // Submit Online Exam Answers Details Route
        Route::get('get-online-exam-result-list', [StudentApiController::class, 'getOnlineExamResultList']); // Online exam result list Route
        Route::get('get-online-exam-result', [StudentApiController::class, 'getOnlineExamResult']); // Online exam result  Route

        //reports
        Route::get('get-online-exam-report', [StudentApiController::class, 'getOnlineExamReport']); // Online Exam Report Route
        Route::get('get-assignments-report', [StudentApiController::class, 'getAssignmentReport']); // Assignment Report Route

        // profile data
        Route::get('get-profile-data', [StudentApiController::class, 'getProfileDetails']); // Get Profile Data

        // Session Year
        Route::get('current-session-year', [StudentApiController::class, 'getSessionYear']);

        Route::get('school-settings', [StudentApiController::class, 'getSchoolSettings']);


    });
});

/**
 * PARENT APIs
 **/
Route::group(['prefix' => 'parent'], static function () {
    //Non Authenticated APIs
    // Route::group(['middleware' => ['APISwitchDatabase']], static function () {
        Route::post('login', [ParentApiController::class, 'login']);
        //Authenticated APIs
        // Route::group(['middleware' => ['']], static function () {
        //     Route::get('test', [ParentApiController::class, 'test']);
        // });
        Route::group(['middleware' => ['APISwitchDatabase']], static function () {
            Route::get('test', [ParentApiController::class, 'test']);

            Route::group(['middleware' => ['checkChild','APISwitchDatabase']], static function () {
                Route::get('subjects', [ParentApiController::class, 'subjects']);
                Route::get('class-subjects', [ParentApiController::class, 'classSubjects']);
                Route::get('timetable', [ParentApiController::class, 'getTimetable']);
                Route::get('lessons', [ParentApiController::class, 'getLessons']);
                Route::get('lesson-topics', [ParentApiController::class, 'getLessonTopics']);
                Route::get('assignments', [ParentApiController::class, 'getAssignments']);
                Route::get('attendance', [ParentApiController::class, 'getAttendance']);
                Route::get('teachers', [ParentApiController::class, 'getTeachers']);
                Route::get('sliders', [ParentApiController::class, 'getSliders']); // Sliders

                // Offline Exams
                Route::get('get-exam-list', [ParentApiController::class, 'getExamList']); // Exam list Route
                Route::get('get-exam-details', [ParentApiController::class, 'getExamDetails']); // Exam Details Route
                Route::get('exam-marks', [ParentApiController::class, 'getExamMarks']); //Exam Marks

                // Fees

                Route::group(['prefix' => 'fees'], static function () {
                    Route::get('/', [ParentApiController::class, 'getFees']);
                    Route::post('/compulsory/pay', [ParentApiController::class, 'payCompulsoryFees']);
                    Route::post('/optional/pay', [ParentApiController::class, 'payOptionalFees']);
                    Route::get('/receipt', [ParentApiController::class, 'feesPaidReceiptPDF']); //Fees Receipt
                });


                // Online Exam
                Route::get('get-online-exam-list', [ParentApiController::class, 'getOnlineExamList']); // Get Online Exam List Route
                Route::get('get-online-exam-result-list', [ParentApiController::class, 'getOnlineExamResultList']); // Online exam result list Route
                Route::get('get-online-exam-result', [ParentApiController::class, 'getOnlineExamResult']); // Online exam result  Route

                // Reports
                Route::get('get-online-exam-report', [ParentApiController::class, 'getOnlineExamReport']); // Online Exam Report Route
                Route::get('get-assignments-report', [ParentApiController::class, 'getAssignmentReport']); // Assignment Report Route

                // Session Year
                Route::get('current-session-year', [ParentApiController::class, 'getSessionYear']);
                Route::get('school-settings', [ParentApiController::class, 'getSchoolSettings']);

                // profile data
                Route::get('get-child-profile-data', [ParentApiController::class, 'getChildProfileDetails']); // Get Profile Data

                // Announcements
                Route::get('announcements', [ParentApiController::class, 'getAnnouncements']);
            });
        });
    // });
});

/**
 * TEACHER APIs
 **/
Route::group(['prefix' => 'teacher'], static function () {
    //Non Authenticated APIs
    Route::post('login', [TeacherApiController::class, 'login']);
    //Authenticated APIs
    Route::group(['middleware' => ['APISwitchDatabase', 'checkSchoolStatus']], static function () {

        Route::get('subjects', [TeacherApiController::class, 'subjects']);

        //Assignment
        Route::get('get-assignment', [TeacherApiController::class, 'getAssignment']);
        Route::post('create-assignment', [TeacherApiController::class, 'createAssignment']);
        Route::post('update-assignment', [TeacherApiController::class, 'updateAssignment']);
        Route::post('delete-assignment', [TeacherApiController::class, 'deleteAssignment']);

        //Assignment Submission
        Route::get('get-assignment-submission', [TeacherApiController::class, 'getAssignmentSubmission']);
        Route::post('update-assignment-submission', [TeacherApiController::class, 'updateAssignmentSubmission']);

        //File
        Route::post('delete-file', [TeacherApiController::class, 'deleteFile']);
        Route::post('update-file', [TeacherApiController::class, 'updateFile']);

        //Lesson
        Route::get('get-lesson', [TeacherApiController::class, 'getLesson']);
        Route::post('create-lesson', [TeacherApiController::class, 'createLesson']);
        Route::post('update-lesson', [TeacherApiController::class, 'updateLesson']);
        Route::post('delete-lesson', [TeacherApiController::class, 'deleteLesson']);

        //Topic
        Route::get('get-topic', [TeacherApiController::class, 'getTopic']);
        Route::post('create-topic', [TeacherApiController::class, 'createTopic']);
        Route::post('update-topic', [TeacherApiController::class, 'updateTopic']);
        Route::post('delete-topic', [TeacherApiController::class, 'deleteTopic']);

        //Announcement
        Route::get('get-announcement', [TeacherApiController::class, 'getAnnouncement']);
        Route::post('send-announcement', [TeacherApiController::class, 'sendAnnouncement']);
        Route::post('update-announcement', [TeacherApiController::class, 'updateAnnouncement']);
        Route::post('delete-announcement', [TeacherApiController::class, 'deleteAnnouncement']);

        Route::get('get-attendance', [TeacherApiController::class, 'getAttendance']);
        Route::post('submit-attendance', [TeacherApiController::class, 'submitAttendance']);


        //Exam
        Route::get('get-exam-list', [TeacherApiController::class, 'getExamList']); // Exam list Route
        Route::get('get-exam-details', [TeacherApiController::class, 'getExamDetails']); // Exam Details Route
        Route::post('submit-exam-marks/subject', [TeacherApiController::class, 'submitExamMarksBySubjects']); // Submit Exam Marks By Subjects Route
        Route::post('submit-exam-marks/student', [TeacherApiController::class, 'submitExamMarksByStudent']); // Submit Exam Marks By Students Route

        Route::group(['middleware' => ['auth:sanctum', 'checkStudent']], static function () {
            Route::get('get-student-result', [TeacherApiController::class, 'GetStudentExamResult']); // Student Exam Result
            Route::get('get-student-marks', [TeacherApiController::class, 'GetStudentExamMarks']); // Student Exam Marks
        });

        //Student List
        Route::get('student-list', [TeacherApiController::class, 'getStudentList']);
        Route::get('student-details', [TeacherApiController::class, 'getStudentDetails']);

        //Schedule List
        Route::get('teacher_timetable', [TeacherApiController::class, 'getTeacherTimetable']);

        Route::post('class-detail', [TeacherApiController::class, 'getClassDetail']);
    });
});


// Staff & Teacher APIs
Route::group(['prefix' => 'staff'], static function () {
    Route::post('login', [TeacherApiController::class, 'login']);

    Route::group(['middleware' => ['APISwitchDatabase', 'checkSchoolStatus']], static function () {
        // Payroll
        Route::get('my-payroll', [StaffApiController::class, 'myPayroll']);
        Route::get('payroll-slip', [StaffApiController::class, 'myPayrollSlip']);
        Route::post('payroll-create', [StaffApiController::class, 'storePayroll']);
        Route::get('payroll-staff-list', [StaffApiController::class, 'staffPayrollList']);

        Route::get('payroll-year', [StaffApiController::class, 'payrollYear']);
        

        Route::get('profile', [StaffApiController::class, 'profile']);
        Route::get('counter', [StaffApiController::class, 'counter']);
        Route::get('teachers', [StaffApiController::class, 'teacher']);
        Route::get('teacher-timetable', [StaffApiController::class, 'teacherTimetable']);
        Route::get('staffs', [StaffApiController::class, 'staff']);

        Route::get('leave-request', [StaffApiController::class, 'leaveRequest']);
        Route::post('leave-approve', [StaffApiController::class, 'leaveApprove']);
        Route::post('leave-delete', [StaffApiController::class, 'leaveDelete']);
        
        // Announcement
        Route::get('get-announcement', [StaffApiController::class, 'getAnnouncement']);
        Route::post('send-announcement', [StaffApiController::class, 'sendAnnouncement']);
        Route::post('update-announcement', [StaffApiController::class, 'updateAnnouncement']);
        Route::post('delete-announcement', [StaffApiController::class, 'deleteAnnouncement']);
        
        Route::get('student/attendance', [StaffApiController::class, 'studentAttendance']);

        Route::get('roles', [StaffApiController::class, 'getRoles']);
        Route::get('users', [StaffApiController::class, 'getUsers']);
        Route::post('notification', [StaffApiController::class, 'storeNotification']);
        Route::get('notification', [StaffApiController::class, 'getNotification']);
        Route::post('notification-delete', [StaffApiController::class, 'deleteNotification']);

        Route::get('get-fees', [StaffApiController::class, 'getFees']);
        Route::get('fees-paid-list', [StaffApiController::class, 'getFeesPaidList']);

        Route::get('student-offline-exam-result', [StaffApiController::class, 'getOfflineExamResult']);
        Route::get('features-permission', [StaffApiController::class, 'getFeaturesPermissions']);
        
        Route::get('class-timetable', [StaffApiController::class, 'getClassTimetable']);

        Route::get('student-fees-receipt', [StaffApiController::class, 'feesReceipt']);
        Route::get('allowances-deductions', [StaffApiController::class, 'allowancesDeductions']);

        
        
    });
});

/**
 * GENERAL APIs
 **/
Route::get('settings', [ApiController::class, 'getSettings']);
Route::post('forgot-password', [ApiController::class, 'forgotPassword']);

Route::get('school-details',[ApiController::class, 'schoolDetails']);

// Route::group(['middleware' => ['auth:sanctum',]], static function () {
Route::group(['middleware' => ['APISwitchDatabase',]], static function () {
    Route::get('school-settings', [ApiController::class, 'getSchoolSettings']);
    Route::get('holidays', [ApiController::class, 'getHolidays']);
    Route::post('change-password', [ApiController::class, 'changePassword']);
//    Route::get('test', [ApiController::class, 'getPaymentMethod']);
    Route::get('payment-confirmation', [ApiController::class, 'getPaymentConfirmation'])->name('payment-confirmation');
    Route::get('payment-transactions', [ApiController::class, 'getPaymentTransactions'])->name('payment-transactions');
    Route::get('gallery', [ApiController::class, 'getGallery']);
    Route::get('session-years', [ApiController::class, 'getSessionYear']);
//    Route::get('features', [ApiController::class, 'getFeatures']);

    // Leaves
    Route::get('leaves', [ApiController::class, 'getLeaves']);
    Route::post('leaves', [ApiController::class, 'applyLeaves']);
    Route::get('my-leaves', [ApiController::class, 'getMyLeaves']);
    Route::post('delete-my-leaves', [ApiController::class, 'deleteLeaves']);
    Route::get('staff-leaves-details', [ApiController::class, 'getStaffLeaveDetail']);
    Route::get('leave-settings', [ApiController::class, 'leaveSettings']);

    Route::get('medium', [ApiController::class, 'getMedium']);
    Route::get('classes', [ApiController::class, 'getClass']);

    Route::post('update-profile', [ApiController::class, 'updateProfile']);
    Route::get('student-exan-result-pdf', [ApiController::class, 'getExamResultPdf']);

    Route::post('message', [ApiController::class, 'sendMessage']);
    Route::get('message', [ApiController::class, 'getMessage']);
    Route::post('delete/message', [ApiController::class, 'deleteMessage']);
    Route::post('message/read', [ApiController::class, 'readMessage']);

    // Get users from role
    // Student, Teacher, Guardian, Other Staff [Teachers / School Staff]
    // Get all users
    Route::get('users', [ApiController::class, 'getUsers']);

    // Get history
    Route::get('users/chat/history', [ApiController::class, 'usersChatHistory']);

    Route::post('class-section/teachers', [ApiController::class, 'classSectionTeachers']);
    
});
