@extends('layouts.master')

@section('title')
    {{__('notification_settings')}}
@endsection


@section('content')

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{__('notification_settings')}}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="edit-form" action="{{route('notification-setting.update')}}" method="POST" novalidate="novalidate">
                            @csrf
                            @include('settings.forms.fcm-form')
                            <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
