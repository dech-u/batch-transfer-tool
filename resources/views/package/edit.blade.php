@extends('layouts.master')

@section('title')
    {{ __('package') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('package') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <h4 class="card-title float-left">
                                    {{ __('edit') . ' ' . __('package') }}
                                </h4>
                            </div>
                            <div class="col-sm-12 col-md-6 text-right">
                                <a href="{{ route('package.index') }}" class="btn btn-theme btn-sm">{{ __('back') }}</a>
                            </div>
                        </div>

                        {{-- @include('package.required_vps') --}}

                        <hr>

                        {!! Form::model($package, [
                            'route' => ['package.update', $package->id],
                            'method' => 'post',
                            'class' => 'edit-form',
                            'novalidate' => 'novalidate',
                            'data-success-function' => 'formSuccessFunction'
                        ]) !!}
                        <div class="row">

                            <div class="form-group col-sm-12 col-md-12">
                                <label for="">{{ __('type') }} <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('type', 0, false, ['class' => 'form-check-input package_type','id' => 'prepaid']) !!}
                                            {{__("prepaid")}}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('type', 1, false, ['class' => 'form-check-input package_type','id' => 'postpaid']) !!}
                                            {{__("postpaid")}}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-12 col-md-6">
                                <label for="">{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, [
                                    'required',
                                    'class' => 'form-control',
                                    'placeholder' => __('package') . ' ' . __('name'),
                                ]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-6">
                                <label for="">{{ __('description') }}</label>
                                {!! Form::textarea('description', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('description'),
                                    'rows' => '3',
                                ]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-6">
                                <label for="">{{ __('tagline') }}</label>
                                {!! Form::text('tagline', null, ['class' => 'form-control', 'placeholder' => __('tagline')]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-2">
                                <label for="" class="day-label">{{ __('days') }}</label> <span class="text-danger">*</span>
                                {!! Form::number('days', null, [
                                    'required',
                                    'class' => 'form-control days',
                                    'min' => 1,
                                    'placeholder' => __('days'),
                                ]) !!}
                            </div>

                            {{-- <div class="form-group col-sm-12 col-md-2">
                                <label for="" class="student-label">{{ __('per_active_student_charges') }}</label>
                                <span class="text-danger">*</span>
                                {!! Form::number('student_charge', null, [
                                    'required',
                                    'class' => 'form-control student-input',
                                    'min' => 0,
                                    'placeholder' => __('per_active_student_charges'),
                                ]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-2">
                                <label for="" class="staff-label">{{ __('per_active_staff_charges') }}</label>
                                <span class="text-danger">*</span>
                                {!! Form::number('staff_charge', null, [
                                    'required',
                                    'class' => 'form-control staff-input',
                                    'min' => 0,
                                    'placeholder' => __('per_active_staff_charges'),
                                ]) !!}
                            </div> --}}


                            {{-- Postpaid --}}
                            <div class="postpaid col-sm-12 col-md-4">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for=""
                                            class="student-label">{{ __('per_active_student_charges') }}</label> <span
                                            class="text-danger">*</span>
                                        {!! Form::number('student_charge', null, [
                                            'required',
                                            'class' => 'form-control student-input',
                                            'min' => 0,
                                            'placeholder' => __('per_active_student_charges'),
                                        ]) !!}
                                    </div>
    
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="" class="staff-label">{{ __('per_active_staff_charges') }}</label>
                                        <span class="text-danger">*</span>
                                        {!! Form::number('staff_charge', null, [
                                            'required',
                                            'class' => 'form-control staff-input',
                                            'min' => 0,
                                            'placeholder' => __('per_active_staff_charges'),
                                        ]) !!}
                                    </div>
                                </div>
                                
                            </div>
                            
                            {{-- Prepaid --}}
                            <div class="prepaid col-sm-12 col-md-4">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="" class="staff-label">{{ __('no_of_students') }} <span class="text-small text-info"> ({{ __('active') }})</span></label>
                                        <span class="text-danger">*</span>
                                        {!! Form::number('no_of_students', null, [
                                            'required',
                                            'class' => 'form-control',
                                            'min' => 1,
                                            'placeholder' => __('no_of_students'),
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="" class="staff-label">{{ __('no_of_staffs') }} <span class="text-small text-info"> ({{ __('active') }})</span></label>
                                        <span class="text-danger">*</span>
                                        {!! Form::number('no_of_staffs', null, [
                                            'required',
                                            'class' => 'form-control',
                                            'min' => 1,
                                            'placeholder' => __('no_of_staffs'),
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="prepaid form-group col-sm-12 col-md-2" style="display: none">
                                <label for="" class="staff-label">{{ __('charges') }}</label>
                                <span class="text-danger">*</span>
                                {!! Form::number('charges', null, [
                                    'required',
                                    'class' => 'form-control',
                                    'min' => 1,
                                    'placeholder' => __('charges'),
                                ]) !!}
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label d-inline">
                                        {!! Form::checkbox('highlight', 1, null, ['class' => 'form-check-input']) !!}
                                        {{ __('highlight') }} {{ __('package') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-check">
                                    <div class="popover-container">
                                    <label class="form-check-label d-inline">
                                        {!! Form::checkbox('instant_effects', 1, null, ['class' => 'form-check-input popover-trigger']) !!}
                                        {{ __('instant_effects') }}
                                    </label>
                                                                        
                                        <i class="popover-trigger fa fa-info-circle"></i>
                                        <div class="popover-content">
                                            <h4>{{ __('Immediate Features Access') }}</h4>
                                            <hr>
                                            <ul>
                                                <li><strong>{{ __('Checked') }}</strong> {{ __('Features changes will be available to existing subscribers in their current billing cycle') }}</li>
                                                <li><strong>{{ __('Unchecked') }}</strong> {{ __('Features changes will only be available to subscribers starting in their next billing cycle') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 text-small text-danger">
                                    <span>{{ __('note: Only features are effected') }}</span>
                                </div>
                                
                            </div>
                            
                        </div>

                        <hr>
                        {{-- Feature --}}
                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-3">
                                <h4 class="card-title">{{ __('features') }}</h4>
                            </div>
                            @foreach ($features as $feature)
                                <div class="form-group col-sm-12 col-md-3">
                                    {{-- Default Feature --}}
                                    @if ($feature->is_default == 1)
                                        <input id="{{ __($feature->name) }}" class="feature-checkbox" disabled
                                        type="checkbox" name="feature_id[]" checked
                                        value="{{ $feature->id }}" />
                                    <label class="feature-list-default text-center"
                                        for="{{ __($feature->name) }}" title="{{ __('default_feature') }}">{{ __($feature->name) }}</label>
                                        <input type="hidden" name="feature_id[]" value="{{ $feature->id }}">
                                        
                                    @else
                                        <input id="{{ __($feature->name) }}" class="feature-checkbox"
                                        type="checkbox" name="feature_id[]" @if (in_array($feature->id, $package->package_feature->pluck('feature_id')->toArray())) checked @endif
                                        value="{{ $feature->id }}" />
                                    <label class="feature-list text-center"
                                        for="{{ __($feature->name) }}">{{ __($feature->name) }}</label>
                                    @endif
                                    
                                </div>
                            @endforeach

                        </div>
                        <hr>
                        <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                        <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('[data-bs-toggle="tooltip"]').tooltip();
        function formSuccessFunction(response) {
            setTimeout(() => {
                window.location.href = "{{route('package.index')}}"
            }, 2000);
        }

        setTimeout(() => {
            window.onload = $('.package_type').trigger('change');
        }, 1000);

        $('.package_type').change(function (e) { 
            e.preventDefault();
            if ($('input[name="type"]:checked').val() == 1) {
                $('.postpaid').slideDown(500);
                $('.prepaid').slideUp(500);
            } else {
                $('.prepaid').slideDown(500);
                $('.postpaid').slideUp(500);
            }
        });
    </script>
@endsection
