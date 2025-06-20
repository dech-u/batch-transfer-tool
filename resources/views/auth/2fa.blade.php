<!DOCTYPE html>
<html lang="en">
    @php
    $lang = Session::get('language');
@endphp
@if($lang)
    @if ($lang->is_rtl)
        <html lang="en" dir="rtl">
    @else
        <html lang="en" dir="ltl">
    @endif
@else
    <html lang="en" dir="ltl">
@endif
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link href="{{ asset('assets/home_page/css/style.css') }}" rel="stylesheet">

    <title>{{ __('verify_code') }} || {{ config('app.name') }}</title>

    @include('layouts.include')

    <style>
        :root {
        --primary-color: {{ $systemSettings['theme_primary_color'] ?? '#56cc99' }};
        --secondary-color: {{ $systemSettings['theme_secondary_color'] ?? '#215679' }};
        --secondary-color1: {{ $systemSettings['theme_secondary_color_1'] ?? '#38a3a5' }};
        --primary-background-color: {{ $systemSettings['theme_primary_background_color'] ?? '#f2f5f7' }};
        --text--secondary-color: {{ $systemSettings['theme_text_secondary_color'] ?? '#5c788c' }};
        
    }
    .modal .modal-dialog {
        margin-top: unset !important;
    }
    a {
        color: #007bff !important;
    }
    </style>

</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper login-d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-xl-6 mx-auto">
                        <div class="auth-form-light rounded-lg text-left p-5">
                            <div class="brand-logo text-center">
                                @if ($schoolSettings['horizontal_logo'] ?? '')
                                    <img class="img-fluid w-25" src="{{ $schoolSettings['horizontal_logo'] ?? '' }}" alt="logo">    
                                @elseif($systemSettings['login_page_logo'] ?? $systemSettings['horizontal_logo'] ?? '')
                                    <img class="img-fluid w-25" src="{{ $systemSettings['login_page_logo'] ?? $systemSettings['horizontal_logo'] ?? '' }}" alt="logo">
                                @else
                                    <img class="img-fluid w-25" src="{{ url('assets/horizontal-logo.svg') }}" alt="logo">
                                @endif

                            </div>
                            <div class="mt-3">
                                {{-- emailSuccess --}}
                                @if (\Session::has('emailSuccess'))
                                    <div class="alert alert-success text-center" role="alert">
                                        {{ \Session::get('emailSuccess') }}.
                                    </div>
                                @endif
                                @if (\Session::has('success'))
                                    <div class="alert alert-success text-center" role="alert">
                                        {{ \Session::get('success') }}.
                                    </div>
                                    <div class="alert alert-success text-center mt-2" role="alert">
                                        Please ensure you use your registered email for login, and your contact number as the password.
                                    </div>
                                @endif
                                {{-- emailError --}}
                                @if (\Session::has('emailError'))
                                    <div class="alert alert-danger text-center" role="alert">
                                        {{ \Session::get('emailError') }}.
                                    </div>
                                @endif
                                @if (\Session::has('error'))
                                    <div class="alert alert-danger text-center" role="alert">
                                        {{ \Session::get('error') }}.
                                    </div>
                                @endif
                            </div>

                            <div class="text-center">
                                <h3>2 Factor Authentication</h3>
                            </div>
                            <form action="{{ route('auth.2fa.code') }}" id="frm2FA" method="POST" class="pt-3">
                                @csrf
                                <div class="form-group">
                                    <label for="two_factor_secret">{{ __('enter_the_verification_code') }}</label>
                                    <input id="two_factor_secret" type="text" class="form-control rounded-lg form-control-lg"
                                        name="two_factor_secret" value="" autocomplete="two_factor_secret"
                                        autofocus placeholder="{{ __('enter_the_verification_code') }}">
                                </div>
                                <div class="mt-3">
                                    <input type="submit" name="btn2FA" id="2FA_btn" value="{{ __('verify_code') }}" class="btn btn-block btn-theme btn-lg font-weight-medium auth-form-btn rounded-lg" />
                                </div>
                            </form>

                            <div class="my-3">
                                <strong>Note:</strong> Please check your email for a verification code. If you still cannot receive the code, try again login with your email and password.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <script src="{{ asset('/assets/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/common.js') }}"></script>
    <script src="{{ asset('/assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/function.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    </script>
</body>

@if (Session::has('error'))
    <script type='text/javascript'>
        $.toast({
            text: '{{ Session::get('error') }}',
            showHideTransition: 'slide',
            icon: 'error',
            loaderBg: '#f2a654',
            position: 'top-right'
        });
    </script>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script type='text/javascript'>
            $.toast({
                text: '{{ $error }}',
                showHideTransition: 'slide',
                icon: 'error',
                loaderBg: '#f2a654',
                position: 'top-right'
            });
        </script>
    @endforeach
@endif

</html>

@section('js')
<script>
    $(document).ready(function() {
        $('#two_factor_secret').val('');
    });
</script>
@endsection
