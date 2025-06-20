@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('form-fields') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('form-fields') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            {{ __('create') . ' ' . __('form-fields') }}
                        </h4>
                        <form class="pt-3 mt-6 create-form" method="POST" data-success-function="formSuccessFunction" action="{{ route('school-custom.store') }}">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-5">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" onkeypress="validateInput(event)" placeholder="{{__('name')}}" class="form-control" required>
                                </div>
                                <div class="form-group col-sm-12 col-md-5">
                                    <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                    <select name="type" id="type-field" class="form-control type-field">
                                        <option value="text" selected>{{__('Text')}}</option>
                                        <option value="number">{{ __('Numeric') }}</option>
                                        <option value="dropdown">{{ __('Dropdown') }}</option>
                                        <option value="radio">{{ __('Radio Button') }}</option>
                                        <option value="checkbox">{{ __('Checkbox') }}</option>
                                        <option value="textarea">{{ __('TextArea') }}</option>
                                        <option value="file">{{ __('File Upload') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-2">
                                    <label>{{ __('required') }} </label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input required-field" name="required" id="customSwitch1">
                                        <label class="custom-control-label" for="customSwitch1"></label>
                                    </div>
                                </div>
                            </div>

                            {{-- Option Section --}}
                            <div class="default-values-section" style="display: none">
                                <div class="mt-4" data-repeater-list="default_data">
                                    <div class="col-md-5 pl-0 mb-4">
                                        <button type="button" class="btn btn-success add-new-option" data-repeater-create title="Add new row">
                                            <span><i class="fa fa-plus"></i> {{__('add_new_option')}}</span>
                                        </button>
                                    </div>
                                    <div class="row option-section" data-repeater-item>
                                        <div class="form-group col-md-5">
                                            <label>{{ __('option') }} - <span class="option-number">1</span> <span class="text-danger">*</span></label>
                                            <input type="text" name="option" placeholder="{{__('text')}}" class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-1 pl-0 mt-4">
                                            <button data-repeater-delete type="button" class="btn btn-icon btn-inverse-danger remove-default-option" title="{{__('remove').' '.__('option')}}" disabled>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End Of Option Section --}}

                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('form-fields') }}
                        </h4>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" id="preview-fields" data-toggle="modal" data-target="#previewFieldModal">{{__('preview').' '.__('form-fields')}}</button>
                        </div>
                        <div class="col-12 mt-4 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>
                        <table aria-describedby="mydesc" class='table reorder-table-row' id='table_list'
                               data-toggle="table" data-url="{{ route('school-custom-field.list', 1) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-trim-on-search="false"
                               data-mobile-responsive="true" data-use-row-attr-func="true"
                               data-reorderable-rows="true" data-maintain-selected="true"
                               data-export-data-type='all' data-export-options='{ "fileName": "{{__('form-fields')}}-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="queryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name">{{ __('name') }}</th>
                                <th scope="col" data-field="type">{{ __('type') }}</th>
                                <th scope="col" data-field="is_required" data-formatter="yesAndNoStatusFormatter">{{ __('is').' '.__('required') }}</th>
                                <th scope="col" data-field="default_values" data-formatter="formFieldDefaultValuesFormatter">{{ __('Default Values') }}</th>
                                <th scope="col" data-field="rank" data-sortable="false">{{ __('rank') }}</th>
                                <th scope="col" data-field="operate" data-sortable="false" data-events="formFieldsEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                        <span class="d-block mb-4 mt-2 text-danger small">{{ __('draggable_rows_notes') }}</span>
                        <div class="mt-1 d-none d-md-block">
                            <button id="change-order-school-form-field" class="btn btn-theme">{{ __('update_rank') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Preview Fields Model --}}
    <div class="modal fade" id="previewFieldModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('preview').' '.__('form-fields')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row preview-content">
                        @if(!empty($formFields))
    
                            {{-- Loop the FormData --}}
                            @foreach ($formFields as $data)
                                <div class="form-group col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                    {{-- Label For Field --}}
                                    <label>{{$data->name}} @if($data->is_required)
                                            <span class="text-danger">*</span>
                                        @endif</label>
    
                                    {{-- Text Field --}}
                                    @if($data->type == 'text')
                                        <input type="text" name="{{$data->name}}" class="form-control" placeholder="{{$data->name}}" @if($data->is_required) required @endif>
    
                                        {{-- Number Field --}}
                                    @elseif($data->type == 'number')
                                        <input type="number" min="0" name="{{$data->name}}" class="form-control" placeholder="{{$data->name}}" @if($data->is_required) required @endif>
    
                                        {{-- Dropdown Field --}}
                                    @elseif($data->type == 'dropdown')
                                        <select class="form-control" @if($data->is_required) required @endif>
                                            <option value="" disabled selected>Select {{$data->name}}</option>
                                            @if(!empty($data->default_values))
                                                @foreach ($data->default_values as $value)
                                                    <option value="{{$value}}">{{$value}}</option>
                                                @endforeach
                                            @endif
                                        </select>
    
                                        {{-- Radio Field --}}
                                    @elseif($data->type == 'radio')
                                        <div class="d-flex flex-wrap">
                                            @if(!empty($data->default_values))
                                                @foreach ($data->default_values as $value)
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="{{$data->name}}" value="{{$value}}">
                                                            {{$value}}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
    
                                        {{-- Checkbox Field --}}
                                    @elseif($data->type == 'checkbox')
                                        <div class="d-flex flex-wrap">
                                            @foreach ($data->default_values as $value)
                                                <div class="form-check mr-3">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input chkclass" value="{{$value}}">{{ $value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
    
                                        {{-- Textarea Field --}}
                                    @elseif($data->type == 'textarea')
                                        <textarea placeholder="{{ $data->name }}" class="form-control" @if($data->is_required) required @endif></textarea>
    
                                        {{-- File Upload Field --}}
                                    @elseif($data->type == 'file')
                                        <div class="input-group">
                                            <input type="file" name="admin_image" class="file-upload-default" @if($data->is_required) required @endif />
                                            <input type="text" class="form-control file-upload-info" disabled placeholder="{{ __('image') }}" required/>
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ __('edit') . ' ' . __('form-fields') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="pt-3 edit-form edit-common-validation-rules" action="{{ url('school-custom-fields') }}" novalidate="novalidate">
                    <input type="hidden" name="edit_id" id="edit-id" value=""/>
                    <div class="modal-body">
                        <div class="form-group col-sm-12">
                            <label>{{ __('name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" onkeypress="validateInput(event)" id="edit-name" placeholder="{{__('name')}}" class="form-control" required>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>{{ __('type') }} <span class="text-danger">*</span></label>
                            <select id="edit-type-select" class="form-control edit-type-field">
                                <option value="text" selected>Text</option>
                                <option value="number">Numeric</option>
                                <option value="dropdown">Dropdown</option>
                                <option value="radio">Radio Button</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="textarea">TextArea</option>
                                <option value="file">File Upload</option>
                            </select>

                            {!! Form::hidden('type', "", ['id' => 'edit-type-field-value']) !!}
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label>{{ __('required') }} </label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="edit_required" id="customSwitch2">
                                <label class="custom-control-label" id="edit-required-toggle" for="customSwitch2"></label>
                            </div>
                        </div>

                        {{-- Option Section --}}
                        <div class="edit-default-values-section ml-4" style="display: none">
                            <div class="mt-4" data-repeater-list="edit_default_data">
                                <div class="pl-0 mb-4">
                                    <button type="button" class="btn btn-success add-new-edit-option" data-repeater-create title="{{__('add_new_option')}}">
                                        <span><i class="fa fa-plus"></i> {{__('add_new_option')}}</span>
                                    </button>
                                </div>
                                <div class="row edit-option-section" data-repeater-item>
                                    <div class="form-group col-md-10">
                                        <label>{{ __('option') }} - <span class="edit-option-number">1</span> <span class="text-danger">*</span></label>
                                        <input type="text" name="option" placeholder="{{__('text')}}" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-1 pl-0 mt-4">
                                        <button data-repeater-delete type="button" class="btn btn-icon btn-inverse-danger remove-edit-default-option" title="{{__('remove').' '.__('option')}}" disabled>
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- End Option Section --}}
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
@section('script')
    <script>
        function formSuccessFunction() {
            $('#type-field').val('text').trigger('change');
            $('[data-repeater-item]').slice(2).remove();
        }

        function validateInput(event) {
            // Get the ASCII code of the key that was pressed
            var charCode = event.which || event.keyCode;

            // Allow letters (A-Z, a-z) and space (ASCII code 32)
            if (!(charCode >= 65 && charCode <= 90) && !(charCode >= 97 && charCode <= 122) && !(charCode === 32)) {
                event.preventDefault(); // Prevent invalid character input
            }
        }
    </script>
@endsection
