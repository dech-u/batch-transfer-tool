@extends('layouts.master')

@section('title')
    {{ __('leave') }} {{ __('details') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') }} {{ __('leave') }} {{ __('details') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') }} {{ __('leave') }} {{ __('details') }}
                        </h4>
                        <div class="row" id="toolbar">

                            @if ($staffs)
                                <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                    <label for="filter_session_year_id" class="filter-menu">{{ __('staff') }}</label>
                                    {!! Form::select('session_year_id', $staffs, null, [
                                        'class' => 'form-control',
                                        'id' => 'filter_staff_id',
                                        'placeholder' => __('select') .' '. __('staff')
                                    ]) !!}
                                </div>
                            @endif

                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                <label for="filter_session_year_id" class="filter-menu">{{ __('Session Year') }}</label>
                                {!! Form::select('session_year_id', $sessionYear, $current_session_year->id ?? null, [
                                    'class' => 'form-control',
                                    'id' => 'filter_session_year_id',
                                ]) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                       data-url="{{ route('leave.detail') }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="false"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false" data-toolbar="#toolbar"
                                       data-show-columns="false" data-show-refresh="true" data-fixed-columns="false"
                                       data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "leave-<?= date('d-m-y') ?>","ignoreColumn":
                                    ["operate"]}'
                                       data-query-params="leaveDetailQueryParams">
                                    <thead>
                                    <tr>
                                        <th scope="col" rowspan="2" data-field="no"> {{ __('no.') }} </th>
                                        <th scope="col" rowspan="2" data-field="month"> {{ __('month') }} </th>
                                        <th scope="col" rowspan="2" data-field="allocated">{{ __('allocated') }}
                                        </th>
                                        <th scope="col" class="text-center" colspan="3">{{ __('used') }}</th>
                                        <th scope="col" data-width="200" class="text-center" colspan="2">{{ __('remaining') }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col" data-field="used_cl">{{ __('CL') }} <small class="text-info">({{ __('casual_leave') }})</small></th>
                                        <th scope="col" data-field="lwp">{{ __('LWP') }} <small class="text-info">({{ __('leave_without_pay') }})</small></th>
                                        <th scope="col" data-field="total">{{ __('total') }} </th>

                                        <th scope="col" data-field="remaining_cl">{{ __('CL') }} </th>
                                        <th scope="col" data-field="remaining_total">{{ __('total') }} </th>
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
