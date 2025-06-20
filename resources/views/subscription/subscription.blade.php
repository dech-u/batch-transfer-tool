@extends('layouts.master')

@section('title')
    {{ __('subscription') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('subscription') }}
            </h3>
        </div>
        <div class="row">
            {{-- Active plan --}}
            <div class="col-md-9 col-sm-12 col-tb-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title float-left">
                            {{ __('active_plan') }}
                        </h4>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 text-right">
                                @if (!empty($features))
                                    <a href="{{ route('subscriptions.index') }}" class="btn btn-theme btn-sm">{{ __('update_current_plan') }}</a>
                                @else
                                    &nbsp;
                                @endif
                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="">{{ __('name') }}</label>
                                {!! Form::text('name', $active_package ? $active_package->name : null, [
                                    'class' => 'form-control form-control-sm',
                                    'readonly',
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="">{{ __('start_date') }}</label>
                                {!! Form::text('start_date', $active_package ? format_date($active_package->start_date) : null, [
                                    'class' => 'form-control form-control-sm',
                                    'readonly',
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="">{{ __('end_date') }}</label>
                                {!! Form::text('end_date', $active_package ? format_date($active_package->end_date) : null, [
                                    'class' => 'form-control form-control-sm',
                                    'readonly',
                                ]) !!}
                            </div>
                            @if ($active_package && $active_package->package_type == 1)
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="">{{ __('per_active_student_charges') }}
                                        ({{ $system_settings['currency_symbol'] }})</label>
                                    {!! Form::text('student_charge', $active_package ? $active_package->student_charge : null, [
                                        'class' => 'form-control form-control-sm',
                                        'readonly',
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="">{{ __('per_active_staff_charges') }}
                                        ({{ $system_settings['currency_symbol'] }})</label>
                                    {!! Form::text('staff_charge', $active_package ? $active_package->staff_charge : null, [
                                        'class' => 'form-control form-control-sm',
                                        'readonly',
                                    ]) !!}
                                </div>
                            @else
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="">{{ __('student_limit') }}</label>
                                    {!! Form::text('student_charge', $active_package ? $active_package->no_of_students : null, [
                                        'class' => 'form-control form-control-sm',
                                        'readonly',
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="">{{ __('staff_limit') }}</label>
                                    {!! Form::text('staff_charge', $active_package ? $active_package->no_of_staffs : null, [
                                        'class' => 'form-control form-control-sm',
                                        'readonly',
                                    ]) !!}
                                </div>
                            @endif
                            
                        </div>

                        <div class="row">
                            @if ($active_package)
                                <div class="form-group col-sm-12 col-md-12">
                                    <h2 class="card-title">{{ __('features') }}</h2>
                                </div>
                                @foreach ($active_package->subscription_feature as $feature)
                                    <div class="form-group col-sm-12 col-md-4">
                                        <input checked class="feature-checkbox" type="checkbox" name="feature_id"/>
                                        <label class="feature-list text-center">{{ __($feature->feature->name) }}</label>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <hr>

                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <h2 class="card-title">{{ __('addons') }}</h2>
                            </div>
                            @if ($active_package)
                                @foreach ($active_package->addons as $addon)
                                    <div class="form-group col-sm-12 col-md-4">
                                        <input checked class="feature-checkbox" type="checkbox" name="feature_id"/>
                                        <label class="feature-list text-center">
                                            {{ __($addon->feature->name) }}
                                            @if ($addon->status)
                                                <i class="fa fa-times text-danger discontinue_addon"
                                                data-id="{{ $addon->id }}"
                                                title="{{ __('discontinue_upcoming_plan') }}"></i>
                                            @endif

                                        </label>
                                    </div>
                                @endforeach
                            @endif
                            
                        </div>

                    </div>
                </div>
            </div>

            {{-- Upcoming billing cycle plan --}}
            <div class="col-md-3 col-sm-12 col-tb-6 grid-margin stretch-card">
                <div class="card">
                    @if ($upcoming_package && ($school_settings['auto_renewal_plan'] ?? 0))
                        <div class="card-body">
                            @if ($upcoming_package->subscription_bill && $upcoming_package->subscription_bill->transaction && $upcoming_package->subscription_bill->transaction->payment_status == "succeed" && $upcoming_package->id != $active_package->id)
                            <h4 class="card-title float-left">{{ __('upcoming_plan') }}</h4>
                            <div class="row text-right">
                                <div class="col-sm-12 col-md-12">
                                    <div class="badge badge-success text-uppercase"><span class="display-5">{{ __('paid') }}</span></div>
                                </div>
                            </div>
                            @else
                            <h4 class="card-title float-left">{{ __('upcoming_plan') }}</h4>
                                <div class="row text-right">
                                    <div class="col-sm-12 col-md-12">
                                        <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-theme">{{ __('update_plan') }}</a>
                                    </div>
                                </div>
                            @endif
                                

                            <hr>
                            <div class="upcoming-feature-list">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('name') }}</label>
                                        <input type="text" name="name" class="form-control form-control-sm"
                                               value="{{ $upcoming_package->name }}" readonly>
                                    </div>
                                </div>
                                <h4 class="card-title">{{ __('features') }}</h4>

                                <ul class="list-unstyled">
                                    @foreach ($upcoming_package->package->package_feature as $feature)
                                        <li><i class="fa fa-check check mr-2"></i>{{ __($feature->feature->name) }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="addon">
                                <h4 class="card-title">{{ __('addons') }}</h4>
                                <ul class="list-unstyled">
                                    @if ($active_package)
                                        @foreach ($active_package->addons as $addon)
                                            <li>
                                                @if ($addon->status)
                                                    <i class="fa fa-check check mr-2"></i>{{ __($addon->feature->name) }}
                                                @else
                                                    <i class="fa fa-times no-feature mr-2"></i>{{ __($addon->feature->name) }}
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="mx-4 text-justify text-uppercase">
                            <small
                                class="text-danger">{{ __('Note : Certain additional features will not be part of the next billing period as they have already been integrated into your upcoming subscription package') }}</small>
                        </div>

                        <div class="d-flex justify-content-between m-3">
                            <div class="ml-auto">
                                @if ($upcoming_package->subscription_bill && $upcoming_package->subscription_bill->transaction && $upcoming_package->subscription_bill->transaction->payment_status == "succeed" && $active_package && $upcoming_package->id != $active_package->id)

                                @else
                                    <button class="btn btn-danger btn-sm text-wrap cancel-upcoming-plan" @if ($active_package->id == $upcoming_package->id) data-id="" @else data-id="{{ $upcoming_package->id }}" @endif >{{ __('Cancel This Plan') }}</button>
                                        
                                @endif
                            </div>
                        </div>
                        

                    @else
                        <div class="card-body">
                            <h4 class="card-title">{{ __('upcoming_plan') }}</h4>
                            <hr>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-sm-12 col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <h3 class="card-title">{{ __('Active Subscription Billing Details') }}</h3>
                                <hr>
                            </div>
                            @if ($active_package)
                                <div class="form-group col-sm-12 col-md-12 table-responsive">
                                    <table class="table table-bordered" data-mobile-responsive="true">
                                        {{-- Postpaid --}}
                                        <tr>
                                            <th>
                                                <div class="text-info text-center">
                                                    @if ($active_package->package_type == 1)
                                                        {{ __('postpaid') }}
                                                    @else
                                                        {{ __('prepaid') }}
                                                    @endif
                                                </div>
                                            </th>
                                        </tr>
                                        @if ($active_package->package_type == 1) 
                                        
                                        <tr>
                                            <th class="text-center">{{ __('plan') }}</th>
                                            <th class="text-center">{{ __('user') }}</th>
                                            <th class="text-center">{{ __('charges') }} ({{ $system_settings['currency_symbol'] }})</th>
                                            <th class="text-center">{{ __('total_user') }}</th>
                                            <th class="text-center">{{ __('total_amount') }} ({{ $system_settings['currency_symbol'] }})</th>
                                        </tr>
                                        <tr>
                                            <td class="text-center" rowspan="2">
                                                {{ $active_package->name }}
                                            </td>
                                            <td>{{ __('students') }}</td>
                                            <td class="text-right">{{ $active_package->student_charge }}</td>
                                            @if ($active_package->package_type == 1)
                                                <td class="text-right">{{ $data['students'] }}</td>    
                                            @else
                                                <td class="text-right">{{ $data['students'] }} / {{ $active_package->no_of_students }}</td>
                                            @endif
                                            
                                            <td class="text-right">
                                                {{ number_format($data['students'] * $active_package->student_charge, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                {{ __('staffs') }}
                                                <div class="text-small text-muted mt-2">
                                                    {{ __('including_teachers_and_other_staffs') }}
                                                </div>
                                            </td>
                                            <td class="text-right">{{ $active_package->staff_charge }}</td>
                                            @if ($active_package->package_type == 1)
                                                <td class="text-right">{{ $data['staffs'] }}</td>    
                                            @else
                                                <td class="text-right">{{ $data['staffs'] }} / {{ $active_package->no_of_staffs }}</td>
                                            @endif
                                            
                                            <td class="text-right">
                                                {{ number_format($data['staffs'] * $active_package->staff_charge, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="4">{{ __('Total User Charges') }} :</th>
                                            <th class="text-right">
                                                {{ $system_settings['currency_symbol'] }}
                                                {{ number_format($data['students'] * $active_package->student_charge + $data['staffs'] * $active_package->staff_charge, 2) }}
                                                @php
                                                    $total_user_charges = $data['students'] * $active_package->student_charge + $data['staffs'] * $active_package->staff_charge;
                                                @endphp
                                            </th>
                                        </tr>
                                        {{-- Prepaid --}}
                                        @else
                                            <tr>
                                                <th colspan="4">{{ __('package_amount') }} :</th>
                                                <th class="text-right">
                                                    {{ $system_settings['currency_symbol'] }}
                                                    {{ number_format($active_package->charges, 2) }}
                                                   
                                                </th>
                                            </tr>
                                        @endif
                                        
                                        
                                        <tr>
                                            <th colspan="5" class="text-center">{{ __('addon_charges') }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">{{ __('addon') }}</th>
                                            <th class="text-center">{{ __('total_amount') }} ({{ $system_settings['currency_symbol'] }})</th>
                                        </tr>
                                        @php
                                            $total_addon_charges = 0;
                                        @endphp
                                        @if ($active_package)
                                            @foreach ($active_package->addons as $addon)
                                                <tr>
                                                    <td colspan="4">{{ __($addon->feature->name) }}</td>
                                                    <td class="text-right">{{ number_format($addon->price, 2) }} </td>
                                                    @php
                                                        $total_addon_charges += $addon->price;
                                                    @endphp
                                                </tr>
                                            @endforeach
                                        @endif
                                        
                                        <tr>
                                            <th colspan="4">{{ __('total_addon_charges') }} :</th>
                                            <th class="text-right">
                                                {{ $system_settings['currency_symbol'] }}{{ number_format($total_addon_charges, 2) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="4">{{ __('total_bill_amount') }} :</th>
                                            @if ($active_package->package_type == 1)
                                                <th class="text-right">
                                                    {{ $system_settings['currency_symbol'] }}{{ number_format($total_addon_charges + $total_user_charges, 2) }}
                                                </th>
                                            @else
                                                <th class="text-right">
                                                    {{ $system_settings['currency_symbol'] }}{{ number_format($total_addon_charges + $active_package->charges, 2) }}
                                                </th>
                                            @endif
                                            
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('history') }}</h4>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('subscriptions.show', 1) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                               data-export-data-type='all' data-query-params="subscriptionQueryParams"
                               data-toolbar="#toolbar"
                               data-export-options='{ "fileName": "subscription-list-<?= date('d-m-y') ?>"
                            ,"ignoreColumn":["operate"]}' data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="date" data-formatter="dateFormatter" data-sortable="false">{{ __('bill_generate_date') }}</th>
                                <th scope="col" data-field="due_date" data-formatter="dateFormatter" data-sortable="false">{{ __('due_date') }}</th>
                                <th scope="col" data-field="name" data-sortable="false">{{ __('name') }}</th>
                                <th scope="col" data-field="subscription.package_type" data-formatter="packageTypeFormatter" data-sortable="false">{{ __('type') }}</th>
                                <th scope="col" data-width="500" data-field="description" data-visible="false" data-sortable="false">{{ __('description') }}</th>
                                <th scope="col" data-field="transaction_id">{{ __('transaction_id') }}</th>
                                <th scope="col" data-field="total_student" data-sortable="false">{{ __('total_students') }}</th>
                                <th scope="col" data-field="total_staff"> {{ __('total_staffs') }}</th>
                                <th scope="col" data-field="amount">{{ __('Amount') }}</th>
                                <th scope="col" data-field="payment_status" data-formatter="transactionPaymentStatus">{{ __('status') }}</th>
                                <th scope="col" data-field="operate" data-events="subscriptionEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bill details --}}
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h5 class="modal-title" id="exampleModalLabel"><span class="billing_cycle btn-gradient-dark p-2 text-small"></span> <span class="package-type badge p-2"></span></h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 table-responsive">
                                            <table class="table table-bordered">
                                                {{-- Postpaid --}}
                                                <tbody class="postpaid-package-info">
                                                <tr>
                                                    <th class="text-center">{{ __('plan') }}</th>
                                                    <th class="text-center">{{ __('user') }}</th>
                                                    <th class="text-center">{{ __('charges') }} ({{ $system_settings['currency_symbol'] }})</th>
                                                    <th class="text-center">{{ __('total_user') }}</th>
                                                    <th class="text-center">{{ __('total_amount') }} ({{ $system_settings['currency_symbol'] }})</th>
                                                </tr>
                                                <tr>
                                                    <td class="text-center plan-name" rowspan="2"></td>
                                                    <td>{{ __('students') }}</td>
                                                    <td class="text-right student-charge"></td>
                                                    <td class="text-right total-student"></td>
                                                    <td class="text-right total-student-charge"></td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ __('staffs') }}
                                                        <div class="text-small text-muted mt-2">
                                                            {{ __('including_teachers_and_other_staffs') }}
                                                        </div>
                                                    </td>
                                                    <td class="text-right staff-charge"></td>
                                                    <td class="text-right total-staff"></td>
                                                    <td class="text-right total-staff-charge"></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-left">
                                                        {{ __('Total User Charges') }} :
                                                    </th>
                                                    <th class="text-right">
                                                        <span>{{ $system_settings['currency_symbol'] }}</span>
                                                        <span class="total-user-charges"></span>
                                                    </th>
                                                </tr>
                                                {{-- Prepaid --}}
                                                </tbody>
                                                <tbody class="prepaid-package-info">
                                                <tr>
                                                    <th colspan="4" class="text-left">
                                                        {{ __('package_amount') }} :
                                                    </th>
                                                    <th class="text-right">
                                                        <span>{{ $system_settings['currency_symbol'] }}</span>
                                                        <span class="package_amount"></span>
                                                    </th>
                                                </tr>
                                                </tbody>
                                                
                                                <tr>
                                                    <th colspan="5" class="text-center">{{ __('addon_charges') }}
                                                    </th>
                                                </tr>
                                                <tr class="postpaid-table">
                                                    <th colspan="4">{{ __('addon') }}</th>
                                                    <th class="text-center">{{ __('total_amount') }} ({{ $system_settings['currency_symbol'] }})</th>
                                                </tr>
                                                <tbody class="postpaid-table postpaid-addon-charges">

                                                </tbody>

                                                <tr class="prepaid-table">
                                                    <th class="prepaid-colspan" colspan="2">{{ __('addon') }}</th>
                                                    <th class="preapid-show">{{ __('order_id') }}</th>
                                                    <th class="preapid-show">{{ __('status') }}</th>
                                                    <th class="text-center">{{ __('total_amount') }} ({{ $system_settings['currency_symbol'] }})</th>
                                                </tr>
                                                <tbody class="prepaid-table prepaid-addon-charges">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
                                    @if ($paymentConfiguration && $paymentConfiguration->payment_method == 'Razorpay')
                                        <div class="payment-status">
                                            <form action="{{ url('subscriptions/razorpay') }}" class="razorpay-form" method="POST">
                                                @csrf
                                                <input type="hidden" name="package_id" class="package_id" value="">
                                                <input type="hidden" name="amount" class="bill_amount" value="">

                                                <input type="hidden" name="type" class="type" value="package">
                                                <input type="hidden" name="package_type" class="package_type" value="bill">

                                                <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id" value="">
                                                <input type="hidden" name="razorpay_signature" class="razorpay_signature" value="">
                                                <input type="hidden" name="razorpay_order_id" class="razorpay_order_id" value="">

                                                <input type="hidden" name="paymentTransactionId" class="paymentTransactionId" value="">

                                                <input type="hidden" name="subscription_id" class="subscription_id" value="">

                                                <button class="btn btn-theme" type="submit" id="razorpay-button">{{ __('Pay_with_Razorpay') }}</button>
                                            </form>
                                        </div>
                                    @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Stripe')
                                        <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-stripe-publishable-key="{{ $settings['stripe_publishable_key'] ?? null }}" data-success-function="formSuccessFunction" method="post">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="stripe">
                                            <input type="hidden" name="id" id="edit_id">
                                            <input class="btn btn-theme payment-status" type="submit" value={{ __('Pay_with_Stripe') }} />
                                        </form>
                                    @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Paystack')
                                        <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-paystack-publishable-key="{{ $paymentConfiguration->api_key ?? null }}" data-success-function="formSuccessFunction" method="post">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="paystack">
                                            <input type="hidden" name="id" id="edit_id">
                                            <input class="btn btn-theme payment-status" type="submit" value={{ __('Pay_with_Paystack') }} />
                                        </form>
                                    @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Flutterwave')
                                        <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-flutterwave-publishable-key="{{ $paymentConfiguration->api_key ?? null }}" data-success-function="formSuccessFunction" method="post">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="flutterwave">
                                            <input type="hidden" name="id" id="edit_id">
                                            <input class="btn btn-theme payment-status" type="submit" value={{ __('Pay_with_Flutterwave') }} />
                                        </form>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>

                        
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@endsection