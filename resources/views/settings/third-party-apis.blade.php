@extends('layouts.master')

@section('title')
    {{ __('Third-Party APIs') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Third-Party APIs') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="custom-card-body">
                        <form id="formdata" class="create-form-without-reset" action="{{ route('system-settings.third-party.update') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            @include('settings.forms.third-party-apis-form')
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
