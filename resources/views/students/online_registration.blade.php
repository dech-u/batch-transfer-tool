@extends('layouts.master')

@section('title')
    {{ __('admission_inquiries') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('admission_inquiries') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('manage') . ' ' . __('students') }}
                        </h4>
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <span class="text-danger">{{ __('Note Upon approving the students application please be aware that the student will remain in inactive status by default You will need to manually activate the student through the Student Details menu in order to complete the enrollment process') }}</span>
                            </div>
                        </div>
                        <div class="row mt-3" id="toolbar">
                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu">{{ __('Class') }} <span class="text-danger">*</span></label>
                                <select name="filter_class_id" id="filter_class_id" class="form-control">
                                    <option value="">{{ __('select_class') }}</option>
                                    @foreach ($classes as $class)
                                        <option value={{ $class->id }}>{{$class->full_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu">{{ __('assign_class_section') }} <span class="text-danger">*</span></label>
                                <select required name="filter_class_section_id" class="form-control" id="filter_class_section_id">
                                    <option value="">{{ __('select_class_section') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                    
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('students.online-registration', 1)}}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                       data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                                       data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                       data-sort-order="desc" data-maintain-selected="true" data-export-types="['pdf','json', 'xml', 'csv', 'txt', 'sql', 'doc', 'excel']" data-show-export="true" data-export-options='{ "fileName": "students-list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}' data-query-params="studentsQueryParams" data-check-on-init="true" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th data-field="state" data-checkbox="true"></th>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="user.id" data-visible="false">{{ __('User Id') }}</th>
                                        <th scope="col" data-field="user.full_name">{{ __('name') }}</th>
                                        <th scope="col" data-field="user.dob" data-formatter="dateFormatter">{{ __('dob') }}</th>
                                        <th scope="col" data-field="user.image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                        <th scope="col" data-field="class.full_name">{{ __('class_name') }}</th>
                                        <th scope="col" data-field="user.gender">{{ __('gender') }}</th>
                                        <th scope="col" data-field="admission_date" data-formatter="dateFormatter">{{ __('admission_date') }}</th>
                                        <th scope="col" data-field="guardian.email">{{ __('guardian') . ' ' . __('email') }}</th>
                                        <th scope="col" data-field="guardian.full_name">{{ __('guardian') . ' ' . __('name') }}</th>
                                        <th scope="col" data-field="guardian.mobile">{{ __('guardian') . ' ' . __('mobile') }}</th>
                                        <th scope="col" data-field="guardian.gender">{{ __('guardian') . ' ' . __('gender') }}</th>

                                        {{-- Admission form fields --}}
                                        @foreach ($extraFields as $field)
                                            <th scope="col" data-visible="false" data-escape="false" data-field="{{ $field->name }}">{{ $field->name }}</th>
                                        @endforeach
                                        {{-- End admission form fields --}}

                                        @canany(['student-edit','student-delete'])
                                            <th data-events="studentEvents" class="align-button text-center" scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                                        @endcanany
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <div id="application-status-options" style="display: none;" class="mt-5">
                                <label>{{ __('application_status') }} <span class="text-danger">*</span></label><br>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('application_status', '1', true , ['class'=> 'application-status-accepted','id' =>"edit-application-status-accepted",'required']) !!}
                                            {{ __('accepted') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('application_status', '0', false , ['class'=> 'application-status-rejected','id' =>"edit-application-status-rejected", 'required']) !!}
                                            {{ __('rejected') }}
                                        </label>
                                    </div>
                                </div>
                                <button id="application-status" class="btn btn-theme mt-3">{{ __('Submit') }}</button>
                            </div>
                        </div>
                        @can('student-edit')
                            <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('students') }}</h4><br>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true"><i class="fa fa-close"></i></span>
                                            </button>
                                        </div>
                                        <form id="create-form" class="online-registration-form" novalidate="novalidate" action="{{ route('update-application-status',1)}}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">

                                                <input type="hidden" name="edit_id" id="edit_id">
                                                <input type="hidden" name="edit_user_id" id="edit_user_id">
                                                <div class="row">
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('admission_no') }} <span class="text-danger">*</span></label>
                                                        {!! Form::text('admission_no', null, ['placeholder' => __('admission_no'), 'class' => 'form-control', 'id' => 'edit_admission_no' ,'readonly'=>true]) !!}
                    
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('Session Year') }} <span class="text-danger">*</span></label>
                                                        <select required name="session_year_id" class="form-control" id="session_year_id">
                                                            @foreach ($sessionYears as $sessionYear)
                                                                <option value="{{ $sessionYear->id }}">{{$sessionYear->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('Class') }} <span class="text-danger">*</span></label>
                                                        <select required name="class_id" class="form-control" id="edit_student_class_id">
                                                            <option value="">{{ __('select_class') }}</option>
                                                            @foreach ($classes as $class)
                                                                <option value={{ $class->id }}>{{$class->full_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('Class Section') }} <span class="text-danger">*</span></label>
                                                        <select name="class_section_id" class="form-control" id="edit_student_class_section_id">
                                                            <option value="">{{ __('select_class_section') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row mt-5">
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                                        {!! Form::text('first_name', null, ['placeholder' => __('first_name'), 'class' => 'form-control', 'id' => 'edit_first_name']) !!}
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                                        {!! Form::text('last_name', null, ['placeholder' => __('last_name'), 'class' => 'form-control', 'id' => 'edit_last_name']) !!}
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                                        {!! Form::text('dob', null, ['placeholder' => __('dob'), 'class' => 'datepicker-popup-no-future form-control', 'id' => 'edit_dob']) !!}
                                                        <span class="input-group-addon input-group-append">
                                                        </span>
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-4">
                                                        <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                                        <div class="d-flex">
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    {!! Form::radio('gender', 'male', false ,['id' => 'male']) !!}
                                                                    {{ __('male') }}
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    {!! Form::radio('gender', 'female', false , ['id' => 'female']) !!}
                                                                    {{ __('female') }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('image') }} </label>
                                                        <input type="file" name="image" class="file-upload-default"/>
                                                        <div class="input-group col-xs-12">
                                                            <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}" required="required" id="edit_image"/>
                                                            <span class="input-group-append">
                                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                            </span>
                                                        </div>
                                                        <div style="width: 100px;">
                                                            <img src="" id="edit-student-image-tag" class="img-fluid w-100" alt=""/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('mobile') }}</label>
                                                        {!! Form::number('mobile', null, ['placeholder' => __('mobile'), 'min' => 1 , 'class' => 'form-control remove-number-increment', 'id' => 'edit_mobile']) !!}
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-6">
                                                        <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                                        {!! Form::textarea('current_address', null, ['required', 'placeholder' => __('current_address'), 'class' => 'form-control', 'rows' => 3,'id'=>'edit-current-address']) !!}
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-6">
                                                        <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                                        {!! Form::textarea('permanent_address', null, ['required', 'placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3,'id'=>'edit-permanent-address']) !!}
                                                    </div>
                                                </div>
                    
                                                @if(!empty($extraFields))
                                                    <div class="row other-details">
                    
                                                        {{-- Loop the FormData --}}
                                                        @foreach ($extraFields as $key => $data)
                                                            @if($data->user_type == 1)
                                                                @php $fieldName = str_replace(' ', '_', $data->name) @endphp
                                                                {{-- Edit Extra Details ID --}}
                                                                {{ Form::hidden('extra_fields['.$key.'][id]', '', ['id' => $fieldName.'_id']) }}
                        
                                                                {{-- Form Field ID --}}
                                                                {{ Form::hidden('extra_fields['.$key.'][form_field_id]', $data->id) }}
                        
                                                                {{-- FormFieldType --}}
                                                                {{ Form::hidden('extra_fields['.$key.'][input_type]', $data->type) }}
                        
                                                                <div class='form-group col-md-12 col-lg-6 col-xl-4 col-sm-12'>
                        
                                                                    {{-- Add lable to all the elements excluding checkbox --}}
                                                                    @if($data->type != 'radio' && $data->type != 'checkbox')
                                                                        <label>{{$data->name}} @if($data->is_required)
                                                                                <span class="text-danger">*</span>
                                                                            @endif</label>
                                                                    @endif
                        
                                                                    {{-- Text Field --}}
                                                                    @if($data->type == 'text')
                                                                        {{ Form::text('extra_fields['.$key.'][data]', '', ['class' => 'form-control text-fields', 'id' => $fieldName, 'placeholder' => $data->name, ($data->is_required == 1 ? 'required' : '')]) }}
                                                                        {{-- Number Field --}}
                                                                    @elseif($data->type == 'number')
                                                                        {{ Form::number('extra_fields['.$key.'][data]', '', ['min' => 0, 'class' => 'form-control number-fields', 'id' => $fieldName, 'placeholder' => $data->name, ($data->is_required == 1 ? 'required' : '')]) }}
                        
                                                                        {{-- Dropdown Field --}}
                                                                    @elseif($data->type == 'dropdown')
                                                                        {{ Form::select(
                                                                            'extra_fields['.$key.'][data]',$data->default_values,
                                                                            null,
                                                                            [
                                                                                'id' => $fieldName,
                                                                                'class' => 'form-control select-fields',
                                                                                ($data->is_required == 1 ? 'required' : ''),
                                                                                'placeholder' => 'Select '.$data->name
                                                                            ]
                                                                        )}}
                        
                                                                        {{-- Radio Field --}}
                                                                    @elseif($data->type == 'radio')
                                                                        <label class="d-block">{{$data->name}} @if($data->is_required)
                                                                                <span class="text-danger">*</span>
                                                                            @endif</label>
                                                                        <div class="row form-check-inline ml-1">
                                                                            @foreach ($data->default_values as $keyRadio => $value)
                                                                                <div class="col-md-12 col-lg-12 col-xl-6 col-sm-12 form-check">
                                                                                    <label class="form-check-label">
                                                                                        {{ Form::radio('extra_fields['.$key.'][data]', $value, null, ['id' => $fieldName.'_'.$keyRadio, 'class' => 'radio-fields',($data->is_required == 1 ? 'required' : '')]) }}
                                                                                        {{$value}}
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                        
                                                                        {{-- Checkbox Field --}}
                                                                    @elseif($data->type == 'checkbox')
                                                                        <label class="d-block">{{$data->name}} @if($data->is_required)
                                                                                <span class="text-danger">*</span>
                                                                            @endif</label>
                                                                        <div class="row form-check-inline ml-1">
                                                                            @foreach ($data->default_values as $chkKey => $value)
                                                                                <div class="col-lg-12 col-xl-6 col-md-12 col-sm-12 form-check">
                                                                                    <label class="form-check-label">
                                                                                        {{ Form::checkbox('extra_fields['.$key.'][data][]', $value, null, ['id' => $fieldName.'_'.$chkKey, 'class' => 'form-check-input chkclass checkbox-fields',($data->is_required == 1 ? 'required' : '')]) }} {{ $value }}
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                        
                                                                        {{-- Textarea Field --}}
                                                                    @elseif($data->type == 'textarea')
                                                                        {{ Form::textarea('extra_fields['.$key.'][data]', '', ['placeholder' => $data->name, 'id' => $fieldName, 'class' => 'form-control textarea-fields', ($data->is_required ? 'required' : '') , 'rows' => 3]) }}
                        
                                                                        {{-- File Upload Field --}}
                                                                    @elseif($data->type == 'file')
                                                                        <div class="input-group col-xs-12">
                                                                            {{ Form::file('extra_fields['.$key.'][data]', ['class' => 'file-upload-default', 'id' => $fieldName,($data->is_required == 1 ? 'required' : '')]) }}
                                                                            {{ Form::text('', '', ['class' => 'form-control file-upload-info', 'disabled' => '', 'placeholder' => __('image')]) }}
                                                                            <span class="input-group-append">
                                                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                                            </span>
                                                                        </div>
                                                                        <div id="file_div_{{$fieldName}}" class="mt-2 d-none file-div">
                                                                            <a href="" id="file_link_{{$fieldName}}" target="_blank">{{$data->name}}</a>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                    
                                                <div class="row">
                                                    <div class="form-group col-sm-12 col-md-4">
                                                        <div class="d-flex">
                                                            <div class="form-check w-fit-content">
                                                                <label class="form-check-label ml-4">
                                                                    <input type="checkbox" class="form-check-input" name="reset_password" value="1">{{ __('reset_password') }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                    
                                                
                    
                                                <hr>
                                                {{-- Guardian Details --}}
                                                <div class="row mt-5">
                                                    <div class="form-group col-sm-12 col-md-12">
                                                        <label>{{ __('guardian') . ' ' . __('email') }} <span class="text-danger">*</span></label>
                                                        <select class="edit-guardian-search form-control" name="guardian_id"></select>
                                                        <input type="hidden" id="edit_guardian_email" name="guardian_email">
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('guardian') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                                        {!! Form::text('guardian_first_name', null, ['placeholder' => __('guardian') . ' ' . __('first_name'), 'class' => 'form-control', 'id' => 'edit_guardian_first_name']) !!}
                                                    </div>
                    
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('guardian') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                                        {!! Form::text('guardian_last_name', null, ['placeholder' => __('guardian') . ' ' . __('last_name'), 'class' => 'form-control', 'id' => 'edit_guardian_last_name']) !!}
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('guardian') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                                        {!! Form::number('guardian_mobile', null, ['placeholder' => __('guardian') . ' ' . __('mobile'), 'class' => 'form-control remove-number-increment', 'min' => 1  ,'id' => 'edit_guardian_mobile']) !!}
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-12">
                                                        <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                                        <div class="d-flex">
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    {!! Form::radio('guardian_gender', 'male', false , ['id' =>"edit-guardian-male"]) !!}
                                                                    {{-- <input type="radio" name="guardian_gender" value="male" id="edit_guardian_male"  disabled> --}}
                                                                    {{ __('male') }}
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    {!! Form::radio('guardian_gender', 'female', false , ['id' =>"edit-guardian-female"]) !!}
                                                                    {{-- <input type="radio" name="guardian_gender" value="female" id="edit_guardian_female" disabled> --}}
                                                                    {{ __('female') }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('image') }} </label>
                                                        <input type="file" name="guardian_image" class="file-upload-default"/>
                                                        <div class="input-group col-xs-12">
                                                            <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}" required="required" id="edit_image"/>
                                                            <span class="input-group-append">
                                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                            </span>
                                                        </div>
                                                        <div style="width: 100px;">
                                                            <img src="" id="edit-guardian-image-tag" class="img-fluid w-100" alt=""/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>
                                                <div class="row mt-5">
                                                    <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                                        <label>{{ __('application_status') }} <span class="text-danger">*</span></label><br>
                                                        <div class="d-flex">
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    {!! Form::radio('application_status', '1', true , ['class'=> 'application-status-accepted','id' =>"edit-application-status-accepted", 'checked','required']) !!}
                                                                    {{ __('accepted') }}
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    {!! Form::radio('application_status', '0', false , ['class'=> 'application-status-rejected','id' =>"edit-application-status-rejected", 'required']) !!}
                                                                    {{ __('rejected') }}
                                                                </label>
                                                            </div>
                                                        </div>
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
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let userIds;
        var applicationStatus;
        let class_section_id = null;

        function updateApplicationStatus(tableId, buttonClass) {
            let selectedRows = $(tableId).bootstrapTable('getSelections');
            let selectedRowsValues = selectedRows.map(function (row) {
                return row.user_id;
            });
           
            class_section_id = $('#filter_class_section_id').val();

            userIds = JSON.stringify(selectedRowsValues);
         

          
            
            if (buttonClass != null) {
                if (selectedRowsValues.length) {
                    $('#application-status-options').show(500);
                    $(buttonClass).prop('disabled', false);
                } else {
                    $('#application-status-options').hide(500);
                    $(buttonClass).prop('disabled', true);
                }
            }
        }


        $('#table_list').bootstrapTable({
            onCheck: function (row) {
                updateApplicationStatus("#table_list", '#application-status');
            },
            onUncheck: function (row) {
                updateApplicationStatus("#table_list", '#application-status');
            },
            onCheckAll: function (rows) {
                updateApplicationStatus("#table_list", '#application-status');
            },
            onUncheckAll: function (rows) {
                updateApplicationStatus("#table_list", '#application-status');
            }
        });
        $("#application-status").on('click', function (e) {
            applicationStatus = $('input[name="application_status"]:checked').val();
            Swal.fire({
                title: window.trans["Are you sure"],
                text: window.trans["Change Status For Selected Users"],
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: window.trans["Yes, Change it"],
                cancelButtonText: window.trans["Cancel"]
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = baseUrl + '/students/update-bulk-application-status';
                    let data = new FormData();
                    data.append("ids", userIds)
                    data.append("application_status", applicationStatus)
                    data.append("class_section_id", class_section_id)
                    function successCallback(response) {
                        $('#table_list').bootstrapTable('refresh');
                        $('#application-status-options').hide();
                        userIds = null;
                        showSuccessToast(response.message);
                    }

                    function errorCallback(response) {
                        showErrorToast(response.message);
                    }

                    ajaxRequest('POST', url, data, null, successCallback, errorCallback);
                }
            })
        })
    </script>
@endsection