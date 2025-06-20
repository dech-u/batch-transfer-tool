@extends('layouts.master')

@section('title')
{{ __('profile') }}
@endsection


@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            {{ __('profile') }}
        </h3>
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <form class="pt-3 profile-update-form edit-form-without-reset" id="edit-form" enctype="multipart/form-data" action="{{ route('auth.profile.update') }}" novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', $userData->first_name, ['required','placeholder' => __('first_name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', $userData->last_name, ['required','placeholder' => __('last_name'), 'class' => 'form-control']) !!}

                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('mobile') }}</label>
                                {!! Form::number('mobile', $userData->mobile, ['placeholder' => __('mobile'), 'class' => 'form-control remove-number-increment']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender','male',Str::lower($userData->gender) == 'male' ? 'checked':'') !!}
                                            {{ __('male') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender','female', Str::lower($userData->gender) == 'female' ? 'checked':'') !!}
                                            {{ __('female') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="image">{{ __('image') }} <span class="text-danger">*</span></label>
                                <input type="file" name="image" accept="image/jpg,image/png,image/jpeg" class="file-upload-default" />
                                <div class="input-group col-xs-12">
                                    <input type="text" id="image" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}" required="required"/>
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                    </span>
                                </div>
                                <div style="width: 40px;">
                                    <img src="{{$userData->image}}" id="edit-user-image-tag" class="img-fluid w-100" alt=""/>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                {!! Form::text('dob', date('d-m-Y',strtotime($userData->dob)) , ['required','placeholder' => __('dob'), 'class' => 'datepicker-popup-no-future form-control','required' => true]) !!}
                                <span class="input-group-addon input-group-append"></span>
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('email', $userData->email, ['required','placeholder' => __('email'), 'class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('current_address', $userData->current_address, ['required','placeholder' => __('current_address'), 'class' => 'form-control', 'id' => 'current_address','rows'=>2]) !!}
                            </div>
                            <div class="form-group col-6">
                                <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('permanent_address', $userData->permanent_address, ['required','placeholder' => __('permanent_address'), 'class' => 'form-control', 'id' => 'permanent_address','rows'=>2]) !!}
                            </div>
                        </div>
                        <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        @if($userData->dob)
            $('.dob').val(moment("{{ $userData->dob }}" ,'YYYY-MM-DD').format('DD-MM-YYYY'))
        @endif
    });
</script>
@endsection
