@extends('layouts.master')

@section('title')
    {{ __('add_bulk_data') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Students') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3" id="create-form" enctype="multipart/form-data"
                            action="{{ route('students.store-bulk-data') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="session_year_id">{{ __('Session Year') }} <span class="text-danger">*</span></label>
                                    <select name="session_year_id" id="session_year_id" class="form-control select2">
                                        @foreach ($sessionYears as $sessionYear)
                                            <option value="{{ $sessionYear->id }}"
                                                {{ $sessionYear->default == 1 ? 'selected' : '' }}>{{ $sessionYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="class_section">{{ __('class_section') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section" class="form-control select2">
                                        <option value="">{{ __('select') . ' ' . __('Class') . ' ' . __('section') }}
                                        </option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
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
                                <div class="form-group col-sm-12 col-xs-12">
                                    <input class="btn btn-theme submit_bulk_file float-right" type="submit" value="{{ __('submit') }}"
                                        name="submit" id="submit_bulk_file">
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="row form-group col-sm-12 col-md-4 mt-5">
                            <a class="btn btn-theme form-control" href="{{ route('student.bulk-data-sample') }}" download>
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

@section('js')
@endsection
