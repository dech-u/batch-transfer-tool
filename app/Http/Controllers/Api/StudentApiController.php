<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimetableCollection;
use App\Http\Resources\UserDataResource;
use App\Models\AssignmentSubmission;
use App\Models\School;
use App\Models\User;
use App\Repositories\Announcement\AnnouncementInterface;
use App\Repositories\Assignment\AssignmentInterface;
use App\Repositories\AssignmentSubmission\AssignmentSubmissionInterface;
use App\Repositories\Attendance\AttendanceInterface;
use App\Repositories\Exam\ExamInterface;
use App\Repositories\ExamResult\ExamResultInterface;
use App\Repositories\Files\FilesInterface;
use App\Repositories\Holiday\HolidayInterface;
use App\Repositories\Lessons\LessonsInterface;
use App\Repositories\OnlineExam\OnlineExamInterface;
use App\Repositories\OnlineExamQuestionChoice\OnlineExamQuestionChoiceInterface;
use App\Repositories\OnlineExamStudentAnswer\OnlineExamStudentAnswerInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Sliders\SlidersInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\StudentOnlineExamStatus\StudentOnlineExamStatusInterface;
use App\Repositories\StudentSubject\StudentSubjectInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use App\Repositories\Timetable\TimetableInterface;
use App\Repositories\Topics\TopicsInterface;
use App\Repositories\User\UserInterface;
use App\Rules\MaxFileSize;
use App\Services\CachingService;
use App\Services\FeaturesService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

class StudentApiController extends Controller {
    private StudentInterface $student;
    private UserInterface $user;
    private AssignmentInterface $assignment;
    private AssignmentSubmissionInterface $assignmentSubmission;
    private FilesInterface $files;
    private CachingService $cache;
    private StudentSubjectInterface $studentSubject;
    private TimetableInterface $timetable;
    private ExamInterface $exam;
    private ExamResultInterface $examResult;
    private LessonsInterface $lesson;
    private TopicsInterface $lessonTopic;
    private AttendanceInterface $attendance;
    private HolidayInterface $holiday;
    private SessionYearInterface $sessionYear;
    private SubjectTeacherInterface $subjectTeacher;
    private AnnouncementInterface $announcement;
    private OnlineExamInterface $onlineExam;
    private StudentOnlineExamStatusInterface $studentOnlineExamStatus;
    private OnlineExamQuestionChoiceInterface $onlineExamQuestionChoice;
    private OnlineExamStudentAnswerInterface $onlineExamStudentAnswer;
    private SlidersInterface $sliders;
    private FeaturesService $featureService;

    public function __construct(StudentInterface $student, UserInterface $user, AssignmentInterface $assignment, AssignmentSubmissionInterface $assignmentSubmission, FilesInterface $files, CachingService $cache, StudentSubjectInterface $studentSubject, TimetableInterface $timetable, ExamInterface $exam, ExamResultInterface $examResult, LessonsInterface $lesson, TopicsInterface $lessonTopic, AttendanceInterface $attendance, HolidayInterface $holiday, SessionYearInterface $sessionYear, SubjectTeacherInterface $subjectTeacher, AnnouncementInterface $announcement, OnlineExamInterface $onlineExam, StudentOnlineExamStatusInterface $studentOnlineExamStatus, OnlineExamQuestionChoiceInterface $onlineExamQuestionChoice, OnlineExamStudentAnswerInterface $onlineExamStudentAnswer, SlidersInterface $sliders, FeaturesService $featuresService) {
        $this->student = $student;
        $this->user = $user;
        $this->assignment = $assignment;
        $this->assignmentSubmission = $assignmentSubmission;
        $this->files = $files;
        $this->cache = $cache;
        $this->studentSubject = $studentSubject;
        $this->timetable = $timetable;
        $this->exam = $exam;
        $this->examResult = $examResult;
        $this->lesson = $lesson;
        $this->lessonTopic = $lessonTopic;
        $this->attendance = $attendance;
        $this->holiday = $holiday;
        $this->sessionYear = $sessionYear;
        $this->subjectTeacher = $subjectTeacher;
        $this->announcement = $announcement;
        $this->onlineExam = $onlineExam;
        $this->studentOnlineExamStatus = $studentOnlineExamStatus;
        $this->onlineExamQuestionChoice = $onlineExamQuestionChoice;
        $this->onlineExamStudentAnswer = $onlineExamStudentAnswer;
        $this->sliders = $sliders;
        $this->featureService = $featuresService;
    }


    #[NoReturn] public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'gr_number' => 'required',
            'password' => 'required',
            'school_code' => 'required|alpha_num',
        ], [
            'gr_number.required' => 'The GR number is required.',
            'password.required' => 'The password is required.',
            'school_code.required' => 'The school code is required.',
            'school_code.alpha_num' => 'The school code must contain only letters and numbers.',
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

        $user = User::withTrashed()
        ->where('email', $request->gr_number)
        ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->trashed()) {
                // User is soft-deleted, handle accordingly
                ResponseService::errorResponse(trans('your_account_has_been_deactivated_please_contact_admin'), null, config('constants.RESPONSE_CODE.INACTIVATED_USER'));
            }
        }

        if (Auth::attempt(['email' => $request->gr_number, 'password' => $request->password, 'status' => 1])) {
            //Here Email Field is referenced as a GR Number for Student
            $auth = Auth::user();
            // Check role
            // $auth->assignRole('Student');
            // if (!$auth->hasRole('Student')) {
            //     ResponseService::errorResponse('Invalid Login Credentials', null, config('constants.RESPONSE_CODE.INVALID_LOGIN'));
            // }
            // Check school status is activated or not
            if ($auth->school->status == 0) {
                ResponseService::errorResponse('Your account has been deactivated', null, config('constants.RESPONSE_CODE.INVALID_LOGIN'));
            }
            $token = $auth->createToken($auth->first_name)->plainTextToken;
            $user = $auth->load(['student.class_section' => function ($q) {
                $q->with('section', 'class', 'medium');
            }, 'student.guardian', 'school']);

            // child.user', 'child.class_section.class', 'child.class_section.section', 'child.class_section.medium', 'child.user.school
            // $user = $auth->load(['student.guardian.child' => function($q) {
            //     $q->with('user.school','class_section.class','class_section.section', 'class_section.medium');
            // }]);

            if ($request->fcm_id) {
                $auth->fcm_id = $request->fcm_id;
                $auth->save();
            }
            ResponseService::successResponse('User logged-in!', new UserDataResource($user), ['error' => false, 'token' => $token]);
        }
        ResponseService::errorResponse('Invalid Login Credentials', null, config('constants.RESPONSE_CODE.INVALID_LOGIN'));
    }

    public function forgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'gr_no' => 'required',
            'dob'   => 'required|date',
            'school_code' => 'required'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $schoolCode = $request->school_code;
            if ($schoolCode) {
                $school = School::on('mysql')->where('code',$schoolCode)->first();

                if ($school) {
                    DB::setDefaultConnection('school');
                    Config::set('database.connections.school.database', $school->database_name);
                    DB::purge('school');
                    DB::connection('school')->reconnect();
                    DB::setDefaultConnection('school');
                
                    $user = $this->user->builder()->whereHas('student', function ($query) use ($request) {
                        $query->where('admission_no', $request->gr_no);
                    })->whereDate('dob', '=', date('Y-m-d', strtotime($request->dob)))->first();
        
                    if ($user) {
                        /*NOTE : Revert this if needed */
                        //$this->user->update($user->id, ['reset_request' => 1,'school_id' => $user->school_id]);
                        $this->user->update($user->id, ['reset_request' => 1, 'school_id' => $user->school_id]);
                        ResponseService::successResponse("Request Send Successfully");
                    } else {
                        ResponseService::errorResponse("Invalid user Details", null, config('constants.RESPONSE_CODE.INVALID_USER_DETAILS'));
                    }
                } else {
                    return response()->json(['message' => 'Invalid school code'], 400);
                }
            } else {
                return response()->json(['message' => 'Unauthenticated'], 400);
            }   
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function classSubjects(Request $request) {
        try {
            $user = $request->user();
            $subjects = $user->student->currentSemesterClassSubjects();
            ResponseService::successResponse('Class Subject Fetched Successfully.', $subjects);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function subjects(Request $request) {
        try {
            $user = $request->user();
            $subjects = $user->student->currentSemesterSubjects();
            ResponseService::successResponse('Student Subject Fetched Successfully.', $subjects);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function selectSubjects(Request $request) {
        $validator = Validator::make($request->all(), [
            'subject_group.*.id'               => 'required',
            'subject_group.*.class_subject_id' => 'required|array',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear(); // Default Session Year From Cache
            $student = $request->user()->student; // Logged in Student Details
            $classSection = $student->class_section; // Class Section Data
            $studentSubject = array();

            // Loop to Subject Group
            foreach ($request->subject_group as $subjectGroup) {
                // Loop to Subject's ID
                foreach ($subjectGroup['class_subject_id'] as $classSubjectId) {
                    // Create Two Dimensional Student Subject Array
                    $studentSubject[] = array(
                        'student_id'       => $student->user_id,
                        'class_subject_id' => $classSubjectId,
                        'class_section_id' => $classSection->id,
                        'session_year_id'  => $sessionYear->id,
                    );
                }
            }

            // Update OR Create Data
            $this->studentSubject->upsert($studentSubject, ['student_id', 'class_subject_id', 'class_section_id', 'session_year_id'], ['student_id', 'class_subject_id', 'class_section_id', 'session_year_id',]);
            DB::commit();
            ResponseService::successResponse("Subject Selected Successfully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "StudentApiController :- selectSubject Method");
            ResponseService::errorResponse();
        }
    }

    public function getGuardianDetails(Request $request) {
        try {
            $student = $request->user()->student->load(['guardian']);
            $data = array(
                'guardian' => (!empty($student->guardian)) ? $student->guardian : (object)[]
            );
            ResponseService::successResponse("Guardian Details Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getTimetable(Request $request) {
        try {
            $student = $request->user()->student;
            $timetable = $this->timetable->builder()->where('class_section_id', $student->class_section_id)->with('subject_teacher.subject:id,name,type,code,bg_color,image', 'subject_teacher.teacher:id,first_name,last_name')
            ->with('subject')
            ->orderBy('day')->orderBy('start_time')->get();
            ResponseService::successResponse("Timetable Fetched Successfully", new TimetableCollection($timetable));
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function getLessons(Request $request) {
        $validator = Validator::make($request->all(), [
            'lesson_id'        => 'nullable|numeric',
            'class_subject_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = $request->user()->student;
            $lessonQuery = $this->lesson->builder()->where(['class_section_id' => $student->class_section_id, 'class_subject_id' => $request->class_subject_id])->with('topic', 'file');
            if ($request->lesson_id) {
                $lessonQuery->where('id', $request->lesson_id);
            }
            $lessonData = $lessonQuery->get();

            ResponseService::successResponse("Lessons Fetched Successfully", $lessonData);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getLessonTopics(Request $request) {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|numeric',
            'topic_id'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $lessonTopicQuery = $this->lessonTopic->builder()->where('lesson_id', $request->lesson_id)->with('file');
            if ($request->topic_id) {
                $lessonTopicQuery->where('id', $request->topic_id);
            }
            $lessonTopicData = $lessonTopicQuery->get();
            ResponseService::successResponse("Topics Fetched Successfully", $lessonTopicData);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "StudentApiController :- getLessonTopics Method");
            ResponseService::errorResponse();
        }
    }

    public function getAssignments(Request $request) {
        $validator = Validator::make($request->all(), [
            'assignment_id'    => 'nullable|numeric',
            'class_subject_id' => 'nullable|numeric',
            'is_submitted'     => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            $student = $request->user()->student;
            $data = $this->assignment->builder()->where('class_section_id', $student->class_section_id)->with(['file', 'class_subject.subject', 'submission' => function ($query) use ($student) {
                $query->where('student_id', $student->user_id)->with('file');
            }]);
            if ($request->assignment_id) {
                $data->where('id', $request->assignment_id);
            }
            if ($request->class_subject_id) {
                $data->where('class_subject_id', $request->class_subject_id);
            }
            if (isset($request->is_submitted)) {
                if ($request->is_submitted) {
                    $data->whereHas('submission', function ($q) use ($student) {
                        $q->where('student_id', $student->user_id);
                    });
                } else {
                    $data->whereDoesntHave('submission', function ($q) use ($student) {
                        $q->where('student_id', $student->user_id);
                    });
                }
            }
            $data = $data->orderBy('id', 'desc')->paginate(15);
            ResponseService::successResponse("Assignments Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Student Api Controller -> getAssignment Method");
            ResponseService::errorResponse();
        }
    }

    public function submitAssignment(Request $request) {
        $file_upload_size_limit = $this->cache->getSystemSettings('file_upload_size_limit');
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|numeric',
            'subject_id'    => 'nullable|numeric',
            'files'         => 'required|array',
            'files.*'       => ['mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx,xml', new MaxFileSize($file_upload_size_limit) ]
        ],[
            'files.*' => trans('The file Uploaded must be less than :file_upload_size_limit MB.', [
                'file_upload_size_limit' => $file_upload_size_limit,  
            ]),
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            DB::beginTransaction();
            $assignmentSubmissionData = array();
            $student = $request->user()->student;
            $sessionYear = $this->cache->getDefaultSessionYear();

            $assignment = $this->assignment->builder()->where(['id' => $request->assignment_id, 'class_section_id' => $student->class_section_id])->first();
            $assignmentSubmissionQuery = $this->assignmentSubmission->builder()->where(['assignment_id' => $assignment->id, 'student_id' => $student->user_id])->first();
            if (empty($assignmentSubmissionQuery)) {
                $assignmentSubmissionData = array(
                    'assignment_id'   => $request->assignment_id,
                    'student_id'      => $student->user_id,
                    'session_year_id' => $sessionYear->id
                );
            } else if ($assignmentSubmissionQuery->status == 2 && $assignment->resubmission) {
                // if assignment submission is rejected and
                // Assignment has resubmission allowed then change the status to resubmitted
                $assignmentSubmissionData = array(
                    'id'     => $assignmentSubmissionQuery->id,
                    'status' => 3
                );
                // Check Old Files and Delete it
                if ($assignmentSubmissionQuery->file) {
                    foreach ($assignmentSubmissionQuery->file as $file) {
                        if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                            Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                        }
                    }
                }
                $assignmentSubmissionQuery->file()->delete();
            } else {
                ResponseService::errorResponse("You already have submitted your assignment.", null, config('constants.RESPONSE_CODE.ASSIGNMENT_ALREADY_SUBMITTED'));
            }
            $assignmentSubmission = $this->assignmentSubmission->updateOrCreate(['id' => $assignmentSubmissionData['id'] ?? null], $assignmentSubmissionData);

            //If File Exists
            if ($request->hasFile('files')) {
                $fileData = array(); // Empty FileData Array
                // Create A File Model Instance
                $assignmentSubmissionModelAssociate = $this->files->model()->modal()->associate($assignmentSubmission); // Get the Association Values of File with Assignment Submission
                foreach ($request->file('files') as $file_upload) {
                    // Create Temp File Data Array
                    $tempFileData = array(
                        'modal_type' => $assignmentSubmissionModelAssociate->modal_type,
                        'modal_id'   => $assignmentSubmissionModelAssociate->modal_id,
                        'file_name'  => $file_upload->getClientOriginalName(),
                        'type'       => 1,
                        'file_url'   => $file_upload
                    );
                    $fileData[] = $tempFileData; // Store Temp File Data in Multi-Dimensional File Data Array
                }
                $this->files->createBulk($fileData); // Store File Data
            }
            $submittedAssignment = $this->assignmentSubmission->builder()->where('id', $assignmentSubmission->id)->with('file')->get();
            DB::commit();
            ResponseService::successResponse("Assignments Submitted Successfully", $submittedAssignment);
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Student Api Controller -> submitAssignment Method");
            ResponseService::errorResponse();
        }
    }

    public function deleteAssignmentSubmission(Request $request) {
        $validator = Validator::make($request->all(), [
            'assignment_submission_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $student = $request->user()->student;
            $assignment_submission = AssignmentSubmission::where('id', $request->assignment_submission_id)->where('student_id', $student->id)->with('file')->first();

            if (!empty($assignment_submission) && $assignment_submission->status == 0) {
                foreach ($assignment_submission->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }
                $assignment_submission->file()->delete();
                $assignment_submission->delete();
                DB::commit();
                ResponseService::successResponse("Assignments Deleted Successfully");
            } else {
                ResponseService::errorResponse("You can not delete assignment");
            }
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Student Api Controller -> deleteAssignmentSubmission Method");
            ResponseService::errorResponse();
        }
    }

    public function getAttendance(Request $request) {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|numeric',
            'year'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = $request->user()->student;
            $sessionYear = $this->cache->getDefaultSessionYear();

            $attendance = $this->attendance->builder()->where(['student_id' => $student->user_id, 'session_year_id' => $sessionYear->id]);
            $holidays = $this->holiday->builder();
            $session_year_data = $this->sessionYear->findById($sessionYear->id);
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


            $data = ['attendance' => $attendance, 'holidays' => $holidays, 'session_year' => $session_year_data];

            ResponseService::successResponse("Attendance Details Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Student Api Controller -> getAttendance Method");
            ResponseService::errorResponse();
        }
    }

    public function getAnnouncements(Request $request) {
        $validator = Validator::make($request->all(), [
            'type'             => 'nullable|in:subject,noticeboard,class',
            'class_subject_id' => 'required_if:type,subject|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = $request->user()->student;
            $classSectionId = $student->class_section->id;
            $sessionYear = $this->cache->getDefaultSessionYear();
            if (isset($request->type) && $request->type == "subject") {
                // TODO : There might be some mistake in this code
                $table = $this->subjectTeacher->builder()->where(['class_section_id' => $student->class_section_id, 'class_subject_id' => $request->class_subject_id])->pluck('id');
                if ($table === null) {
                    ResponseService::errorResponse("Invalid Subject ID", null, config('constants.RESPONSE_CODE.INVALID_SUBJECT_ID'));
                }
            }

            $announcementData = $this->announcement->builder()->with('file', 'announcement_class')->where('session_year_id', $sessionYear->id);

            if (isset($request->type) && $request->type == "class") {
                $announcementData = $announcementData->whereHas('announcement_class', function ($query) use ($classSectionId) {
                    $query->where(['class_section_id' => $classSectionId, 'class_subject_id' => null]);
                });
            }

            if (isset($request->type) && $request->type == "subject") {
                $announcementData = $announcementData->whereHas('announcement_class', function ($query) use ($classSectionId, $request) {
                    $query->where(['class_section_id' => $classSectionId, 'class_subject_id' => $request->class_subject_id]);
                });
            }

            $announcementData = $announcementData->orderBy('id', 'desc')->paginate(15);
            ResponseService::successResponse("Announcement Details Fetched Successfully", $announcementData);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "StudentApiController :- getAnnouncements Method");
            ResponseService::errorResponse();
        }
    }

    public function getExamList(Request $request) {
        try {
            $studentId = Auth::user()->student->id;
            $student = $this->student->findById($studentId, ['*'], ['class_section']);
            $classId = $student->class_section->class_id;
            $currentSessionYear = $this->cache->getDefaultSessionYear();
            $exam = $this->exam->builder()
                ->where(['class_id' => $classId, 'session_year_id' => $currentSessionYear->id])
                ->whereHas('timetable', function ($query) {
                    $query->owner();
                })
                ->with([
                    'timetable' => function ($query) {
                        $query->owner()->selectRaw('* , SUM(total_marks) as total_marks')
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


            ResponseService::successResponse("", $exam_data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "StudentApiController :- getExamList Method");
            ResponseService::errorResponse();
        }
    }

    public function getExamDetails(Request $request) {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|nullable'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $studentData = Auth::user()->student;
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
            ResponseService::logErrorResponse($e, "StudentApiController :- getExamDetails Method");
            ResponseService::errorResponse();
        }
    }

    public function getExamMarks() {
        try {
            $studentData = Auth::user()->student->load('class_section.class:id,name', 'class_section.section:id,name', 'class_section.medium:id,name');

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
                    }]);
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
                            'passing_marks'    => $marks->timetable->passing_marks,
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

    public function getProfileDetails() {
        try {
            $studentData = Auth::user()->load(['student' => function ($query) {
                $query->with(['class_section' => function ($query) {
                    $query->with('section', 'class', 'medium', 'class.shift', 'class.stream');
                }], 'guardian');
            }, 'extra_student_details.form_field', 'school']);

            $data = array(
                'id'                => $studentData->id,
                'first_name'        => $studentData->first_name,
                'last_name'         => $studentData->last_name,
                'mobile'            => $studentData->mobile,
                'roll_number'       => $studentData->student->roll_number,
                'admission_no'      => $studentData->student->admission_no,
                'admission_date'    => $studentData->student->admission_date,
                'gender'            => $studentData->gender,
                'image'             => $studentData->image,
                'dob'               => $studentData->dob,
                'current_address'   => $studentData->current_address,
                'permanent_address' => $studentData->permanent_address,
                'occupation'        => $studentData->occupation,
                'status'            => $studentData->status,
                'fcm_id'            => $studentData->fcm_id,
                'school_id'         => $studentData->school_id,
                'session_year_id'   => $studentData->student->session_year_id,
                'email_verified_at' => $studentData->email_verified_at,
                'created_at'        => $studentData->created_at,
                'updated_at'        => $studentData->updated_at,
                'class_section'     => $studentData->student->class_section,
                'guardian'          => $studentData->student->guardian,
                'extra_details'     => $studentData->extra_student_details,
                'school'            => $studentData->school,
            );

            ResponseService::successResponse('Data Fetched Successfully', $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }

    }

    public function getSessionYear() {
        try {
            $sessionYear = $this->cache->getDefaultSessionYear();
            ResponseService::successResponse("Session Year Fetched Successfully", $sessionYear);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function getOnlineExamList(Request $request) {
        $validator = Validator::make($request->all(), [
            'class_subject_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;
            $classSectionId = $student->class_section->id;
            $sessionYear = $this->cache->getDefaultSessionYear();

            if (env('DEMO_MODE')) {
                $check_student_status = $this->studentOnlineExamStatus->builder()->where('student_id', $student->user_id);
                if ($check_student_status->count()) {
                    $status_id = $check_student_status->pluck('id');
                    $this->studentOnlineExamStatus->builder()->whereIn('id', $status_id)->delete();
                }

                $check_student_answers = $this->onlineExamStudentAnswer->builder()->where('student_id', $student->user_id);
                if ($check_student_answers->count()) {
                    $status_id = $check_student_answers->pluck('id');
                    $this->onlineExamStudentAnswer->builder()->whereIn('id', $status_id)->delete();
                }
            }

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
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamQuestions(Request $request) {
        $validator = Validator::make($request->all(), [
            'exam_id'  => 'required',
            'exam_key' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;
            // Checks Student Exam Status
            if ($this->studentOnlineExamStatus->builder()
                ->where(['online_exam_id' => $request->exam_id, 'student_id' => $student->user_id])
                ->exists()) {
                ResponseService::errorResponse('Student already attempted exam', null, config('constants.RESPONSE_CODE.STUDENT_ALREADY_ATTEMPTED_EXAM'));
            }

            $onlineExam = $this->onlineExam->builder();

            // Checks Exam Key
            if (!$onlineExam->where(['id' => $request->exam_id, 'exam_key' => $request->exam_key])->exists()) {
                ResponseService::errorResponse("Invalid Exam Key");
            }


            // Checks that Exam Started or Not
            if ($onlineExam->where('id', $request->exam_id)->whereDate('start_date', '>', now())->exists()) {
                ResponseService::successResponse('Exam not started yet');
            }

            // Add Student Status Entry
            $this->studentOnlineExamStatus->create([
                'student_id'     => $student->user_id,
                'online_exam_id' => $request->exam_id,
                'status'         => 1,
            ]);

            $onlineExamQuestionChoice = $this->onlineExamQuestionChoice->builder();

            // Get Total Questions
            $totalQuestions = $onlineExamQuestionChoice->where('online_exam_id', $request->exam_id)->count();

            // Get Questions Data
            $examQuestionData = $onlineExamQuestionChoice->where('online_exam_id', $request->exam_id)->with('questions')->get();
            $totalMarks = 0;
            $questionData = [];
            foreach ($examQuestionData as $examQuestion) {
                $totalMarks += $examQuestion->marks;

                // Make Options Array
                $optionData = $examQuestion->questions->options->map(function ($optionsData) {
                    return [
                        'id'        => $optionsData->id,
                        'option'    => htmlspecialchars_decode($optionsData->option),
                        'is_answer' => $optionsData->is_answer == 1 ? 1 : 0
                    ];
                });


                // Make Question Array Data
                $questionData[] = [
                    'id'       => $examQuestion->id,
                    'question' => htmlspecialchars_decode($examQuestion->questions->question),
                    'options'  => $optionData,
                    'marks'    => $examQuestion->marks,
                    'image'    => $examQuestion->questions->image_url,
                    'note'     => $examQuestion->questions->note,
                ];
            }
            ResponseService::successResponse('Data Fetched Successfully', $questionData, [
                'total_questions' => $totalQuestions, 'total_marks' => $totalMarks
            ]);

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function submitOnlineExamAnswers(Request $request) {
        $validator = Validator::make($request->all(), [
            'online_exam_id' => 'required|numeric',
            'answers_data'   => 'nullable|array',
            //            'answers_data.*.question_id'    => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;

            DB::beginTransaction();
            // Check Online Exam Exists or not
//            $onlineExamData = $this->onlineExam->findById($request->online_exam_id);
//            if (!$onlineExamData) {
//                ResponseService::errorResponse('Invalid online exam id');
//            }

            // Check if answers already submitted
            if ($this->onlineExamStudentAnswer->builder()->where(['student_id' => $student->id, 'online_exam_id' => $request->online_exam_id])->exists()) {
                ResponseService::errorResponse('Answers already submitted');
            }
            $answers = [];
            foreach ($request->answers_data ?? [] as $answerData) {

                // checks the question exists with provided exam id
                $questionChoice = $this->onlineExamQuestionChoice->findById($answerData['question_id']);
                if (!$questionChoice || $questionChoice->online_exam_id != $request->online_exam_id) {
                    ResponseService::errorResponse('Invalid question id');
                }

                foreach ($answerData['option_id'] as $optionId) {
                    // checks the option exists with provided question
//                    if (!$this->onlineExamQuestionOption->findById($optionId)->exists()) {
//                        ResponseService::errorResponse('Invalid option id');
//                    }

                    // add the data of answers
                    $answers[] = [
                        'student_id'     => $student->user_id,
                        'online_exam_id' => $request->online_exam_id,
                        'question_id'    => $answerData['question_id'],
                        'option_id'      => $optionId,
                        'submitted_date' => now()->toDateString(),
                    ];
                }
            }
            if (count($answers) > 0) {
                $this->onlineExamStudentAnswer->createBulk($answers);
            }
            // Update student exam status
            $this->studentOnlineExamStatus->updateOrCreate(
                ['student_id' => $student->user_id, 'online_exam_id' => $request->online_exam_id],
                ['status' => 2]
            );
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');

        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "StudentApiController submitOnlineExamAnswers Method");
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamResultList(Request $request) {
        $validator = Validator::make($request->all(), [
            'class_subject_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;
            $classSectionId = $student->class_section_id;
            $sessionYear = $this->cache->getDefaultSessionYear();

            // Get Online Exam Data Where Logged in Student have attempted data and Relation Data with Question Choice , Student's answer with user submitted question with question and its option
            $onlineExamData = $this->onlineExam->builder()
                ->when($request->class_subject_id, function ($query) use ($request) {
                    $query->where('class_subject_id', $request->class_subject_id);
                })
                ->where(['class_section_id' => $classSectionId, 'session_year_id' => $sessionYear->id])
                ->whereHas('student_attempt', function ($q) use ($student) {
                    $q->where('student_id', $student->user_id);
                })
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
                    $examSubmittedDate = date('Y-m-d', strtotime($student_answer['submitted_date']));
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
                    'obtained_marks'      => $totalObtainedMarks,
                    'total_marks'         => $totalMarks ?? "0",
                    'exam_submitted_date' => $examSubmittedDate ?? date('Y-m-d', strtotime($data['end_date']))
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
            ResponseService::logErrorResponse($e, "StudentApiController :- getOnlineExamList Method");
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamResult(Request $request) {
        $validator = Validator::make($request->all(), [
            'online_exam_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;

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
            ResponseService::logErrorResponse($e, "StudentApiController getOnlineExamResult Method");
            ResponseService::errorResponse();
        }
    }

    public function getOnlineExamReport(Request $request) {
        $validator = Validator::make($request->all(), [
            'class_subject_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;
            $sessionYear = $this->cache->getDefaultSessionYear();

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
            'class_subject_id' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $student = Auth::user()->student;
            $sessionYear = $this->cache->getDefaultSessionYear();

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

    public function getSchoolSettings() {
        try {
            if (Auth::user()) {
                $settings = $this->cache->getSchoolSettings();
                $sessionYear = $this->cache->getDefaultSessionYear();
                $semester = $this->cache->getDefaultSemesterData();
                $features = FeaturesService::getFeatures();
                $data = [
                    'school_id'    => Auth::user()->school_id,
                    'session_year' => $sessionYear,
                    'semester'     => $semester,
                    'settings'     => $settings,
                    'features'     => (count($features) > 0) ? $features : (object)[]
                ];
                ResponseService::successResponse('Settings Fetched Successfully.', $data);    
            } else {
                ResponseService::errorResponse(trans('your_account_has_been_deactivated_please_contact_admin'), null, config('constants.RESPONSE_CODE.INACTIVATED_USER'));
            }
            
            
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function getSliders() {
        try {
            $studentData = Auth::user();
            $data = $this->sliders->builder()->where('school_id', $studentData->school_id)->whereIn('type',[1,3])->get();
            ResponseService::successResponse("Sliders Fetched Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
