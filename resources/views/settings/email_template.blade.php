@extends('layouts.master')

@section('title')
    {{ __('email_template') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('email_template') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="email-template-setting-form" action="{{ route('system-settings.email-template.update', 1) }}" method="POST" novalidate="novalidate">
                            @csrf
                            @include('settings.forms.email-template-form')
                            <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
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
            $('.email-template').trigger('change');
        }, 500);
        $('.email-template').change(function (e) { 
            e.preventDefault();
            let type = $('input[name="template"]:checked').val();

            if (type == 'school-email-template') {
                $('.school-email-template').show(500);
                $('.school-reject-template').hide(500);
           
            } else if(type == 'school-reject-template') {
                $('.school-reject-template').show(500);
                $('.school-email-template').hide(500);
               
            
            }
        });
    </script>
@endsection
