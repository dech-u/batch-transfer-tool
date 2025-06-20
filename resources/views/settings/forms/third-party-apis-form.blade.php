{{-- System Settings --}}
<div class="border border-secondary rounded-lg my-4 mx-1">
    <div class="col-md-12 mt-3">
        <h4>{{ __('google_recaptcha') }}</h4>
    </div>
    <div class="col-12 mb-3">
        <hr class="mt-0">
    </div>
    <div class="row my-4 mx-1">
        {{-- <div class="form-group col-md-4 col-sm-12">
            <label for="RECAPTCHA_SITE">{{ __('RECAPTCHA_SITE') }} <span class="text-danger">*</span></label>
            <input name="RECAPTCHA_SITE" id="RECAPTCHA_SITE" value="{{ env('RECAPTCHA_SITE') ?? '' }}" type="text" placeholder="{{ __('RECAPTCHA_SITE') }}" class="form-control"/>
        </div> --}}
        <div class="form-group col-md-4 col-sm-12">
            <label for="RECAPTCHA_SITE_KEY">{{ __('RECAPTCHA_SITE_KEY') }}</label>
            <input name="RECAPTCHA_SITE_KEY" id="RECAPTCHA_SITE_KEY" value="{{ env('RECAPTCHA_SITE_KEY') ?? '' }}" type="text" placeholder="{{ __('RECAPTCHA_SITE_KEY') }}" class="form-control"/>
        </div>

        <div class="form-group col-md-4 col-sm-12">
            <label for="RECAPTCHA_SECRET_KEY">{{ __('RECAPTCHA_SECRET_KEY') }}</label>
            <input name="RECAPTCHA_SECRET_KEY" id="RECAPTCHA_SECRET_KEY" value="{{ env('RECAPTCHA_SECRET_KEY') ?? '' }}" type="text" placeholder="{{ __('RECAPTCHA_SECRET_KEY') }}" class="form-control"/>
        </div>

        {{-- Add link for reCAPTCHA Admin --}}
        <div class="col-md-12">
            <p class="mt-3">
                <a href="https://www.google.com/recaptcha/admin/create" target="_blank" class="text-info">
                    {{ __('Click here to create or manage reCAPTCHA keys') }}
                </a>
            </p>
        </div>

        {{-- Add link for the video tutorial --}}
        <div class="col-md-12">
            <p class="mt-3">
                <a href="https://drive.google.com/file/d/1sw2YJd-n8eJbm7R-IS5CUv_nx4bc5oDy/view?usp=sharing" target="_blank" class="text-info">
                    {{ __('Watch the video tutorial for setup steps') }}
                </a>
            </p>
        </div>

    </div>

</div>
{{-- End System Settings --}}