@extends('layouts.master')

@section('title')
    {{ __('general_settings') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('general_settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form class="create-form-without-reset" action="{{ route('school-settings.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            <div class="border border-secondary rounded-lg mb-2">
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="school_name">{{ __('school_name') }} <span class="text-danger">*</span></label>
                                        <input name="school_name" id="school_name" value="{{ $settings['school_name'] ?? '' }}" type="text" maxlength="73" required placeholder="{{ __('school_name') }}" class="form-control"/>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="school_email">{{ __('school_email') }} <span class="text-danger">*</span></label>
                                        <input name="school_email" id="school_email" value="{{ $settings['school_email'] ?? '' }}" type="email" required placeholder="{{ __('school_email') }}" class="form-control"/>
                                    </div>

                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="school_code">{{ __('school_code') }}</label>
                                        <input name="school_code" id="school_code" value="{{ Auth::user()->school->code }}" type="text" disabled placeholder="{{ __('school_code') }}" class="form-control"/>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="school_phone">{{ __('school_phone') }} <span class="text-danger">*</span></label>
                                        <input name="school_phone" id="school_phone" value="{{ $settings['school_phone'] ?? '' }}" type="number" required placeholder="{{ __('school_phone') }}" class="form-control remove-number-increment"/>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="school_tagline">{{ __('school_tagline') }} <span class="text-danger">*</span></label>
                                        <textarea name="school_tagline" id="school_tagline" required placeholder="{{ __('school_tagline') }}" class="form-control">{{ $settings['school_tagline'] ?? '' }}</textarea>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="school_address">{{ __('school_address') }} <span class="text-danger">*</span></label>
                                        <textarea name="school_address" id="school_address" required placeholder="{{ __('school_address') }}" class="form-control">{{ $settings['school_address'] ?? '' }}</textarea>
                                    </div>

                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="date_format">{{ __('date_format') }}</label>
                                        <select name="date_format" id="date_format" required class="form-control">
                                            @foreach ($getDateFormat as $key => $dateformat)
                                                <option value="{{ $key }}"{{ isset($settings['date_format']) && $settings['date_format'] == $key ? 'selected' : '' }}>{{ $dateformat }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="time_format">{{ __('time_format') }}</label>
                                        <select name="time_format" id="time_format" required class="form-control">
                                            @foreach ($getTimeFormat as $key => $timeFormat)
                                                <option value="{{ $key }}"{{ isset($settings['time_format']) && $settings['time_format'] == $key ? 'selected' : '' }}>{{ $timeFormat }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row my-4 mx-1">
                                    <div class="form-group col-md-6 col-lg-6 col-xl-4 col-sm-12">
                                        <label for="favicon">{{ __('favicon') }} <span class="text-danger">*</span></label>
                                        <input type="file" name="favicon" class="file-upload-default"/>
                                        <div class="input-group col-xs-12">
                                            <input type="text" id="favicon" class="form-control file-upload-info" disabled="" placeholder="{{ __('favicon') }}"/>
                                            <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                            <div class="col-md-12 mt-2">
                                                <img height="50px" src='{{ $settings['favicon'] ??  '' }}' alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-lg-6 col-xl-4 col-sm-12">
                                        <label for="horizontal_logo">{{ __('horizontal_logo') }} <span class="text-danger">*</span></label>
                                        <input type="file" name="horizontal_logo" class="file-upload-default"/>
                                        <div class="input-group col-xs-12">
                                            <input type="text" id="horizontal_logo" class="form-control file-upload-info" disabled="" placeholder="{{ __('horizontal_logo') }}"/>
                                            <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                            <div class="col-md-12 mt-2">
                                                <img height="50px" src='{{ $settings['horizontal_logo'] ?? '' }}' alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 col-lg-6 col-xl-4 col-sm-12">
                                        <label for="vertical_logo">{{ __('vertical_logo') }} <span class="text-danger">*</span></label>
                                        <input type="file" name="vertical_logo" class="file-upload-default"/>
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" id="vertical_logo" disabled="" placeholder="{{ __('vertical_logo') }}"/>
                                            <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                            <div class="col-md-12 mt-2">
                                                <img height="50px" src='{{ $settings['vertical_logo'] ?? '' }}' alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 col-sm-12 mt-3">
                                        <label for="school_google_map_link">{{ __('google_map_link')}} <span class="text-danger">*</span><span class="text-small text-info">{{ __('convert_into_embed_url') }}</span></label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" required name="google_map_link" placeholder="{{ __('google_map_link') }}" value="{{ $settings['google_map_link'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="fees_remainder_duration">{{ __('fees_remainder_duration')}}<span class="text-small text-info">{{ __('reminder_days_before_due') }}</span></label>
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" required name="fees_remainder_duration" placeholder="{{ __('fees_remainder_duration') }}" value="{{ $settings['fees_remainder_duration'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3"><h4>{{__("Domain Settings")}}</h4></div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div> 
                                <div class="row my-4 mx-1">  
                                    <div class="form-group col-sm-12 col-md-4">
                                        <label>{{ __('domain').' '. __('type') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    {!! Form::radio('domain_type', 'default', false, ['class' => 'default' , ($domain_type ==  "default") ? "checked" : "" ]) !!}{{ __('default') }}
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    {!! Form::radio('domain_type', 'custom', false, ['class' => 'custom', ($domain_type ==  "custom") ? "checked" : "" ]) !!}{{ __('custom') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-4 defaultDomain" style="display: none">
                                        <label for="school_domain">{{ __('default_domain')}}</label>
                                        <div class="input-group mb-3">
                                                <input type="text" class="form-control domain-pattern" name="domain" placeholder="{{ __('domain') }}" aria-label="Recipient's username" aria-describedby="basic-addon2" disabled value="{{ ($domain_type ==  "default" && $settings['domain']) ? $settings['domain'] : "" }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text text-body" id="basic-addon2">.{{ $baseUrlWithoutScheme }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-4 customDomain" style="display: none">
                                        <label for="school_domain">{{ __('custom_domain')}}</label>
                                        <div class="input-group mb-3">
                                                <input type="text" class="form-control domain-pattern" name="domain" placeholder="{{ __('domain') }}" aria-label="Recipient's username" aria-describedby="basic-addon2" disabled value="{{ ($domain_type ==  "custom" && $settings['domain']) ? $settings['domain'] : "" }}">
                                        </div>
                                    </div>
                                    @if (!env('DEMO_MODE'))
                                        <div class="form-group col-sm-12 col-md-4 serverinfo" style="display: none">
                                            <label for="serinfo">{{ __('server_info')}}</label>
                                            <div class="input-group mb-3">
                                                    <input type="text" class="form-control" name="server_ip" placeholder="{{ __('domain') }}" aria-describedby="basic-addon2" disabled value="{{ $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'; }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="mx-4 text-justify text-uppercase">
                                        <small
                                            class="text-danger">{{ __('Note : If You are using Custom Domain then you have add a dns entry with pointing the server ip address.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3"><h4>{{__("Roll Number Settings")}}</h4></div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="form-group col-md-12 col-sm-12">
                                    <label for="roll-number-order">{{__("Roll Number Sorting")}}</label>
                                    <input type="hidden" id="roll-number-sort-column" name="roll_number_sort_column" value="{{ $settings['roll_number_sort_column'] ?? "" }}">
                                    <input type="hidden" id="roll-number-sort-order" name="roll_number_sort_order" value="{{ $settings['roll_number_sort_order'] ?? "" }}">
                                    <select name="" id="roll-number-order" class="form-control" required>
                                        <option value="" hidden="">-- {{__('Select')}} --</option>
                                        <option value="first_name,asc">{{__("First Name - Ascending")}}</option>
                                        <option value="first_name,desc">{{__("First Name - Descending")}}</option>
                                        <option value="last_name,asc">{{__("Last Name - Ascending")}}</option>
                                        <option value="last_name,desc">{{__("Last Name - Descending")}}</option>
                                    </select>

                                    <div class="form-check">
                                        <label class="form-check-label"> <input type="checkbox" class="form-check-input" name="change_roll_number" id="change-roll-ckh-settings" value="1"> {{ __('Change Roll Number for All Classes') }} <i class="input-helper"></i></label>
                                    </div>
                                </div>
                            </div>

{{--                            <div class="border border-secondary rounded-lg mb-3">--}}
{{--                                <h3 class="col-12 page-title mt-3 ">--}}
{{--                                    {{ __('Currency Settings') }}--}}
{{--                                </h3>--}}
{{--                                <div class="row my-4 mx-1">--}}
{{--                                    <div class="form-group col-md-3 col-sm-12">--}}
{{--                                        <label for="currency_code">{{__('currency_code')}} <span class="text-danger">*</span></label>--}}
{{--                                        <input name="currency_code" id="currency_code" value="{{ $settings['currency_code'] ?? ''}}" type="text" placeholder="{{__('currency_code')}}" class="form-control" required/>--}}
{{--                                    </div>--}}
{{--                                    <div class="form-group col-md-3 col-sm-12">--}}
{{--                                        <label for="currency_symbol">{{__('currency_symbol')}} <span class="text-danger">*</span></label>--}}
{{--                                        <input name="currency_symbol" id="currency_symbol" value="{{$settings['currency_symbol'] ??  ''}}" type="text" placeholder="{{__('currency_symbol')}}" class="form-control" required/>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            {{-- <input class="btn btn-theme" type="submit" value="{{ __('submit') }}"> --}}
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
    $(document).ready(function () {
        function toggleFields() {
            if ($('.default').is(':checked')) {
                $('.defaultDomain').show().find('input').prop('disabled', false);
                $('.customDomain').hide().find('input').prop('disabled', true);
                $('.serverinfo').hide().find('input');
            } else if ($('.custom').is(':checked')) {
                $('.customDomain').show().find('input').prop('disabled', false);
                $('.serverinfo').show().find('input');
                $('.defaultDomain').hide().find('input').prop('disabled', true);
            }
        }  
        $("input[name='domain_type']").on('change', toggleFields);

        toggleFields();
    });
</script>
@endsection
