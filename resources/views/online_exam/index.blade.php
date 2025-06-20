@extends('layouts.master')

@section('title')
{{ __('manage_online_exam') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_online_exam') }}
            </h3>
        </div>
        <div class="row">
            @can('online-exam-create')
                <div class="col-md-12 grid-margin stretch-card search-container">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                {{ __('create_online_exam') }}
                            </h4>
                            <form class="pt-3 mt-6" id="create-form" method="POST" action="{{ route('online-exam.store') }}">
                                {!! Form::hidden('user_id', Auth::user()->id, ['id' => 'user_id']) !!}
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('Class Section') }} <span class="text-danger">*</span></label>
                                        <select required name="class_section_id[]" id="class-section-id" class="form-control select2 online-exam-class-section-id select2-dropdown select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true" multiple>
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
                                        <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                        @if (Auth::user()->hasRole('School Admin'))
                                            <select required name="subject_id" id="subject-id" class="form-control">
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
                                <div class="row">
                                    <div class="form-group col-md-4 col-lg-4 col-xl-4">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('title', "", ['required','id' => "title","placeholder" => trans('title'),"class" => "form-control" ]) !!}
                                    </div>
                                    <div class="form-group col-md-4 col-lg-4 col-xl-2">
                                        <label>{{ __('exam_key') }} <span class="text-danger">*</span></label>
                                        {!! Form::number('exam_key', "", ['required','id' => "key","placeholder" => trans('exam_key'),"class" => "form-control","min" => 1,'readonly' => true]) !!}
                                    </div>
                                    <div class="form-group col-md-4 col-lg-4 col-xl-2">
                                        <label>{{ __('duration') }} <span class="text-danger">*</span> <span class="text-info small">( {{__('in_minutes')}} )</span></label>
                                        {!! Form::number('duration', "", ['required','id' => "duration","placeholder" => trans('duration'),"class" => "form-control","min" => 1]) !!}
                                    </div>
                                    <div class="form-group col-md-4 col-lg-4 col-xl-2">
                                        <label>{{ __('start_date')}} <span class="text-danger">*</span></label>
                                        {!! Form::datetimeLocal('start_date', "", ['required','id' => "start-date timepicker-example","placeholder" => trans('start_date'),"class" => "form-control"]) !!}
                                    </div>
                                    <div class="form-group col-md-4 col-lg-4 col-xl-2">
                                        <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                        {!! Form::datetimeLocal('end_date', "", ['required','id' => "end-date","placeholder" => trans('end_date'),"class" => "form-control"]) !!}
                                    </div>
                                </div>

                                {{-- <input class="btn btn-theme" id="add-online-exam-btn" type="submit" value={{ __('submit') }}> --}}
                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('exams') }}
                        </h4>
                        <div class="d-block">
                            <div class="">
                                <div class="col-12 text-right d-flex justify-content-end text-right align-items-end">
                                    <b><a href="#" class="table-list-type active mr-2" data-id="0">{{ __('all') }}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{ __('Trashed') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="toolbar">

                            <div class="form-group col-12 col-sm-12 col-md-3">
                                <label for="filter-session-year-id" class="filter-menu">{{__("session_year")}}</label>
                                {!! Form::select('session_year_id', $sessionYear, $defaultSessionYear->id, ['class' => 'form-control', 'id' => 'filter_session_year_id']) !!}
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-3">
                                <label for="filter-class-section-id" class="filter-menu">{{__("class_section")}}</label>
                                <select name="class_section_id" id="filter-class-section-id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($classSections as $data)
                                        <option value="{{ $data->id }}" data-class-id="{{ $data->class_id }}">
                                            {{ $data->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-3">
                                <label for="filter-subject-id" class="filter-menu">{{__("subject")}}</label>
                                @if (Auth::user()->hasRole('School Admin'))
                                    <select name="class_subject_id" id="filter-class-subject-id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        {{-- <option value="data-not-found">-- {{ __('no_data_found') }} --</option> --}}
                                        @foreach ($classSubjects as $item)
                                            <option value="{{ $item->id }}" data-class-id="{{ $item->class_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select name="subject_id" id="filter-subject-id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        {{-- <option value="data-not-found">-- {{ __('no_data_found') }} --</option> --}}
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->subject_id }}" data-class-section="{{ $item->class_section_id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                @endif

                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('online-exam.show', 1) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "{{__('online').' '.__('exam')}}-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="onlineExamQueryParams" data-escape="true" data-escape-title="false">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="class_section_with_medium" data-formatter="ClassSectionFormatter">{{ __('class_section') }}</th>
                                <th scope="col" data-field="subject_name">{{ __('subject') }}</th>
                                <th scope="col" data-field="title">{{ __('title') }}</th>
                                <th scope="col" data-field="exam_key" data-align="center">{{ __('exam_key')}}</th>
                                <th scope="col" data-field="duration" data-align="center">{{ __('duration')}} <span class="text-info small">( {{__('in_minutes')}} )</span></th>
                                <th scope="col" data-field="start_date" data-formatter="dateTimeFormatter" data-sortable="true">{{ __('start_date') }}</th>
                                <th scope="col" data-field="end_date" data-formatter="dateTimeFormatter" data-sortable="true">{{ __('end_date') }}</th>
                                <th scope="col" data-field="total_questions" data-align="center">{{ __('total').' '.__('questions') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-formatter="actionColumnFormatter" data-events="onlineExamEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- model --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('edit')}} {{__('online')}} {{__('exam')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="edit-form" class="pt-3 edit-form" action="{{ url('online-exam') }}">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{ __('title') }} <span class="text-danger">*</span></label>
                            <input type="text" id="edit-online-exam-title" required name="edit_title" placeholder="{{ __('title') }}" class="form-control"/>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>{{ __('exam') }} {{__('key')}} <span class="text-danger">*</span></label>
                                <input type="number" id="edit-online-exam-key" required name="edit_exam_key" placeholder="{{ __('exam_key') }}" class="form-control"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('duration') }} <span class="text-danger">*</span></label><span class="text-info small">( {{__('in_minutes')}} )</span>
                                <input type="number" id="edit-online-exam-duration" required name="edit_duration" placeholder="{{ __('duration') }}" class="form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>{{ __('start_date')}} <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="edit-online-exam-start-date" required name="edit_start_date" placeholder="{{__('start_date')}}" class='form-control'>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" id="edit-online-exam-end-date" required name="edit_end_date" placeholder="{{ __('end_date')}}" class='form-control'>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
                        <input class="btn btn-theme" type="submit" value={{ __('submit') }} />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {

            function random_exam_key() { 
                return Math.floor(100000 + Math.random() * 900000);
            }

            // Initialize the exam key if not set
            if ($("#key").val() === "") {
                let rndInt = random_exam_key();
                $('#key').attr("value", rndInt);
            }

            // Generate a new exam key when the form is submitted
            $("form").submit(function(event) {
                event.preventDefault();
                let rndInt = random_exam_key();
                $('#key').attr("value", rndInt);
            });

        });
    </script>
@endsection