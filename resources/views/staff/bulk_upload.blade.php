@extends('layouts.master')

@section('title')
    {{ __('staff') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Staff Bulk Upload') }}
                        </h4>
                        <form class="pt-3 create-staff-form" id="create-form" action="{{ route('staff.store-bulk-upload') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="role_id">{{ __('role') }} <span class="text-danger">*</span></label>
                                    <select name="role_id" id="role_id" class="form-control" required>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="file-upload-default">{{ __('file_upload') }} <span class="text-danger">*</span></label>
                                    <input type="file" name="file" class="file-upload-default" />
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" id="file-upload-default" disabled="" placeholder="{{ __('file_upload') }}" required="required" />
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                    <div class="form-check w-fit-content">
                                        <label class="form-check-label user-select-none">
                                            <input type="checkbox" class="form-check-input" name="is_send_notification" id="send_notification">
                                            {{ __('send_notification') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <input class="btn btn-theme" id="create-btn" type="submit" value={{ __('submit') }}>
                        </form>
                        <hr>
                        <div class="row form-group col-sm-12 col-md-2 mt-5">
                            <a class="btn btn-theme form-control" href="{{ route('staff.bulk-data-sample') }}" download>
                                <strong>{{ __('download_dummy_file') }}</strong>
                            </a>
                        </div>
                        <div class="row col-sm-12 col-xs-12">
                            <span style="font-size: 14px">
                                <b>{{ __('note') }} :- </b>{{ __('First download dummy file and convert to .csv file then upload it') }}.</span>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection        