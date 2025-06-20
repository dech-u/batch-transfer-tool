@extends('layouts.master')

@section('title')
    {{ __('payroll_setting') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('payroll_setting') }}
            </h3>
        </div>    
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') }} {{ __('payroll_setting') }}
                        </h4>

                        <form id="create-form" class="pt-3" action="{{ route('payroll-setting.store') }}" method="POST"
                        novalidate="novalidate">
                            @csrf
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                        <div class="col-12 d-flex row">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" checked name="type" value="allowance" required="required">
                                                    {{ __('allowances') }}
                                                </label>
                                            </div>
        
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="type" value="deduction" required="required">
                                                    {{ __('deductions') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['required','class' => 'form-control','placeholder' => __('name')]) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="">{{ __('fixed_amount') }}</label>
                                        {!! Form::number('amount', null, ['class' => 'form-control','placeholder' => __('fixed_amount'), 'min' => '1']) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="">{{ __('percentage') }}</label>
                                        {!! Form::number('percentage', null, ['class' => 'form-control','placeholder' => __('percentage'), 'min' => '0.1', 'max' => '100']) !!}
                                    </div>
                                    
                                </div>
                                <span class="text-info text-small">{{ __('Note:- Compulsory Choose one between fixed amount or percentage') }}</span>
                                <br>
                                <br>
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
                            {{ __('list_payroll_setting') }}
                        </h4>
                        <div class="d-block">
                            <div class="">
                                <div class="col-12 text-right d-flex justify-content-end text-right align-items-end">
                                    <b><a href="#" class="table-list-type active mr-2" data-id="0">{{ __('all') }}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{ __('Trashed') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="toolbar">
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="">{{ __('type') }}</label>
                                {!! Form::select('filter_type', ['allowance' => __('allowances'), 'deduction' => __('deductions')], 'allowance', ['class' => 'form-control', 'id' => 'filter_type']) !!}
                            </div>
                        </div>

                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('payroll-setting.show',1) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                               data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                               data-export-options='{ "fileName": "allowances-list-<?= date('d-m-y') ?>","ignoreColumn":["operate"]}'
                               data-escape="true" data-query-params="PayrollSettingsqueryParams">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name">{{ __('name') }}</th>
                                <th scope="col" data-field="amount">{{ __('amount') }}</th>
                                <th scope="col" data-field="percentage">{{ __('percentage') }}</th>
                                <th scope="col" data-events="paryollSettingsEvents" data-field="operate" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{__('edit').' '.__('payroll_setting')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 section-edit-form" id="edit-form" action="{{ route('payroll-setting.update',1) }}" novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value=""/>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                        <div class="col-12 d-flex row">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input allowance" id="edit_type" checked name="type" value="allowance" required="required">
                                                    {{ __('allowances') }}
                                                </label>
                                            </div>
        
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input deduction" id="edit_type" name="type" value="deduction" required="required">
                                                    {{ __('deductions') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['required','class' => 'form-control','placeholder' => __('name'), 'id' => 'name']) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12" id="amount-div" style="display: none">
                                        <label for="">{{ __('fixed_amount') }}</label>
                                        {!! Form::number('amount', null, ['class' => 'form-control','placeholder' => __('fixed_amount'), 'id' => 'amount', 'min' => '1']) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12" id="percentage-div" style="display: none">
                                        <label for="">{{ __('percentage') }}</label>
                                        {!! Form::number('percentage', null, ['required','class' => 'form-control','placeholder' => __('percentage'), 'id' => 'percentage', 'min' => '0.1', 'max' => '100']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('close')}}</button>
                                <input class="btn btn-theme" type="submit" value={{ __('submit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>                
        
    </div>
@endsection
        