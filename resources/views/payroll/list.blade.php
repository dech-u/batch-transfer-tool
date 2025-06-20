@extends('layouts.master')

@section('title')
    {{ __('payroll') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('payroll') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('list') . ' ' . __('payroll') }}</h4>
                        <div class="row" id="toolbar">
                            <div class="form-group col-sm-12 col-md-3">
                                <label class="filter-menu">{{ __('year') }} </label>
                                {!! Form::selectRange(
                                    'year',
                                    $FirstsessionYear,
                                    date('Y', strtotime(Carbon\Carbon::now())),
                                    date('Y', strtotime(Carbon\Carbon::now())),
                                    ['class' => 'form-control', 'id' => 'filter_year'],
                                ) !!}
                            </div>
                        </div>
                        <div class="staff-table">

                            <table aria-describedby="mydesc" class='table' id='table_list'
                                   data-toggle="table" data-url="{{ route('payroll.slip.list') }}"
                                   data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                                   data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="false"
                                   data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                                   data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                                   data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                                   data-export-data-type='all' data-query-params="payrollListQueryParams"
                                   data-toolbar="#toolbar"
                                   data-export-options='{ "fileName": "payroll-list-<?= date('d-m-y') ?>"
                                ,"ignoreColumn":["operate"]}' data-show-export="true" data-escape="true">
                                <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                    <th scope="col" data-field="no">{{ __('no.') }}</th>
                                    <th scope="col" data-field="title">{{ __('title') }}</th>
                                    <th scope="col" data-field="basic_salary" data-formatter="amountFormatter">{{ __('basic_salary') }}</th>
                                    <th scope="col" data-field="amount" data-formatter="amountFormatter" data-sortable="false">{{ __('net_salary') }}</th>
                                    <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

