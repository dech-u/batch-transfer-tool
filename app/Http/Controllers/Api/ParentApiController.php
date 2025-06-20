<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimetableCollection;
use App\Http\Resources\UserDataResource;
use App\Models\School;
use App\Repositories\Announcement\AnnouncementInterface;
use App\Repositories\Assignment\AssignmentInterface;
use App\Repositories\AssignmentSubmission\AssignmentSubmissionInterface;
use App\Repositories\Attendance\AttendanceInterface;
use App\Repositories\Exam\ExamInterface;
use App\Repositories\ExamResult\ExamResultInterface;
use App\Repositories\Fees\FeesInterface;
use App\Repositories\FeesPaid\FeesPaidInterface;
use App\Repositories\Holiday\HolidayInterface;
use App\Repositories\Lessons\LessonsInterface;
use App\Repositories\OnlineExam\OnlineExamInterface;
use App\Repositories\PaymentConfiguration\PaymentConfigurationInterface;
use App\Repositories\PaymentTransaction\PaymentTransactionInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\Sliders\SlidersInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Repositories\Timetable\TimetableInterface;
use App\Repositories\Topics\TopicsInterface;
use App\Repositories\User\UserInterface;
use App\Services\CachingService;
use App\Services\FeaturesService;
use App\Services\GeneralFunctionService;
use App\Services\Payment\PaymentService;
use App\Services\ResponseService;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

class ParentApiController extends Controller {

    private StudentInterface $student;
    private UserInterface $user;
    private AssignmentInterface $assignment;
    private AssignmentSubmissionInterface $assignmentSubmission;
    private CachingService $cache;
    private TimetableInterface $timetable;
    private ExamInterface $exam;
    private ExamResultInterface $examResult;
    private LessonsInterface $lesson;
    private TopicsInterface $lessonTopic;
    private AttendanceInterface $attendance;
    private HolidayInterface $holiday;
    private SubjectTeacherInterface $subjectTeacher;
    private AnnouncementInterface $announcement;
    private OnlineExamInterface $onlineExam;
    private FeesInterface $fees;
    private PaymentTransactionInterface $paymentTransaction;
    private SlidersInterface $sliders;
    private FeesPaidInterface $feesPaid;
    private SubjectTeacherInterface $subjectTeachers;
    private PaymentConfigurationInterface $paymentConfigurations;
    private SystemSettingInterface $systemSetting;
    private SchoolSettingInterface $schoolSetting;
    private GeneralFunctionService $generalFunction;

    public function __construct(StudentInterface $student, UserInterface $user, AssignmentInterface $assignment, AssignmentSubmissionInterface $assignmentSubmission, CachingService $cache, TimetableInterface $timetable, ExamInterface $exam, ExamResultInterface $examResult, LessonsInterface $lesson, TopicsInterface $lessonTopic, AttendanceInterface $attendance, HolidayInterface $holiday, SubjectTeacherInterface $subjectTeacher, AnnouncementInterface $announcement, OnlineExamInterface $onlineExam, FeesInterface $fees, PaymentTransactionInterface $paymentTransaction, SlidersInterface $sliders, PaymentConfigurationInterface $paymentConfigurations, FeesPaidInterface $feesPaid, SubjectTeacherInterface $subjectTeachers, SystemSettingInterface $systemSetting, SchoolSettingInterface $schoolSetting, GeneralFunctionService $generalFunction) {
        $this->student = $student;
        $this->user = $user;
        $this->assignment = $assignment;
        $this->assignmentSubmission = $assignmentSubmission;
        $this->cache = $cache;
        $this->timetable = $timetable;
        $this->exam = $exam;
        $this->examResult = $examResult;
        $this->lesson = $lesson;
        $this->lessonTopic = $lessonTopic;
        $this->attendance = $attendance;
        $this->holiday = $holiday;
        $this->subjectTeacher = $subjectTeacher;
        $this->announcement = $announcement;
        $this->onlineExam = $onlineExam;
        $this->fees = $fees;
        $this->paymentTransaction = $paymentTransaction;
        $this->sliders = $sliders;
        $this->paymentConfigurations = $paymentConfigurations;
        $this->feesPaid = $feesPaid;
        $this->subjectTeachers = $subjectTeachers;
        $this->systemSetting = $systemSetting;
        $this->schoolSetting = $schoolSetting;
        $this->generalFunction = $generalFunction;
    }

    #[NoReturn] public function login(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'The email field cannot be empty.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'The password field cannot be empty.',
        ]);
        
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        $school = School::on('mysql')->where('code',$request->school_code)->first();

        if ($school) {
            DB::setDefaultConnection('school');
            Config::set('database.connections.school.database', $school->database_name);
            DB::purge('school');
            DB::connection('school')->reconnect();
            DB::setDefaultConnection('school');
        } else {
            ResponseService::errorResponse('Invalid Login Credentials', null, config('constants.RESPONSE_CODE.INVALID_LOGIN'));
        }

        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password
        ])) {
            // $auth = Auth::user()->load('child.user', 'child.class_section.class', 'child.class_section.section', 'child.class_section.medium', 'child.user.school');
            
            // Only active child
            $auth = Auth::user()->load(['child' => function($q) {
                $q->whereHas('user.student', function($q) {
                    $q->where('application_status',1);
                })->with('class_section.class', 'class_section.section', 'class_section.medium', 'user.school');
            }]);
            // ==============================

            // $auth->assignRole('Guardian');
            if (!$auth->hasRole('Guardian')) {
                ResponseService::errorResponse('Invalid Login Credentials', null, config('constants.RESPONSE_CODE.INVALID_LOGIN'));
            }

            if ($request->fcm_id) {
                $auth->fcm_id = $request->fcm_id;
                $auth->save();
            }

            // session(['database_name' => $school->database_name]);
            $token = $auth->createToken($auth->first_name)->plainTextToken;
            // $token = $auth->createToken('API Token', ['school_code' => $request->school_code])->plainTextToken;
            $user = $auth;
            $request->headers->set('school_code',$request->school_code);
            ResponseService::successResponse('User logged-in!', new UserDataResource($user), ['token' => $token], config('constants.RESPONSE_CODE.LOGIN_SUCCESS'));
        } else {
            ResponseService::errorResponse('Invalid Login Credentials', null, config('constants.RESPONSE_CODE.INVALID_LOGIN'));
        }
    }

    public function subjects(Request $request) {
        $validator = Validator::make($request->all(), ['child_id' => 'required|numeric',]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $subjects = $children->currentSemesterSubjects();
            ResponseService::successResponse('Student Subject Fetched Successfully.', $subjects);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function classSubjects(Request $request) {
        $validator = Validator::make($request->all(), ['child_id' => 'required|numeric',]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $subjects = $children->currentSemesterClassSubjects();
            ResponseService::successResponse('Class Subject Fetched Successfully.', $subjects);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getTimetable(Request $request) {
        $validator = Validator::make($request->all(), ['child_id' => 'required|numeric',]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $timetable = $this->timetable->builder()->where('class_section_id', $children->class_section_id)->with('subject_teacher')->orderBy('day')->orderBy('start_time')->get();
            ResponseService::successResponse("Timetable Fetched Successfully", new TimetableCollection($timetable));
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function getLessons(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'         => 'required|numeric',
            'lesson_id'        => 'nullable|numeric',
            'class_subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $data = $this->lesson->builder()->where(['class_section_id' => $children->class_section_id, 'class_subject_id' => $request->class_subject_id])->with('topic', 'file');
            if ($request->lesson_id) {
                $data->where('id', $request->lesson_id);
            }
            $data = $data->get();
            ResponseService::successResponse("Lessons Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function getLessonTopics(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'  => 'required|numeric',
            'lesson_id' => 'required|numeric',
            'topic_id'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $data = $this->lessonTopic->builder()->where('lesson_id', $request->lesson_id)->with('file');
            if ($request->topic_id) {
                $data->where('id', $request->topic_id);
            }
            $data = $data->get();
            ResponseService::successResponse("Topics Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getAssignments(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'         => 'required|numeric',
            'assignment_id'    => 'nullable|numeric',
            'class_subject_id' => 'nullable|numeric',
            'is_submitted'     => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $data = $this->assignment->builder()->where('class_section_id', $children->class_section_id)->with(['file', 'class_subject.subject', 'submission' => function ($query) use ($children) {
                $query->where('student_id', $children->user_id)->with('file');
            }]);
            if ($request->assignment_id) {
                $data->where('id', $request->assignment_id);
            }
            if ($request->class_subject_id) {
                $data->where('class_subject_id', $request->class_subject_id);
            }
            if (isset($request->is_submitted)) {
                if ($request->is_submitted) {
                    $data->whereHas('submission', function ($q) use ($children) {
                        $q->where('student_id', $children->user_id);
                    });
                } else {
                    $data->whereDoesntHave('submission', function ($q) use ($children) {
                        $q->where('student_id', $children->user_id);
                    });
                }
            }
            $data = $data->orderBy('id', 'desc')->paginate(15);
            ResponseService::successResponse("Assignments Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getAttendance(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
            'month'    => 'nullable|numeric',
            'year'     => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $sessionYear = $this->cache->getDefaultSessionYear($children->school_id);

            $attendance = $this->attendance->builder()->where(['student_id' => $children->user_id, 'session_year_id' => $sessionYear->id]);
            $holidays = $this->holiday->builder();
            if (isset($request->month)) {
                $attendance = $attendance->whereMonth('date', $request->month);
                $holidays = $holidays->whereMonth('date', $request->month);
            }

            if (isset($request->year)) {
                $attendance = $attendance->whereYear('date', $request->year);
                $holidays = $holidays->whereYear('date', $request->year);
            }
            $attendance = $attendance->get();
            $holidays = $holidays->get();

            $data = ['attendance' => $attendance, 'holidays' => $holidays, 'session_year' => $sessionYear];

            ResponseService::successResponse("Attendance Details Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getAnnouncements(Request $request) {
        $validator = Validator::make($request->all(), [
            'type'             => 'required|in:subject,class',
            'child_id'         => 'required_if:type,subject,class|numeric',
            'class_subject_id' => 'required_if:type,subject|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classSectionId = $children->class_section_id;

            $sessionYear = $this->cache->getDefaultSessionYear($children->school_id);
            if (isset($request->type) && $request->type == "subject") {
                $table = $this->subjectTeacher->builder()->where(['class_section_id' => $children->class_section_id, 'class_subject_id' => $request->class_subject_id])->get()->pluck('id');
                if ($table === null) {
                    ResponseService::errorResponse("Invalid Subject ID", null, config('constants.RESPONSE_CODE.INVALID_SUBJECT_ID'));
                }
            }
            $data = $this->announcement->builder()->with('file')->where('session_year_id', $sessionYear->id);

            if (isset($request->type) && $request->type == "class") {
                $data = $data->whereHas('announcement_class', function ($query) use ($classSectionId) {
                    $query->where(['class_section_id' => $classSectionId, 'class_subject_id' => null]);
                });
            }


            if (isset($request->type) && $request->type == "subject") {
                $data = $data->whereHas('announcement_class', function ($query) use ($classSectionId, $request) {
                    $query->where(['class_section_id' => $classSectionId, 'class_subject_id' => $request->class_subject_id]);
                });
            }

            $data = $data->orderBy('id', 'desc')->paginate(15);
            ResponseService::successResponse("Announcement Details Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function getTeachers(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $class_subject_id = $children->selectedStudentSubjects()->pluck('class_subject_id');
            $subjectTeachers = $this->subjectTeachers->builder()->select('id', 'subject_id', 'teacher_id', 'school_id')->whereIn('class_subject_id', $class_subject_id)->where('class_section_id', $children->class_section_id)->with('subject:id,name,type', 'teacher:id,first_name,last_name,image,mobile')->get();
            ResponseService::successResponse("Teacher Details Fetched Successfully", $subjectTeachers);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getSessionYear(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $children = $request->user()->guardianRelationChild()->where('id', $request->child_id)->first();
            $children = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($children)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $sessionYear = $this->cache->getDefaultSessionYear($children->school_id);
            ResponseService::successResponse("Session Year Fetched Successfully", $sessionYear ?? []);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getChildProfileDetails(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $childData = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->with(['class_section' => function ($query) {
//                $query->with('section', 'class', 'medium', 'class.shift', 'class.stream');
//            }, 'guardian', 'user'                                                                                      => function ($q) {
//                $q->with('extra_student_details.form_field', 'school');
//            }])->first();

            $childData = Auth::user()->guardianRelationChild()->with(['class_section' => function ($query) {
                $query->with('section', 'class', 'medium', 'class.shift', 'class.stream');
            }, 'guardian', 'user'                                                     => function ($q) {
                $q->with('extra_student_details.form_field', 'school');
            }])->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($childData)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }

            $data = array(
                'id'                => $childData->id,
                'first_name'        => $childData->user->first_name,
                'last_name'         => $childData->user->last_name,
                'mobile'            => $childData->user->mobile,
                'roll_number'       => $childData->roll_number,
                'admission_no'      => $childData->admission_no,
                'admission_date'    => $childData->admission_date,
                'gender'            => $childData->user->gender,
                'image'             => $childData->user->image,
                'dob'               => $childData->user->dob,
                'current_address'   => $childData->user->current_address,
                'permanent_address' => $childData->user->permanent_address,
                'occupation'        => $childData->user->occupation,
                'status'            => $childData->user->status,
                'fcm_id'            => $childData->user->fcm_id,
                'school_id'         => $childData->school_id,
                'session_year_id'   => $childData->session_year_id,
                'email_verified_at' => $childData->user->email_verified_at,
                'created_at'        => $childData->created_at,
                'updated_at'        => $childData->updated_at,
                'class_section'     => $childData->class_section,
                'guardian'          => $childData->guardian,
                'extra_details'     => $childData->user->extra_student_details,
                'school'            => $childData->user->school,
            );
            ResponseService::successResponse('Data Fetched Successfully', $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getExamList(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
            'status'   => 'nullable:digits:0,1,2,3'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $student = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id'], ['class_section']);
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classId = $student->class_section->class_id;
            $exam = $this->exam->builder()
                ->where('class_id', $classId)
                ->with([
                    'timetable' => function ($query) {
                        $query->selectRaw('* , SUM(total_marks) as total_marks')
                            ->groupBy('exam_id');
                    }
                ])->get();

            $exam_data = array();
            foreach ($exam as $data) {
                if (isset($request->status) && $request->status != $data->exam_status && $request->status != 3) {
                    continue;
                }

                $exam_data[] = [
                    'id'                 => $data->id,
                    'name'               => $data->name,
                    'description'        => $data->description,
                    'publish'            => $data->publish,
                    'session_year'       => $data->session_year->name,
                    'exam_starting_date' => $data->start_date,
                    'exam_ending_date'   => $data->end_date,
                    'exam_status'        => $data->exam_status,
                ];
            }

            ResponseService::successResponse("Exam List fetched Successfully", $exam_data ?? []);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'parentApiController :- getExamList Method');
            ResponseService::errorResponse();
        }
    }

    public function getExamDetails(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
            'exam_id'  => 'required|nullable'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $studentData = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id'], ['class_section']);
            $studentData = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($studentData)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classId = $studentData->class_section->class_id;
            $examData = $this->exam->builder()
                ->where([
                    'id'       => $request->exam_id,
                    'class_id' => $classId
                ])
                ->with([
                    'timetable' => function ($query) {
                        $query->owner()->with(['class_subject.subject'])->orderby('date');
                    }
                ])->first();


            if (!$examData) {
                ResponseService::successResponse("", []);
            }


            foreach ($examData->timetable as $data) {
                $exam_data[] = array(
                    'exam_timetable_id' => $data->id,
                    'total_marks'       => $data->total_marks,
                    'passing_marks'     => $data->passing_marks,
                    'date'              => $data->date,
                    'starting_time'     => $data->start_time,
                    'ending_time'       => $data->end_time,
                    'subject'           => array(
                        'id'               => $data->class_subject->subject->id,
                        'class_subject_id' => $data->class_subject_id,
                        'name'             => $data->class_subject->subject->name,
                        'bg_color'         => $data->class_subject->subject->bg_color,
                        'image'            => $data->class_subject->subject->image,
                        'type'             => $data->class_subject->subject->type,
                    )
                );
            }
            ResponseService::successResponse("", $exam_data ?? []);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, 'parentApiController :- getExamDetails Method');
            ResponseService::errorResponse();
        }
    }

    public function getExamMarks(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            // Student Data
//            $studentData = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id'], ['class_section']);
            $studentData = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($studentData)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            // Exam Result Data
            $examResultDB = $this->examResult->builder()->with([
                'user'       => function ($q) {
                    $q->select('id', 'first_name', 'last_name')->with('student:id,user_id,roll_number');
                },
                'exam.timetable:id,exam_id,start_time,end_time',
                'session_year',
                'exam.marks' => function ($q) use ($studentData) {
                    $q->where('student_id', $studentData->user_id)
                    ->with(['class_subject' => function($q) {
                        $q->withTrashed()->with(['subject' => function($q) {
                            $q->withTrashed();
                        }]);
                    }]);;
                }
            ])->where('student_id', $studentData->user_id)->get();


            // Check that Exam Result DB is not empty
            if (count($examResultDB)) {
                foreach ($examResultDB as $examResultData) {
                    $exam_result = array(
                        'result_id'      => $examResultData->id,
                        'exam_id'        => $examResultData->exam_id,
                        'exam_name'      => $examResultData->exam->name,
                        'class_name'     => $studentData->class_section->full_name,
                        'student_name'   => $examResultData->user->full_name,
                        'exam_date'      => $examResultData->exam->start_date,
                        'total_marks'    => $examResultData->total_marks,
                        'obtained_marks' => $examResultData->obtained_marks,
                        'percentage'     => $examResultData->percentage,
                        'grade'          => $examResultData->grade,
                        'session_year'   => $examResultData->session_year->name,
                    );
                    $exam_marks = array();
                    foreach ($examResultData->exam->marks as $marks) {
                        $exam_marks[] = array(
                            'marks_id'       => $marks->id,
                            'subject_name'   => $marks->class_subject->subject->name,
                            'subject_type'   => $marks->class_subject->subject->type,
                            'total_marks'    => $marks->timetable->total_marks,
                            'obtained_marks' => $marks->obtained_marks,
                            'teacher_review' => $marks->teacher_review,
                            'grade'          => $marks->grade,
                        );
                    }
                    $data[] = array(
                        'result'     => $exam_result,
                        'exam_marks' => $exam_marks,
                    );
                }
                ResponseService::successResponse("Exam Result Fetched Successfully", $data ?? null);
            } else {
                ResponseService::successResponse("Exam Result Fetched Successfully", []);
            }
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamList(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'         => 'required|numeric',
            'class_subject_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $student = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id', 'school_id'], ['class_section']);
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classSectionId = $student->class_section->id;
            $sessionYear = $this->cache->getDefaultSessionYear($student->school_id);

            $onlineExamData = $this->onlineExam->builder()
                ->where(['class_section_id' => $classSectionId, 'session_year_id' => $sessionYear->id])
                ->where('end_date', '>=', now())
                ->has('question_choice')
                ->with('class_subject', 'question_choice:id,online_exam_id,marks')
                ->whereDoesntHave('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->user_id);
                })
                ->when($request->class_subject_id, function ($query, $classSubjectId) {
                    return $query->where('class_subject_id', $classSubjectId);
                })
                ->orderby('start_date')
                ->paginate(15);

            ResponseService::successResponse('Data Fetched Successfully', $onlineExamData);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Parent API ");
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamResultList(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'         => 'required|numeric',
            'class_subject_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $student = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id', 'school_id'], ['class_section']);
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classSectionId = $student->class_section_id;
            $sessionYear = $this->cache->getDefaultSessionYear($student->school_id);

            $studentSubjects = $student->selectedStudentSubjects()->pluck('class_subject_id');
            // Get Online Exam Data Where Logged in Student have attempted data and Relation Data with Question Choice , Student's answer with user submitted question with question and its option
            $onlineExamData = $this->onlineExam->builder()
                ->when($request->class_subject_id, function ($query) use ($request) {
                    $query->where('class_subject_id', $request->class_subject_id);
                })
                ->where(['class_section_id' => $classSectionId, 'session_year_id' => $sessionYear->id])
                ->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->user_id);
                })
                ->whereIn('class_subject_id', $studentSubjects)
                ->with('question_choice:id,online_exam_id,marks', 'student_answers.user_submitted_questions.questions:id', 'student_answers.user_submitted_questions.questions.options:id,question_id,is_answer', 'class_subject.subject:id,name,type,code,bg_color,image')
                ->paginate(15)->toArray();

            $examListData = array(); // Initialized Empty examListData Array

            // Loop through Exam data
            foreach ($onlineExamData['data'] as $data) {

                // Get Total Marks of Particular Exam
                $totalMarks = collect($data['question_choice'])->sum('marks');

                // Initialized totalObtainedMarks with 0
                $totalObtainedMarks = 0;

                // Group Student's Answers by question_id
                $grouped_answers = [];
                foreach ($data['student_answers'] as $student_answer) {
                    $grouped_answers[$student_answer['question_id']][] = $student_answer;
                }

                // Loop through Student's Grouped answers
                foreach ($grouped_answers as $student_answers) {

                    // Filter the options whose is_answer values is 1
                    $correct_option_ids = array_filter($student_answers[0]['user_submitted_questions']['questions']['options'], static function ($option) {
                        return $option['is_answer'] == 1;
                    });

                    // Get All Correct Options
                    $correct_option_ids = array_column($correct_option_ids, 'id');

                    // Get Student's Correct Options
                    $student_option_ids = array_column($student_answers, 'option_id');

                    // Check if the student's answers exactly match the correct answers then add marks with totalObtainedMarks
                    if (!array_diff($correct_option_ids, $student_option_ids) && !array_diff($student_option_ids, $correct_option_ids)) {
                        $totalObtainedMarks += $student_answers[0]['user_submitted_questions']['marks'];
                    }
                }

                // Make Exam List Data
                $examListData[] = array(
                    'online_exam_id'      => $data['id'],
                    'subject'             => array(
                        'id'   => $data['class_subject']['subject']['id'],
                        'name' => $data['class_subject']['subject']['name'] . ' - ' . $data['class_subject']['subject']['type'],
                    ),
                    'title'               => $data['title'],
                    'obtained_marks'      => $totalObtainedMarks ?? "0",
                    'total_marks'         => $totalMarks ?? "0",
                    'exam_submitted_date' => date('Y-m-d', strtotime($data['end_date']))
                );
            }

            $examList = array(
                'current_page' => $onlineExamData['current_page'],
                'data'         => $examListData,
                'from'         => $onlineExamData['from'],
                'last_page'    => $onlineExamData['last_page'],
                'per_page'     => $onlineExamData['per_page'],
                'to'           => $onlineExamData['to'],
                'total'        => $onlineExamData['total'],
            );

            ResponseService::successResponse("", $examList);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamResult(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'       => 'required|numeric',
            'online_exam_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $student = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id'], ['class_section']);
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            // Online Exam Data
            $onlineExam = $this->onlineExam->builder()
                ->where('id', $request->online_exam_id)
                ->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->user_id);
                })
                ->with([
                    'question_choice:id,online_exam_id,marks',
                    'student_answers.user_submitted_questions.questions:id',
                    'student_answers.user_submitted_questions.questions.options:id,question_id,is_answer',
                ])
                ->first();

            if (isset($onlineExam) && $onlineExam != null) {

                //Get Total Question Count and Total Marks
                $totalQuestions = $onlineExam->question_choice->count();
                $totalMarks = $onlineExam->question_choice->sum('marks');

                // Group Student's Answers by question_id
                $grouped_answers = [];
                foreach ($onlineExam->student_answers as $student_answer) {
                    $grouped_answers[$student_answer['question_id']][] = $student_answer->toArray();
                }

                // Initialized the variables
                $correctQuestionData = array();
                $correctQuestions = 0;
                $totalObtainedMarks = "0";

                // Loop through Student's Grouped answers
                foreach ($grouped_answers as $student_answers) {

                    // Filter the options whose is_answer values is 1
                    $correct_option_ids = array_filter($student_answers[0]['user_submitted_questions']['questions']['options'], static function ($option) {
                        return $option['is_answer'] == 1;
                    });

                    // Get All Correct Options
                    $correct_option_ids = array_column($correct_option_ids, 'id');

                    // Get Student's Correct Options
                    $student_option_ids = array_column($student_answers, 'option_id');

                    // Check if the student's answers exactly match the correct answers then add marks with totalObtainedMarks
                    if (!array_diff($correct_option_ids, $student_option_ids) && !array_diff($student_option_ids, $correct_option_ids)) {

                        // Sum Question marks with ObtainedMarks
                        $totalObtainedMarks += $student_answers[0]['user_submitted_questions']['marks'];

                        // Get Correct Questions Ids
                        $correctQuestionIds[] = $student_answers[0]['user_submitted_questions']['id'];

                        // Increment Correct Question by 1
                        ++$correctQuestions;

                        // Correct Question Data
                        $correctQuestionData[] = array(
                            'question_id' => $student_answers[0]['user_submitted_questions']['id'],
                            'marks'       => $student_answers[0]['user_submitted_questions']['marks']
                        );
                    }
                }


                // Check correctQuestionIds exists and not empty
                if (!empty($correctQuestionIds)) {
                    // Get Incorrect Questions Excluding Correct answer using correctQuestionIds
                    $incorrectQuestionsData = $onlineExam->question_choice->whereNotIn('id', $correctQuestionIds);
                } else {
                    // Get All Question Choice as incorrectQuestionsData
                    $incorrectQuestionsData = $onlineExam->question_choice;
                }

                // Total Incorrect Questions
                $incorrectQuestions = $incorrectQuestionsData->count();

                // Incorrect Question Data
                $inCorrectQuestionData = array();
                foreach ($incorrectQuestionsData as $incorrectData) {
                    $inCorrectQuestionData[] = array(
                        'question_id' => $incorrectData->id,
                        'marks'       => $incorrectData->marks
                    );
                }

                // Final Array Data
                $onlineExamResult = array(
                    'total_questions'      => $totalQuestions,
                    'correct_answers'      => array(
                        'total_questions' => $correctQuestions,
                        'question_data'   => $correctQuestionData
                    ),
                    'in_correct_answers'   => array(
                        'total_questions' => $incorrectQuestions,
                        'question_data'   => $inCorrectQuestionData
                    ),
                    'total_obtained_marks' => $totalObtainedMarks,
                    'total_marks'          => $totalMarks ?? '0'
                );
                ResponseService::successResponse("", $onlineExamResult);
            } else {
                ResponseService::successResponse("", []);
            }
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamReport(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'         => 'required|numeric',
            'class_subject_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $student = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id', 'school_id'], ['class_section']);
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $sessionYear = $this->cache->getDefaultSessionYear($student->school_id);

            $onlineExams = $this->onlineExam->builder()
                ->has('question_choice')
                ->where(['class_section_id' => $student->class_section_id, 'class_subject_id' => $request->class_subject_id, 'session_year_id' => $sessionYear->id])
                ->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->user_id);
                })
                ->with([
                    'question_choice:id,online_exam_id,marks',
                    'student_answers.user_submitted_questions.questions:id',
                    'student_answers.user_submitted_questions.questions.options:id,question_id,is_answer',
                    'class_subject.subject:id,name,type,code,bg_color,image'
                ])
                ->paginate(10);
            $totalMarks = 0;
            $totalObtainedMarks = 0;
            if ($onlineExams->count() > 0) {
                $totalExamIds = $onlineExams->pluck('id')->toArray();
                $totalExamsAttempted = $this->user->builder()->role('Student')->where('id', $student->user_id)->has('online_exam_attempts')->count();

                $examList = array();
                foreach ($onlineExams->toArray()['data'] as $onlineExam) {
                    $totalMarks = collect($onlineExam['question_choice'])->sum('marks');

                    // Initialized totalObtainedMarks with 0
                    $totalObtainedMarks = "0";

                    // Group Student's Answers by question_id
                    $grouped_answers = [];
                    foreach ($onlineExam['student_answers'] as $student_answer) {
                        $grouped_answers[$student_answer['question_id']][] = $student_answer;
                    }

                    // Loop through Student's Grouped answers
                    foreach ($grouped_answers as $student_answers) {

                        // Filter the options whose is_answer values is 1
                        $correct_option_ids = array_filter($student_answers[0]['user_submitted_questions']['questions']['options'], static function ($option) {
                            return $option['is_answer'] == 1;
                        });

                        // Get All Correct Options
                        $correct_option_ids = array_column($correct_option_ids, 'id');

                        // Get Student's Correct Options
                        $student_option_ids = array_column($student_answers, 'option_id');

                        // Check if the student's answers exactly match the correct answers then add marks with totalObtainedMarks
                        if (!array_diff($correct_option_ids, $student_option_ids) && !array_diff($student_option_ids, $correct_option_ids)) {
                            $totalObtainedMarks += $student_answers[0]['user_submitted_questions']['marks'];
                        }
                    }

                    // Add exam to the list
                    $examList[] = [
                        'online_exam_id' => $onlineExam['id'],
                        'title'          => $onlineExam['title'],
                        'obtained_marks' => (string)$totalObtainedMarks,
                        'total_marks'    => (string)$totalMarks,
                    ];
                }


                // Calculate Percentage
                if ($totalMarks > 0) {
                    // Avoid division by zero error
                    $percentage = number_format(($totalObtainedMarks * 100) / max($totalMarks, 1), 2);
                } else {
                    // If total marks is zero, then percentage is also zero
                    $percentage = 0;
                }

                // Build the final data array
                $onlineExamReportData = array(
                    'total_exams'          => count($totalExamIds),
                    'attempted'            => $totalExamsAttempted,
                    'missed_exams'         => count($totalExamIds) - $totalExamsAttempted,
                    'total_marks'          => (string)$totalMarks,
                    'total_obtained_marks' => (string)$totalObtainedMarks,
                    'percentage'           => (string)$percentage,
                    'exam_list'            => [
                        'current_page' => (string)$onlineExams->currentPage(),
                        'data'         => array_values($examList),
                        'from'         => (string)$onlineExams->firstItem(),
                        'last_page'    => (string)$onlineExams->lastPage(),
                        'per_page'     => (string)$onlineExams->perPage(),
                        'to'           => (string)$onlineExams->lastItem(),
                        'total'        => (string)$onlineExams->total(),
                    ],
                );
            } else {
                $onlineExamReportData = [];
            }


            // Return the response
            ResponseService::successResponse("", $onlineExamReportData);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getAssignmentReport(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'         => 'required|numeric',
            'class_subject_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
//            $student = $this->student->findById($request->child_id, ['id', 'user_id', 'class_section_id', 'school_id'], ['class_section']);
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $sessionYear = $this->cache->getDefaultSessionYear($student->school_id);

            // Assignment Data
            $assignments = $this->assignment->builder()
                ->where(['class_section_id' => $student->class_section_id, 'session_year_id' => $sessionYear->id, 'class_subject_id' => $request->class_subject_id])->whereNotNull('points')
                ->get();

            // Get the assignment submissions
            $submitted_assignment_ids = $this->assignmentSubmission->builder()->where('student_id', $student->user_id)->whereIn('assignment_id', $assignments->pluck('id'))->pluck('assignment_id');

            // Calculate various statistics
            $total_assignments = $assignments->count();
            $total_submitted_assignments = $submitted_assignment_ids->count();
            $total_assignment_submitted_points = $assignments->sum('points');
            $total_points_obtained = $this->assignmentSubmission->builder()->whereIn('assignment_id', $submitted_assignment_ids)->sum('points');

            // Calculate the percentage
            $percentage = $total_assignment_submitted_points ? number_format(($total_points_obtained * 100) / $total_assignment_submitted_points, 2) : 0;

            // Get the submitted assignment data with points (using pagination manually)
            $perPage = 15;
            $currentPage = $request->input('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $submitted_assignment_data_with_points = $assignments->filter(function ($assignment) use ($submitted_assignment_ids) {
                return $assignment->points !== null && $submitted_assignment_ids->contains($assignment->id);
            })->slice($offset, $perPage)->map(function ($assignment) {
                return [
                    'assignment_id'   => $assignment->id,
                    'assignment_name' => $assignment->name,
                    'obtained_points' => optional($assignment->submission)->points ?? 0,
                    'total_points'    => $assignment->points
                ];
            });

            $assignment_report = [
                'assignments'                           => $total_assignments,
                'submitted_assignments'                 => $total_submitted_assignments,
                'unsubmitted_assignments'               => $total_assignments - $total_submitted_assignments,
                'total_points'                          => $total_assignment_submitted_points,
                'total_obtained_points'                 => $total_points_obtained,
                'percentage'                            => $percentage,
                'submitted_assignment_with_points_data' => [
                    'current_page' => $currentPage,
                    'data'         => array_values($submitted_assignment_data_with_points->toArray()),
                    'from'         => $offset + 1,
                    'to'           => $offset + $submitted_assignment_data_with_points->count(),
                    'per_page'     => $perPage,
                    'total'        => $total_submitted_assignments,
                    'last_page'    => ceil($total_submitted_assignments / $perPage),
                ],
            ];


            ResponseService::successResponse("Data Fetched Successfully", $assignment_report);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    //Get Fees Details
    public function getFees(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'        => 'required',
            'session_year_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classId = $student->class_section->class_id;
            $schoolId = $student->user->school_id;

            $currentSessionYear = $this->cache->getDefaultSessionYear($schoolId);
            $sessionYearId = $request->session_year_id ?? $currentSessionYear->id;

            $fees = $this->fees->builder()->where('class_id', $classId)
                ->with(['fees_class_type.fees_type',
                        'fees_paid' => function ($query) use ($student) {
                            $query->where(['student_id' => $student->user_id])->with('compulsory_fee.advance_fees', 'optional_fee');
                        }, 'session_year', 'class.medium', 'class.stream'
                ])->where(['session_year_id' => $sessionYearId])->get();

            $currentDateTimestamp = new DateTime(date('Y-m-d'));

            foreach ($fees as $fee) {
                $feesDateTimestamp = new DateTime($fee->due_date);

                // Set Optional Fees Data in response
                if (count($fee->optional_fees) > 0) {
                    collect($fee->optional_fees)->map(function ($optionalFees) use ($student) {
                        $isOptionalFeesPaid = $student->user->optional_fees->first(function ($optionalFeesPaid) use ($optionalFees, $student) {
                            return $optionalFeesPaid->fees_class_id == $optionalFees->id && $optionalFeesPaid->student_id == $student->user->id;
                        });
                        $optionalFees['is_paid'] = $isOptionalFeesPaid ? true : false;
                        return $optionalFees;
                    });
                }


                // Set Compulsory Fees Data in response
                if (count($fee->compulsory_fees) > 0) {
                    $fee->is_overdue = $currentDateTimestamp > $feesDateTimestamp; // true/false
                    collect($fee->compulsory_fees)->map(function ($compulsoryFees) use ($student) {
                        $isCompulsoryFeesPaid = $student->user->compulsory_fees->first(function ($compulsoryFeesPaid) use ($student) {
                            return $compulsoryFeesPaid->type == 'Full Payment' && $compulsoryFeesPaid->student_id == $student->user->id;
                        });
                        $compulsoryFees['is_paid'] = $isCompulsoryFeesPaid ? true : false;
                        return $compulsoryFees;
                    });
                }

                // Set Installment Data in Response
                if (count($fee->installments) > 0) {
                    $totalFeesAmount = $fee->total_compulsory_fees;
                    $totalInstallments = count($fee->installments);

                    $previousInstallmentDate = new DateTime('now -1 day');
                    collect($fee->installments)->map(function ($installment) use ($student, &$totalFeesAmount, &$totalInstallments, $currentDateTimestamp, &$previousInstallmentDate) {
                        $installmentDueDateTimestamp = new DateTime($installment['due_date']);

                        $installmentPaid = $student->user->compulsory_fees->first(function ($compulsoryFeesPaid) use ($installment, $student) {
                            return $compulsoryFeesPaid->type == "Installment Payment" && $compulsoryFeesPaid->installment_id == $installment->id && $compulsoryFeesPaid->student_id == $student->user->id;
                        });

                        // If installment is not Paid
                        if (!empty($installmentPaid)) {
                            --$totalInstallments;
                            $totalFeesAmount -= $installmentPaid->amount;
                            $installment['minimum_amount'] = $installmentPaid->amount;
                            $installment['maximum_amount'] = $installmentPaid->amount;
                            $installment['due_charges_amount'] = $installmentPaid->due_charges;
                        } else {
                            // If installment is paid
                            $installment['minimum_amount'] = $totalFeesAmount / $totalInstallments;
                            $installment['maximum_amount'] = $totalFeesAmount;

                            //Calculate Due Charges amount for not paid installment
                            if ($currentDateTimestamp > $installmentDueDateTimestamp) {
                                if ($installment->due_charges_type == "percentage") {
                                    $installment['due_charges_amount'] = ($installment['minimum_amount'] * $installment['due_charges']) / 100;
                                } else if ($installment->due_charges_type == "fixed") {
                                    $installment['due_charges_amount'] = $installment->due_charges;
                                }

                            } else {
                                $installment['due_charges_amount'] = 0;
                            }
                        }
                        $installment['is_paid'] = $installmentPaid ? true : false;

                        //identify which installment is the correct installment

                        /* Current date should be less then the due date && greater than the due date of previous installments  */
                        /* In case of first installment , previous installment date will be current date - 1 */
                        if ($currentDateTimestamp <= $installmentDueDateTimestamp && $currentDateTimestamp > $previousInstallmentDate) {
                            $installment['is_current'] = true;
                        } else {
                            $installment['is_current'] = false;
                        }
                        $previousInstallmentDate = new DateTime($installment['due_date']);
                        return $installment;
                    });
                }

                // Unsetting fees_class_type at the end of the loop
                // unset($fee['fees_class_type']);
            }

            ResponseService::successResponse("Fees Fetched Successfully", $fees);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function payCompulsoryFees(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'          => 'required',
            'fees_id'           => 'required',
            'installment_ids'   => 'nullable|array',
            'installment_ids.*' => 'required|integer',
            'advance'           => 'present|numeric',
            'payment_method'    => 'required|in:Stripe,Razorpay,Flutterwave,Paystack',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();

            $paymentConfigurations = $this->paymentConfigurations->builder()->where(['status' => 1, 'payment_method' => $request->payment_method])->first();

            if (empty($paymentConfigurations)) {
                ResponseService::errorResponse("Payment is not Enabled", [], config('constants.RESPONSE_CODE.ENABLE_PAYMENT_GATEWAY'));
            }

            $parentId = Auth::user()->id;

            $studentData = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($studentData)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $schoolId = $studentData->user->school_id;
            $compulsory_fees = $studentData->user->compulsory_fees()->whereHas('fees_paid', function ($q) use ($request) {
                $q->where('fees_id', $request->fees_id);
            })->get();

            $sessionYear = $this->cache->getDefaultSessionYear($schoolId);

            $fees = $this->fees->builder()
                ->where('id', $request->fees_id)
                ->with(['fees_class_type'              => function ($q) {
                    $q->where('optional', 0);
                }, 'fees_class_type.fees_type',
                        'installments.compulsory_fees' => function ($query) use ($studentData) {
                            $query->where(['student_id' => $studentData->user_id]);
                        },
                        'fees_paid'                    => function ($query) use ($studentData) {
                            $query->where(['student_id' => $studentData->user_id])->with('compulsory_fee', 'optional_fee');
                        }
                ])->firstOrFail();
            $fees->append(['total_compulsory_fees']);

            if (!empty($fees->fees_paid) && !empty($fees->fees_paid->is_fully_paid)) {
                ResponseService::errorResponse("Fees Already Paid", "", config('constants.RESPONSE_CODE.FEE_ALREADY_PAID'));
            }

            // If installment id is not empty then user is going to pay in installment
            $installmentDetails = [];
            if (isset($request->installment_ids) && count($request->installment_ids)) {
                $amount = 0;
                $dueChargesAmount = 0;
                $validInstallmentIDS = array_intersect($fees->installments->pluck('id')->toArray(), $request->installment_ids);

                if (empty($validInstallmentIDS)) {
                    ResponseService::errorResponse('Invalid Installment ID');
                }

                $totalInstallments = count($fees->installments);
                $remainingAmount = $fees->total_compulsory_fees;
                if (count($compulsory_fees) > 0) {
                    $validInstallmentIDS = array_diff($validInstallmentIDS, $compulsory_fees->pluck('installment_id')->toArray());
                    // Removing the Paid installments from total installments so that minimum amount can be calculated for the remaining installments.
                    foreach ($compulsory_fees as $paidInstallment) {
                        if (!empty($paidInstallment->installment_id)) {
                            --$totalInstallments;
                            $remainingAmount -= $paidInstallment->amount;
                        }
                    }
                }


                // Calculate amount per installment
                $installmentAmount = $remainingAmount / $totalInstallments;

                foreach ($validInstallmentIDS as $key => $installment_id) {
                    $installment = $fees->installments->first(function ($data) use ($installment_id) {
                        return $data->id == $installment_id;
                    });

                    // Calculate Due Charges amount if installment is overdue
                    if (new DateTime(date('Y-m-d')) > new DateTime($installment['due_date'])) {
                        if ($installment->due_charges_type == "percentage") {
                            $dueChargesAmount = ($installmentAmount * $installment['due_charges']) / 100;
                        } else if ($installment->due_charges_type == "fixed") {
                            $dueChargesAmount = $installment->due_charges;
                        }
                        $amount += $installmentAmount + $dueChargesAmount;
                    } else {
                        $dueChargesAmount = 0;
                        $amount += $installmentAmount;
                    }
                    // Removing installment amount from remaining amount so that we can add validation for advance amount
                    $remainingAmount -= $installmentAmount;
                    $installmentDetails[$key] = [
                        'id'               => $installment_id,
                        'amount'           => $installmentAmount,
                        'dueChargesAmount' => $dueChargesAmount,
                    ];
                }

                if ($request->advance > $remainingAmount) {
                    ResponseService::errorResponse("Advance Amount cannot be greater then : " . $remainingAmount);
                }

            } else {
                /* Full Payment */
                $dueChargesAmount = 0;
                $amount = $fees->total_compulsory_fees;

                if (new DateTime(date('Y-m-d')) > new DateTime($fees->due_date)) {
                    $dueChargesAmount = $fees->due_charges_amount;
                    $amount += $dueChargesAmount;
                }
            }
            
            // if advance amount is greater than 0 and less than amount then add advance amount to amount
            if($request->advance > 0 && $request->advance < $amount) {
                $finalAmount = $request->advance;
            } else {
                $finalAmount = $amount;
            }
         
            //Add Payment Data to Payment Transactions Table
            $paymentTransactionData = $this->paymentTransaction->create([
                'user_id'         => $parentId,
                'amount'          => $finalAmount,
                'payment_gateway' => $request->payment_method,
                'payment_status'  => 'Pending',
                'school_id'       => $schoolId,
                'order_id'        => null
            ]);
            
            $paymentIntent = PaymentService::create($request->payment_method, $schoolId)->createPaymentIntent(round($finalAmount, 2), [
                'user_id'                => Auth::user()->id,
                'name'                   => Auth::user()->full_name,
                'email'                  => Auth::user()->email,
                'mobile'                 => Auth::user()->mobile,
                'fees_id'                => $request->fees_id,
                'student_id'             => $studentData->user_id,
                'parent_id'              => $parentId,
                'session_year_id'        => $sessionYear->id,
                'payment_transaction_id' => $paymentTransactionData->id,
                'installment'            => json_encode($installmentDetails, JSON_THROW_ON_ERROR),
                'total_amount'           => $finalAmount,
                'advance_amount'         => $request->advance,
                'dueChargesAmount'       => $dueChargesAmount,
                'school_id'              => $schoolId,
                'type'                   => 'fees',
                'fees_type'              => 'compulsory',
                'is_fully_paid'          => $amount > $fees->total_compulsory_fees,
            ]);

            if ($request->payment_method == "Flutterwave" || $request->payment_method == "Paystack") {
                $this->paymentTransaction->update($paymentTransactionData->id, ['order_id' => $paymentIntent['order_id'] ?? null, 'school_id' => $schoolId]);
                $paymentTransactionData = $this->paymentTransaction->findById($paymentTransactionData->id);
                DB::commit();

                \Log::info("Payment Intent:", ['payment_intent' => $paymentIntent]);
                
                // Return only the payment_link for Flutterwave
                if($request->payment_method == "Flutterwave") {
                    ResponseService::successResponse("", [
                        "payment_link" => $paymentIntent['payment_link']
                    ]);
                } else {
                    ResponseService::successResponse("", [
                        "payment_link" => $paymentIntent['data']['authorization_url']
                    ]);
                }
            } else {
                $this->paymentTransaction->update($paymentTransactionData->id, ['order_id' => $paymentIntent->id, 'school_id' => $schoolId]);

                $paymentTransactionData = $this->paymentTransaction->findById($paymentTransactionData->id);
                // Custom Array to Show as response
                $paymentGatewayDetails = array(
                    ...$paymentIntent->toArray(),
                    'payment_transaction_id' => $paymentTransactionData->id,
                );
                DB::commit();
                ResponseService::successResponse("", ["payment_intent" => $paymentGatewayDetails, "payment_transaction" => $paymentTransactionData]);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function payOptionalFees(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'       => 'required',
            'fees_id'        => 'required',
            'optional_id'    => 'required|array',
            'optional_id.*'  => 'required|integer',
            'payment_method' => 'required|in:Stripe,Razorpay,Flutterwave,Paystack',
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $parentId = Auth::user()->id;
            $studentData = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($studentData)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $classId = $studentData->class_section->class_id;
            $schoolId = $studentData->user->school_id;
            $sessionYear = $this->cache->getDefaultSessionYear($schoolId);

            $paymentConfigurations = $this->paymentConfigurations->builder()->where(['status' => 1, 'payment_method' => $request->payment_method])->first();

            if (empty($paymentConfigurations)) {
                ResponseService::errorResponse("Payment is not Enabled", [], config('constants.RESPONSE_CODE.ENABLE_PAYMENT_GATEWAY'));
            }


            // Fees Data
            $fees = $this->fees->builder()
                ->where('id', $request->fees_id)
                ->with(['fees_class_type' => function ($q) use ($request) {
                    $q->where('optional', 1)->whereIn('id', $request->optional_id);
                }, 'fees_class_type.fees_type',
                        'fees_paid'       => function ($query) use ($studentData) {
                            $query->where(['student_id' => $studentData->user_id])->with('optional_fee');
                        }
                ])->firstOrFail();

            $optional_fees = $studentData->user->optional_fees()->whereIn('fees_class_id', $request->optional_id)->whereHas('fees_paid', function ($q) use ($request) {
                $q->where('fees_id', $request->fees_id);
            })->get();

            if (count($optional_fees) > 0) {
                ResponseService::errorResponse("Please select only unpaid fees");
            }
            $amount = $fees->total_optional_fees;

            if ($amount <= 0) {
                ResponseService::errorResponse("No Optional Fees Found");
            }

            $optional_fee = [];
            foreach ($fees->fees_class_type as $row) {
                $optional_fee[] = [
                    'id'     => $row->id,
                    'amount' => $row->amount
                ];
            }
            // Add Payment Data to Payment Transactions Table
            $paymentTransactionData = $this->paymentTransaction->create([
                'user_id'         => $parentId,
                'amount'          => $amount,
                'payment_gateway' => $request->payment_method,
                'payment_status'  => 'Pending',
                'school_id'       => $schoolId,
                'order_id'        => null
            ]);

            $paymentIntent = PaymentService::create($request->payment_method, $schoolId)->createPaymentIntent($amount, [
                'fees_id'                => $request->fees_id,
                'student_id'             => $studentData->user_id,
                'parent_id'              => $parentId,
                'session_year_id'        => $sessionYear->id,
                'payment_transaction_id' => $paymentTransactionData->id,
                'total_amount'           => $amount,
                'school_id'              => $schoolId,
                'class_id'               => $classId,
                'optional_fees_id'       => json_encode($optional_fee, JSON_THROW_ON_ERROR),
                'type'                   => 'fees',
                'fees_type'              => 'optional',
                'name'                   => Auth::user()->full_name,
                'email'                  => Auth::user()->email,
                'mobile'                 => Auth::user()->mobile
            ]);

            if ($request->payment_method == "Flutterwave" || $request->payment_method == "Paystack") {
                $this->paymentTransaction->update($paymentTransactionData->id, ['order_id' => $paymentIntent['order_id'] ?? null, 'school_id' => $schoolId]);
                $paymentTransactionData = $this->paymentTransaction->findById($paymentTransactionData->id);
                DB::commit();

                \Log::info("Payment Intent:", ['payment_intent' => $paymentIntent]);
                
                // Return only the payment_link for Flutterwave
                if($request->payment_method == "Flutterwave") {
                    ResponseService::successResponse("", [
                        "payment_link" => $paymentIntent['payment_link']
                    ]);
                } else {
                    ResponseService::successResponse("", [
                        "payment_link" => $paymentIntent['data']['authorization_url']
                    ]);
                }
            } else {
                $this->paymentTransaction->update($paymentTransactionData->id, ['order_id' => $paymentIntent->id, 'school_id' => $schoolId]);

                $paymentTransactionData = $this->paymentTransaction->findById($paymentTransactionData->id);
                // Custom Array to Show as response
                $paymentGatewayDetails = array(
                    ...$paymentIntent->toArray(),
                    'payment_transaction_id' => $paymentTransactionData->id,
                );
                DB::commit();
                ResponseService::successResponse("", ["payment_intent" => $paymentGatewayDetails, "payment_transaction" => $paymentTransactionData]);
            }
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function feesPaidReceiptPDF(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|integer',
            'fees_id'  => 'required|integer'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $student = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->with('user:id,first_name,last_name', 'class_section.class.stream', 'class_section.section', 'class_section.medium')->first();

            if (empty($student)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }
            $feesPaid = $this->feesPaid->builder()->where(['fees_id' => $request->fees_id, 'student_id' => $student->user_id])
                ->with([
                    'fees'         => function ($q) {
                        $q->with('class:id,medium_id,name', 'class.medium', 'fees_class_type.fees_type');
                    },
                    'compulsory_fee.installment_fee:id,name',
                    'optional_fee' => function ($q) {
                        $q->with(['fees_class_type' => function ($q) {
                            $q->select('id', 'fees_type_id')->with('fees_type:id,name');
                        }]);
                    },
                ])->first();
            $systemVerticalLogo = $this->systemSetting->builder()->where('name', 'vertical_logo')->first();
            $schoolVerticalLogo = $this->schoolSetting->builder()->where('name', 'vertical_logo')->first();
            $school = $this->cache->getSchoolSettings();
//            return view('fees.fees_receipt', compact('systemVerticalLogo', 'school', 'feesPaid', 'student', 'schoolVerticalLogo'));
            $output = Pdf::loadView('fees.fees_receipt', compact('systemVerticalLogo', 'school', 'feesPaid', 'student', 'schoolVerticalLogo'))->output();
            $response = array(
                'error' => false,
                'pdf'   => base64_encode($output),
            );
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
        return response()->json($response);
    }

    public function getSchoolSettings(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id'        => 'required|numeric',
            'session_year_id' => 'nullable'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $child = Auth::user()->guardianRelationChild()->where('id', $request->child_id)->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })->first();

            if (empty($child)) {
                ResponseService::errorResponse("Child's Account is not Active.Contact School Support", NULL, config('constants.RESPONSE_CODE.INACTIVE_CHILD'));
            }

            $settings = $this->cache->getSchoolSettings(['*'], $child->user->school_id);
            $sessionYear = $this->cache->getDefaultSessionYear($child->user->school_id);
            $semester = $this->cache->getDefaultSemesterData($child->user->school_id);
            $features = FeaturesService::getFeatures($child->user->school_id);
            $paymentGateways = $this->paymentConfigurations->builder()->select(['id', 'payment_method', 'api_key', 'currency_code'])->where('status', 1)->get();
            $data = [
                'school_id'       => $child->user->school_id,
                'settings'        => $settings,
                'session_year'    => $sessionYear,
                'semester'        => $semester,
                'features'        => (count($features) > 0) ? $features : (object)[],
                'payment_gateway' => $paymentGateways
            ];
            ResponseService::successResponse('Settings Fetched Successfully.', $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getSliders(Request $request) {
        $validator = Validator::make($request->all(), [
            'child_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $child = $this->student->findById($request->child_id);
            $data = $this->sliders->builder()->where('school_id', $child->user->school_id)->get();
            ResponseService::successResponse("Sliders Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function test(Request $request)
    {
        // return $request->header();
        // DB::setDefaultConnection('school');
        // return DB::getDatabaseName();
        return Auth::user();
    }


    // add the transaction data in transaction table
//    public function completeFeeTransaction(Request $request) {
//        $validator = Validator::make($request->all(), [
//            'child_id'               => 'required',
//            'payment_transaction_id' => 'required',
//            'payment_id'             => 'required',
//            'payment_signature'      => 'required',
//        ]);
//        if ($validator->fails()) {
//            ResponseService::validationError($validator->errors()->first());
//        }
//        try {
//            DB::beginTransaction();
//            $child = $this->student->findById($request->child_id);
//            $this->paymentTransaction->update($request->payment_transaction_id, ['payment_id' => $request->payment_id, 'payment_signature' => $request->payment_signature, 'school_id' => $child->school_id]);
//            DB::commit();
//            ResponseService::successResponse("Data Updated Successfully");
//        } catch (Throwable $e) {
//            DB::rollBack();
//            ResponseService::logErrorResponse($e);
//            ResponseService::errorResponse();
//        }
//    }

    //get the fees paid list
//    public function feesPaidList(Request $request) {
//        $validator = Validator::make($request->all(), [
//            'child_id'        => 'required',
//            'session_year_id' => 'nullable'
//        ]);
//
//        if ($validator->fails()) {
//            ResponseService::validationError($validator->errors()->first());
//        }
//        try {
//            $child = $this->student->findById($request->child_id);
//            $currentSessionYear = $this->cache->getDefaultSessionYear($child->user->school_id);
//            $sessionYearId = $request->session_year_id ?? $currentSessionYear->id;
//            $fees_paid = $this->feesPaid->builder()->where(['student_id' => $child->user_id, 'session_year_id' => $sessionYearId])->with('session_year:id,name', 'class.medium')->get();
//
//            ResponseService::successResponse("", $fees_paid);
//        } catch (Throwable $e) {
//            DB::rollBack();
//            ResponseService::logErrorResponse($e);
//            ResponseService::errorResponse();
//        }
//    }


    // // Make Transaction Fail API
    // public function failPaymentTransactionStatus(Request $request){
    //     try{
    //         $update_status = PaymentTransaction::findOrFail($request->payment_transaction_id);
    //         $update_status->payment_status = 0;
    //         $update_status->save();
    //         $response = array(
    //             'error' => false,
    //             'message' => 'Data Updated Successfully',
    //             'code' => 200,
    //         );
    //     } catch (\Exception $e) {
    //         $response = array(
    //             'error' => true,
    //             'message' => trans('error_occurred'),
    //             'code' => 103,
    //         );
    //     }
}
