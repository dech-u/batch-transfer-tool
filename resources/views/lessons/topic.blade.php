@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('topic') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('topic') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('topic') }}
                        </h4>
                        <form class="pt-3 add-topic-form" id="create-form" data-success-function="formSuccessFunction" action="{{ route('lesson-topic.store') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                {!! Form::hidden('user_id', Auth::user()->id, ['id' => 'user_id']) !!}
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('Class Section') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id[]" id="class-section-id" class="class_section_id form-control select2-dropdown select2-hidden-accessible" multiple>
                                        {{-- <option value="">{{ __('Select Class Section') }}</option> --}}
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-check w-fit-content">
                                        <label class="form-check-label user-select-none">
                                            <input type="checkbox" class="form-check-input" id="select-all" value="1">{{__("Select All")}}
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject-id" class="form-control">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('lesson') }} <span class="text-danger">*</span></label>
                                    <select name="lesson_id" id="topic-lesson-id" class="form-control topic-lesson-id">
                                        <option value="">-- {{ __('lesson') }} --</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($lessons as $item)
                                            <option value="{{ $item->id }}" data-class-section="{{ $item->class_section_id }}" data-subject="{{ $item->class_subject_id }}">{{ $item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('topic_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" placeholder="{{ __('topic_name') }}"
                                           class="form-control"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('topic_description') }} <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" placeholder="{{ __('topic_description') }}" class="form-control"></textarea>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-3">{{ __('files') }}</h4>

                            <div class="form-group files_data">
                                <div data-repeater-list="file_data">
                                    <div class="row file_type_div" id="file_type_div" data-repeater-item>
                                        <div class="form-group col-xl-2">
                                            <label>{{ __('type') }} </label>
                                            <select id="file_type" name="file[0][type]" class="form-control file_type">
                                                <option value="">--{{ __('select') }}--</option>
                                                <option value="file_upload">{{ __('file_upload') }}</option>
                                                <option value="youtube_link">{{ __('youtube_link') }}</option>
                                                <option value="video_upload">{{ __('video_upload') }}</option>
                                                <option value="other_link">{{ __('other_link') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_name_div" style="display: none">
                                            <label>{{ __('file_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="file[0][name]" class="file_name form-control" placeholder="{{ __('file_name') }}" required>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_thumbnail_div" style="display: none">
                                            <label>{{ __('thumbnail') }} <span class="text-danger">*</span></label>
                                            <input type="file" name="file[0][thumbnail]" class="file_thumbnail form-control" required>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_div" style="display: none">
                                            <label>{{ __('file_upload') }} <span class="text-danger">*</span></label>
                                            <input type="file" name="file[0][file]" class="file form-control" placeholder="" required>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_link_div" style="display: none">
                                            <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="file[0][link]" class="file_link form-control" placeholder="{{ __('link') }}" required>
                                        </div>

                                        <div class="form-group col-xl-1 mt-4">
                                            <button type="button" class="btn btn-inverse-danger btn-icon remove-lesson-topic-fileremove-lesson-topic-file" data-repeater-delete>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-inverse-success add-lesson-topic-file" data-repeater-create>
                                        <i class="fa fa-plus"></i> {{__('add_new_files')}}
                                    </button>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('topic') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="filter-class-section-id" class="filter-menu">{{__("Class Section")}}</label>
                                    <select name="filter-class-section-id" id="filter-class-section-id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($class_section as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="filter-class-subject-id" class="filter-menu">{{__("Subject")}}</label>
                                    <select name="filter-class-subject-id" id="filter-subject-id" class="form-control">
                                        <option value="" data-all="true">{{ __('all') }}</option>
                                        <option value="data-not-found" style="display: none">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->class_subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="lesson_id" class="filter-menu">{{__("Lessons")}}</label>
                                    <select name="lesson_id" id="topic-lesson-id" class="form-control topic-lesson-id">
                                        <option value="">-- {{ __('lesson') }} --</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($lessons as $item)
                                            <option value="{{ $item->id }}" data-class-section="{{ $item->class_section_id }}" data-subject="{{ $item->class_subject_id }}">{{ $item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('lesson-topic.show', 1) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                               data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                               data-maintain-selected="true" data-export-data-type='all'
                               data-query-params="CreateTopicQueryParams"
                               data-export-options='{ "fileName": "topic-list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                               data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="false" data-visible="false"> {{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name" data-sortable="false">{{ __('name') }}</th>
                                <th scope="col" data-events="tableDescriptionEvents" data-formatter="descriptionFormatter" data-field="description" data-sortable="false"> {{ __('description') }}</th>
                                <th scope="col" data-field="class_section_with_medium" data-formatter="ClassSectionFormatter" data-sortable="false"> {{ __('class_section') }}</th>
                                <th scope="col" data-field="lesson.class_subject.subject.name_with_type" data-sortable="false"> {{ __('subject') }}</th>
                                <th scope="col" data-field="lesson.name" data-sortable="false"> {{ __('lesson') }}</th>
                                <th scope="col" data-field="file" data-formatter="fileFormatter" data-sortable="false">{{ __('file') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false"> {{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false"> {{ __('updated_at') }}</th>
                                <th scope="col" data-events="lessonTopicEvents" data-field="operate" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function formSuccessFunction(response) {
            setTimeout(() => {
                window.location.reload()
            }, 1000);
        }
    </script>
@endsection
