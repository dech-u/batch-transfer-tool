@extends('layouts.master')

@section('title')
    {{ __('Fees Configuration')}}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{__('Fees Settings')}}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <form id="create-fees-config-form" class="fees-config" action="{{ route('fees.config.update') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="row my-4 mx-1">
                                    <div class="col-md-12"><h4>{{__("Other Fees Configuration")}}</h4></div>
                                    <div class="col-12 mb-3">
                                        <hr class="mt-0">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="currency_code">{{ __('Currency Code') }} <span class="text-danger">*</span></label>
                                        <input name="currency_code" id="currency_code" value="{{ $settings['currency_code'] ?? '' }}" type="text" placeholder="{{ __('Currency Code') }}" class="form-control"/>
                                        <span style="color: rgb(0, 55, 107);font-size: 0.8rem" class="ml-2">eg :- inr</span>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="currency_symbol">{{ __('Currency Symbol') }} <span class="text-danger">*</span></label>
                                        <input name="currency_symbol" id="currency_symbol" value="{{ $settings['currency_symbol'] ?? '' }}" type="text" placeholder="{{ __('Currency Symbol') }}" class="form-control"/>
                                        <span style="color: rgb(0, 55, 107);font-size: 0.8rem" class="ml-2">eg :- ₹</span>
                                    </div>

                                    <div class="form-group col-md-4 col-lg-4 col-sm-12">
                                        <label>{{__('Online payment mode') }}</label>&nbsp;<span class="text-danger">*</span>
                                        <div class="row ml-0">
                                            <div class="form-check mr-3">
                                                <label class="form-check-label">
                                                    <input type="radio" name="online_payment" class="online_payment_toggle" value="1" {{(isset($settings['online_payment']) && $settings['online_payment']) ? "checked" : ""}}>
                                                    {{ __('Enable') }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="radio" name="online_payment" class="online_payment_toggle" value="0" {{(!isset($settings['online_payment']) || !$settings['online_payment']) ? "checked" : ""}}>
                                                    {{ __('Disable') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h3 class="card-title">
                                {{__('Payment Gateways')}}
                            </h3>

                            <hr>
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="row my-4 mx-1">
                                    <div class="col-md-12">
                                        <h4>
                                            <i class="fa fa-angle-double-right menu-icon"></i>&nbsp;
                                            {{__("stripe")}}
                                        </h4>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <hr class="mt-0">
                                    </div>
                                    <div class="row col-12">
                                        <div class="form-group col-md-2">
                                            <label for="stripe_status">{{ __('status') }} <span class="text-danger">*</span></label>
                                            <select required name="stripe_status" id="stripe_status" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                                <option value="0" {{(!isset($settings['stripe_status']) || !$settings['stripe_status'] ? "selected" : "")}}>{{__('Disable')}}</option>
                                                <option value="1" {{(isset($settings['stripe_status']) && $settings['stripe_status'] ? "selected" : "")}}>{{__('Enable')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row col-12">
                                        <div class="form-group col-md-6">
                                            <label for="stripe_publishable_key">{{ __('Stripe Publishable Key') }}</label>
                                            <input name="stripe_publishable_key" id="stripe_publishable_key" value="{{ $settings['stripe_publishable_key'] ?? '' }}" type="text" placeholder="{{ __('Stripe Publishable Key') }}" class="form-control"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="stripe_secret_key">{{ __('Stripe Secret Key') }}</label>
                                            <input name="stripe_secret_key" id="stripe_secret_key" value="{{$settings['stripe_secret_key'] ?? '' }}" type="text" placeholder="{{ __('Stripe Secret Key') }}" class="form-control"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="stripe_webhook_secret">{{ __('Stripe Webhook Secret') }}</label>
                                            <input name="stripe_webhook_secret" id="stripe_webhook_secret" value="{{ $settings['stripe_webhook_secret']?? '' }}" type="text" placeholder="{{ __('Stripe Webhook Secret') }}" class="form-control"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="stripe_webhook_url">{{__('Stripe Webhook URL') }}</label>
                                            <input name="stripe_webhook_url" id="stripe_webhook_url" value="{{ isset($domain) ? $domain.'/webhook/stripe' : ''}}" type="text" placeholder="{{ __('Stripe Webhook URL')}}" class="form-control" readonly/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <input class="btn btn-theme mt-5" type="submit" value="{{ __('submit') }}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
