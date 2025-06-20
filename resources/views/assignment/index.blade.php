@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('assignment') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('assignment') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('assignment') }}
                        </h4>
                        <form class="pt-3 add-assignment-form" id="create-form" action="{{ route('assignment.store') }}"
                              method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">

                                {!! Form::hidden('user_id', Auth::user()->id, ['id' => 'user_id']) !!}
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('Class') . ' ' . __('section') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="class_section_id[]" id="class-section-id"
                                        class="class_section_id form-control select2-dropdown select2-hidden-accessible" multiple>
                                        {{-- <option value="">--{{ __('select_class_section') }}--</option> --}}
                                        @foreach ($classSections as $section)
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

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="subject-id">{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject-id" class="form-control">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="name">{{ __('assignment_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" placeholder="{{ __('assignment_name') }}" class="form-control"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="instructions">{{ __('assignment_instructions') }}</label>
                                    <textarea id="instructions" name="instructions" placeholder="{{ __('assignment_instructions') }}" class="form-control"></textarea>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('files') }} </label>
                                    <input type="file" name="file[]" class="form-control" multiple accept="image/*,application/pdf,.doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" value=""/>
                                    
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input checkbox_add_url" name="checkbox_add_url" id="checkbox_add_url" value="">{{ __('add_url') }}
                                        </label>
                                    </div>

                                    <input type="text" name="add_url" id="add_url" placeholder="{{ __('add_url') }}" class="form-control mt-2 add_url" value="" style="display: none;">
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="due_date">{{ __('last_submission_date') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="due_date" id="due_date" placeholder="{{ __('last_submission_date') }}" class='form-control'>
                                    <span class="input-group-addon input-group-append"> </span>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="points">{{ __('points') }}</label>
                                    <input type="number" id="points" name="points" placeholder="{{ __('points') }}" class="form-control" min="1"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="resubmission" id="resubmission_allowed" value="">{{ __('resubmission_allowed') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-12" id="extra_days_for_resubmission_div"
                                     style="display: none;">
                                    <div class="row col-sm-12 col-md-4">
                                        <label for="extra_days_for_resubmission">{{ __('extra_days_for_resubmission') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="extra_days_for_resubmission" name="extra_days_for_resubmission" placeholder="{{ __('extra_days_for_resubmission') }}" class="form-control"/>
                                    </div>

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
                        <h4 class="card-title">{{ __('list') . ' ' . __('assignment') }}</h4>

                        <div id="toolbar">
                            <div class="row">
                                {{-- <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                    <label for="filter_subject_id" class="filter-menu">{{__("subject")}}</label>
                                    <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name.' - '.$subject->type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                                    <label for="filter_class_section_id" class="filter-menu">{{__("Class Section")}}</label>
                                    <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($class_section as $class)
                                            <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                                <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                    <label for="filter-class-section-id" class="filter-menu">{{ __('class_section') }}</label>
                                    <select name="class_id" id="filter-class-section-id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($classSections as $data)
                                            <option value="{{ $data->id }}">
                                                {{ $data->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                    <label for="filter-subject-id" class="filter-menu">{{ __('subject') }}</label>
                                    <select name="subject_id" id="filter-subject-id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('all') }}</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->class_subject_id }}" data-class-section="{{ $item->class_section_id }}">{{ $item->subject_with_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                    <label for="filter_session_year_id" class="filter-menu">{{ __('Session Year') }}</label>
                                    <select name="filter_session_year_id" id="filter_session_year_id" class="form-control">
                                        @foreach ($sessionYears as $sessionYear)
                                            <option value="{{ $sessionYear->id }}"
                                                    {{ $sessionYear->default == 1 ? 'selected' : '' }}>
                                                {{ $sessionYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('assignment.show', 1) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                               data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                               data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "assignment-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-query-params="CreateAssignmentSubmissionQueryParams"
                               data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name" data-sortable="false">{{ __('name') }}</th>
                                <th scope="col" data-events="tableDescriptionEvents" data-formatter="descriptionFormatter" data-field="instructions" data-sortable="false">{{ __('instructions') }}</th>
                                <th scope="col" data-field="file" data-sortable="false" data-formatter="fileFormatter">{{ __('files') }}</th>
                                <th scope="col" data-field="class_section_with_medium" data-formatter="ClassSectionFormatter" data-sortable="false">{{ __('Class Section') }}</th>
                                <th scope="col" data-field="class_subject.subject.name_with_type" data-sortable="false"> {{ __('subject') }}</th>
                                <th scope="col" data-field="due_date" data-formatter="dateTimeFormatter" data-sortable="false">{{ __('due_date') }}</th>
                                <th scope="col" data-field="points" data-sortable="false">{{ __('points') }}
                                </th>
                                <th scope="col" data-field="resubmission" data-formatter="yesAndNoStatusFormatter" data-sortable="false">{{ __('resubmission') }}</th>
                                <th scope="col" class="text-wrap" data-field="extra_days_for_resubmission" data-sortable="false">{{ __('extra_days_for_resubmission') }}</th>
                                <th scope="col" data-field="created_by_teacher" data-sortable="false" data-visible="true">{{ __('created_by_teacher') }}</th>
                                <th scope="col" data-field="edited_by_teacher" data-sortable="false" data-visible="true">{{ __('edited_by_teacher') }}</th>
                                <th scope="col" data-field="session_year_id" data-sortable="false" data-visible="false">{{ __('session_year_id') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false"> {{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-sortable="false" data-formatter="dateTimeFormatter" data-visible="false"> {{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-events="assignmentEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('assignment') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-assignment-form" id="edit-form" action="{{ url('assignment') }}" novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value=""/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('class_section') }}</label>
                                        <select name="class_section_id[]" id="edit-class-section-id" class="form-control edit_class_section_id select2-dropdown select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true" multiple>
                                            @foreach ($classSections as $item)
                                                <option value="{{ $item->id }}" data-class="{{ $item->class->id }}">{{ $item->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('subject') }}</label>
                                        <select name="subject_id" id="edit-subject-id" class="form-control edit_subject_id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="">-- {{ __('Select Subject') }} --</option>
                                            <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                            @foreach ($subjectTeachers as $item)
                                                <option value="{{ $item->subject_id }}" data-class-section="{{ $item->class_section_id }}">{{ $item->subject_with_name}}</option>
                                            @endforeach
                                        </select>
                                        {!! Form::hidden('class_subject_id',"", ["id" => "class_subject_id_value"]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="edit_name">{{ __('assignment_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_name" name="name" placeholder="{{ __('assignment_name') }}" class="form-control"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="edit_instructions">{{ __('assignment_instructions') }}</label>
                                        <textarea id="edit_instructions" name="instructions" placeholder="{{ __('assignment_instructions') }}" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('files_attachment') }} </label>
                                        <div id="old_files"></div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('upload_new_files') }} </label>
                                        <input type="file" name="file[]" class="form-control" multiple/>

                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input edit_checkbox_add_url" name="checkbox_add_url" id="edit_checkbox_add_url" value="">{{ __('add_url') }}
                                            </label>
                                        </div>
    
                                        <input type="text" name="add_url" id="edit_add_url" placeholder="{{ __('add_url') }}" class="form-control mt-2 edit_add_url" value="" style="display: none;">
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label for="edit_due_date">{{ __('last_submission_date') }} <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="due_date" id="edit_due_date" placeholder="{{ __('last_submission_date') }}" class='form-control'>
                                        <span class="input-group-addon input-group-append"></span>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label for="edit_points">{{ __('points') }}</label>
                                        <input type="number" id="edit_points" name="points" placeholder="{{ __('points') }}" class="form-control" min="1"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="resubmission" id="edit_resubmission_allowed" value="1">{{ __('resubmission_allowed') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4" id="edit_extra_days_for_resubmission_div"
                                         style="display: none;">
                                        <label for="edit_extra_days_for_resubmission">{{ __('extra_days_for_resubmission') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_extra_days_for_resubmission" name="extra_days_for_resubmission" placeholder="{{ __('extra_days_for_resubmission') }}" class="form-control"/>
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
        </div>
    </div>
@endsection
@section('script')
    <script>
        function formSuccessFunction(response) {
            setTimeout(() => {
                $('.class_section_id').val('').trigger('change');
                $('.edit_class_section_id').val('').trigger('change');
                $('#add_url').hide().val('');
            }, 500);
        }
    </script>
@endsection