<?php

namespace App\Http\Controllers;

use App\Repositories\ClassSchool\ClassSchoolInterface;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ClassSubject\ClassSubjectInterface;
use App\Repositories\ClassTeachers\ClassTeachersInterface;
use App\Repositories\Semester\SemesterInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use App\Repositories\Timetable\TimetableInterface;
use App\Repositories\User\UserRepository;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClassSectionController extends Controller {
    private ClassSchoolInterface $class;
    private ClassSectionInterface $classSection;
    private UserRepository $user;
    private ClassSubjectInterface $classSubjects;
    private ClassTeachersInterface $classTeachers;
    private SubjectTeacherInterface $subjectTeachers;
    private TimetableInterface $timetable;
    private SemesterInterface $semester;
    private CachingService $cache;

    public function __construct(ClassSchoolInterface $class, ClassSectionInterface $classSection, UserRepository $userRepository, ClassSubjectInterface $classSubjects, ClassTeachersInterface $classTeachers, SubjectTeacherInterface $subjectTeachers, TimetableInterface $timetable, SemesterInterface $semester, CachingService $cache) {
        $this->class = $class;
        $this->classSection = $classSection;
        $this->user = $userRepository;
        $this->classSubjects = $classSubjects;
        $this->classTeachers = $classTeachers;
        $this->subjectTeachers = $subjectTeachers;
        $this->timetable = $timetable;
        $this->semester = $semester;
        $this->cache = $cache;
    }


    public function index() {
        ResponseService::noPermissionThenRedirect('class-section-list');
        $classes = $this->class->all(['id', 'name', 'medium_id'], ['stream', 'medium']);
        return response(view('class-section.index', compact('classes')));
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('class-section-list');
        $offset = request('offset', 0);
        $limit = request('limit', 5);

        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $semesters = $this->semester->builder()->get();

        // $sql = $this->classSection->builder()->with(['class.stream', 'section', 'medium', 'class_teachers.teacher', 'subject_teachers', 'subject_teachers.teacher', 'subject_teachers.semester:id,name','subject_teachers.class_subject'=> function ($q){
        //         $q->whereNull('deleted_at')->with('semester')
        //         ->with('subject')->owner();
        //     }]);
        
        $sql = $this->classSection->builder()->with(['class.stream', 'section', 'medium', 'class_teachers.teacher', 'class.shift' , 'subject_teachers'=> function ($q) {
            $q->with('teacher')
            ->has('class_subject')->with(['class_subject' => function($q) {
                $q->whereNull('deleted_at')->with('semester');
            }])
            ->with('subject')->owner();
        }])->where(function ($query) use ($request) {
            if (!empty($request->class_id)) {
                $query->where('class_id', $request->class_id);
            }
        
            if (!empty($request->section_id)) {
                $query->where('section_id', $request->section_id);
            }
        
            if (!empty($request->medium_id)) {
                $query->where('medium_id', $request->medium_id);
            }
        
            if (!empty($request->teacher_id)) {
                $query->whereHas('class_teachers', function ($q) use ($request) {
                    $q->where('teacher_id', $request->teacher_id);
                });
            }
        
            if (!empty($request->subject_id)) {
                $query->whereHas('subject_teachers', function ($q) use ($request) {
                    $q->where('subject_id', $request->subject_id);
                });
            }
        
            if (!empty($request->class_subject_id)) {
                $query->whereHas('subject_teachers.class_subject', function ($q) use ($request) {
                    $q->where('id', $request->class_subject_id);
                });
            }
        
        })->when(!empty($showDeleted), function ($q) {
            $q->onlyTrashed();
        });

        //Show only Trashed Data
        if (!empty($request->show_deleted)) {
            $sql = $sql->onlyTrashed();
        }
        if (!empty($request->search)) {
            $search = $request->search;
            $sql->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                ->orWhereHas('class', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->orWhereHas('section', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->orWhereHas('medium', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->orWhereHas('class_teachers.teacher', function ($q) use ($search) {
                    $q->whereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'");
                })->orWhereHas('subject_teachers.subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            });
        }
        if (!empty($request->class_id)) {
            $sql = $sql->where('class_id', $request->class_id);
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '';
            if ($request->show_deleted) {
                //Show Restore and Hard Delete Buttons
                if (Auth::user()->can('class-section-delete')) {
                    $operate .= BootstrapTableService::restoreButton(route('class-section.restore', $row->id));
                    $operate .= BootstrapTableService::trashButton(route('class-section.trash', $row->id));
                }
            } else {
                //Show Edit and Soft Delete Buttons
                if (Auth::user()->can('class-section-edit')) {
                    $operate .= BootstrapTableService::editButton(route('class-section.edit', $row->id), false);
                }
                if (Auth::user()->can('class-section-delete')) {
                    $operate .= BootstrapTableService::deleteButton(route('class-section.destroy', $row->id));
                }
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['class_teachers_list'] = $row->class_teachers->pluck('teacher.full_name');
            $tempRow['subject_teachers_list'] = $row->subject_teachers->pluck('subject_with_name');

            $tempRow['current_sem_subject_teachers_list'] = array();
            if ($row->class->include_semesters) {
                
                $tempRow['subject_teachers_with_semester'] = array();
                foreach ($semesters as $semesterData) {
                    $tempRow['subject_teachers'] = array();
                    $teacherWithSubjectName = array();

                    foreach ($row->subject_teachers as $teacherData) {
                        if ($semesterData->current) {
                            if ($teacherData->class_subject->semester_id == $semesterData->id) {
                                $tempRow['current_sem_subject_teachers_list'][] = $teacherData->subject_with_name;
                            }
                        }

                        if ($teacherData->class_subject->semester_id == $semesterData->id) {
                            $teacherWithSubjectName[] = array(
                                'teacher_name' => $teacherData->teacher->full_name,
                                'subject_name' => $teacherData->subject_with_name,
                            );
                        }
                    }

                    $tempRow['subject_teachers_with_semester'][] = array(
                        'semester_id'      => $semesterData->id,
                        'semester_name'    => $semesterData->name,
                        'subject_teachers' => $teacherWithSubjectName,
                    );
                }
            }

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function edit($id) {
        ResponseService::noPermissionThenRedirect('class-section-edit');
        $semesters = $this->semester->all();
        $classSection = $this->classSection->findById($id, ['*'], ['class', 'class.stream', 'section', 'medium', 'subjects', 'class_teachers']);
        $teachers = $this->user->builder()->role('Teacher')->get();
        $classSubjects = $this->classSubjects->builder()->where('class_id', $classSection->class_id)->with(['subjectTeachers' => function ($q) use ($id) {
            $q->where('class_section_id', $id)->get();
        }, 'subject'])->get();

        return response(view('class-section.edit', compact('classSection', 'teachers', 'id', 'classSubjects', 'semesters')));
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenRedirect('class-section-edit');
        try {
            DB::beginTransaction();

            // Initialize Empty Arrays
            $classTeachersData = array();
            $subjectTeachersData = array();

            // Check that Class Section has semester or not
            $isClassSemester = $this->classSection->findById($id, ['*'], ['class'])->class->include_semesters;

            if (!empty($request->class_teacher_id)) {
                // Loop on the Class Teacher and add Data as Multi Dimensional Array in classTeachersData Array
                foreach ($request->class_teacher_id as $teacherId) {
                    $classTeachersData[] = array(
                        "class_section_id" => $id,
                        "teacher_id"       => $teacherId,
                    );
                    $this->user->findById($teacherId)->givePermissionTo(['class-teacher', 'exam-upload-marks', 'exam-result', 'attendance-list']);

                }
                // Update or Insert Data in Class Teachers on the basis of Class Section ID And TeacherID
                $this->classTeachers->upsert($classTeachersData, ['class_section_id', 'teacher_id'], ['created_at', 'updated_at']);

            }


            if (!empty($request->subject_teachers)) {
                // Loop on the Subject Teacher and do nested loop on teacher id and add Data in subjectTeachersData Array
                foreach ($request->subject_teachers as $subjectTeachers) {
                    $subjectTeachers = (object)$subjectTeachers;
                    if (!empty($subjectTeachers->teacher_user_id)) {
                        foreach ($subjectTeachers->teacher_user_id as $teacherId) {
                            $subjectTeachersData[] = array(
                                "class_section_id" => $id,
                                "teacher_id"       => $teacherId,
                                "subject_id"       => $subjectTeachers->subject_id,
                                "class_subject_id" => $subjectTeachers->class_subject_id,
                            );

                            $this->user->findById($teacherId)->givePermissionTo(['exam-upload-marks', 'exam-result']);
                        }
                    }
                }

                // Update or Insert Data in Class Teachers on the basis of Class Section ID , TeacherID
                $this->subjectTeachers->upsert($subjectTeachersData, ['class_section_id', 'teacher_id', 'subject_id', 'semester_id'], ['class_subject_id']);

                /*
                IF New Subject Teacher is added then Reflect that Subject teacher in Timetable
                if there are any timetable subjects which don't have any Teacher Assigned
                */
                $noTeacherAssignedToTimetable = $this->timetable->builder()->where(['type' => 'Lecture', 'subject_teacher_id' => null])->get();
                if (!empty($noTeacherAssignedToTimetable)) {
                    $updateTimetable = [];
                    $subjectTeachers = $this->subjectTeachers->builder()->where('class_section_id', $id)->get();
                    foreach ($noTeacherAssignedToTimetable as $timetable) {
                        $subjectTeacher = $subjectTeachers->first(function ($data) use ($timetable) {
                            return $data->subject_id === $timetable->subject_id;
                        });
                        if (!empty($subjectTeacher)) {
                            $updateTimetable[] = [
                                'id'                 => $timetable->id,
                                'subject_teacher_id' => $subjectTeacher->id
                            ];
                        }
                    }

                    if (!empty($updateTimetable)) {
                        $this->timetable->upsert($updateTimetable, ['id'], ['subject_teacher_id']);
                    }
                }
            }


            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('class-section-delete');
        try {
//            // check whether the id in $delete_class_section is associated with other data .
//            $assignments = Assignment::whereIn('class_section_id', $id)->count();
//            $attendances = Attendance::whereIn('class_section_id', $id)->count();
//            $exam_result = ExamResult::whereIn('class_section_id', $id)->count();
//            $lessons = Lesson::whereIn('class_section_id', $id)->count();
//            $student_session = PromoteStudents::whereIn('class_section_id', $id)->count();
//            $students = Students::whereIn('class_section_id', $id)->count();
//            $subject_teachers = SubjectTeacher::whereIn('class_section_id', $id)->count();
//            $timetables = Timetable::whereIn('class_section_id', $id)->count();
//
//            if ($assignments || $attendances || $exam_result || $lessons || $student_session || $students || $subject_teachers || $timetables) {
//                $response = array('error' => true, 'message' => trans('cannot_delete_because_data_is_associated_with_other_data'));
//                return response()->json($response);
//            }
            $this->classSection->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');

        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('class-section-delete');
        try {
            DB::beginTransaction();
            $classSection = $this->classSection->findOnlyTrashedById($id);
            $classSection->class->restore();
            $classSection->restore();
            DB::commit();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            DB::commit();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('class-section-delete');
        try {
            $this->classSection->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Subject Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function removeClassTeacher($classTeacherID, $classSectionID) {
        ResponseService::noPermissionThenRedirect('class-section-edit');
        try {
            DB::beginTransaction();
            $this->classTeachers->builder()->where(['class_section_id' => $classSectionID, 'teacher_id' => $classTeacherID])->delete();
            $teacher = $this->user->findById($classTeacherID);

            // Check this teacher has another class teacher is exists
            $classTeachers = $this->classTeachers->builder()->where('teacher_id',$classTeacherID)->first();
            if (!$classTeachers) {
                $teacher->revokePermissionTo(['class-teacher', 'exam-upload-marks', 'exam-result']);
            }
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function removeSubjectTeacher($classSectionID, $teacherID, $subjectID) {
        ResponseService::noPermissionThenRedirect('class-section-edit');
        try {
            DB::beginTransaction();
            $subjectTeachers =   $this->subjectTeachers->builder()->where('teacher_id',$teacherID)->first();
            if (!$subjectTeachers) {
                $this->user->findById($teacherID)->revokePermissionTo(['exam-upload-marks', 'exam-result']);
            }
            $this->subjectTeachers->builder()->where(['class_section_id' => $classSectionID, 'teacher_id' => $teacherID, 'subject_id' => $subjectID])->delete();
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
