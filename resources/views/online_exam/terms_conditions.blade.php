@extends('layouts.master')

@section('title')
    {{__('online').' '.__('exam').' '.__('terms_condition')}}
@endsection


@section('content')

<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title">
        {{__('online').' '.__('exam').' '.__('terms_condition')}}
    </h3>
  </div>
  <div class="row grid-margin">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
            {{-- create-form-without-reset-text-editor --}}
            <form id="" class="create-form-without-reset-text-editor" action="{{route('school-settings.online-exam.store')}}" method="POST" novalidate="novalidate">
            @csrf
            <div class="row">
                <input type="hidden" name="name" id="name" value="{{$name}}">
                <div class="form-group col-md-12 col-sm-12">
                    <label for="data"></label>
                    <textarea id="tinymce_message" name="data" id="data" required placeholder="{{__('online').' '.__('exam').' '.__('terms_condition')}}">{{ isset($onlineExamTermsConditions) && !empty($onlineExamTermsConditions) ? htmlspecialchars_decode($onlineExamTermsConditions) : ''}}</textarea>
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
