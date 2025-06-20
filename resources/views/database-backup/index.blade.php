    @extends('layouts.master')

    @section('title')
        {{ __('database_backup') }}
    @endsection

    @section('content')
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    {{ __('manage_backup') }}
                </h3>
            </div>
            <div class="row">
                <!-- Left Column: Generate Backup -->
                <div class="col-md-6 grid-margin">
                    <div class="card" style="width: 100%; height: 300px;"> <!-- Fixed height and width -->
                        <div class="custom-card-body">
                            <div class="row">

                                <div class="col-md-12 text-left">
                                    <h4>{{ __('generate_backup') }}</h4>
                                    
                                    <button class="btn create-backup btn-theme btn-sm">{{ __('generate_backup') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Restore Backup -->
                <div class="col-md-6 grid-margin stretch-card" id="restore-container">
                    <div class="card" style="width: 100%; height: 300px;"> <!-- Fixed height and width -->
                        <div class="card-body">
                            <h4>{{ __('select_zip') }}</h4>
                        
                            <form class="pt-3 restore-form" id="create-form" action="{{ route('database-backup.restore',$schoolId) }}" method="POST" enctype="multipart/form-data">
                                <div class="col-12">
                                    <input type="hidden" name="school_id" value="{{ $schoolId }}">
                                    <div class="form-group">
                                        <label>{{ __('zip_file') }} <span class="text-danger">*</span></label>
                                        <input type="file" name="zip_file" id="zip_file" class="file-upload-default" accept="zip"/>
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('zip_file') }}" aria-label=""/>
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                            </span>
                                        </div>
                                    </div>
                                    <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value="{{ __('submit') }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
