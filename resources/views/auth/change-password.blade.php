@extends('layouts.master')

@section('title')
    {{ __('reset_password') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('reset_password') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="create-form password_form" action="{{ route('auth.change-password.update') }}"
                              method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-10">
                                    <label for="old_password">{{ __('old_password') }}</label>
                                    <div class="input-group">
                                        <input type="password" name="old_password" id="old_password" required placeholder="{{ __('old_password') }}" class="form-control"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye-slash" id="togglePassword1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-md-2"><br><br>
                                    <label id="old_status"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-10">
                                    <label for="new_password">{{ __('new_password') }}</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" id="new_password" required placeholder="{{ __('new_password') }}" class="form-control"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye-slash" id="togglePassword2"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-10">
                                    <label for="confirm_password">{{ __('confirm_password') }}</label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" id="confirm_password" required placeholder="{{ __('confirm_password') }}" class="form-control"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye-slash" id="togglePassword3"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input class="btn btn-theme" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            let allowsubmit = false;
            $('#old_password').on('blur input', function () {
                let old_password = $(this).val();
                $.ajax({
                    type: "GET",
                    dataType: "JSON",
                    url: "{{route('auth.check-password')}}",
                    data: {
                        old_password: old_password
                    },
                    beforeSend: function () {
                        $('#old_status').html('Checking..');
                    },
                    success: function (result) {
                        if (result == 1) {
                            $('#old_status').html("<i class='fa fa-check-circle fa-2x text-success'></i>");
                            allowsubmit = true;
                        } else {
                            $('#old_status').html("<i class='fa fa-times-circle fa-2x text-danger'></i>");
                            $('#old_password').focus();
                            allowsubmit = false;
                        }
                    },
                    error: function (result) {
                        $('#old_status').html("Error" + result);
                    }
                });
            });
            $('.password_form').submit(function () {
                return !!allowsubmit;
            });

            const togglePassword1 = document.querySelector("#togglePassword1");
            const password1 = document.querySelector("#old_password");
            togglePassword1.addEventListener("click", function () {
                const type1 = password1.getAttribute("type") === "password" ? "text" : "password";
                password1.setAttribute("type", type1);
                // this.classList.toggle("fa-eye");
                if (password1.getAttribute("type") === 'password') {
                    $('#togglePassword1').addClass('fa-eye-slash');
                    $('#togglePassword1').removeClass('fa-eye');
                } else {
                    $('#togglePassword1').removeClass('fa-eye-slash');
                    $('#togglePassword1').addClass('fa-eye');
                }
            });

            const togglePassword2 = document.querySelector("#togglePassword2");
            const password2 = document.querySelector("#new_password");

            togglePassword2.addEventListener("click", function () {
                const type2 = password2.getAttribute("type") === "password" ? "text" : "password";
                password2.setAttribute("type", type2);
                // this.classList.toggle("fa-eye");
                if (password2.getAttribute("type") === 'password') {
                    $('#togglePassword2').addClass('fa-eye-slash');
                    $('#togglePassword2').removeClass('fa-eye');
                } else {
                    $('#togglePassword2').removeClass('fa-eye-slash');
                    $('#togglePassword2').addClass('fa-eye');
                }
            });


            const togglePassword3 = document.querySelector("#togglePassword3");
            const password3 = document.querySelector("#confirm_password");

            togglePassword3.addEventListener("click", function () {
                const type3 = password3.getAttribute("type") === "password" ? "text" : "password";
                password3.setAttribute("type", type3);
                // this.classList.toggle("fa-eye");
                if (password3.getAttribute("type") === 'password') {
                    $('#togglePassword3').addClass('fa-eye-slash');
                    $('#togglePassword3').removeClass('fa-eye');
                } else {
                    $('#togglePassword3').removeClass('fa-eye-slash');
                    $('#togglePassword3').addClass('fa-eye');
                }
            });
        });
    </script>
@endsection
