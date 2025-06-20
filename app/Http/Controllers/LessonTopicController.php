<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ClassSubject\ClassSubjectInterface;
use App\Repositories\Files\FilesInterface;
use App\Repositories\Lessons\LessonsInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\Subject\SubjectInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use App\Repositories\TopicCommon\TopicCommonInterface;
use App\Repositories\Topics\TopicsInterface;
use App\Rules\DynamicMimes;
use App\Rules\MaxFileSize;
use App\Rules\uniqueTopicInLesson;
use App\Rules\YouTubeUrl;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class LessonTopicController extends Controller {
    private LessonsInterface $lesson;
    private TopicsInterface $topic;
    private FilesInterface $files;
    private ClassSectionInterface $classSection;
    private SubjectTeacherInterface $subjectTeacher;
    private StudentInterface $student;
    private SubjectInterface $subject;
    private TopicCommonInterface $topicCommon;
    private CachingService $cache;
    private ClassSubjectInterface $class_subjects;

    public function __construct(LessonsInterface $lesson, TopicsInterface $topic, FilesInterface $files, ClassSectionInterface $classSection, SubjectTeacherInterface $subjectTeacher, StudentInterface $student, SubjectInterface $subject, TopicCommonInterface $topicCommon, CachingService $cache, ClassSubjectInterface $class_subjects) {
        $this->lesson = $lesson;
        $this->topic = $topic;
        $this->files = $files;
        $this->classSection = $classSection;
        $this->subjectTeacher = $subjectTeacher;
        $this->topicCommon = $topicCommon;
        $this->cache = $cache;
        $this->student = $student;
        $this->subject = $subject;
        $this->class_subjects = $class_subjects;
    }

    public function index() {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenRedirect('topic-list');
        $class_section = $this->classSection->builder()->with('class', 'class.stream', 'section', 'medium')->get();
        $subjectTeachers = $this->subjectTeacher->builder()->with('subject:id,name,type')->get();
        $lessons = $this->lesson->builder()->get();
        return response(view('lessons.topic', compact('class_section', 'subjectTeachers', 'lessons')));
    }

    public function store(Request $request) {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenRedirect('topic-create');
        $file_upload_size_limit = $this->cache->getSystemSettings('file_upload_size_limit');
        $validator = Validator::make($request->all(), [
            'class_section_id'      => 'required|array',
            'class_section_id.*'    => 'numeric',
            'subject_id'      => 'required|numeric',
            'lesson_id'             => 'required|numeric',
            'name'                  => ['required', new uniqueTopicInLesson($request->lesson_id)],
            'description'           => 'required',
            'file_data'             => 'nullable|array',
            'file_data.*.type'      => 'required|in:file_upload,youtube_link,video_upload,other_link',
            'file_data.*.name'      => 'required_with:file_data.*.type',
            'file_data.*.thumbnail' => 'required_if:file_data.*.type,youtube_link,video_upload,other_link',
            'file_data.*.link' => [
                    'nullable',
                    'required_if:file_data.*.type,youtube_link,other_link',
                    new YouTubeUrl, 
                ],
                
                'file_data.*.link' => [
                    'nullable',
                    'required_if:file_data.*.type,other_link',
                    'url',
                    
                ],

                'file_data.*.file' => [
                    'nullable',
                    'required_if:file_data.*.type,file_upload,video_upload',
                    new DynamicMimes(),
                    new MaxFileSize($file_upload_size_limit), // Max file size validation
                ],
        ],[
            'file_data.*.file.required_if' => trans('The file field is required when uploading a file.'),
            'file_data.*.file.dynamic_mimes' => trans('The uploaded file type is not allowed.'),
            'file_data.*.file.max_file_size' => trans('The file uploaded must be less than :file_upload_size_limit MB.', [
                'file_upload_size_limit' => $file_upload_size_limit,
            ]),
            'file_data.*.link.required_if' => trans('The link field is required when the type is YouTube link or Other link.'),
            'file_data.*.link.url' => trans('The provided link must be a valid URL.'),
            'file_data.*.link.youtube_url' => trans('The provided YouTube URL is not valid.'),
            'file_data.*.file.required_if' => trans('The file is required when uploading a video or file.'),
        ]);
        // dd('done');
        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            DB::beginTransaction();

            $section_ids = is_array($request->class_section_id) ? $request->class_section_id : [$request->class_section_id];

            $lessonTopicFileData = [];

            // Prepare file data if provided
            if (!empty($request->file_data)) {
                foreach ($request->file_data as $file) {
                    if ($file['type']) {
                        $lessonTopicFileData[] = $this->prepareFileData($file);
                    }
                }
            }

            $lessonTopicData = []; // Initialize outside loop
            foreach ($section_ids as $section_id) {
                // Merge the class_section_id into the lessonTopicData
                $lessonTopicData = array_merge($request->all(), ['class_section_id' => $section_id]);
            }

            // Get the related class subject for each section
            if ($request->class_section_id) {
                foreach ($request->class_section_id as $section_id) {
                    $classSection = $this->classSection->builder()->where('id', $section_id)->with(['class_subject' => function ($q) use ($request) {
                        $q->where('subject_id', $request->subject_id);
                    }])->first();
                }
            }

            $lessonTopicData['class_subject_id'] = $classSection->class_subject->id;
            unset($lessonTopicData['subject_id']);
            unset($lessonTopicData['user_id']);
        
            // Create topics and store them
            $topics = $this->topic->create($lessonTopicData);
            $lessonTopicCommonData = ['lesson_topics_id' => $topics->id];

             // Create lesson topic common data for each section
             foreach ($section_ids as $section_id) {
                $classSection = $this->classSection->builder()->where('id', $section_id)->with('class')->first();
                $classSubjects = $this->class_subjects->builder()
                    ->where('class_id', $classSection->class->id)
                    ->where('subject_id', $request->subject_id)
                    ->first();
        
                $lessonTopicCommonData['class_section_id'] = $section_id;
                $lessonTopicCommonData['class_subject_id'] = $classSubjects->id;
                $this->topicCommon->create($lessonTopicCommonData);
            }   


            $lessonFile = $this->files->model();
            $lessonModelAssociate = $lessonFile->modal()->associate($topics);

            // Create a file model instance
            if (!empty($lessonTopicFileData)) {
                foreach ($lessonTopicFileData as &$fileData) {
                    // Set modal_type and modal_id for each fileData
                    $fileData['modal_type'] = $lessonModelAssociate->modal_type; // Adjust according to your model's name
                    $fileData['modal_id'] = $topics->id; // Use the last created topic's id (or adjust logic if needed)
                }

                // Bulk create files
                $this->files->createBulk($lessonTopicFileData);
            }

            $user = $this->student->builder()->with('user')->where('class_section_id', $request->class_section_id)->pluck('user_id');
            $subjectTeacherData = $this->subjectTeacher->builder()->whereIn('class_section_id', $request->class_section_id)->where(['teacher_id' => $request->user_id, 'class_subject_id' => $classSubjects->id])->with('subject')->first(); // Get the Subject Teacher Data
            $subjectName = $subjectTeacherData->subject_with_name; // Subject Name

            $title = 'Topic Alert !!!';
            $body = 'A new topic has been added to the lesson "' . $request->name . '" under the subject "' . $subjectName . '".';
            $type = "lesson";
           
            send_notification($user, $title, $body, $type);

            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "Lesson Topic Controller -> Store Method");
                ResponseService::errorResponse();
            }
        }
    }

    private function prepareFileData($file)
    {

        if ($file['type']) {
            $tempFileData = [
                'file_name'  => $file['name']
            ];
            // If File Upload
            if ($file['type'] == "file_upload") {

                // Add Type And File Url to TempDataArray and make Thumbnail data null
                $tempFileData['type'] = 1;
                $tempFileData['file_thumbnail'] = null;
                $tempFileData['file_url'] = $file['file'];
            } elseif ($file['type'] == "youtube_link") {

                // Add Type , Thumbnail and Link to TempDataArray
                $tempFileData['type'] = 2;
                $tempFileData['file_thumbnail'] = $file['thumbnail'];
                $tempFileData['file_url'] = $file['link'];
            } elseif ($file['type'] == "video_upload") {

                // Add Type , File Thumbnail and File URL to TempDataArray
                $tempFileData['type'] = 3;
                $tempFileData['file_thumbnail'] = $file['thumbnail'];
                $tempFileData['file_url'] = $file['file'];
            } elseif ($file['type'] == "other_link") {

                // Add Type , File Thumbnail and File URL to TempDataArray
                $tempFileData['type'] = 4;
                $tempFileData['file_thumbnail'] = $file['thumbnail'];
                $tempFileData['file_url'] = $file['link'];
            }

        }

        return $tempFileData;
    }

    public function show() {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenRedirect('topic-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');

        $sql = $this->topic->builder()
            ->has('lesson')
            ->with('lesson.class_section','lesson.class_subject','file','lesson.class_subject.subject','topic_commons')
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")
                        ->orwhere('name', 'LIKE', "%$search%")
                        ->orwhere('description', 'LIKE', "%$search%")
                        ->orWhereHas('lesson.class_section.section', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        })->orWhereHas('lesson.class_section.class', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        })->orWhereHas('lesson.class_subject.subject', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        })->orWhereHas('lesson', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        });
                });
                });
            })
            ->when(request('class_subject_id') != null, function ($query) {
                $class_subject_id = request('class_subject_id');
                $query->where(function ($query) use ($class_subject_id) {
                    $query->whereHas('topic_commons', function ($q) use ($class_subject_id) {
                        $q->where('class_subject_id', $class_subject_id);
                    });
                });
            })
            ->when(request('class_id') != null, function ($query) {
                $class_id = request('class_id');
                
                $query->whereHas('topic_commons', function ($q) use ($class_id) {
                    $q->where('class_section_id', $class_id);
                });
            })
            ->when(request('lesson_id') != null, function ($query) {
                $lesson_id = request('lesson_id');
                $query->where(function ($query) use ($lesson_id) {
                    $query->where('lesson_id', $lesson_id);
                });
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {

            $row = (object)$row;
         
            $lessonTopicCommons = $row->topic_commons->map(function ($common) {
                return $common->class_section_id ? $common->class_section->full_name : null;
            });

            $lessonTopicCommons->filter()->map(function ($name) {
                return "{$name},";
            })->toArray();

            // $operate = BootstrapTableService::button(route('lesson-topic.edit', $row->id), ['btn-gradient-primary'], ['title' => 'Edit'], ['fa fa-edit']);
            $operate = BootstrapTableService::button('fa fa-edit', route('lesson-topic.edit', $row->id), ['btn-gradient-primary'], ['title' => 'Edit']);
            $operate .= BootstrapTableService::deleteButton(route('lesson-topic.destroy', $row->id));

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['class_section_with_medium'] =  $lessonTopicCommons;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function edit($id) {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenRedirect('topic-edit');
        $class_section = $this->classSection->builder()->with('class', 'class.stream', 'section', 'medium')->get();
        $subjectTeachers = $this->subjectTeacher->builder()->with('subject:id,name,type')->get();
        $lessons = $this->lesson->builder()->get();
        $topic = $this->topic->builder()->with('file')->where('id', $id)->first();

        return response(view('lessons.edit_topic', compact('class_section', 'subjectTeachers', 'lessons', 'topic')));
    }

    public function update($id, Request $request) {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenRedirect('topic-edit');
        $file_upload_size_limit = $this->cache->getSystemSettings('file_upload_size_limit');
        $validator = Validator::make(
            $request->all(),
            [
                'class_section_id' => 'required|numeric',
                'class_subject_id' => 'required|numeric',
                'lesson_id'        => 'required|numeric',
                'name'             => ['required', new uniqueTopicInLesson($request->lesson_id, $id)],
                'description'      => 'required',
                'file_data'        => 'nullable|array',
                'file_data.*.type' => 'required|in:file_upload,youtube_link,video_upload,other_link',
                'file_data.*.name' => 'required_with:file_data.*.type',
                'file_data.*.link' => ['nullable', 'required_if:file_data.*.type,youtube_link', new YouTubeUrl], //Regex for YouTube Link
                'file_data.*.file' => ['nullable', new DynamicMimes, new MaxFileSize($file_upload_size_limit) ],
            ],
            [
                'name.unique' => trans('topic_already_exists'),
                'file_data.*.file' => trans('The file Uploaded must be less than :file_upload_size_limit MB.', [
                    'file_upload_size_limit' => $file_upload_size_limit,  
                ]),
            ]
        );
        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $topic = $this->topic->update($id, $request->all());

            //Add the new Files
            if ($request->file_data) {

                foreach ($request->file_data as $file) {
                    if ($file['type']) {

                        // Create A File Model Instance
                        $topicFile = $this->files->model();

                        // Get the Association Values of File with Topic
                        $topicModelAssociate = $topicFile->modal()->associate($topic);

                        // Make custom Array for storing the data in fileData
                        $fileData = array(
                            'id'         => $file['id'] ?? null,
                            'modal_type' => $topicModelAssociate->modal_type,
                            'modal_id'   => $topicModelAssociate->modal_id,
                            'file_name'  => $file['name'],
                        );

                        // If File Upload
                        if ($file['type'] == "file_upload") {

                            // Add Type And File Url to TempDataArray and make Thumbnail data null
                            $fileData['type'] = 1;
                            $fileData['file_thumbnail'] = null;
                            if (!empty($file['file'])) {
                                $fileData['file_url'] = $file['file'];
                            }
                        } elseif ($file['type'] == "youtube_link") {

                            // Add Type , Thumbnail and Link to TempDataArray
                            $fileData['type'] = 2;
                            if (!empty($file['thumbnail'])) {
                                $fileData['file_thumbnail'] = $file['thumbnail'];
                            }
                            $fileData['file_url'] = $file['link'];
                        } elseif ($file['type'] == "video_upload") {

                            // Add Type , File Thumbnail and File URL to TempDataArray
                            $fileData['type'] = 3;
                            if (!empty($file['thumbnail'])) {
                                $fileData['file_thumbnail'] = $file['thumbnail'];
                            }
                            if (!empty($file['file'])) {
                                $fileData['file_url'] = $file['file'];
                            }
                        } elseif ($file['type'] == "other_link") {

                            // Add Type , File Thumbnail and File URL to TempDataArray
                            $fileData['type'] = 4;
                            if ($file['thumbnail']) {
                                $fileData['file_thumbnail'] = $file['thumbnail'];
                            }
                            $fileData['file_url'] = $file['link'];
                        }
                        $fileData['created_at'] = date('Y-m-d H:i:s');
                        $fileData['updated_at'] = date('Y-m-d H:i:s');

                        $this->files->updateOrCreate(['id' => $file['id']], $fileData);
                    }
                }
            }

            $user = $this->student->builder()->with('user')->where('class_section_id', $request->class_section_id)->pluck('user_id');
            $lesson = $this->lesson->builder()->where('id', $request->lesson_id)->pluck('name')->first();
            $subjectName = $this->class_subjects->builder()->with('subject')->where('id', $request->class_subject_id)->first();
           
            $title = 'Topic Alert !!!';
            $body = 'A new topic has been updated for the lesson "' . $lesson . '" under the subject "' . $subjectName->subject->name . '".';
            $type = "lesson";
           
            send_notification($user, $title, $body, $type);

            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                DB::rollBack();
                ResponseService::logErrorResponse($e, "Lesson Topic Controller -> Update Method");
                ResponseService::errorResponse();
            }
        }
    }


    public function destroy($id) {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenSendJson('topic-delete');
        try {
            DB::beginTransaction();
            $this->topic->deleteById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Lesson Topic Controller -> Delete Method");
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenSendJson('topic-delete');
        try {
            $this->topic->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noFeatureThenRedirect('Lesson Management');
        ResponseService::noPermissionThenSendJson('topic-delete');
        try {
            $this->topic->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
