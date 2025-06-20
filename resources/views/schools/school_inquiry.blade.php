@extends('layouts.master')

@section('title')
    {{ __('schools') .' '. __('inquiry') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('schools') .' '. __('inquiry') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('schools') .' '. __('inquiry') }}
                        </h4>
                        <div class="row" id="toolbar">
                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu" for="status">{{ __('status') }}</label>
                                {!! Form::select('status', ['' => 'All', '0' => 'Pending', '2' => 'Reject'], null, ['class' => 'form-control','id' => 'filter_status_id']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu" for="status">{{ __('date') }}</label>
                                {!! Form::text('filter_date', null, ['id' => 'filter_date', 'placeholder' => __('date'), 'class' => 'daterange form-control']) !!}
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('school-inquiry.list', 1) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "{{__('school') }}-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="schoolInquiryQueryParams" data-escape="true">
                            <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                    <th scope="col" data-field="no">{{ __('no.') }}</th>
                                    <th scope="col" data-field="school_name">{{__('school').' '.__('name') }}</th>
                                    <th scope="col" data-field="school_phone">{{__('school').' '.__('phone')}}</th>
                                    <th scope="col" data-field="school_email">{{ __('school').' '.__('email') }}</th>
                                    <th scope="col" data-visible="false" data-field="school_address">{{__('school').' '.__('address')}}</th>
                                    <th scope="col" data-visible="false" data-field="school_tagline">{{ __('school').' '.__('tagline') }}</th>
                                    <th scope="col" data-field="date" data-formatter="dateFormatter">{{__('date')}}</th>
                                    <th scope="col" data-field="status" data-formatter="schoolInquiryStatusFormatter">{{ __('application').' '. __('status') }}</th>
                                    <th scope="col" data-field="operate" data-formatter="actionColumnFormatter" data-events="schoolInquiryEvents" data-escape="false">{{ __('action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- School Edit Model --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('edit')}} {{__('school')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="create-form" class="pt-3 school-registration-form" action="{{ route('school-inquiry.update') }}">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" id="edit_school_id" name="edit_id">
                            <div class="form-group col-sm-12 col-md-12">
                                <label for="school_name">{{ __('name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="school_name" id="edit_school_name" placeholder="{{__('schools')}}" class="form-control" required readonly>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="school_support_email">{{ __('school').' '.__('email') }} <span class="text-danger">*</span></label>
                                <input type="email" name="school_support_email" id="edit_school_support_email" placeholder="{{__('support').' '.__('email')}}" class="form-control" required readonly>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="school_support_phone">{{ __('school').' '.__('phone') }} <span class="text-danger">*</span></label>
                                <input type="number" name="school_support_phone" min="0" id="edit_school_support_phone" placeholder="{{__('support').' '.__('phone')}}" class="form-control remove-number-increment" required readonly>
                            </div>
                            
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="school_tagline">{{ __('tagline')}} <span class="text-danger">*</span></label>
                                <textarea name="school_tagline" id="edit_school_tagline" cols="30" rows="3" class="form-control" placeholder="{{__('tagline')}}" required readonly></textarea>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="school_address">{{ __('address')}} <span class="text-danger">*</span></label>
                                <textarea name="school_address" id="edit_school_address" cols="30" rows="3" class="form-control" placeholder="{{__('address')}}" required readonly></textarea>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="school_domain">{{ __('domain')}}</label>
                                <div class="input-group mb-3">
                                        <input type="text" class="form-control domain-pattern" name="domain" placeholder="{{ __('domain') }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <span class="input-group-text text-body" id="basic-addon2">.{{ $baseUrlWithoutScheme }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="school_domain">{{ __('School Code Prefix')}}</label> <span class="text-danger">*</span>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control school_code_prefix" id="school_code_prefix" name="school_code_prefix" required placeholder="{{ __('prefix') }}" value="{{ $prefix }}">
                                    <div class="input-group-append">
                                        <input type="text" class="input-group-text text-body school_code" id="basic-addon2" name="school_code" value="{{ $school_code }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($extraFields))
                            <div class="row other-details">

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
                                            {{ Form::text('extra_fields['.$key.'][data]', '', ['class' => 'form-control text-fields', 'id' => $fieldName, 'placeholder' => $data->name, 'readonly' => true, ($data->is_required == 1 ? 'required' : ''), ]) }}
                                            {{-- Number Field --}}
                                        @elseif($data->type == 'number')
                                            {{ Form::number('extra_fields['.$key.'][data]', '', ['min' => 0, 'class' => 'form-control number-fields', 'id' => $fieldName, 'placeholder' => $data->name, 'readonly' => true, ($data->is_required == 1 ? 'required' : '')]) }}

                                            {{-- Dropdown Field --}}
                                        @elseif($data->type == 'dropdown')
                                            {{ Form::select(
                                                'extra_fields['.$key.'][data]',$data->default_values,
                                                null,
                                                [
                                                    'id' => $fieldName,
                                                    'class' => 'form-control select-fields',
                                                    ($data->is_required == 1 ? 'required' : ''),
                                                    'placeholder' => 'Select '.$data->name,
                                                    'disabled' => true,
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
                                                            {{ Form::radio('extra_fields['.$key.'][data]', $value, null, ['id' => $fieldName.'_'.$keyRadio, 'class' => 'radio-fields', 'disabled' => true, ($data->is_required == 1 ? 'required' : '')]) }}
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
                                                            {{ Form::checkbox('extra_fields['.$key.'][data][]', $value, null, ['id' => $fieldName.'_'.$chkKey, 'class' => 'form-check-input chkclass checkbox-fields', 'disabled' => true, ($data->is_required == 1 ? 'required' : '')]) }} {{ $value }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Textarea Field --}}
                                        @elseif($data->type == 'textarea')
                                            {{ Form::textarea('extra_fields['.$key.'][data]', '', ['placeholder' => $data->name, 'readonly' => true, 'id' => $fieldName, 'class' => 'form-control textarea-fields', ($data->is_required ? 'required' : '') , 'rows' => 3]) }}

                                            {{-- File Upload Field --}}
                                        @elseif($data->type == 'file')
                                            <div class="input-group col-xs-12">
                                                {{ Form::file('extra_fields['.$key.'][data]', ['class' => 'file-upload-default', 'disabled' => true, 'id' => $fieldName,($data->is_required == 1 ? 'required' : '')]) }}
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
                            </div>
                        @endif
                        <div class="row">    
                            <div class="form-group col-sm-12 col-md-12 col-lg-6 col-xl-4">
                                <label>{{ __('application').' '. __('status') }} <span class="text-danger">*</span></label><br>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('status', 1) !!}
                                            {{ __('approved') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('status', 2) !!}
                                            {{ __('rejected') }}
                                        </label>
                                    </div>
                                </div>
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
        function successFunction() {
            $('#editAdminModal').modal('hide');
        }
    </script>
@endsection
