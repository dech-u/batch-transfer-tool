@extends('layouts.master')

@section('title')
    {{ __('system_update') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ __('system_update') }}
                <small class="theme-color">{{isset($system_version['data']) ? __('current_version').$system_version['data'] :''}}</small>
            </h3>

        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" action="{{ url('system-update') }}" id="system-update" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('Purchase Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="purchase_code" class="form-control"/>
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('files') }} <span class="text-danger">* <small>({{ __('Only Zip File is allowed') }})</small></span></label>
                                    <input type="file" name="file" class="form-control" multiple/>
                                    <small class="theme-color">{{__('Your Current Version is')}} {{isset($system_version['data']) ? $system_version['data'] :''}}, {{__('Please update nearest version here if available')}}</small>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
