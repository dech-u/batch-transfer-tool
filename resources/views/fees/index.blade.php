@extends('layouts.master')

@section('title')
    {{ __('Manage Fees')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Fees')}}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <form id="create-form" class="create-form common-validation-rules" action="{{ route('fees.store') }}" method="POST" novalidate="novalidate" data-success-function="successFunction">
                            <div class="border border-secondary rounded-lg mb-2 p-2 mb-3">
                                <div class="col-12 mt-1">
                                    <h4 class="card-title">
                                        {{ __('Create Fees')}}
                                    </h4>
                                    <hr>
                                </div>
                                <div class="row col-12">
                                    <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                        <label>{{ __('Prefix Name') }} <span class="text-danger">*</span> <span class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="{{__("Fees names will be created based on the Classes Prefix will be appended before Class Name.eg. Prefix Name - Class Name")}}"></span></label>
                                        {!! Form::text('name', null, ['placeholder' => __('Prefix Name'), 'class' => 'form-control','required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12 col-lg-6">
                                        <label for="class-id">{{ __('Classes') }} <span class="text-danger">*</span></label>
                                        <select name="class_id[]" id="class-id" class="class-id form-control select2-dropdown select2-hidden-accessible" tabindex="-1" aria-hidden="true" required multiple>
                                            @foreach ($classes as $item)
                                                <option value="{{ $item->id }}">{{ $item->full_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-check w-fit-content">
                                            <label class="form-check-label user-select-none">
                                                <input type="checkbox" class="form-check-input" id="select-all" value="1">{{__("Select All")}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-secondary rounded-lg mb-2 p-2 mb-3">
                                <div class="col-12 mt-1">
                                    <h4 class="card-title">
                                        {{ __('Compulsory Fees') }}
                                    </h4>
                                    <hr>
                                </div>
                                <div class="compulsory-fees-types">
                                    <div data-repeater-list="compulsory_fees_type" class="row col-12">
                                        <div class="row col-12 mb-3" data-repeater-item>
                                            <div class="form-group col-md-12 col-lg-4">
                                                <select name="fees_type_id" id="fees_type_id" class="form-control fees_type" aria-label="Fees Type" required>
                                                    <option value="">{{ __('Select Fees Type')}}</option>
                                                    @foreach ($feesTypeData as $feesType)
                                                        <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-12 col-lg-3">
                                                {!! Form::text('amount', null, ['class' => 'form-control amount','placeholder' => __('enter').' '.__('fees').' '.__('amount'),'id' => 'amount', 'required' => true, 'min' => 0, "data-convert" => "number"]) !!}
                                            </div>

                                            <div class="col-md-12 col-lg-1">
                                                <button type="button" class="btn btn-inverse-danger btn-icon remove-fees-type" data-repeater-delete>
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col-md-4 pl-0 mb-4">
                                            <button class="btn btn-dark btn-sm" type="button" data-repeater-create>
                                                <i class="fa fa-plus-circle fa-3x mr-2" aria-hidden="true"></i>
                                                {{__('Add New Data')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 row">
                                    <div class="form-group col-sm-12 col-md-6 col-lg-3">
                                        <label>{{ __('due_date')}} <span class="text-danger">*</span></label>
                                        {{ Form::text('due_date', null, ['class' => 'datepicker-popup-no-past form-control', 'placeholder' => __('due_date'), 'required','autocomplete'=>'off']) }}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 col-lg-3">
                                        <label>{{ __('due_charges')}} <span class="text-danger">*</span> <span class="text-info small">( {{__('in_percentage')}} )</span></label>
                                        {{ Form::number('due_charges_percentage', null, ['id'=>'due_charges_percentage','class' => 'form-control', 'placeholder' => __('due_charges'), 'required', 'min' => 0]) }}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 col-lg-3">
                                        <label>{{ __('due_charges')}} <span class="text-danger">*</span> <span class="text-info small">( {{__('Amount')}} )</span></label>
                                        {{ Form::number('due_charges_amount', null, ['id'=>'due_charges_amount','class' => 'form-control', 'placeholder' => __('due_charges'), 'required', 'min' => 0]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="border border-secondary rounded-lg mb-2 p-2 mb-3">
                                <div class="col-12 mt-1">
                                    <h4 class="card-title">
                                        {{ __('Fees Installment')}}
                                    </h4>
                                    <hr>
                                </div>
                                <div class="mb-4">
                                    <div class="form-inline col-md-4">
                                        <label>{{__('include_fees_installment')}}</label> <span class="ml-1 text-danger">*</span>
                                        <div class="ml-4 d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="include_fee_installments" class="fees-installment-toggle user-select-none" value="1">
                                                    {{ __('Enable') }}
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="include_fee_installments" class="fees-installment-toggle user-select-none" value="0" checked>
                                                    {{ __('Disable') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="fees-installment-repeater" style="display: none">
                                    <div data-repeater-list="fees_installments">
                                        <div data-repeater-item class="col-12 row">
                                            <div class="form-group col-lg-12 col-xl-3">
                                                <label>{{ __('installment_name') }} <span class="text-danger">*</span></label>
                                                {{ Form::text('name', null, ['class' => 'form-control installment-name', 'placeholder' => __('installment') . ' ' . __('name'), 'required']) }}
                                            </div>
                                            <div class="form-group col-lg-12 col-xl-3">
                                                <label>{{ __('due_date') }} <span class="text-danger">*</span></label>
                                                {{ Form::text('due_date', null, ['class' => 'datepicker-popup-no-past form-control installment-due-date', 'placeholder' => __('due_date'),'autocomplete'=>'off' ,'required']) }}
                                            </div>
                                            <div class="form-group col-md-12 col-lg-2">
                                                <label>{{ __('Due Charges Type') }} <span class="text-danger">*</span></label>
                                                <div>
                                                    <div class="form-check form-check-inline my-0 d-flex">
                                                        <label class="form-check-label mr-2">
                                                            {!! Form::radio('due_charges_type',"fixed" , false, ['class' => 'form-check-input', 'required' => true]) !!}
                                                            {{ __('Fixed Amount') }}
                                                            <i class="input-helper"></i>
                                                        </label>
                                                        <span data-toggle="tooltip" data-placement="top" title="{{__("Due Charges will be in fixed amount once the due date is passed")}}" class="fa fa-info-circle mb-2"></span>
                                                    </div>
                                                    <div class="form-check form-check-inline my-0 d-flex">
                                                        <label class="form-check-label mr-2">
                                                            {!! Form::radio('due_charges_type', "percentage", true, ['class' => 'form-check-input', 'required' => true]) !!}
                                                            {{ __('Percentage') }}
                                                            <i class="input-helper"></i>
                                                        </label>
                                                        <span data-toggle="tooltip" data-placement="top" title="{{__("Due Charges will be calculated in % on minimum Installment Amount")}}" class="fa fa-info-circle mb-2"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-12 col-xl-3">
                                                <label>{{ __('due_charges') }} <span class="text-danger">*</span><span class="text-info small"></span></label>
                                                {!! Form::number("due_charges",null, ["class" => "installment-due-charges form-control" , "placeholder" => trans('due_charges') , "required" => true , "data-convert" => "number", "min"=>0]) !!}
                                            </div>
                                            <div class="form-group col-lg-12 col-xl-1 mt-4">
                                                <button type="button" class="btn btn-inverse-danger btn-icon remove-installment-fee" data-repeater-delete>
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col-md-4 pl-0 mb-4 mt-4">
                                            <button id="add-installment" class="btn btn-dark btn-sm" type="button" data-repeater-create>
                                                <i class="fa fa-plus-circle fa-3x mr-2" aria-hidden="true"></i>
                                                {{__('Add New Data')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border border-secondary rounded-lg mb-2 p-2 mb-3">
                                <div class="col-12 mt-1">
                                    <h4 class="card-title">
                                        {{ __('Optional Fees') }}
                                    </h4>
                                    <small class="text-danger">* {{__("Optional Fees does not support Due charges & Installment Facility")}}</small>
                                    <hr>
                                </div>
                                <div class="optional-fees-types">
                                    <div data-repeater-list="optional_fees_type" class="row col-12">
                                        <div class="row col-12 mb-3" data-repeater-item>
                                            <div class="form-group col-md-12 col-lg-4">
                                                <select name="fees_type_id" id="fees_type_id" class="form-control fees_type" aria-label="Fees Type" required>
                                                    <option value="">{{ __('Select Fees Type')}}</option>
                                                    @foreach ($feesTypeData as $feesType)
                                                        <option value="{{ $feesType->id }}">{{ $feesType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-12 col-lg-3">
                                                {!! Form::text('amount', null, ['class' => 'form-control amount','placeholder' => __('enter').' '.__('fees').' '.__('amount'),'id' => 'amount', 'required' => true, 'min' => 0, "data-convert" => "number"]) !!}
                                            </div>

                                            <div class="col-md-12 col-lg-1">
                                                <button type="button" class="btn btn-inverse-danger btn-icon remove-fees-type" data-repeater-delete>
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="col-md-4 pl-0 mb-4">
                                            <button class="btn btn-dark btn-sm" type="button" data-repeater-create>
                                                <i class="fa fa-plus-circle fa-3x mr-2" aria-hidden="true"></i>
                                                {{__('Add New Data')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <input class="btn btn-theme float-right" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('List Fees')}}
                        </h4>


                        <div class="row" id="toolbar">
                            <div class="form-group col-sm-12 col-md-3">
                                <label for="filter-session-year-id" class="filter-menu">{{__("session_year")}}</label>
                                {!! Form::select('session_year_id', $sessionYear, $defaultSessionYear->id, ['class' => 'form-control', 'id' => 'filter_session_year_id']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-3">
                                <label for="filter-medium_id" class="filter-menu">{{__("medium")}}</label>
                                {!! Form::select('medium_id', $mediums, null, ['class' => 'form-control', 'id' => 'filter_medium_id', 'placeholder' => __('all')]) !!}
                            </div>
                        </div>
                        <div class="col-12 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('fees.show',1) }}"
                                       data-click-to-select="true" data-side-pagination="server"
                                       data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                       data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                                       data-show-refresh="true" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-query-params="feesQueryParams" data-escape="true" data-escape-title="false">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{__('id')}}</th>
                                        <th scope="col" data-field="no">{{__('no.')}}</th>
                                        <th scope="col" data-field="name" data-sortable="true">{{__('name')}}</th>
                                        <th scope="col" data-field="class.full_name" data-visible="false">{{__('Class')}}</th>
                                        <th scope="col" data-field="due_date" data-sortable="true">{{__('due_date')}}</th>
                                        <th scope="col" data-field="due_charges" data-align="center">{{__('due_charges')}} <small>(%)</small></th>
                                        <th scope="col" data-field="installments" data-formatter="feesInstallmentFormatter">{{__('Fees Installment')}}</th>
                                        <th scope="col" data-field="fees_type" data-align="left" data-formatter="feesTypeFormatter">{{ __('Fees') }} {{__('type')}}</th>
                                        <th scope="col" data-field="compulsory_fees" data-align="center">{{ __('Compulsory Amount')}}</th>
                                        <th scope="col" data-field="total_fees" data-align="center">{{ __('Total Amount')}}</th>
                                        <th scope="col" data-events="feesEvents" data-field="operate" data-escape="false">{{__('action')}}</th>
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

@endsection
@section('js')
    <script>
        $('.compulsory-fees-types').find('[data-repeater-create]').click();

        function successFunction() {
            $('.compulsory-fees-types [data-repeater-item]').slice(1).empty();
            $('.fees-installment-repeater [data-repeater-item]').slice(0).empty();
            $('.fees-installment-repeater').hide();
        }
    </script>
@endsection
