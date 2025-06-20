@extends('layouts.master')

@section('title')
    {{ __('send_mail_to_schools') }}
@endsection


@section('content')
    {{-- student App Settings --}}
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('send_mail_to_schools') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="create-form" action="{{ url('schools/send-mail') }}" novalidate="novalidate">
                            @csrf
                            <div class="pt-3 row">

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="assign_schools">{{__('subject') }}<span class="text-danger">*</span></label>
                                    {!! Form::text('subject', null, ['class' => 'form-control', 'required']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="assign_schools">{{ __('schools') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('school_id[]', $schools, null, ['class' => 'form-control select2-dropdown select2-hidden-accessible','multiple','required']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label for="">{{ __('description') }} <span class="text-danger">*</span></label>
                                    <textarea id="tinymce_message" name="description" id="data" required placeholder="{{__('description')}}"></textarea>
                                </div>
                                {{-- School information --}}
                                <div class="form-group col-sm-12 col-md-12">
                                    <a data-value="{school_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_name') }} }</a>
                                    <a data-value="{school_admin_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_admin_name') }} }</a>
                                    <a data-value="{school_email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_email') }} }</a>
                                    <a data-value="{school_admin_email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_admin_email') }} }</a>
                                    <a data-value="{school_admin_mobile}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_admin_mobile') }} }</a>
                                    <a data-value="{code}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('code') }} }</a>
                                </div>

                                {{-- System informatio --}}
                                <div class="form-group col-sm-12 col-md-12">
                                    <a data-value="{system_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('system_name') }} }</a>
                                    <a data-value="{support_email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('support_email') }} }</a>
                                    <a data-value="{support_contact}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('support_contact') }} }</a>
                                    <a data-value="{website}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('website') }} }</a>
                                </div>
                            </div>
                            
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    
@endsection
