<?php

namespace App\Http\Controllers;

use App\Models\OnlineExamCommon;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ClassSubject\ClassSubjectInterface;
use App\Repositories\OnlineExam\OnlineExamInterface;
use App\Repositories\OnlineExamCommon\OnlineExamCommonInterface;
use App\Repositories\OnlineExamQuestion\OnlineExamQuestionInterface;
use App\Repositories\OnlineExamQuestionChoice\OnlineExamQuestionChoiceInterface;
use App\Repositories\OnlineExamQuestionOption\OnlineExamQuestionOptionInterface;
use App\Repositories\OnlineExamStudentAnswer\OnlineExamStudentAnswerInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\StudentOnlineExamStatus\StudentOnlineExamStatusInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class OnlineExamController extends Controller {
    private ClassSectionInterface $classSection;
    private SubjectTeacherInterface $subjectTeacher;
    private OnlineExamInterface $onlineExam;
    private OnlineExamQuestionChoiceInterface $onlineExamQuestionChoice;
    private OnlineExamQuestionInterface $onlineExamQuestion;
    private OnlineExamQuestionOptionInterface $onlineExamQuestionOption;
    private OnlineExamStudentAnswerInterface $onlineExamStudentAnswer;
    private CachingService $cache;
    private StudentInterface $student;
    private StudentOnlineExamStatusInterface $studentOnlineExamStatus;
    private ClassSubjectInterface $classSubjects;
    private SessionYearInterface $sessionYear;
    private OnlineExamCommonInterface $onlineExamCommon;


    public function __construct(ClassSectionInterface $classSection, SubjectTeacherInterface $subjectTeacher, OnlineExamInterface $onlineExam, OnlineExamQuestionChoiceInterface $onlineExamQuestionChoice, OnlineExamQuestionInterface $onlineExamQuestion, OnlineExamQuestionOptionInterface $onlineExamQuestionOption, OnlineExamStudentAnswerInterface $onlineExamStudentAnswer, CachingService $cachingService, StudentInterface $student, StudentOnlineExamStatusInterface $studentOnlineExamStatus, ClassSubjectInterface $classSubjects, SessionYearInterface $sessionYear, OnlineExamCommonInterface $onlineExamCommon) {
        $this->classSection = $classSection;
        $this->subjectTeacher = $subjectTeacher;
        $this->onlineExam = $onlineExam;
        $this->onlineExamQuestionChoice = $onlineExamQuestionChoice;
        $this->onlineExamQuestion = $onlineExamQuestion;
        $this->onlineExamQuestionOption = $onlineExamQuestionOption;
        $this->onlineExamStudentAnswer = $onlineExamStudentAnswer;
        $this->cache = $cachingService;
        $this->student = $student;
        $this->studentOnlineExamStatus = $studentOnlineExamStatus;
        $this->classSubjects = $classSubjects;
        $this->sessionYear = $sessionYear;
        $this->onlineExamCommon = $onlineExamCommon;
        
    }

    public function index() {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-list');
        $subjectTeachers = array();
        $classSubjects = array();
        if (Auth::user()->hasRole('School Admin')) {
            $classSubjects = $this->classSubjects->builder()->with('subject')->get();
        } else {
            $subjectTeachers = $this->subjectTeacher->builder()->with('subject')->get();    
        }
        $classSections = $this->classSection->builder()->with('class', 'class.stream', 'class.stream', 'section', 'medium')->get();

        $sessionYear = $this->sessionYear->builder()->pluck('name','id');
        $defaultSessionYear = $this->cache->getDefaultSessionYear();
        $rand_key = random_int(100000, 999999);

        return response(view('online_exam.index', compact('classSections', 'subjectTeachers','classSubjects','sessionYear','defaultSessionYear','rand_key')));
    }

    public function store(Request $request) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-create');
        $section_ids = is_array($request->class_section_id) ? $request->class_section_id : [$request->class_section_id];
        $request->validate([
            'class_section_id'      => 'required|array',
            'class_section_id.*'    => 'numeric',
            'subject_id' => 'required',
            'title'            => 'required',
            'exam_key'         => 'required|unique:online_exams,exam_key,NULL,id,school_id,' . Auth::user()->school_id,
            'duration'         => 'required|numeric|gte:1',
            'start_date'       => 'required',
            'end_date'         => 'required|after:start_date',
        ]);

        try {

            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            // $onlineExamData = array(
            //     'class_section_id' => $request->class_section_id,
            //     'class_subject_id' => $request->class_subject_id,
            //     'title'            => htmlspecialchars($request->title),
            //     'exam_key'         => $request->exam_key,
            //     'duration'         => $request->duration,
            //     'start_date'       => date('Y-m-d H:i:s', strtotime($request->start_date)),
            //     'end_date'         => date('Y-m-d H:i:s', strtotime($request->end_date)),
            //     'session_year_id'  => $sessionYear->id,
            // );

            $onlineExamList = [];
            foreach ($section_ids as $section_id) {
                $onlineExamList = array_merge($request->all(), ['class_section_id' => $section_id]);
            }

            // Get the related class subject for each section
            if ($request->class_section_id) {
                foreach ($request->class_section_id as $section_id) {
                    $classSection = $this->classSection->builder()->where('id', $section_id)->with(['class_subject' => function ($q) use ($request) {
                        $q->where('subject_id', $request->subject_id);
                    }])->first();
                }
            }

            // Associate class_subject_id with the lesson data
            $onlineExamList['exam_key'] = $request->exam_key;
            $onlineExamList['class_subject_id'] = $classSection->class_subject->id;
            $onlineExamList['session_year_id'] = $sessionYear->id;

            unset($onlineExamList['subject_id']);

            $onlineExam = $this->onlineExam->create($onlineExamList);

            $onlineExamCommonData = [];
            $onlineExamCommonData['online_exam_id'] = $onlineExam->id;

            // Create online_exam_common data for each section
            foreach ($section_ids as $section_id) {
                $classSection = $this->classSection->builder()->where('id', $section_id)->with('class')->first();
                $classSubjects = $this->classSubjects->builder()
                    ->where('class_id', $classSection->class->id)
                    ->where('subject_id', $request->subject_id)
                    ->first();
        
                $onlineExamCommonData['class_section_id'] = $section_id;
                $onlineExamCommonData['class_subject_id'] = $classSubjects->id;
                $this->onlineExamCommon->create($onlineExamCommonData);
            }
            
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Controller -> Store method");
            ResponseService::errorResponse();
        }
    }


    public function show() {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');
        $showDeleted = request('show_deleted');
        $subject_id = request('subject_id');
        $session_year_id = request('session_year_id');

        $sql = $this->onlineExam->builder()->with('class_section', 'class_subject.subject', 'question_choice','online_exam_commons')
            //search query
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")->orWhere('title', 'LIKE', "%$search%")->orWhere('exam_key', 'LIKE', "%$search%")->orWhere('duration', 'LIKE', "%$search%")->orWhere('start_date', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")->orWhere('end_date', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")->orWhereHas('class_subject.subject', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%")->orWhere('type', 'LIKE', "%$search%");
                        });
                    });
                });
            })
            ->when(!empty($showDeleted), function ($query) {
                $query->onlyTrashed();
            })
            ->when(request('class_section_id') != null, function ($query) {
                // $query->where('class_section_id', request('class_section_id'));
                $class_id = request('class_section_id');
                $query->whereHas('online_exam_commons', function ($q) use ($class_id) {
                    $q->where('class_section_id', $class_id);
                });
            })
            ->when(request('class_subject_id') != null, function ($query) {
                $query->where('class_subject_id', request('class_subject_id'));
            })
            ->when($subject_id != null, function($q) use($subject_id) {
                $q->where('class_subject_id',$subject_id);
            })
            ->when($session_year_id, function($q) use($session_year_id) {
                $q->where('session_year_id',$session_year_id);
            });

        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);

        $total = $sql->count();
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '';
            $onlineExamCommons = $row->online_exam_commons->map(function ($common) {
                return $common->class_section ? $common->class_section->full_name : null;
            });
            
            $onlineExamCommons->filter()->map(function ($name) {
                return "{$name},";
            })->toArray();

            if ($showDeleted) {
                //Show Restore and Hard Delete Buttons
                $operate .= BootstrapTableService::menuRestoreButton('restore',route('online-exam.restore', $row->id));

                $operate .= BootstrapTableService::menuTrashButton('delete',route('online-exam.trash', $row->id));


            } else {
                if (Auth::user()->can('online-exam-result-list')) {
                    $operate .= BootstrapTableService::menuButton('Result',route('online-exam.result.index', ['id' => $row->id]),[],[]);
                }
                if (Auth::user()->can('online-exam-list')) {
                    $operate .= BootstrapTableService::menuButton('add_questions',route('online-exam.add.questions.index', $row->id),[],[]);
                    $operate .= BootstrapTableService::menuEditButton('edit',route('online-exam.update', $row->id));                    
                    $operate .= BootstrapTableService::menuDeleteButton('delete',route('online-exam.destroy', $row->id));
                }
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['class_section_with_medium'] =  $onlineExamCommons;
            $tempRow['subject_name'] = $row->subject_with_name;
            $tempRow['title'] = htmlspecialchars_decode($row->title);
            $tempRow['start_date'] = date('Y-m-d H:i', strtotime($row->start_date));
            $tempRow['start_date_db'] = $row->start_date;
            $tempRow['end_date'] = date('Y-m-d H:i', strtotime($row->end_date));
            $tempRow['end_date_db'] = $row->end_date;
            $tempRow['total_questions'] = $row->question_choice->count();
            // $tempRow['operate'] = $operate;
            $tempRow['operate'] = BootstrapTableService::menuItem($operate);
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(Request $request, $id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-edit');
        $validator = Validator::make($request->all(), [
            'edit_title'      => 'required',
            'edit_exam_key'   => 'required|numeric',
            'edit_duration'   => 'required|numeric|gte:1',
            'edit_start_date' => 'required|date',
            'edit_end_date'   => 'required|date'
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $this->onlineExam->update($id, array(
                'title'      => $request->edit_title,
                'exam_key'   => $request->edit_exam_key,
                'duration'   => $request->edit_duration,
                'start_date' => $request->edit_start_date,
                'end_date'   => $request->edit_end_date,
            ));
            DB::commit();
            ResponseService::successResponse("Data Updated Successfully");
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-delete');
        try {
            DB::beginTransaction();
            $this->onlineExam->deleteById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Controller -> Delete method");
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-delete');
        try {
            $this->onlineExam->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-delete');
        try {
            $this->onlineExam->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Online Exam Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function addQuestionIndex($id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-list');
        $onlineExam = $this->onlineExam->findById($id, ['*'], ['class_section', 'class_subject']);

        $examQuestions = $this->onlineExamQuestionChoice->builder()->where('online_exam_id', $id)->with('online_exam', 'questions')->get();
        return response(view('online_exam.exam_questions', compact('onlineExam', 'examQuestions')));
    }

    public function storeExamQuestionChoices(Request $request) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-create');
        $request->validate([
            'question'             => 'required',
            'option_data.*.option' => 'required',
            'answer.*'             => 'required',
            'image'                => 'nullable|mimes:jpeg,png,jpg|image|max:3048',
        ]);

        try {
            DB::beginTransaction();
            $currentSemester = $this->cache->getDefaultSemesterData();
            $isClassSemester = $this->classSection->findById($request->class_section_id, ['*'], ['class'])->class->include_semesters;

            $onlineExamQuestionData = array(
                'class_section_id' => $request->class_section_id,
                'class_subject_id' => $request->class_subject_id,
                'semester_id'      => $isClassSemester ? $currentSemester->id : null,
                'question'         => htmlspecialchars($request->question),
                'image_url'        => $request->image,
                'note'             => $request->note,
                'last_edited_by'   => Auth::user()->id,
            );
            $onlineExamQuestion = $this->onlineExamQuestion->create($onlineExamQuestionData);

            $onlineExamOptionData = array();
            foreach ($request->option_data as $key => $optionValue) {
                $onlineExamOptionData[$key] = array(
                    'question_id' => $onlineExamQuestion->id,
                    'option'      => htmlspecialchars($optionValue['option']),
                    'is_answer'   => 0, // Initialize is_answer to 0
                );
                foreach ($request->answer as $answerValue) {
                    if ($optionValue['number'] == $answerValue) {
                        $onlineExamOptionData[$key]['is_answer'] = 1; // Set is_answer to 1 if a match is found
                        break; // Break the loop as we've found a match
                    }
                }
            }
            $this->onlineExamQuestionOption->createBulk($onlineExamOptionData);
            DB::commit();

            ResponseService::successResponse('Data Stored Successfully', array(
                'exam_id'     => $request->online_exam_id,
                'question_id' => $onlineExamQuestion->id,
                'question'    => "<textarea id='qc" . $onlineExamQuestion->id . "'>" . htmlspecialchars_decode($onlineExamQuestion->question) . "</textarea><script>setTimeout(() => {equation_editor = CKEDITOR.inline('qc" . $onlineExamQuestion->id . "', { skin:'moono',extraPlugins: 'mathjax', mathJaxLib: 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML', readOnly:true, }); },1000);</script>"
            ));
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Controller -> storeExamQuestionChoices method");
            ResponseService::errorResponse();
        }
    }

    public function getClassQuestions($onlineExamId) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-create');

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');

        $onlineExamData = $this->onlineExam->findById($onlineExamId);
        $excludeQuestionId = $this->onlineExamQuestionChoice->builder()->where('online_exam_id', $onlineExamId)->pluck('question_id');

        $sql = $this->onlineExamQuestion->builder()->with('class_section', 'class_subject', 'options')->where(['class_section_id' => $onlineExamData->class_section_id, 'class_subject_id' => $onlineExamData->class_subject_id])->whereNotIn('id', $excludeQuestionId)
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")
                            ->orWhere('question', 'LIKE', "%$search%")
                            ->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                            ->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                            ->orWhereHas('class_subject', function ($q) use ($search) {
                                $q->whereHas('class', function ($c) use ($search) {
                                    $c->where('name', 'LIKE', "%$search%")->orWhereHas('medium', function ($m) use ($search) {
                                        $m->where('name', 'LIKE', "%$search%");
                                    });
                                })->orWhereHas('subject', function ($s) use ($search) {
                                    $s->where('name', 'LIKE', "%$search%")->orWhere('type', 'LIKE', "%$search%");
                                });
                            })->orWhereHas('options', function ($p) use ($search) {
                                $p->where('option', 'LIKE', "%$search%");
                            });
                    });
                });
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {

            $tempRow['question_id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['class_section_name'] = $row->class_section_with_medium;
            $tempRow['class_subject_id'] = $row->class_subject_id;
            $tempRow['subject_name'] = $row->subject_with_name;
            $tempRow['question'] = "<div class='equation-editor-inline' contenteditable=false name='qc" . $row->id . "'>" . htmlspecialchars_decode($row->question) . "</div>";
            $tempRow['question_row'] = htmlspecialchars_decode($row->question);

            $tempRow['options'] = array();
            $tempRow['answers'] = array();

            foreach ($row->options as $options) {
                $option_data = array(
                    'id'     => $options->id,
                    'option' => "<div class='equation-editor-inline' contenteditable=false>" . htmlspecialchars_decode($options->option) . "</div>", 'option_row' => htmlspecialchars_decode($options->option)
                );
                $tempRow['options'][] = $option_data;
                if ($options->is_answer) {
                    $answer_data = array(
                        'id'     => $options->id,
                        'answer' => "<div class='equation-editor-inline' contenteditable=false>" . htmlspecialchars_decode($options->option) . "</div>",
                    );
                    $tempRow['answers'][] = $answer_data;
                }
            }

            $tempRow['image'] = $row->image_url;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function storeQuestionsChoices(Request $request) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-create');
        $request->validate([
            'exam_id'                        => 'required',
            'assign_questions.*.question_id' => 'required',
            'assign_questions.*.marks'       => 'required|numeric'
        ], [
            'assign_questions.*.marks.required' => trans('marks_are_required')
        ]);

        try {
            DB::beginTransaction();

            $onlineExamQuestionChoiceData = array();
            foreach ($request->assign_questions as $question) {
                $onlineExamQuestionChoiceData[] = array(
                    'id'             => $question['edit_id'] ?? null,
                    'online_exam_id' => $request->exam_id,
                    'question_id'    => $question['question_id'],
                    'marks'          => $question['marks']
                );
            }
            $this->onlineExamQuestionChoice->upsert($onlineExamQuestionChoiceData, ["id"], ['online_exam_id', 'question_id', 'marks']);

            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Controller -> storeQuestionsChoices method");
            ResponseService::errorResponse();
        }
    }

    public function removeQuestionsChoices($id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-create');
        try {
            $student_submitted_answers = $this->onlineExamStudentAnswer->builder()->where('question_id', $id)->count();
            if ($student_submitted_answers) {
                ResponseService::errorResponse("cannot delete because data is associated with other data");
            } else {
                DB::beginTransaction();
                $this->onlineExamQuestionChoice->deleteById($id);
                DB::commit();
                ResponseService::successResponse('Data Deleted Successfully');
            }
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Controller -> removeQuestionsChoices method");
            ResponseService::errorResponse();
        }
    }

    public function onlineExamResultIndex($id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-result-list');
        $onlineExamData = $this->onlineExam->findById($id, ['*'], ['class_subject', 'class_section']);
        return response(view('online_exam.online_exam_result', compact('onlineExamData')));
    }

    // To Be Optimised With API

    public function showOnlineExamResult($id) {
        ResponseService::noPermissionThenRedirect('online-exam-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');


        $sql = $this->studentOnlineExamStatus->builder()->with('student_data', 'online_exam.question_choice')->where(['online_exam_id' => $id, 'status' => 2]);
        $total = $sql->count();
        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $student_attempt) {
            //get the total marks and obtained marks
//            $total_obtained_marks = 0;
//            $total_marks = 0;

            $exam_submitted_question_ids = $this->onlineExamStudentAnswer->builder()->where(['student_id' => $student_attempt->student_id, 'online_exam_id' => $student_attempt->online_exam_id])->pluck('question_id');

            $question_ids = $this->onlineExamQuestionChoice->builder()->whereIn('id', $exam_submitted_question_ids)->pluck('question_id');


            $exam_attempted_answers = $this->onlineExamStudentAnswer->builder()->where(['student_id' => $student_attempt->student_id, 'online_exam_id' => $student_attempt->online_exam_id])->pluck('option_id');

            //removes the question id of the question if one of the answer of particular question is wrong
            foreach ($question_ids as $question_id) {
                $check_questions_answers_exists = $this->onlineExamQuestionOption->builder()->where(['question_id' => $question_id, 'is_answer' => 1])->whereNotIn('id', $exam_attempted_answers)->count();
                if ($check_questions_answers_exists) {
                    unset($question_ids[array_search($question_id, $question_ids->toArray())]);
                }
            }

            $exam_correct_answers_question_id = $this->onlineExamQuestionOption->builder()->where(['is_answer' => 1])->whereIn('id', $exam_attempted_answers)->whereIn('question_id', $question_ids)->pluck('question_id');

            // get the data of only attempted data
            $total_obtained_marks = $this->onlineExamQuestionChoice->builder()->select(DB::raw("sum(marks)"))->where('online_exam_id', $student_attempt->online_exam_id)->whereIn('question_id', $exam_correct_answers_question_id)->first();
            $total_obtained_marks = $total_obtained_marks['sum(marks)'];
            $total_marks = $this->onlineExamQuestionChoice->builder()->select(DB::raw("sum(marks)"))->where('online_exam_id', $student_attempt->online_exam_id)->first();
            $total_marks = $total_marks['sum(marks)'];

            $tempRow['student_id'] = $student_attempt->student_id;
            $tempRow['no'] = $no++;
            $tempRow['student_name'] = $student_attempt->student_data->full_name;
            if ($total_obtained_marks) {
                $tempRow['marks'] = $total_obtained_marks . ' / ' . $total_marks;
            } else {
                $total_obtained_marks = 0;
                $tempRow['marks'] = $total_obtained_marks . ' / ' . $total_marks;
            }
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}
