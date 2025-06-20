@extends('layouts.master')

@section('title')
    {{ __('Payment Settings') }}
@endsection

{{-- THIS VIEW IS COMMON FOR BOTH THE SUPER ADMIN & SCHOOL ADMIN --}}
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                @if (Auth::user()->school_id)
                    {{ __('fees_payment_settings') }}
                @else
                    {{ __('Payment Settings') }}
                @endif
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form class="create-form-without-reset" action="{{ route('system-settings.payment.update') }}"
                            method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            
                            @include('settings.forms.payment-form')
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit"
                                value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        window.onload = setTimeout(() => {
            $('#currency_code').trigger("change");
        }, 500);

        @if (!empty($paymentGateway['Stripe']['currency_code']))
            $('#currency_code').val("{{ $settings['currency_code'] }}").trigger("change");
        @endif

        $('#currency_code').on('change', function() {
            $('#stripe_currency').val($(this).val());
        })

        $('#currency_code').on('change', function() {
            $('#razorpay_currency').val($(this).val());
        })

        $('#currency_code').on('change', function() {
            $('#flutterwave_currency').val($(this).val());
        })

        $('#currency_code').on('change', function() {
            $('#paystack_currency').val($(this).val());
        })

        $('#currency_code').on('change', function() {
            $('#bank_transfer_currency').val($(this).val());
        })
    </script>
@endsection
