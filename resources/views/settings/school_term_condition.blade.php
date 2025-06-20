@extends('layouts.master')

@section('title')
    {{ __('terms_condition') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('school') }} {{ __('terms_condition') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="setting-form" action="{{ route('system-settings.update', 1) }}"
                            method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="name" id="name" value="{{ $name }}">
                                <label for="data"></label>
                                <div class="form-group col-md-12 col-sm-12">
                                    <textarea id="tinymce_message" name="data" id="data" required placeholder="{{ __('school'). ' ' .__('terms_condition') }}">{{ $data ?? '' }}</textarea>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
