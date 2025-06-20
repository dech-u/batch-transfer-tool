@extends('layouts.master')

@section('title')
    {{__('online')}} {{__('fees')}} {{ __('transactions') }} {{__('logs')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{__('online')}} {{__('fees')}} {{ __('transactions') }} {{__('logs')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <div id="toolbar" class="row">
                            <div class="form-group col-md-4">
                                <label class="filter-menu" for="filter_payment_status" style="font-size: 0.86rem;width: 110px">
                                    {{ __('Payment Status') }}
                                </label>
                                <select name="filter_payment_status" id="filter_payment_status" class="form-control">
                                    <option value="">{{__('all')}}</option>
                                    <option value="failed">{{__('failed')}}</option>
                                    <option value="succeed">{{__('succeed')}}</option>
                                    <option value="pending">{{__('pending')}}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="filter-menu" for="filter_paid_status"> {{ __('month') }} </label>
                                {!! Form::select('month', $months, date('n'), ['class' => 'form-control paid-month','placeholder' => __('all')]) !!}
                            </div>

                            <div class="form-group col-md-3">
                                <label class="filter-menu" for="session_year_id"> {{ __('Session Years') }} </label>
                                <select name="session_year_id" id="filter_session_year_id" class="form-control">
                                    @foreach ($session_year_all as $session_year)
                                        <option value="{{ $session_year->id }}"
                                            {{ $session_year->default ? 'selected' : '' }}> {{ $session_year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('fees.transactions.log.list', 1) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "{{__('fees')}}-{{__('transactions')}}-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="feesPaymentTransactionQueryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{__('id')}}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="user.full_name" data-align="center">{{ __('User')}}</th>
                                <th scope="col" data-field="amount" data-align="center">{{ __('Amount')}}</th>
                                <th scope="col" data-field="payment_gateway" data-align="center" data-formatter="feesTransactionParentGateway">{{ __('Payment Gateway') }}</th>
                                <th scope="col" data-field="payment_status" data-align="center" data-formatter="transactionPaymentStatus">{{ __('Payment Status') }}</th>
                                <th scope="col" data-field="order_id" data-align="center" data-visible="false">{{ __('order_id') }}</th>
                                <th scope="col" data-field="payment_id" data-align="center" data-visible="false">{{ __('payment_id') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="true">{{ __('date') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false">{{ __('updated_at') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
