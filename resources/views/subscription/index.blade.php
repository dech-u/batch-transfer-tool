@extends('layouts.master')

@section('title')
    {{ __('plans') }}
@endsection

@section('content')
<style>
    :root {
    --primary-color: {{ $settings['theme_primary_color'] ?? '#56cc99' }};
    --secondary-color: {{ $settings['theme_secondary_color'] ?? '#215679' }};
   
}
</style>
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('subscription') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @if ($upcoming_package)
                            <h3 class="card-title text-danger">{{ __('note') }} : {{ __('if_youve_already_made_payment_for_your_upcoming_plan_changes_or_updates_to_the_current_and_upcoming_plan_will_not_be_permitted') }}</h3>
                        @endif
                        
                        <div class="row pricing-table mt-4">
                            @foreach ($packages as $package)
                                <div class="col-md-6 col-xl-4 grid-margin stretch-card pricing-card">
                                    <div class="card @if ($package->highlight) border-success ribbon @else border-primary @endif  border pricing-card-body">
                                        @if ($package->is_trial != 1)
                                            @if ($package->type == 1)
                                                <span class="package-type-badge postpaid-color">{{ __('postpaid') }}</span>
                                            @else
                                                <span class="package-type-badge prepaid-color">{{ __('prepaid') }}</span>
                                            @endif
                                        @endif
                                        
                                        <div class="text-center pricing-card-head mb-2">
                                            <h3>{{ __($package->name) }}</h3>
                                            <p>{{ $package->description }}</p>
                                            <h1 class="font-weight-normal mb-2"></h1>
                                            <hr>
                                            <div class="row">
                                                @if ($package->is_trial == 1)
                                                    <div class="col-sm-12 col-md-12">
                                                        <b>{{ __('package_information') }}</b>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 mt-3 text-small">
                                                        {{ __('student_limit') }} : {{ $settings['student_limit'] }}
                                                    </div>

                                                    <div class="col-sm-12 col-md-12 mt-1 text-small">
                                                        {{ __('staff_limit') }} : {{ $settings['staff_limit'] }}
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 mt-1 text-small">
                                                        {{ $settings['trial_days'] }} / {{ __('days') }}
                                                    </div>
                                                @elseif($package->type == 0)
                                                    <div class="col-sm-12 col-md-12">
                                                        <b>{{ __('package_price_information') }}</b>
                                                        <hr>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12">
                                                        <h3> {{ $settings['currency_symbol'] }} {{ $package->charges }}</h3>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 mt-3 text-small">
                                                        {{ __('student_limit') }} : {{ $package->no_of_students }} / {{ __('staff_limit') }} : {{ $package->no_of_staffs }}
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 mt-2 text-small">
                                                        {{ $package->days }} / {{ __('days') }}
                                                    </div>
                                                @else
                                                    <div class="col-sm-12 col-md-12">
                                                        <b>{{ __('package_price_information') }}</b>
                                                        <hr>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 mt-3 text-small">
                                                        <h5>
                                                            {{ __('per_student_charges') }} : {{ $settings['currency_symbol'] }} {{ $package->student_charge }} / {{ __('per_staff_charges') }} : {{ $settings['currency_symbol'] }} {{ $package->staff_charge }}
                                                        </h5>
                                                    </div>

                                                    <div class="col-sm-12 col-md-12 mt-1 text-small">
                                                        {{ $package->days }} / {{ __('days') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>

                                        <ul class="list-unstyled">
                                            @foreach ($features as $feature)
                                                @if (in_array($feature->id, $package->package_feature->pluck('feature_id')->toArray()))
                                                    <li><i class="fa fa-check check mr-2"></i>{{ __($feature->name) }}</li>
                                                @else
                                                    <li><i class="fa fa-times no-feature mr-2"></i><span
                                                            class="text-decoration-line-through">{{ __($feature->name) }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        @if (!$upcoming_package)
                                            @if ($current_plan)
                                                @if ($package->id == $current_plan->package_id)
                                                    <div class="wrapper mb-3">
                                                        <a href="#" class="btn disabled @if ($package->highlight) btn-success @else btn-outline-primary @endif btn-block select-plan" data-type="{{ $package->type }}" data-id="{{ $package->id }}">{{ __('current_active_plan') }}</a>
                                                    </div>

                                                    {{-- Set upcoming --}}
                                                    <div class="col-sm-12 col-md-12">
                                                        <a href="#" class="btn disabled @if ($package->highlight) btn-outline-success @else btn-outline-primary @endif btn-block select-plan" data-type="{{ $package->type }}" data-id="{{ $package->id }}">{{ __('update_upcoming_plan') }}</a>
                                                    </div>
                                                @else
                                                    <div class="row">
                                                        <div class="col-sm-12 col-md-12 mb-3">
                                                            {{-- Start Immediate plan --}}
                                                            @if ($paymentConfiguration && $paymentConfiguration->payment_method == 'Razorpay' && $package->type == 0)
                                                                <form action="{{ url('subscriptions/razorpay') }}" class="razorpay-form-{{ $package->id }}" method="POST"> @csrf
                                                                    <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                                    <input type="hidden" name="amount" class="bill_amount_{{ $package->id }}" value="{{ $package->charges }}">
        
                                                                    <input type="hidden" name="type" class="type_{{ $package->id }}" value="package">
                                                                    <input type="hidden" name="package_type" class="package_type_{{ $package->id }}" value="immediate">
        
                                                                    <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id" value="">
                                                                    <input type="hidden" name="razorpay_signature" class="razorpay_signature" value="">
                                                                    <input type="hidden" name="razorpay_order_id" class="razorpay_order_id" value="">
        
                                                                    <input type="hidden" name="paymentTransactionId" class="paymentTransactionId" value="">
        
                                                                    <button class="btn btn-theme w-100" id="razorpay-button-{{ $package->id }}">{{ __('update_current_plan') }}</button>
                                                                </form>

                                                            @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Stripe' && $package->type == 0)
                                                                <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-stripe-publishable-key="{{ $settings['stripe_publishable_key'] ?? null }}" data-success-function="formSuccessFunction" method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="payment_method" value="stripe">
                                                                    <input type="hidden" name="id" id="edit_id">
                                                                    <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                                    <input type="hidden" name="amount" class="bill_amount" value="{{ $package->charges }}">
                                                                    <input type="hidden" name="type" class="type" value="package">
                                                                    <input type="hidden" name="package_type" class="package_type" value="immediate">

                                                                    <button class="btn btn-theme w-100" type="submit" id="stripe-button-{{ $package->id }}">{{ __('update_current_plan') }}</button>
                                                                </form>
                                                            @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Paystack' && $package->type == 0)
                                                                <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-paystack-publishable-key="{{ $paymentConfiguration->api_key ?? null }}" data-success-function="formSuccessFunction" method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="payment_method" value="paystack">
                                                                    <input type="hidden" name="id" id="edit_id">
                                                                    <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                                    <input type="hidden" name="amount" class="bill_amount" value="{{ $package->charges }}">
                                                                    <input type="hidden" name="type" class="type" value="package">
                                                                    <input type="hidden" name="package_type" class="package_type" value="immediate">
                                                                    
                                                                    {{-- <button type="button" class="btn btn-theme w-100 paystack-button">{{ __('get_start') }}</button> --}}
                                                                    <button class="btn btn-theme w-100" id="paystack-button-{{ $package->id }}">{{ __('update_current_plan') }}</button>
                                                                </form>
                                                            @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Flutterwave' && $package->type == 0)
                                                                <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-flutterwave-publishable-key="{{ $paymentConfiguration->api_key ?? null }}" data-success-function="formSuccessFunction" method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="payment_method" value="flutterwave">
                                                                    <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                                    <input type="hidden" name="amount" class="bill_amount" value="{{ $package->charges }}">
                                                                    <input type="hidden" name="type" class="type" value="package">
                                                                    <input type="hidden" name="package_type" class="package_type" value="immediate">
                                                                    <input type="hidden" name="id" id="edit_id">
                                                                    {{-- <input class="btn btn-theme payment-status" type="submit" value={{ __('Flutterwave') }} /> --}}
                                                                    <button class="btn btn-theme w-100" id="flutterwave-button-{{ $package->id }}">{{ __('update_current_plan') }}</button>
                                                                </form>
                                                            @else
                                                                <a href="#" class="btn start-immediate-plan @if ($package->highlight) btn-success @else btn-primary @endif btn-block" data-type="{{ $package->type }}" data-id="{{ $package->id }}">{{ __('update_current_plan') }}</a>
                                                            @endif                                                   
                                                        </div>

                                                        {{-- Set upcoming --}}
                                                        <div class="col-sm-12 col-md-12">
                                                            <a href="#" class="btn @if ($package->highlight) btn-outline-success @else btn-outline-primary @endif btn-block select-plan" data-type="{{ $package->type }}" data-id="{{ $package->id }}" data-iscurrentplan="0">{{ __('update_upcoming_plan') }}</a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                @if ($paymentConfiguration && $paymentConfiguration->payment_method == 'Razorpay')
                                                    {{-- New subscription --}}
                                                    <div class="wrapper">
                                                        <form action="{{ url('subscriptions/razorpay') }}" class="razorpay-form-{{ $package->id }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                            <input type="hidden" name="amount" class="bill_amount_{{ $package->id }}" value="{{ $package->charges }}">

                                                            <input type="hidden" name="type" class="type_{{ $package->id }}" value="package">
                                                            <input type="hidden" name="package_type" class="package_type_{{ $package->id }}" value="new">

                                                            <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id" value="">
                                                            <input type="hidden" name="razorpay_signature" class="razorpay_signature" value="">
                                                            <input type="hidden" name="razorpay_order_id" class="razorpay_order_id" value="">

                                                            <input type="hidden" name="paymentTransactionId" class="paymentTransactionId" value="">

                                                            <button class="btn btn-theme w-100" id="razorpay-button-{{ $package->id }}">{{ __('get_start') }}</button>
                                                        </form>
                                                    </div>
                                                @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Stripe')
                                                    <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-stripe-publishable-key="{{ $settings['stripe_publishable_key'] ?? null }}" data-success-function="formSuccessFunction" method="post">
                                                        @csrf
                                                        <input type="hidden" name="payment_method" value="stripe">
                                                        <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                        <input type="hidden" name="type" value="package">
                                                        <input type="hidden" name="package_type" value="new">

                                                        <button class="btn btn-theme w-100" id="stripe-button-{{ $package->id }}">{{ __('get_start') }}</button>
                                                    </form>
                                                @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Paystack')
                                                    <form action="{{ route('subscriptions.store') }}" class="paystack-form-{{ $package->id }}" data-paystack-publishable-key="{{ $paymentConfiguration->api_key ?? null }}" data-success-function="formSuccessFunction" method="post">
                                                        @csrf
                                                        <input type="hidden" name="payment_method" value="paystack">
                                                        <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                        <input type="hidden" name="type" value="package">
                                                        <input type="hidden" name="package_type" value="new">
                                                        
                                                        <button class="btn btn-theme w-100" type="submit">{{ __('get_start') }}</button>
                                                    </form>
                                                @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Flutterwave' && $package->type == 0)
                                                    <form class="" action="{{ route('subscriptions.store') }}" novalidate="novalidate" data-flutterwave-publishable-key="{{ $paymentConfiguration->api_key ?? null }}" data-success-function="formSuccessFunction" method="post">
                                                        @csrf
                                                        <input type="hidden" name="payment_method" value="flutterwave">
                                                        <input type="hidden" name="id" id="edit_id">
                                                        <input type="hidden" name="package_id" class="package_id_{{ $package->id }}" value="{{ $package->id }}">
                                                        <input type="hidden" name="amount" class="bill_amount_{{ $package->id }}" value="{{ $package->charges }}">
                                                        <input type="hidden" name="type" class="type_{{ $package->id }}" value="package">
                                                        <input type="hidden" name="package_type" class="package_type_{{ $package->id }}" value="new">

                                                        <button class="btn btn-theme w-100" type="submit" id="flutterwave-button-{{ $package->id }}">{{ __('get_start') }}</button>
                                                    </form>
                                                @else
                                                    <div class="wrapper">
                                                        <a href="#" class="btn @if ($package->highlight) btn-success @else btn-outline-primary @endif btn-block select-plan" data-type="{{ $package->type }}" data-iscurrentplan="1" data-id="{{ $package->id }}">{{ __('get_start') }}</a>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // razorpay-payment-button
    setTimeout(() => {
        $('.razorpay-payment-button').addClass('btn btn-info w-100');
    }, 100);
    </script>


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@foreach ($packages as $package)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add the event listener for the button to initiate the payment

        setTimeout(() => {
            document.getElementById('razorpay-button-{{ $package->id }}').onclick = function(e) {
                let baseUrl = window.location.origin;
                var order_id = '';
                var paymentTransactionId = '';

                $.ajax({
                    type: "post",
                    url: baseUrl + '/subscriptions/create/razorpay/order-id',
                    data: {
                        amount : $('.bill_amount_{{ $package->id }}').val(),
                        currency : "{{ $system_settings['currency_code'] ?? 'INR' }}",

                        type : $('.type_{{ $package->id }}').val(),
                        package_type : $('.package_type_{{ $package->id }}').val(),
                        package_id : $('.package_id_{{ $package->id }}').val(),
                        upcoming_plan_type : $('.upcoming_plan_type').val(),
                        subscription_id : $('.subscription_id').val(),
                        feature_id : $('.feature_id').val(),
                        end_date : $('.end_date').val(),
                        
                    },
                    success: function (response) {
                        if (response.data) {
                            order_id = response.data.order.id;
                            paymentTransactionId = response.data.paymentTransaction.id;
                            var options = {
                                "key": "{{ $paymentConfiguration->api_key ?? '' }}", // Enter the Key ID generated from the Dashboard
                                "amount": $('.bill_amount').val() * 100, // Amount is in currency subunits. Default currency is INR. Hence, 100 refers to 1 INR
                                "currency": "{{ $system_settings['currency_code'] ?? 'INR' }}",
                                "name": "{{ $system_settings['system_name'] ?? 'eSchool-Saas' }}",
                                "description": "Razorpay",
                                "order_id": order_id,
                                "handler": function(response) {
                                    // Set the response data in the form
                                    $('.razorpay_payment_id').val(response.razorpay_payment_id);
                                    $('.razorpay_signature').val(response.razorpay_signature);
                                    $('.razorpay_order_id').val(response.razorpay_order_id);
                                    $('.paymentTransactionId').val(paymentTransactionId);

                                    // Submit the form
                                    document.querySelector('.razorpay-form-{{ $package->id }}').submit();
                                }
                            };

                            var rzp1 = new Razorpay(options);
                            rzp1.open();
                        } else {
                            Swal.fire({icon: 'error', text: response.message});
                        }
                    }
                });
                e.preventDefault();
            }
        }, 100); 
        
    });
</script> 
@endforeach


@endsection