<?php

namespace App\Http\Controllers;

use App\Repositories\ClassSchool\ClassSchoolInterface;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ClassSubject\ClassSubjectInterface;
use App\Repositories\ElectiveSubjectGroup\ElectiveSubjectGroupInterface;
use App\Repositories\Medium\MediumInterface;
use App\Repositories\Section\SectionInterface;
use App\Repositories\Semester\SemesterInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Shift\ShiftInterface;
use App\Repositories\Stream\StreamInterface;
use App\Repositories\Subject\SubjectInterface;
use App\Repositories\Timetable\TimetableInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ClassSchoolController extends Controller {
    private MediumInterface $medium;
    private SectionInterface $section;
    private ClassSchoolInterface $class;
    private ClassSectionInterface $classSection;
    private SubjectInterface $subject;
    private ClassSubjectInterface $classSubject;
    private ElectiveSubjectGroupInterface $electiveSubjectGroup;
    private CachingService $cache;
    private SemesterInterface $semester;
    private ShiftInterface $shift;
    private StreamInterface $stream;
    private TimetableInterface $timetable;

    public function __construct(MediumInterface $medium, SectionInterface $section, ClassSchoolInterface $class, ClassSectionInterface $classSection, SubjectInterface $subject, ClassSubjectInterface $classSubject, ElectiveSubjectGroupInterface $electiveSubjectGroup, SemesterInterface $semester, CachingService $cache, ShiftInterface $shift, StreamInterface $stream, TimetableInterface $timetable) {
        $this->medium = $medium;
        $this->section = $section;
        $this->class = $class;
        $this->classSection = $classSection;
        $this->subject = $subject;
        $this->classSubject = $classSubject;
        $this->electiveSubjectGroup = $electiveSubjectGroup;
        $this->semester = $semester;
        $this->cache = $cache;
        $this->shift = $shift;
        $this->stream = $stream;
        $this->timetable = $timetable;
    }


    public function index() {
        ResponseService::noPermissionThenRedirect('class-list');
        $classes = $this->class->builder()->orderBy('id', 'DESC')->with('stream', 'medium', 'sections')->get();
        $sections = $this->section->builder()->orderBy('id', 'ASC')->get();
        $mediums = $this->medium->builder()->orderBy('id', 'ASC')->get();
        $subjects = $this->subject->builder()->orderBy('id', 'ASC')->get();
        $semesters = $this->semester->all();
        $shifts = $this->shift->active();
        $streams = $this->stream->all();
        return response(view('class.index', compact('classes', 'sections', 'mediums', 'subjects', 'semesters', 'shifts', 'streams')));
    }


    public function store(Request $request) {
        ResponseService::noPermissionThenRedirect('class-create');
        $validator = Validator::make($request->all(), [
            'medium_id'      => 'required|numeric',
            'name'           => [
                'required',
                new uniqueForSchool('classes', ['name' => $request->name, 'medium_id' => $request->medium_id, 'stream_id' => $request->stream_id])
            ],
            'stream_id' => 'nullable|array',
            'shift_id'       => 'nullable|numeric',
            'stream_id.*'    => 'nullable|numeric',
            'section_id'     => 'required|array',
            'section_id.*.*' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            // $request->stream_id = $request->stream_id ?? [0];
            /* Create Class */
            if (!empty($request->stream_id) && $request->stream_id[0] != null) {
                foreach ($request->stream_id as $stream) {
                    $classDetails = [
                        ...$request->all(),
                        'stream_id' => ($stream != 0) ? $stream : null,
                        'include_semesters' => $request->include_semesters[$stream] ?? 0,
                    ];

                    $class = $this->class->create($classDetails);
                    /* Create Class Sections */
                    $class_section = array();
                    foreach ($request->section_id[$stream] as $section_id) {
                        $class_section[] = array('class_id' => $class->id, 'section_id' => $section_id, 'medium_id' => $request->medium_id);
                    }

                    $this->classSection->createBulk($class_section);
                }
            } else {
                $classDetails = [
                    ...$request->all(),
                    'stream_id' => null,
                    'include_semesters' => $request->include_semesters[0] ?? 0,
                ];

                $class = $this->class->create($classDetails);
                /* Create Class Sections */
                $class_section = array();
                foreach ($request->section_id[0] as $section_id) {
                    $class_section[] = array('class_id' => $class->id, 'section_id' => $section_id, 'medium_id' => $request->medium_id);
                }

                $this->classSection->createBulk($class_section);
            }

            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function edit($id) {
        ResponseService::noPermissionThenRedirect('class-edit');
        $class = $this->class->findById($id, ['*'], ['stream', 'sections', 'core_subjects:id', 'elective_subject_groups.subjects:id']);
        $class_section = $class->sections->pluck('id');
        $subjects = $this->subject->builder()->where('medium_id', $class->medium_id)->orderBy('id', 'ASC')->get();
        $sections = $this->section->builder()->orderBy('id', 'ASC')->get();
        $semesters = $this->semester->all();
        $shifts = $this->shift->active();
        $streams = $this->stream->all();
        return response(view('class.edit', compact('class', 'subjects', 'sections', 'class_section', 'id', 'semesters', 'shifts', 'streams')));
    }

    public function update(Request $request, $id) {
        ResponseService::noPermissionThenRedirect('class-edit');
        $validator = Validator::make($request->all(), [
            'name'      => [
                'required',
                new uniqueForSchool('classes', ['name' => $request->name, 'medium_id' => $request->medium_id, 'stream_id' => $request->stream_id], $id)
            ],
            'stream_id' => 'nullable|numeric',
            'shift_id'  => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {

            DB::beginTransaction();
            // Class Update
            $class = $this->class->findById($id, ['*'], ['class_sections']);
            $semesterIncluded = $request->include_semesters[0] ?? 0;
            if ($class->include_semesters != $semesterIncluded) {
                //If include_semester is changed then delete the class subjects
                $this->electiveSubjectGroup->builder()->where('class_id', $class->id)->delete();
                $this->classSubject->builder()->where('class_id', $class->id)->delete();
            }
            if (count($class->class_sections) == 0 && count($request->section_id) == 0) {
                ResponseService::errorResponse("Class Section is Required");
            }
            $this->class->update($id, [...$request->all(), 'include_semesters' => $semesterIncluded]);

            // Section Update
            if (!empty($request->section_id)) {
                $class_section = array();
                foreach ($request->section_id as $section_id) {
                    $class_section[] = array(
                        'class_id'   => $id,
                        'section_id' => $section_id,
                        'medium_id' => $class->medium_id,
                        'deleted_at' => null);
                }
                $this->classSection->upsert($class_section, ['class_id', 'section_id'], ['deleted_at']);
            }


            // Subjects Update

            $coreSubjects = array();
            if (!empty($request->core_subject)) {
                foreach ($request->core_subject as $row) {
                    $coreSubjects[] = array('class_id' => $id, 'type' => "Compulsory", 'subject_id' => $row['id'], "elective_subject_group_id" => null, "semester_id" => $row['semester_id'] ?? null);
                }
            }

            // Upsert Elective Subjects
            $electiveSubject = [];
            if (!empty($request->elective_subject_group)) {
                foreach ($request->elective_subject_group as $subjectGroup) {

                    $electiveSubjectGroup = $this->electiveSubjectGroup->updateOrCreate(
                        ['id' => $subjectGroup['id'],],
                        [
                            'total_subjects'            => count($subjectGroup['subject']),
                            'total_selectable_subjects' => $subjectGroup['total_selectable_subjects'],
                            'class_id'                  => $id,
                            "semester_id"               => $subjectGroup['semester_id'] ?? null
                        ]
                    );
                    foreach ($subjectGroup['subject'] as $subject) {
                        $electiveSubject[] = array('class_id' => $id, 'type' => "Elective", 'subject_id' => $subject['id'], 'elective_subject_group_id' => $electiveSubjectGroup->id, "semester_id" => $subjectGroup['semester_id'] ?? null);
                    }
                }
            }
            // Check that If Elective Subjects exists then merge or else store only Core Subjects
            $classSubjects = array_merge($coreSubjects, $electiveSubject);
            $this->classSubject->upsert($classSubjects, ['class_id', 'subject_id', 'semester_id'], ['type']);

            $this->timetable->builder()->whereHas('class_section', function($q) use($id) {
                $q->where('class_id',$id);
            })->delete();

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function show(Request $request) {
        ResponseService::noPermissionThenRedirect('class-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $request->search;
        $showDeleted = $request->show_deleted;

//        $currentSemester = $this->cache->getDefaultSemesterData();
        $semesters = $this->semester->all();
        $sql = $this->class->builder()->with('stream', 'sections', 'medium', 'stream:id,name', 'shift:id,name')
            ->where(function ($query) use ($search) {
                $query->when($search, function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->orWhereHas('sections', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('medium', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('shift', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->Owner();
                });
            })
            ->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed()->Owner()->with('section');
            });
        if (!empty($request->medium_id)) {
            $sql = $sql->where('medium_id', $request->medium_id);
        }

        if (!empty($request->shift_id)) { 
            $sql = $sql->where('shift_id', $request->shift_id);
        }

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            if ($request->show_deleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::restoreButton(route('class.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('class.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate = BootstrapTableService::editButton(route('class.edit', $row->id), false);
                $operate .= BootstrapTableService::deleteButton(route('class.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            if (empty($showDeleted)) {
                $tempRow['section_names'] = $row->sections->pluck('name');    
            } else {
                $tempRow['section_names'] = $row->section->pluck('name');
            }
            
            
            $tempRow['semesters'] = $semesters;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('class-delete');
        try {
            DB::beginTransaction();
            $class = $this->class->findById($id, ['*'], ['class_sections']);
            if (!empty($class->class_sections)) {
                foreach ($class->class_sections as $section) {
                    $section->delete();
                }

            }
            $this->class->deleteById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('class-delete');
        try {
            $this->class->findOnlyTrashedById($id)->restore();
            $this->classSection->builder()->where('class_id',$id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('class-delete');
        try {
            $this->class->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ClassSchool Controller -> Trash Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function classSubjectIndex() {
        ResponseService::noPermissionThenRedirect('class-list');
        $classes = $this->class->builder()->orderBy('id', 'DESC')->with('stream', 'medium', 'sections')->get();
        $sections = $this->section->builder()->orderBy('id', 'ASC')->get();
        $mediums = $this->medium->builder()->orderBy('id', 'ASC')->get();
        $subjects = $this->subject->builder()->orderBy('id', 'ASC')->get();
        $semesters = $this->semester->all();
        $shifts = $this->shift->active();
        $streams = $this->stream->all();
        return response(view('class-subject.index', compact('classes', 'sections', 'mediums', 'subjects', 'semesters', 'shifts', 'streams')));
    }

    public function classSubjectEdit($id) {
        ResponseService::noPermissionThenRedirect('class-edit');
        $class = $this->class->findById($id, ['*'], ['stream', 'sections', 'core_subjects', 'elective_subject_groups.subjects:id']);
        $subjects = $this->subject->builder()->where('medium_id', $class->medium_id)->orderBy('id', 'ASC')->get();
        $semesters = $class->include_semesters ? $this->semester->all() : [];
        return response(view('class-subject.edit', compact('class', 'subjects', 'id', 'semesters')));
    }

    public function classSubjectUpdate(Request $request, $id) {
        ResponseService::noPermissionThenRedirect('class-edit');
        $validator = Validator::make($request->all(), [
            'core_subject'                                  => 'nullable|array',
            'core_subject.*.id'                             => 'required|numeric',
            'elective_subject_group'                        => 'array|nullable',
            'elective_subjects.*.subject.*.id'              => 'required|array',
            'elective_subjects.*.total_selectable_subjects' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {

            DB::beginTransaction();
            // Subjects Update

            $coreSubjects = array();
            if (!empty($request->core_subject)) {
                foreach ($request->core_subject as $row) {
                    $coreSubjects[] = array('class_id' => $id, 'type' => "Compulsory", 'subject_id' => $row['id'], "elective_subject_group_id" => null, "semester_id" => $row['semester_id'] ?? null);
                }
            }

            $this->classSubject->builder()->where('class_id',$id)->delete();

            // Upsert Elective Subjects
            $electiveSubject = [];
            if (!empty($request->elective_subject_group)) {
                foreach ($request->elective_subject_group as $subjectGroup) {

                    $electiveSubjectGroup = $this->electiveSubjectGroup->updateOrCreate(
                        ['id' => $subjectGroup['id'],],
                        [
                            'total_subjects'            => count($subjectGroup['subject']),
                            'total_selectable_subjects' => $subjectGroup['total_selectable_subjects'],
                            'class_id'                  => $id,
                            "semester_id"               => $subjectGroup['semester_id'] ?? null
                        ]
                    );
                    foreach ($subjectGroup['subject'] as $subject) {
                        $electiveSubject[] = array('class_id' => $id, 'type' => "Elective", 'subject_id' => $subject['id'], 'elective_subject_group_id' => $electiveSubjectGroup->id, "semester_id" => $subjectGroup['semester_id'] ?? null);
                    }
                }
            }
            // Check that If Elective Subjects exists then merge or else store only Core Subjects
            $classSubjects = array_merge($coreSubjects, $electiveSubject);

            foreach ($classSubjects as $subject) {
                // Check if the subject exists in the database
                $existingSubject = $this->classSubject->builder()->onlyTrashed()
                    ->where('class_id', $subject['class_id'])
                    ->where('subject_id', $subject['subject_id'])
                    ->where('semester_id', $subject['semester_id'])
                    ->first();
            
                // If the subject exists and is soft-deleted, restore it
                if ($existingSubject && $existingSubject->trashed()) {
                    $existingSubject->restore();
                }
            }

            $this->classSubject->upsert($classSubjects, ['class_id', 'subject_id', 'semester_id'], ['type','elective_subject_group_id']);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }


    public function classSubjectList(Request $request) {
        ResponseService::noPermissionThenRedirect('class-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = $request->search;
        $showDeleted = $request->show_deleted;

        $currentSemester = $this->cache->getDefaultSemesterData();
        $semesters = $this->semester->all();
        $sql = $this->class->builder()->with('stream', 'sections', 'medium', 'core_subjects:id,name,type', 'elective_subject_groups.subjects:id,name,type', 'stream:id,name', 'shift:id,name')
            ->where(function ($query) use ($search) {
                $query->when($search, function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->orWhereHas('sections', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('medium', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->Owner();
                });
            })
            ->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed()->Owner();
            });
        if (!empty($request->medium_id)) {
            $sql = $sql->where('medium_id', $request->medium_id);
        }

        if (!empty($request->shift_id)) {
            $sql = $sql->where('shift_id', $request->shift_id);
        }

        if (!empty($request->stream_id)) {
            $sql = $sql->where('stream_id', $request->stream_id);
        }

        if (!empty($request->core_subject_id)) {
            $sql = $sql->whereHas('core_subjects', function($q) use ($request) {
                $q->where('id', $request->core_subject_id);
            });
        }

        if (!empty($request->elective_subject_group_id)) {
            $sql = $sql->whereHas('elective_subject_groups.subjects', function($q) use ($request) {
                $q->where('id', $request->elective_subject_group_id);
            });
        }

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '';
            if ($showDeleted) {
                $operate = BootstrapTableService::restoreButton(route('class.restore', $row->id));
            } else {
                $operate = BootstrapTableService::editButton(route('class.subject.edit', $row->id), false);
            }
            
            
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['section_names'] = $row->sections->pluck('name');
            $tempRow['semesters'] = $semesters;
            $tempRow['semester_wise_core_subjects'] = array();
            if ($row->include_semesters && !empty($currentSemester)) {
                $tempRow['core_subjects'] = array_values($row->core_subjects->filter(function ($data) use ($currentSemester) {
                    return $data->pivot->semester_id == $currentSemester->id;
                })->toArray());

                $tempRow['elective_subject_groups'] = $row->elective_subject_groups->filter(function ($data) use ($currentSemester) {
                    return $data->semester_id == $currentSemester->id;
                });

                $tempRow['semester_wise_core_subjects'] = $row->core_subjects;
                $tempRow['semester_wise_elective_subject_groups'] = $row->elective_subject_groups;
            }

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function deleteClassSubject($id) {
        // TODO : Set Permission
        try {
            DB::beginTransaction();
            $class_subject = $this->classSubject->findById($id);
            if ($class_subject->type == "Elective") {
                $this->electiveSubjectGroup->findById($class_subject->elective_subject_group_id)->decrement('total_subjects');
            }
            $class_subject->delete();
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "ClassSchool Controller ->deleteClassSubject Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function deleteClassSubjectGroup($id) {
        // TODO : Set Permission
        try {
            // Delete Subject Group will automatically delete ClassSubject
            $this->electiveSubjectGroup->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ClassSchool Controller ->deleteClassSubjectGroup Method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function classAttendance($id = null)
    {
        ResponseService::noPermissionThenSendJson('attendance-list');
        try {
            $sessionYear = $this->cache->getDefaultSessionYear();
            $sql = $this->classSection->builder()->select('id','section_id')->where('class_id',$id)->with('section')
            ->withCount(['attendance as present' => function($q) use($sessionYear) {
                $q->where('type',1)->where('session_year_id',$sessionYear->id);
            }])
            ->withCount(['attendance as absent' => function($q) use($sessionYear) {
                $q->where('type',0)->where('session_year_id',$sessionYear->id);
            }])
            ->withCount(['attendance' => function($q) use($sessionYear) {
                $q->where('session_year_id',$sessionYear->id);
            }])
            ->get();

            $data = $sql->map(function($q) {
                if ($q->attendance_count) {
                    return [
                        $q->section->name, number_format(($q->present * 100) / $q->attendance_count, 2)
                    ];    
                }
            })->toArray();

            $data = array_filter($data);
            if (count($data)) {
                $attendance = [
                    'section' => $this->pluck($data,0),
                    'data' => $this->pluck($data,1)
                ];    
            } else {
                $attendance = [
                    'section' => ['A','B','C','D'],
                    'data' => [0,0,0,0]
                ];
            }
            

            ResponseService::successResponse('Data Fetched Successfully',$attendance);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function pluck($array, $key) {
        return array_map(function($item) use ($key) {
            return $item[$key];
        }, $array);
    }
}
