@extends('layouts.master')

@section('title')
    {{ __('subscription_settings') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('subscription_settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="create-form-without-reset"
                            action="{{ route('system-settings.subscription-settings-store') }}" method="POST"
                            data-success-function="formSuccessFunction" novalidate="novalidate"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- Subscription Settings --}}
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('subscription_settings') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="additional_billing_days">{{ __('Additional Billing Days') }} <span
                                                class="text-danger">*</span></label>
                                        <input name="additional_billing_days" id="additional_billing_days"
                                            value="{{ $settings['additional_billing_days'] ?? '' }}" min="2"
                                            type="number" required placeholder="{{ __('Additional Billing Days') }}"
                                            class="form-control" />
                                    </div>

                                    <div class="form-group col-md-3 col-sm-12">
                                        <label
                                            for="current_plan_expiry_warning_days">{{ __('Current Plan Expiry Warning Days') }}
                                            <span class="text-danger">*</span></label>
                                        <input name="current_plan_expiry_warning_days" id="current_plan_expiry_warning_days"
                                            value="{{ $settings['current_plan_expiry_warning_days'] ?? '' }}"
                                            min="2" type="number" required
                                            placeholder="{{ __('Current Plan Expiry Warning Days') }}"
                                            class="form-control" />
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12 mt-3">
                                        <label for=""><strong>{{ __('Cron Job URL') }}</strong> :</label>

                                        {!! Form::text('info-link', url('subscription/cron-job'), ['class' => 'form-control', 'readonly']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <div class="alert alert-danger" role="alert">
                                            {{ __('Kindly configure the cron job on your server to execute the URL every day This will facilitate the regular examination of school subscription expirations and the creation of bills') }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{-- End Subscription Settings --}}

                            {{-- Free trial Subscription Settings --}}

                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('free_trial_subscription_settings') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-md-6 col-lg-6 col-xl-4 col-sm-12">
                                        <label for="trial_days">{{ __('trial_days') }} <span
                                                class="text-danger">*</span></label>
                                        <input name="trial_days" id="trial_days"
                                            value="{{ $settings['trial_days'] ?? '' }}" min="1" type="number"
                                            required placeholder="{{ __('trial_days') }}" class="form-control" />
                                    </div>

                                    <div class="form-group col-md-6 col-lg-6 col-xl-4 col-sm-12">
                                        <label for="student_limit">{{ __('student_limit') }} <span
                                                class="text-danger">*</span></label>
                                        <input name="student_limit" id="student_limit"
                                            value="{{ $settings['student_limit'] ?? '' }}" min="1" type="number"
                                            required placeholder="{{ __('student_limit') }}" class="form-control" />
                                    </div>

                                    <div class="form-group col-md-6 col-lg-6 col-xl-4 col-sm-12">
                                        <label for="staff_limit">{{ __('staff_limit') }}
                                            <span class="text-danger">*</span></label>
                                        <input name="staff_limit" id="staff_limit"
                                            value="{{ $settings['staff_limit'] ?? '' }}" min="1" type="number"
                                            required placeholder="{{ __('staff_limit') }}" class="form-control" />
                                    </div>
                                    <div class="form-group col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                        <label for="description">{{ __('description') }}</label>
                                        <textarea name="free_trial_subscription_description" placeholder="{{ __('description') }}" class="form-control">{{ $package ? $package->description : '' }}</textarea>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3 mt-4">
                                        <div class="form-group row ml-3">
                                            <div class="form-check col-sm-12 col-md-6 form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" value="1" name="status" {{ $package ? $package->status == 1 ? 'checked' : '' : '' }}>
                                                    {{ __('active') }}
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>
                                            <div class="form-check col-sm-12 col-md-6 form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" value="0" name="status" {{ $package ? $package->status == 0 ? 'checked' : '' : '' }}>
                                                    {{ __('inactive') }}
                                                    <i class="input-helper"></i>
                                                </label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-2 mt-4">
                                        <div class="form-check">
                                            <label class="form-check-label d-inline">
                                                {!! Form::checkbox('highlight', 1, $package ? $package->highlight : false, ['class' => 'form-check-input']) !!}
                                                {{ __('highlight') }} {{ __('package') }}
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                {{-- Feature --}}
                                <div class="row my-4 mx-1">
                                    <div class="col-sm-12 col-md-12 mb-3 text-center">
                                        <h4 class="card-title">{{ __('features') }}</h4>
                                    </div>
                                    @foreach ($features as $feature)
                                        <div class="form-group col-sm-12 col-md-3 d-flex justify-content-center">
                                            {{-- Default Feature --}}
                                            @if ($feature->is_default == 1)
                                                <input id="{{ __($feature->name) }}" class="feature-checkbox" disabled
                                                    type="checkbox" name="feature_id[]"
                                                    @if ($package && str_contains($package->package_feature->pluck('feature_id'), $feature->id)) checked @endif
                                                    value="{{ $feature->id }}" />
                                                <label class="feature-list-default text-center"
                                                    for="{{ __($feature->name) }}"
                                                    title="{{ __('default_feature') }}">{{ __($feature->name) }}</label>
                                                <input type="hidden" name="feature_id[]" value="{{ $feature->id }}">
                                            @else
                                                <input id="{{ __($feature->name) }}" class="feature-checkbox"
                                                    type="checkbox" name="feature_id[]"
                                                    @if ($package && in_array($feature->id, $package->package_feature->pluck('feature_id')->toArray())) checked @endif
                                                    value="{{ $feature->id }}" />
                                                <label class="feature-list text-center"
                                                    for="{{ __($feature->name) }}">{{ __($feature->name) }}</label>
                                            @endif

                                        </div>
                                    @endforeach

                                </div>
                                <hr>
                            </div>

                            {{-- End Free trial Subscription Settings --}}

                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function formSuccessFunction(response) {
            setTimeout(() => {}, 500);
        }
    </script>
@endsection
