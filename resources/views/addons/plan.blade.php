@extends('layouts.master')

@section('title')
    {{ __('addons') }}
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('addons') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row pricing-table">
                            {{-- @foreach ($addons as $addon)
                                <div class="col-md-6 col-xl-3 grid-margin stretch-card pricing-card">
                                    <div class="card border-primary ribbon  border pricing-card-body">
                                        <div class="text-center pricing-card-head mb-2 text-center">
                                            <h4>{{ $addon->name }}</h4>
                                            <p>{{ __('price') }} : {{ $settings['currency_symbol'] ?? null }} {{ number_format($addon->price, 2) }} </p>
                                            <h1 class="font-weight-normal mb-2"></h1>
                                            <hr>
                                            <div class="text-center">
                                                {{ $addon->feature->name }}
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="wrapper">
                                            @if (in_array($addon->feature_id, $subscibed_addons) || in_array($addon->feature_id, $subscription))
                                                <button disabled data-id="{{ $addon->id }}" class="btn btn-outline-success add-addon btn-block">{{ __('added') }}</button>
                                            @else
                                                <button data-id="{{ $addon->id }}" class="btn btn-outline-success add-addon btn-block">{{ __('add') }}</button>
                                            @endif
                                            
                                        </div>

                                    </div>
                                </div>
                            @endforeach --}}

                            @foreach ($addons as $addon)
                                @if (in_array($addon->feature_id, $features))
                                    <div class="col-md-6 col-xl-3 grid-margin stretch-card pricing-card">
                                        <div class="card addon-border-primary border-primary border pricing-card-body">
                                            <div class="addon-ribbon text-uppercase">{{ __('added') }}</div>
                                            <div class="text-center pricing-card-head mb-2 text-center">
                                                <h4>{{ $addon->name }}</h4>
                                                <p>{{ __('price') }} : {{ $settings['currency_symbol'] ?? null }} {{ number_format($addon->price, 2) }} </p>
                                                <h1 class="font-weight-normal mb-2"></h1>
                                                <hr>
                                                <div class="text-center">
                                                    {{ __($addon->feature->name) }}
                                                </div>
                                                <hr>
                                            </div>
                                            <div class="wrapper">
                                                <button disabled data-id="{{ $addon->id }}" class="btn btn-outline-success add-addon btn-block">{{ __('added') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6 col-xl-3 grid-margin stretch-card pricing-card">
                                        <div class="card border-primary border pricing-card-body">
                                            <div class="text-center pricing-card-head mb-2 text-center">
                                                <h4>{{ $addon->name }}</h4>
                                                <p>{{ __('price') }} : {{ $settings['currency_symbol'] ?? null }} {{ number_format($addon->price, 2) }} </p>
                                                <h1 class="font-weight-normal mb-2"></h1>
                                                <hr>
                                                <div class="text-center">
                                                    {{ $addon->feature->name }}
                                                </div>
                                                <hr>
                                            </div>
                                            <div class="wrapper">
                                                @if ($subscription)
                                                    @if ($paymentConfiguration && $paymentConfiguration->payment_method == 'Razorpay' &&  $subscription->package_type == 0)
                                                        <form action="{{ url('subscriptions/razorpay') }}" class="razorpay-form" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="package_id" class="package_id" value="">
                                                            <input type="hidden" name="subscription_id" class="subscription_id" value="{{ $subscription->id }}">

                                                            <input type="hidden" name="feature_id" class="feature_id-{{ $addon->id }}" value="{{ $addon->feature_id }}">
                                                            <input type="hidden" name="amount" class="bill_amount-{{ $addon->id }}" value="{{ $addon->price }}">

                                                            <input type="hidden" name="type" class="type" value="addon">
                                                            <input type="hidden" name="end_date" class="end_date" value="{{ $subscription->end_date }}">
                                                            <input type="hidden" name="package_type" class="package_type" value="">

                                                            <input type="hidden" name="razorpay_payment_id" class="razorpay_payment_id" value="">
                                                            <input type="hidden" name="razorpay_signature" class="razorpay_signature" value="">
                                                            <input type="hidden" name="razorpay_order_id" class="razorpay_order_id" value="">

                                                            <input type="hidden" name="paymentTransactionId" class="paymentTransactionId" value="">

                                                            <button class="btn btn-outline-success w-100" id="razorpay-button-{{ $addon->id }}">{{ __('add') }}</button>
                                                        </form>
                                                    @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Stripe' && $subscription->package_type == 0)
                                                        <form class="stripe-form" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="addon_id" class="addon_id" value="{{ $addon->id }}">
                                                            <input type="hidden" name="subscription_id" class="subscription_id" value="{{ $subscription->id }}">
                                                            <input type="hidden" name="feature_id" class="feature_id-{{ $addon->id }}" value="{{ $addon->feature_id }}">
                                                            <input type="hidden" name="amount" class="bill_amount-{{ $addon->id }}" value="{{ $addon->price }}">
                                                            <button data-id="{{ $addon->id }}" data-type="{{ $subscription->package_type }}" class="btn btn-outline-success add-addon btn-block">{{ __('add') }}</button>
                                                        </form>
                                                    @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Flutterwave' && $subscription->package_type == 0)
                                                        <form class="flutterwave-form" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="addon_id" class="addon_id" value="{{ $addon->id }}">
                                                            <input type="hidden" name="subscription_id" class="subscription_id" value="{{ $subscription->id }}">
                                                            <input type="hidden" name="feature_id" class="feature_id-{{ $addon->id }}" value="{{ $addon->feature_id }}">
                                                            <input type="hidden" name="amount" class="bill_amount-{{ $addon->id }}" value="{{ $addon->price }}">
                                                            <button data-id="{{ $addon->id }}" data-payment-method="flutterwave" data-type="{{ $subscription->package_type }}" class="btn btn-outline-success add-addon btn-block">{{ __('add') }}</button>
                                                        </form>
                                                    @elseif ($paymentConfiguration && $paymentConfiguration->payment_method == 'Paystack' && $subscription->package_type == 0)
                                                        <form class="paystack-form" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="addon_id" class="addon_id" value="{{ $addon->id }}">
                                                            <input type="hidden" name="subscription_id" class="subscription_id" value="{{ $subscription->id }}">
                                                            <input type="hidden" name="feature_id" class="feature_id-{{ $addon->id }}" value="{{ $addon->feature_id }}">
                                                            <input type="hidden" name="amount" class="bill_amount-{{ $addon->id }}" value="{{ $addon->price }}">
                                                            <button data-id="{{ $addon->id }}" data-type="{{ $subscription->package_type }}" class="btn btn-outline-success add-addon btn-block">{{ __('add') }}</button>
                                                        </form>
                                                    @else
                                                        <button data-id="{{ $addon->id }}" data-type="{{ $subscription->package_type }}" class="btn btn-outline-success add-addon btn-block">{{ __('add') }}</button>
                                                    @endif
                                                @endif

                                                
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@foreach ($addons as $addon)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Add the event listener for the button to initiate the payment
        setTimeout(() => {
            document.getElementById('razorpay-button-{{ $addon->id }}').onclick = function(e) {
                
                let baseUrl = window.location.origin;
                var order_id = '';
                var paymentTransactionId = '';

                $.ajax({
                    type: "post",
                    url: baseUrl + '/subscriptions/create/razorpay/order-id',
                    data: {
                        amount : $('.bill_amount-{{ $addon->id }}').val(), // Amount is in currency subunits. Default currency is INR. Hence, 100 refers to 1 INR
                        currency : "{{ $system_settings['currency_code'] ?? 'INR' }}",

                        type : 'addon',
                        package_type : $('.package_type').val(),
                        package_id : $('.package_id').val(),
                        upcoming_plan_type : $('.upcoming_plan_type').val(),
                        subscription_id : $('.subscription_id').val(),
                        feature_id : $('.feature_id-{{ $addon->id }}').val(),
                        end_date : $('.end_date').val(),
                        
                    },
                    success: function (response) {
                        if (response.data) {
                            order_id = response.data.order.id;
                            paymentTransactionId = response.data.paymentTransaction.id;
                            var options = {
                                "key": "{{ $paymentConfiguration->api_key ?? '' }}", // Enter the Key ID generated from the Dashboard
                                "amount": $('.bill_amount-{{ $addon->id }}').val() * 100, // Amount is in currency subunits. Default currency is INR. Hence, 100 refers to 1 INR
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
                                    document.querySelector('.razorpay-form').submit();
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
