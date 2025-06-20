@extends('layouts.master')

@section('title') {{__('teacher').' '.__('privacy_policy')}} @endsection


@section('content')

<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title">
      {{__('privacy_policy')}}
    </h3>
  </div>
  <div class="row grid-margin">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
            <div class="mb-3 d-flex">
                {{__("Public URL")}} :&nbsp;&nbsp;<a href="{{route('public.teacher-privacy-policy')}}" target="_blank">{{route('public.teacher-privacy-policy')}}</a>
            </div>
            <form id="formdata" class="setting-form" action="{{route('system-settings.update',1)}}" method="POST" novalidate="novalidate">
            @csrf
            <div class="row">
              <input type="hidden" name="name" id="name" value="{{$name}}">
                <label for="data"></label>
              <div class="form-group col-md-12 col-sm-12">
                  <textarea id="tinymce_message" name="data" id="data" required placeholder="{{__('privacy_policy')}}">{{$data ?? ''}}</textarea>
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
