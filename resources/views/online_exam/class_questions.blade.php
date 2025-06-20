@extends('layouts.master')

@section('title')
    {{ __('Manage Online Exam Questions') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Add Online Exam Questions') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3 mt-6 common-validation" id="create-online-exam-questions-form" method="POST" action="{{ route('online-exam-question.store') }}">
                            {!! Form::hidden('user_id', Auth::user()->id, ['id' => 'user_id']) !!}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="class-section-id">{{ __('class_section') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id[]" required id="class-section-id" class="form-control select2 online-exam-class-section-id select2-dropdown select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true" multiple>
                                        {{-- <option value="">--- {{ __('select') . ' ' . __('Class Section') }} ---</option> --}}
                                        @foreach ($classSections as $data)
                                            <option value="{{ $data->id }}" data-class-id="{{ $data->class_id }}">
                                                {{ $data->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-check w-fit-content">
                                        <label class="form-check-label user-select-none">
                                            <input type="checkbox" class="form-check-input" id="select-all" value="1">{{__("Select All")}}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="subject-id">{{ __('subject') }} <span class="text-danger">*</span></label>
                                    @if (Auth::user()->hasRole('School Admin'))
                                        <select required name="subject_id" id="xsubject-id" class="form-control">
                                            <option value="">-- {{ __('Select Subject') }} --</option>
                                            <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                            @foreach ($classSubjects as $item)
                                                <option value="{{ $item->id }}" data-class-id="{{ $item->class_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select required name="subject_id" id="subject-id" class="form-control">
                                            <option value="">-- {{ __('Select Subject') }} --</option>
                                            <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                            @foreach ($subjectTeachers as $item)
                                                <option value="{{ $item->subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-light p-4">
                                <div class="form-group">
                                    <label for="question">{{ __('question') }} <span class="text-danger">*</span></label>
                                    <textarea class="editor_question" name="question" id="question" required placeholder="{{__('enter').' '.__('question')}}"></textarea>
                                </div>
                                <div class="options-data">
                                    <div data-repeater-list="option_data" class="row">
                                        <div class="form-group col-lg-6 col-md-12" data-repeater-item>
                                            <label for="option">{{ __('option') }} <span class="option-number">0</span> <span class="text-danger">*</span></label>
                                            <textarea class="editor_options" name="option" id="option" required placeholder="{{__('enter').' '.__('option')}}"></textarea>
                                            {!! Form::hidden('number','', ['class'=>'option-number']) !!}
                                            <button type="button" class="btn btn-inverse-danger mt-2 btn-icon remove-option" data-repeater-delete>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-dark btn-sm" type="button" id="add-new-option" data-repeater-create>
                                            <i class="fa fa-plus-circle fa-3x mr-2" aria-hidden="true"></i>
                                            {{__('add_option')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="form-group col-md-6 mt-2">
                                        <div class="form-group">
                                            <label for="answer_select">{{ __('answer') }} <span class="text-danger">*</span></label>
                                            <select multiple required name="answer[]" id="answer_select" class="form-control select2-dropdown select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="image">{{ __('image') }}</label>
                                        <input type="file" name="image" accept="image/jpg,image/png,image/jpeg" class="file-upload-default"/>
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" id="image" disabled="" placeholder="{{ __('image') }}"/>
                                            <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group p-1">
                                    <label for="note">{{ __('note') }}</label>
                                    <input type="text" name="note" id="note" class="form-control">
                                </div>
                            </div>
                            <input class="btn btn-theme float-right ml-3 mt-3" id="new-question-add" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            {{ __('list') . ' ' . __('online'). ' ' . __('exam').' '.__('question') }}
                        </h4>
                        <div id="toolbar" class="row">
                            <div class="form-group col-sm-12 col-md-3">
                                <label for="filter-class-section-id" class="filter-menu">{{__("Class Section")}}</label>
                                <select name="class_section_id" id="filter-class-section-id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($classSections as $data)
                                        <option value="{{ $data->id }}" data-class-id="{{ $data->class_id }}">
                                            {{ $data->class->name }} {{ $data->section->name }} - {{ $data->class->medium->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-3">
                                <label for="filter-subject-id" class="filter-menu">{{__("Subject")}}</label>
                                @if (Auth::user()->hasRole('School Admin'))
                                    <select name="class_subject_id" id="filter-class-subject-id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        {{-- <option value="data-not-found">-- {{ __('no_data_found') }} --</option> --}}
                                        @foreach ($classSubjects as $item)
                                            <option value="{{ $item->id }}" data-class-id="{{ $item->class_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select name="class_subject_id" id="filter-subject-id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        {{-- <option value="data-not-found">-- {{ __('no_data_found') }} --</option> --}}
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->class_subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('online-exam-question.show', 1) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "{{__('online').' '.__('exam').' '.__('questions')}}-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="onlineExamQuestionsQueryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="online_exam_question_id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="class_name" data-formatter="ClassSectionFormatter">{{ __('Class') }}</th>
                                <th scope="col" data-field="subject_name">{{ __('subject') }}</th>
                                <th scope="col" data-field="question" data-escape="false">{{ __('question')}}</th>
                                <th scope="col" data-field="options" data-formatter="optionsFormatter">{{ __('option') }}</th>
                                <th scope="col" data-field="answers" data-formatter="answersFormatter">{{ __('answer') }}</th>
                                <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
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
        $(document).ready(function () {
            $("#filter-class-section-id").val("").trigger('change');
            setTimeout(() => {
                createCkeditor();
            }, 500);
        });

        $('#table_list').bootstrapTable({
            onLoadSuccess: function () {
                createCkeditor();
            },
        });
    </script>
@endsection
