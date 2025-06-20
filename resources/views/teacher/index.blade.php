@extends('layouts.master')

@section('title')
    {{ __('teacher') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_teacher') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_teacher') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" action="{{ route('teachers.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, ['required', 'placeholder' => __('first_name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', null, ['required', 'placeholder' => __('last_name'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'male',true) !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'female') !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('mobile', null, ['required', 'placeholder' => __('mobile'),'min' => 1 , 'class' => 'form-control remove-number-increment']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', null, ['required', 'placeholder' => __('dob'), 'class' => 'datepicker-popup-no-future form-control','autocomplete'=>'off']) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="image">{{ __('image') }} <span class="text-info text-small">(308px*338px)</span></label>
                                    <input type="file" name="image" class="file-upload-default" accept="image/png,image/jpeg,image/jpg"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" id="image" disabled="" placeholder="{{ __('image') }}"/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('qualification') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('qualification', null, ['required', 'placeholder' => __('qualification'), 'class' => 'form-control', 'rows' => 3]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('current_address', null, ['required', 'placeholder' => __('current_address'), 'class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('permanent_address', null, ['required', 'placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="salary">{{__('Salary') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="salary" id="salary" placeholder="{{__('Salary')}}" class="form-control" min="0" required>
                                </div>
                                @hasFeature('Teacher Management')
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="joining_date">{{ __('joining_date') }}</label>
                                    {!! Form::text('joining_date', null, ['placeholder' => __('joining_date'), 'class' => 'datepicker-popup form-control','autocomplete'=>'off']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('status', 1) !!}
                                                {{ __('Active') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('status', 0,true) !!}
                                                {{ __('inactive') }}
                                            </label>
                                        </div>
                                    </div>
                                    <span class="text-danger small">{{ __('Note :- Activating this will consider in your current subscription cycle') }}</span>
                                </div>
                                @if(count($extraFields))
                                    {{-- Loop the FormData --}}
                                    @foreach ($extraFields as $key => $data)
                                        @if ($data->user_type == 2)
                                            <div class="form-group col-sm-12 col-md-4">
                                                {{-- Edit Extra Details ID --}}
                                                {{ Form::hidden('extra_fields['.$key.'][id]', '', ['id' => $data->type.'_'.$key.'_id']) }}
    
                                                {{-- Form Field ID --}}
                                                {{ Form::hidden('extra_fields['.$key.'][form_field_id]', $data->id, ['id' => $data->type.'_'.$key.'_id']) }}
    
    
                                                    {{-- Add lable to all the elements excluding checkbox --}}
                                                    @if($data->type != 'radio' && $data->type != 'checkbox')
                                                        <label>{{$data->name}} @if($data->is_required)
                                                                <span class="text-danger">*</span>
                                                            @endif</label>
                                                    @endif
    
                                                    {{-- Text Field --}}
                                                    @if($data->type == 'text')
                                                        {{ Form::text('extra_fields['.$key.'][data]', '', ['class' => 'form-control text-fields', 'id' => $data->type.'_'.$key, 'placeholder' => $data->name, ($data->is_required == 1 ? 'required' : '')]) }}
                                                        {{-- Number Field --}}
                                                    @elseif($data->type == 'number')
                                                        {{ Form::number('extra_fields['.$key.'][data]', '', ['min' => 0, 'class' => 'form-control number-fields', 'id' => $data->type.'_'.$key, 'placeholder' => $data->name, ($data->is_required == 1 ? 'required' : '')]) }}
    
                                                        {{-- Dropdown Field --}}
                                                    @elseif($data->type == 'dropdown')
                                                        {{ Form::select('extra_fields['.$key.'][data]',$data->default_values,null,
                                                            ['id' => $data->type.'_'.$key,'class' => 'form-control select-fields',
                                                                ($data->is_required == 1 ? 'required' : ''),
                                                                'placeholder' => 'Select '.$data->name
                                                            ]
                                                        )}}
    
                                                        {{-- Radio Field --}}
                                                    @elseif($data->type == 'radio')
                                                        <label class="d-block">{{$data->name}} @if($data->is_required)
                                                                <span class="text-danger">*</span>
                                                            @endif</label>
                                                        <div class="">
                                                            @if(count($data->default_values))
                                                                @foreach ($data->default_values as $keyRadio => $value)
                                                                    <div class="form-check mr-2">
                                                                        <label class="form-check-label">
                                                                            {{ Form::radio('extra_fields['.$key.'][data]', $value, null, ['id' => $data->type.'_'.$keyRadio, 'class' => 'radio-fields',($data->is_required == 1 ? 'required' : '')]) }}
                                                                            {{$value}}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
    
                                                        {{-- Checkbox Field --}}
                                                    @elseif($data->type == 'checkbox')
                                                        <label class="d-block">{{$data->name}} @if($data->is_required)
                                                                <span class="text-danger">*</span>
                                                            @endif</label>
                                                        @if(count($data->default_values))
                                                            <div class="row col-lg-12 col-xl-6 col-md-12 col-sm-12">
                                                                @foreach ($data->default_values as $chkKey => $value)
                                                                    <div class="mr-2 form-check">
                                                                        <label class="form-check-label">
                                                                            {{ Form::checkbox('extra_fields['.$key.'][data][]', $value, null, ['id' => $data->type.'_'.$chkKey, 'class' => 'form-check-input chkclass checkbox-fields',($data->is_required == 1 ? 'required' : '')]) }} {{ $value }}
    
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
    
                                                        {{-- Textarea Field --}}
                                                    @elseif($data->type == 'textarea')
                                                        {{ Form::textarea('extra_fields['.$key.'][data]', '', ['placeholder' => $data->name, 'id' => $data->type.'_'.$key, 'class' => 'form-control textarea-fields', ($data->is_required ? 'required' : '') , 'rows' => 3]) }}
    
                                                        {{-- File Upload Field --}}
                                                    @elseif($data->type == 'file')
                                                        <div class="input-group col-xs-12">
                                                            {{ Form::file('extra_fields['.$key.'][data]', ['class' => 'file-upload-default', 'id' => $data->type.'_'.$key, ($data->is_required ? 'required' : '')]) }}
                                                            {{ Form::text('', '', ['class' => 'form-control file-upload-info', 'disabled' => '', 'placeholder' => __('image')]) }}
                                                            <span class="input-group-append">
                                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                            </span>
                                                        </div>
                                                        <div id="file_div_{{$key}}" class="mt-2 d-none file-div">
                                                            <a href="" id="file_link_{{$key}}" target="_blank">{{$data->name}}</a>
                                                        </div>
    
                                                    @endif
                                                </div>
                                            @endif
                                    @endforeach
                                @endif
                               
                                @endHasFeature
                                <hr class="col-sm-12 col-md-12">
                                {{-- allowances --}}
                                <div class="form-group col-sm-12 col-md-6">
                                    <h4 class="mb-3">{{ __('allowances') }}</h4>

                                    <div class="form-group col-md-12 col-sm-12 allowance-div">
                                        <div data-repeater-list="allowance_data">
                                            <div class="row allowance_type_div" id="allowance_type_div" data-repeater-item>
                                                <div class="form-group col-xl-4">
                                                    <label>{{ __('allowance_type') }} </label>
                                                    <select id="allowance_id" name="allowance[0][id]" class="form-control allowance_id">
                                                        <option value="">--{{ __('select') }}--</option>
                                                        @foreach ( $allowances as  $allowance)
                                                            <option value="{{ $allowance->id }}" data-value="{{ (isset($allowance->amount)) ? $allowance->amount : $allowance->percentage }}" data-type="{{ (isset($allowance->amount)) ? 'amount' : 'percentage' }}">{{ $allowance->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-xl-4" id="amount_allowance_div" style="display: none">
                                                    <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                                    <input type="text" id="allowance_amount" name="allowance[0][amount]" class="allowance_amount form-control" placeholder="{{ __('amount') }}" required>
                                                </div>
                                                
                                                <div class="form-group col-xl-4" id="percentage_allowance_div" style="display: none">
                                                    <label>{{ __('percentage') }} <span class="text-danger">*</span></label>
                                                    <input type="text" id="allowance_percentage" name="allowance[0][percentage]" class="allowance_percentage form-control" placeholder="{{ __('percentage') }}" required>
                                                </div>

                                                <div class="form-group col-xl-1 mt-4">
                                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-allowance-div" data-repeater-delete>
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group col-sm-12 mt-4">
                                        <button type="button" class="btn btn-inverse-success add-allowance-div"
                                                data-repeater-create>
                                            <i class="fa fa-plus"></i> {{ __('add_new_allowances') }}
                                        </button>
                                    </div>
                                </div>
                                

                                {{-- deductions --}}

                                <div class="form-group col-sm-12 col-md-6">
                                    <h4 class="mb-3">{{ __('deductions') }}</h4>

                                    <div class="form-group col-sm-12 deduction-div">
                                        <div data-repeater-list="deduction_data">
                                            <div class="row deduction_type_div" id="deduction_type_div" data-repeater-item>
                                                <div class="form-group col-xl-4">
                                                    <label>{{ __('deduction_type') }} </label>
                                                    <select id="deduction_id" name="deduction[0][id]" class="form-control deduction_id">
                                                        <option value="">--{{ __('select') }}--</option>
                                                        @foreach ( $deductions as  $deduction)
                                                            <option value="{{ $deduction->id }}" data-value="{{ (isset($deduction->amount)) ? $deduction->amount : $deduction->percentage }}" data-type="{{ (isset($deduction->amount)) ? 'amount' : 'percentage' }}">{{ $deduction->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group col-xl-4" id="amount_deduction_div" style="display: none">
                                                    <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                                    <input type="text" id="deduction_amount" name="deduction[0][amount]" class="deduction_amount form-control" placeholder="{{ __('amount') }}" required>
                                                </div>
                                                
                                                <div class="form-group col-xl-4" id="percentage_deduction_div" style="display: none">
                                                    <label>{{ __('percentage') }} <span class="text-danger">*</span></label>
                                                    <input type="text" id="deduction_percentage" name="deduction[0][percentage]" class="deduction_percentage form-control" placeholder="{{ __('percentage') }}" required>
                                                </div>

                                                <div class="form-group col-xl-1 mt-4">
                                                    <button type="button" class="btn btn-inverse-danger btn-icon remove-deduction-div" data-repeater-delete>
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div> 
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group col-sm-12 mt-4">
                                        <button type="button" class="btn btn-inverse-success add-deduction-div"
                                                data-repeater-create>
                                            <i class="fa fa-plus"></i> {{ __('add_new_deduction') }}
                                        </button>
                                    </div>
                                </div>

                                
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
                            {{ __('list_teacher') }}
                        </h4>
                        <div class="row" id="toolbar">
                            <div class="form-group col-12">
                                <button id="update-status" class="btn btn-secondary" disabled><span class="update-status-btn-name">{{ __('Inactive') }}</span></button>
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('active')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Inactive")}}</a>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                       data-url="{{ route('teachers.show',[1]) }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "teacher-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                                       data-query-params="activeDeactiveQueryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th data-field="state" data-checkbox="true"></th>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="first_name">{{ __('first_name') }}</th>
                                        <th scope="col" data-field="last_name">{{ __('last_name') }}</th>
                                        <th scope="col" data-field="gender">{{ __('gender') }}</th>
                                        <th scope="col" data-field="email">{{ __('email') }}</th>
                                        <th scope="col" data-field="mobile">{{ __('mobile') }}</th>
                                        <th scope="col" data-field="dob" data-visible="false" data-formatter="dateFormatter">{{ __('dob') }}</th>
                                        <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                        <th scope="col" data-field="staff.qualification">{{ __('qualification') }}</th>
                                        <th scope="col" data-field="current_address">{{ __('current_address') }}</th>
                                        <th scope="col" data-field="permanent_address">{{ __('permanent_address') }}</th>
                                        <th scope="col" data-field="staff.salary" data-visible="false"> {{ __('Salary') }}</th>
                                        <th data-events="teacherEvents" scope="col" data-formatter="actionColumnFormatter" data-field="operate" data-escape="false">{{ __('action') }}</th>
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


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('edit_teacher') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="editdata" class="edit-form" action="{{ url('teachers') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control', 'id' => 'email']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', null, ['required', 'placeholder' => __('first_name'), 'class' => 'form-control', 'id' => 'first_name']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', null, ['required', 'placeholder' => __('last_name'), 'class' => 'form-control', 'id' => 'last_name']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-12">
                                <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'male', null, ['class' => 'form-check-input edit', 'id' => 'gender']) !!}
                                            {{ __('male') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'female', null, ['class' => 'form-check-input edit', 'id' => 'gender']) !!}
                                            {{ __('female') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('mobile') }} <span class="text-danger">*</span></label>
                                {!! Form::number('mobile', null, ['required', 'min' => 1 , 'placeholder' => __('mobile'), 'class' => 'form-control remove-number-increment', 'id' => 'mobile']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                {!! Form::text('dob', null, ['required', 'placeholder' => __('dob'), 'class' => 'datepicker-popup-no-future form-control', 'id' => 'edit-dob']) !!}
                                <span class="input-group-addon input-group-append"></span>
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label for="edit-image">{{ __('image') }} <span class="text-info text-small">(308px*338px)</span></label><br>
                                {{-- <input type="file" name="image" id="edit_image" class="form-control" placeholder="{{__('image')}}"> --}}
                                <input type="file" name="image" class="file-upload-default" accept="image/png,image/jpeg,image/jpg"/>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" id="edit-image" disabled="" placeholder="{{ __('image') }}"/>
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                    </span>
                                </div>
                                <div style="width: 60px;" class="mt-2">
                                    <img src="" id="edit-teacher-image-tag" class="img-fluid w-100" alt=""/>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('qualification') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('qualification', null, ['required', 'placeholder' => __('qualification'), 'class' => 'form-control', 'rows' => 3, 'id' => 'qualification']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('current_address', null, ['required', 'placeholder' => __('current_address'), 'class' => 'form-control', 'rows' => 3, 'id' => 'current_address']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12 col-lg-4">
                                <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('permanent_address', null, ['required', 'placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3, 'id' => 'permanent_address']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="edit_salary">{{__('Salary') }} <span class="text-danger">*</span></label>
                                <input type="number" name="salary" min="0" id="edit_salary" placeholder="{{__('Salary')}}" class="form-control" required>
                            </div>

                            <div class="form-group col-sm-12 col-md-4">
                                <label for="joining_date">{{ __('joining_date') }}</label>
                                {!! Form::text('joining_date', null, ['placeholder' => __('joining_date'), 'class' => 'datepicker-popup form-control','autocomplete'=>'off','id' => 'edit_joining_date']) !!}
                            </div>

                            @if(!empty($extraFields))

                                {{-- Loop the FormData --}}
                                @foreach ($extraFields as $key => $data)
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
                                                {{ Form::file('extra_fields['.$key.'][data]', ['class' => 'file-upload-default', 'id' => $fieldName]) }}
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
                                @endforeach
                            @endif

                        </div>

                        <div class="row">
                            
                        </div>

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
        let userIds;
        $('.table-list-type').on('click', function (e) {
            let value = $(this).data('id');
            let ActiveLang = window.trans['Active'];
            let DeactiveLang = window.trans['Inactive'];
            if (value === "" || value === 0 || value == null) {
                $("#update-status").data("id")
                $('.update-status-btn-name').html(DeactiveLang);
            } else {
                $('.update-status-btn-name').html(ActiveLang);
            }
        })


        function updateUserStatus(tableId, buttonClass) {
            var selectedRows = $(tableId).bootstrapTable('getSelections');
            var selectedRowsValues = selectedRows.map(function (row) {
                return row.id;
            });
            userIds = JSON.stringify(selectedRowsValues);

            if (buttonClass != null) {
                if (selectedRowsValues.length) {
                    $(buttonClass).prop('disabled', false);
                } else {
                    $(buttonClass).prop('disabled', true);
                }
            }
        }

        $('#table_list').bootstrapTable({
            onCheck: function (row) {
                updateUserStatus("#table_list", '#update-status');
            },
            onUncheck: function (row) {
                updateUserStatus("#table_list", '#update-status');
            },
            onCheckAll: function (rows) {
                updateUserStatus("#table_list", '#update-status');
            },
            onUncheckAll: function (rows) {
                updateUserStatus("#table_list", '#update-status');
            }
        });
        $("#update-status").on('click', function (e) {
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
                    let url = baseUrl + '/teachers/change-status-bulk';
                    let data = new FormData();
                    data.append("ids", userIds)

                    function successCallback(response) {
                        $('#table_list').bootstrapTable('refresh');
                        $('#update-status').prop('disabled', true);
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
    <script type="text/javascript">

        document.addEventListener('DOMContentLoaded', function() {
            let allowanceCounter = 1; // Initialize a counter for new allowance rows

            // Function to toggle visibility of amount and percentage fields
            function toggleAllowanceFields(allowanceTypeElement) {
                const selectedOption = allowanceTypeElement.options[allowanceTypeElement.selectedIndex];
                const allowanceType = selectedOption.getAttribute('data-type');
                const allowanceValue = selectedOption.getAttribute('data-value');
                const allowanceDiv = allowanceTypeElement.closest('.allowance_type_div');
                const amountDiv = allowanceDiv.querySelector('#amount_allowance_div');
                const percentageDiv = allowanceDiv.querySelector('#percentage_allowance_div');
            
                if (allowanceType === 'amount') {
                    percentageDiv.style.display = 'none';
                    amountDiv.style.display = 'block';
                    allowanceDiv.querySelector('.allowance_amount').value = allowanceValue;
                    allowanceDiv.querySelector('.allowance_percentage').value = '';
                } else if (allowanceType === 'percentage') {
                    amountDiv.style.display = 'none';
                    percentageDiv.style.display = 'block';
                    allowanceDiv.querySelector('.allowance_amount').value = '';
                    allowanceDiv.querySelector('.allowance_percentage').value = allowanceValue;
                } else {
                    amountDiv.style.display = 'none';
                    percentageDiv.style.display = 'none';
                }
            }

            // Attach change event listener to the initial allowance type dropdown
            document.querySelectorAll('.allowance_id').forEach(function(element) {
                element.addEventListener('change', function() {
                    toggleAllowanceFields(element);
                });
            });

            // Repeater functionality to handle adding new allowance rows
            const addAllowanceButton = document.querySelector('.add-allowance-div');
            const allowanceDataContainer = document.querySelector('[data-repeater-list="allowance_data"]');

            addAllowanceButton.addEventListener('click', function() {
                const newItem = allowanceDataContainer.querySelector('[data-repeater-item]').cloneNode(true);

                // Clear input values
                allowanceDataContainer.querySelector('#allowance_type_div').style.display = '';
                newItem.querySelectorAll('input').forEach(input => input.value = '');
                newItem.querySelector('.allowance_id').value = '';
                newItem.querySelector('#amount_allowance_div').style.display = 'none';
                newItem.querySelector('#percentage_allowance_div').style.display = 'none';

                // Update the name attributes
                newItem.querySelectorAll('[name]').forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/\[\d+\]/, `[${allowanceCounter}]`);
                    input.setAttribute('name', newName);
                });

                // Increment the counter
                allowanceCounter++;

                // Add event listeners to new elements
                newItem.querySelector('.allowance_id').addEventListener('change', function() {
                    toggleAllowanceFields(newItem.querySelector('.allowance_id'));
                });
                newItem.querySelector('.remove-allowance-div').addEventListener('click', function() {
                    newItem.remove();
                });

                allowanceDataContainer.appendChild(newItem);
            });

            // Attach click event listener to the initial remove button
            document.querySelectorAll('.remove-allowance-div').forEach(function(button) {
                button.addEventListener('click', function() {
                    // button.closest('[data-repeater-item]').remove();
                    button.closest('[data-repeater-item]').style.display = 'none';
                });
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            let deductionCounter = 1; // Initialize a counter for new deduction rows

            // Function to toggle visibility of amount and percentage fields
            function toggleDeductionFields(deductionTypeElement) {
                const selectedOption = deductionTypeElement.options[deductionTypeElement.selectedIndex];
                const deductionType = selectedOption.getAttribute('data-type');
                const deductionValue = selectedOption.getAttribute('data-value');
                const deductionDiv = deductionTypeElement.closest('.deduction_type_div');
                const amountDiv = deductionDiv.querySelector('#amount_deduction_div');
                const percentageDiv = deductionDiv.querySelector('#percentage_deduction_div');
            
                if (deductionType === 'amount') {
                    percentageDiv.style.display = 'none';
                    amountDiv.style.display = 'block';
                    deductionDiv.querySelector('.deduction_amount').value = deductionValue;
                    deductionDiv.querySelector('.deduction_percentage').value = '';
                } else if (deductionType === 'percentage') {
                    amountDiv.style.display = 'none';
                    percentageDiv.style.display = 'block';
                    deductionDiv.querySelector('.deduction_amount').value = '';
                    deductionDiv.querySelector('.deduction_percentage').value = deductionValue;
                } else {
                    amountDiv.style.display = 'none';
                    percentageDiv.style.display = 'none';
                }
            }

            // Attach change event listener to the initial deduction type dropdown
            document.querySelectorAll('.deduction_id').forEach(function(element) {
                element.addEventListener('change', function() {
                    toggleDeductionFields(element);
                });
            });

            // Repeater functionality to handle adding new deduction rows
            const addDeductionButton = document.querySelector('.add-deduction-div');
            const deductionDataContainer = document.querySelector('[data-repeater-list="deduction_data"]');

            addDeductionButton.addEventListener('click', function() {
                const newItem = deductionDataContainer.querySelector('[data-repeater-item]').cloneNode(true);

                deductionDataContainer.querySelector('#deduction_type_div').style.display = '';
                // Clear input values
                newItem.querySelectorAll('input').forEach(input => input.value = '');
                newItem.querySelector('.deduction_id').value = '';
                newItem.querySelector('#amount_deduction_div').style.display = 'none';
                newItem.querySelector('#percentage_deduction_div').style.display = 'none';

                // Update the name attributes
                newItem.querySelectorAll('[name]').forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/\[\d+\]/, `[${deductionCounter}]`);
                    input.setAttribute('name', newName);
                });

                // Increment the counter
                deductionCounter++;

                // Add event listeners to new elements
                newItem.querySelector('.deduction_id').addEventListener('change', function() {
                    toggleDeductionFields(newItem.querySelector('.deduction_id'));
                });
                newItem.querySelector('.remove-deduction-div').addEventListener('click', function() {
                    newItem.remove();
                });

                deductionDataContainer.appendChild(newItem);
            });

            // Attach click event listener to the initial remove button
            document.querySelectorAll('.remove-deduction-div').forEach(function(button) {
                button.addEventListener('click', function() {
                    // button.closest('[data-repeater-item]').remove();
                    button.closest('[data-repeater-item]').style.display = 'none';
                });
            });
        });

        

    </script>
@endsection
