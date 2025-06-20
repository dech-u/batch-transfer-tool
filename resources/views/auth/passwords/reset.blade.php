{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}


    <!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ __('Reset Password') }} || {{ config('app.name') }}</title>

    @include('layouts.include')

</head>

<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
            <div class="row flex-grow">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left p-5">
                        <div class="brand-logo text-center">
                            {{-- <img src="{{asset(config('global.LOGO1')) }}" alt="logo"> --}}
                            {{-- <img src="{{ asset('logo.svg') }}" alt="logo"> --}}
                            <img src="{{ $systemSettings['horizontal_logo']  ?? url('assets/vertical-logo.svg') }}" alt="logo">
                        </div>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label>{{ __('school_code') }}</label>
                                <input id="school_code" type="school_code" class="form-control form-control-lg" name="school_code" value="{{ old('school_code') }}" autocomplete="school_code" autofocus placeholder="{{ __('school_code') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('email') }}</label>
                                <input id="email" type="email" class="form-control form-control-lg" name="email"
                                       value="{{ old('email') }}" required autocomplete="email" autofocus
                                       placeholder="{{ __('email') }}">
                            </div>
                            <div class="form-group">
                                <label>{{ __('password') }}</label>

                                <div class="input-group">
                                    <input id="password" type="password" class="form-control form-control-lg"
                                           name="password" required autocomplete="current-password"
                                           placeholder="{{ __('password') }}">
                                    <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye-slash" id="togglePassword"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ __('confirm_password') }}</label>
                                {{-- <input type="password" name="password" required class="form-control form-control-lg" placeholder="{{__('password')}}"> --}}

                                <div class="input-group">
                                    <input id="password-confirm" type="password"
                                           class="form-control form-control-lg" name="password_confirmation" required
                                           autocomplete="new-password" placeholder="{{ __('confirm_password') }}">
                                    <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye-slash" id="toggleConfirmPassword"></i>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <input type="submit" name="btnlogin" value="{{ __('Reset Password') }}"
                                       class="btn btn-block btn-theme btn-lg font-weight-medium auth-form-btn"/>
                            </div>
                        </form>
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

<script type='text/javascript'>
    $("#frmLogin").validate({
        rules: {
            username: "required",
            password: "required",
        },
        errorPlacement: function (label, element) {
            label.addClass('mt-2 text-danger');
            label.insertAfter(element);
        },
        highlight: function (element, errorClass) {
            $(element).parent().addClass('has-danger')
            $(element).addClass('form-control-danger')
        }
    });
</script>
<script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        // this.classList.toggle("fa-eye");
        if (password.getAttribute("type") === 'password') {
            $('#togglePassword').addClass('fa-eye-slash');
            $('#togglePassword').removeClass('fa-eye');
        } else {
            $('#togglePassword').removeClass('fa-eye-slash');
            $('#togglePassword').addClass('fa-eye');
        }
    });
</script>
<script>
    const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
    const password_confirm = document.querySelector("#password-confirm");

    toggleConfirmPassword.addEventListener("click", function () {
        const type = password_confirm.getAttribute("type") === "password" ? "text" : "password";
        password_confirm.setAttribute("type", type);
        // this.classList.toggle("fa-eye");
        if (password_confirm.getAttribute("type") === 'password') {
            $('#toggleConfirmPassword').addClass('fa-eye-slash');
            $('#toggleConfirmPassword').removeClass('fa-eye');
        } else {
            $('#toggleConfirmPassword').removeClass('fa-eye-slash');
            $('#toggleConfirmPassword').addClass('fa-eye');
        }
    });
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
