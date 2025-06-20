@extends('layouts.master')

@section('title')
    {{ __('app_settings') }}
@endsection


@section('content')
    {{-- student App Settings --}}
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('app_settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="create-form-without-reset" action="{{ route('system-settings.app.update') }}" novalidate="novalidate">
                            @csrf
                            <h4 class="card-title">
                                {{ __('Student/Guardian App Settings') }}
                            </h4>
                            <div class="pt-3 row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="app_link">{{ __('app_link') }} </label>
                                    <input name="app_link" id="app_link" value="{{ $settings['app_link'] ?? '' }}" type="url" placeholder="{{ __('app_link') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="ios_app_link">{{ __('ios_app_link') }} </label>
                                    <input name="ios_app_link" id="ios_app_link" value="{{ $settings['ios_app_link'] ?? '' }}" type="url" placeholder="{{ __('ios_app_link') }}" class="form-control"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="app_version">{{ __('app_version') }} </label>
                                    <input name="app_version" id="app_version" value="{{ $settings['app_version'] ?? '' }}" type="text" placeholder="{{ __('app_version') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="ios_app_version">{{ __('ios_app_version') }} </label>
                                    <input type="text" name="ios_app_version" id="ios_app_version" placeholder="{{ __('ios_app_version') }}" class="form-control" value="{{ $settings['ios_app_version'] ?? '' }}">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('force_app_update') }}</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" value="{{ $settings['force_app_update'] ?? 0 }}" id="force_app_update">{{ __('force_app_update') }}
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="force_app_update" id="txt_force_app_update">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('app_maintenance') }}</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" value="{{ $settings['app_maintenance'] ?? 0 }}" id="app_maintenance">{{ __('app_maintenance') }}
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="app_maintenance" id="txt_app_maintenance">
                                </div>
                            </div>
                            <hr>

                            {{-- Teacher app --}}
                            <h4 class="card-title">
                                {{ __('Teacher / Staff App Settings') }}
                            </h4>
                            <div class="pt-3 row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="app_link">{{ __('app_link') }} </label>
                                    <input name="teacher_app_link" id="app_link" value="{{ $settings['teacher_app_link'] ?? '' }}" type="url" placeholder="{{ __('app_link') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="ios_app_link">{{ __('ios_app_link') }} </label>
                                    <input name="teacher_ios_app_link" id="ios_app_link" value="{{ $settings['teacher_ios_app_link'] ?? '' }}" type="url" placeholder="{{ __('ios_app_link') }}" class="form-control"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="app_version">{{ __('app_version') }} </label>
                                    <input name="teacher_app_version" id="app_version" value="{{ $settings['teacher_app_version'] ?? '' }}" type="text" placeholder="{{ __('app_version') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="ios_app_version">{{ __('ios_app_version') }} </label>
                                    <input type="text" name="teacher_ios_app_version" id="ios_app_version" placeholder="{{ __('ios_app_version') }}" class="form-control" value="{{ $settings['teacher_ios_app_version'] ?? '' }}">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('force_app_update') }}</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" value="{{ $settings['teacher_force_app_update'] ?? 0 }}" id="teacher_force_app_update">{{ __('force_app_update') }}
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="teacher_force_app_update" id="teacher_txt_force_app_update">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('app_maintenance') }}</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" value="{{ $settings['teacher_app_maintenance'] ?? 0 }}" id="teacher_app_maintenance">{{ __('app_maintenance') }}
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                    <input type="hidden" name="teacher_app_maintenance" id="teacher_txt_app_maintenance">
                                </div>
                            </div>
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
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
        function app_setting() {
            let force_app_update = $('#force_app_update').val();
            let app_maintenance = $('#app_maintenance').val();

            let teacher_force_app_update = $('#teacher_force_app_update').val();
            let teacher_app_maintenance = $('#teacher_app_maintenance').val();
            // Student & Guardian App
            if (force_app_update == 1) {
                $('#force_app_update').attr('checked', true);
                $('#force_app_update').val(1);
                $('#txt_force_app_update').val(1);
            } else {
                $('#force_app_update').val(0);
                $('#txt_force_app_update').val(0);
            }
            if (app_maintenance == 1) {
                $('#app_maintenance').attr('checked', true);
                $('#app_maintenance').val(1);
                $('#txt_app_maintenance').val(1);
            } else {
                $('#app_maintenance').val(0);
                $('#txt_app_maintenance').val(0);
            }

            // Teacher & Staff App
            if (teacher_force_app_update == 1) {
                $('#teacher_force_app_update').attr('checked', true);
                $('#teacher_force_app_update').val(1);
                $('#teacher_txt_force_app_update').val(1);
            } else {
                $('#teacher_force_app_update').val(0);
                $('#teacher_txt_force_app_update').val(0);
            }
            if (teacher_app_maintenance == 1) {
                $('#teacher_app_maintenance').attr('checked', true);
                $('#teacher_app_maintenance').val(1);
                $('#teacher_txt_app_maintenance').val(1);
            } else {
                $('#teacher_app_maintenance').val(0);
                $('#teacher_txt_app_maintenance').val(0);
            }
        }

        $(document).ready(function () {
            app_setting();
        });

        // Student & Guardian App
        $(document).on('change', '#force_app_update', function () {
            if ($('#force_app_update').val() == 1) {
                $('#force_app_update').val(0);
                $('#txt_force_app_update').val(0);
            } else {
                $('#force_app_update').val(1);
                $('#txt_force_app_update').val(1);
            }
        });
        $(document).on('change', '#app_maintenance', function () {
            if ($('#app_maintenance').val() == 1) {
                $('#app_maintenance').val(0);
                $('#txt_app_maintenance').val(0);
            } else {
                $('#app_maintenance').val(1);
                $('#txt_app_maintenance').val(1);
            }
        });

        // Teacher & Staff App
        $(document).on('change', '#teacher_force_app_update', function () {
            if ($('#teacher_force_app_update').val() == 1) {
                $('#teacher_force_app_update').val(0);
                $('#teacher_txt_force_app_update').val(0);
            } else {
                $('#teacher_force_app_update').val(1);
                $('#teacher_txt_force_app_update').val(1);
            }
        });
        $(document).on('change', '#teacher_app_maintenance', function () {
            if ($('#teacher_app_maintenance').val() == 1) {
                $('#teacher_app_maintenance').val(0);
                $('#teacher_txt_app_maintenance').val(0);
            } else {
                $('#teacher_app_maintenance').val(1);
                $('#teacher_txt_app_maintenance').val(1);
            }
        });
    </script>
@endsection
