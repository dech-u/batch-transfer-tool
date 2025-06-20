<div class="row">
    <div class="form-group col-md-6 col-sm-12">
        <label for="firebase_project_id">{{ __('firebase_project_id') }}</label>
        <input name="firebase_project_id" id="firebase_project_id" value="{{ $project_id ?? '' }}"  required placeholder="{{ __('firebase_project_id') }}" class="form-control"/>
    </div>
    <div class="form-group col-md-6 col-sm-12">
        <label>{{ __('firebase_service_file') }} <span class="text-info text-small">({{ __('Only Json File Allowed') }} )</span></label>
        <a href="{{ asset('assets/notification-format.json') }}" target="_blank">{{ __('Sample Service File') }}</a>
        <input type="file" name="firebase_service_file" class="file-upload-default" accept="application/json"/>
        <div class="input-group col-xs-12">
            <input type="text" class="form-control file-upload-info" accept="application/json" disabled="" placeholder="{{ __('firebase_service_file') }}" aria-label=""/>
            <span class="input-group-append">
                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
            </span>
        </div>
        <a href="{{ $serviceFile ?? '' }}"> {{ __('Service File') }}</a>
    </div>
</div>