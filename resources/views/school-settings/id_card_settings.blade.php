@extends('layouts.master')

@section('title')
    {{ __('id_card_setting') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('id_card_setting') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3 create-form-without-reset" id="formdata" action="{{ url('id-card-settings') }}"
                            method="POST" novalidate="novalidate">
                            <div class="row">
                                {{--  --}}
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <div class="col-12 d-flex row">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input type" name="type"
                                                        id="type" value="Student" checked required="required">
                                                    {{ __('student') }}
                                                </label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input type" name="type"
                                                        id="type" value="Staff" required="required">
                                                    {{ __('staff') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>

                            <div id="student-id-card">
                                @include('school-settings.student_id_card')
                            </div>

                            <div id="staff-id-card">
                                @include('school-settings.staff_id_card')
                            </div>

                            {{-- Signature --}}
                            <div class="form-group col-sm-12 col-md-6">
                                <label for="image">{{ __('signature') }} </label>
                                <input type="file" name="signature"
                                    accept="image/jpg,image/png,image/jpeg,image/svg" class="file-upload-default" />
                                <div class="input-group col-xs-12">
                                    <input type="text" id="image" class="form-control file-upload-info"
                                        disabled="" placeholder="{{ __('image') }}" />
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-theme"
                                            type="button">{{ __('upload') }}</button>
                                    </span>
                                </div>
                                @if ($settings['signature'] ?? '')
                                    <div id="signature">
                                        <img src="{{ $settings['signature'] }}" class="img-fluid w-25"
                                            alt="">

                                        <div class="mt-2">
                                            <a href="" data-type="signature"
                                                class="btn btn-inverse-danger btn-sm id-card-settings">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                        <div class="mt-3">
                                            <span class="text-info">
                                                {{ __('note_these_signature_image_are_also_used_in_certificates') }}
                                                
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {{-- End signature --}}

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
        window.onload = setTimeout(() => {
            $('.type').trigger('change');
        }, 500);
        $('.type').change(function(e) {
            e.preventDefault();
            let type = $('input[name="type"]:checked').val();
            if (type == 'Student') {
                $('#student-id-card').slideDown(500);
                $('#staff-id-card').slideUp(500);
            }
            if (type == 'Staff') {
                $('#student-id-card').slideUp(500);
                $('#staff-id-card').slideDown(500);
            }
        });
    </script>
@endsection
