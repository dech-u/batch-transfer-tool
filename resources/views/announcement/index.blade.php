@extends('layouts.master')

@section('title')
    {{ __('announcement') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('announcement') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('announcement') }}
                        </h4>
                        <form class="create-form pt-3 common-validation-rules" data-success-function="formSuccessFunction" action="{{ route('announcement.store') }}" method="POST" novalidate="novalidate">
                            {!! Form::hidden('user_id', Auth::user()->id, ['id' => 'user_id']) !!}
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>{{ __('description') }}</label>
                                    {!! Form::textarea('description', null, ['rows' => '2', 'placeholder' => __('description'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>{{ __('files') }} </label>
                                    {{-- <input type="file" name="file[]" class="form-control" multiple/> --}}
                                    <input type="file" name="file[]" multiple id="uploadInput" class="file-upload-default" accept="image/*,application/pdf,.doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled=""
                                               placeholder="{{ __('File') }}"/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                    <div class="mt-2 text-small text-danger">
                                        {{ __('note') }} : {{__('Please note that only image or document files are allowed for upload')}}.
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input checkbox_add_url" name="checkbox_add_url" id="checkbox_add_url" value="">{{ __('add_url') }}
                                        </label>
                                    </div>

                                    <input type="text" name="add_url" id="add_url" placeholder="{{ __('add_url') }}" class="form-control mt-2 add_url" value="" style="display: none;">
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('class_sections') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id[]" required multiple id="class-section-id" class="class_section_id form-control select2-dropdown select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                        {{-- @if (Auth::user()->hasRole('Teacher'))
                                            <option value="">{{ __('select') }} {{ __('class_section') }}</option>
                                        @endif --}}
                                        @foreach ($class_section as $item)
                                            <option value="{{ $item->id }}" data-class="{{ $item->class->id }}">{{ $item->full_name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- @if (!Auth::user()->hasRole('Teacher')) --}}
                                        <div class="form-check w-fit-content">
                                            <label class="form-check-label user-select-none">
                                                <input type="checkbox" class="form-check-input" id="select-all" value="1">{{__("Select All")}}
                                            </label>
                                        </div>
                                    {{-- @endif --}}
                                </div>
                            </div>
                            <div class="row">

                                @if (Auth::user()->hasRole('Teacher'))
                                    <div class="form-group col-sm-12 col-md-6 show_class_section_id">
                                        <label>{{ __('subject') }}</label>
                                        <select name="subject_id" id="subject-id" class="form-control">
                                            <option value="">-- {{ __('Select Subject') }} --</option>
                                            <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                            @foreach ($subjectTeachers as $item)
                                                <option value="{{ $item->subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                            </div>
                            {{-- <input class="btn btn-theme" type="submit" value={{ __('submit') }}> --}}
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('announcement') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                       data-url="{{ route('announcement.show',1) }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                       data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                       data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                       data-sort-order="desc" data-maintain-selected="true"
                                       data-export-data-type='all'
                                       data-show-export="true"
                                       data-export-options='{ "fileName": "announcement-list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}'
                                       data-query-params="queryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="title">{{ __('title') }}</th>
                                        <th scope="col" data-events="tableDescriptionEvents" data-formatter="descriptionFormatter" data-field="description">{{ __('description') }}</th>
                                        <th scope="col" data-field="assignto" data-formatter="ClassSectionFormatter" >{{ __('assign_to') }}</th>
                                        <th scope="col" data-field="file" data-formatter="fileFormatter">{{ __('files') }}</th>
                                        <th data-events="announcementEvents" data-width="150" scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit') . ' ' . __('announcement') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="edit-form" action="{{ url('announcement') }}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control', 'id' => 'title']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('description') }}</label>
                                {!! Form::textarea('description', null, ['rows' => 2, 'placeholder' => __('description'), 'class' => 'form-control', 'id' => 'description']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('class_section') }}</label>
                                <select name="class_section_id[]" @if(!Auth::user()->hasRole('Teacher')) multiple @endif id="edit-class-section-id" class="form-control edit_class_section_id @if(!Auth::user()->hasRole('Teacher')) select2-dropdown select2-hidden-accessible @endif" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if (Auth::user()->hasRole('Teacher'))
                                        <option value="">{{ __('select') . ' ' . __('Class Section') }}</option>
                                    @endif
                                    @foreach ($class_section as $item)
                                        <option value="{{ $item->id }}" data-class="{{ $item->class->id }}">{{ $item->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (Auth::user()->hasRole('Teacher'))
                                <div class="form-group col-sm-12 col-md-12">
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
                            @endif

                            <br>
                            <br>
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('files_attachment') }} :- </label>
                                <div id="old_files" class="mt-2"></div>
                            </div>

                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('upload_new_files') }} </label>
                                <input type="file" name="file[]" multiple id="edit_uploadInput" class="file-upload-default" accept="image/*,application/pdf,.doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"/>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled=""
                                           placeholder="{{ __('File') }}"/>
                                    <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                        </span>

                                        
                                </div>
                                <div class="mt-2 text-small text-danger">
                                    {{ __('note') }} : {{__('Please note that only image or document files are allowed for upload')}}.
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input edit_checkbox_add_url" name="checkbox_add_url" id="edit_checkbox_add_url" value="">{{ __('add_url') }}
                                    </label>
                                </div>

                                <input type="text" name="add_url" id="edit_add_url" placeholder="{{ __('add_url') }}" class="form-control mt-2 edit_add_url" value="" style="display: none;">
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                    </div>
                </form>
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

        const uploadInput = document.getElementById('uploadInput');
        const edit_uploadInput = document.getElementById('edit_uploadInput');
        multi_files(uploadInput);
        multi_files(edit_uploadInput);
        
        function multi_files(uploadInput)
        {
            // Event listener to handle file selection
            uploadInput.addEventListener('change', function () {
                // Update file counter with the number of selected files
                $(this).parent().find('.form-control').val(this.files.length + (this.files.length === 1 ? ' file selected' : ' files selected'));
                
                if ($('#checkbox_add_url').is(':checked')) {
                    $('#add_url').show().val('');
                } else {
                    $('#add_url').hide().val('');
                }
               
                
                if ($('.edit_checkbox_add_url').is(':checked') ) {
                    $('#edit_add_url').val('');
                } else {
                    $('#edit_add_url').val('');
                }
            });
        }
        
    </script>
@endsection
