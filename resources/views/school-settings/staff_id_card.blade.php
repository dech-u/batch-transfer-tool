<div class="row">

    {{-- Colour --}}
    <div class="form-group col-sm-12 col-md-4">
        <label for="header_color">{{ __('header_color') }} <span class="text-danger">*</span></label>
        <input name="staff_header_color" id="header_color" value="{{ $settings['staff_header_color'] ?? '' }}" type="text" required
            placeholder="{{ __('color') }}" class="color-picker" />
    </div>
    <div class="form-group col-sm-12 col-md-4">
        <label for="footer_color">{{ __('footer_color') }} <span class="text-danger">*</span></label>
        <input name="staff_footer_color" id="footer_color" value="{{ $settings['staff_footer_color'] ?? '' }}" type="text"
            required placeholder="{{ __('color') }}" class="color-picker" />
    </div>
    <div class="form-group col-sm-12 col-md-4">
        <label for="header_footer_color">{{ __('header_footer_text_color') }} <span class="text-danger">*</span></label>
        <input name="staff_header_footer_text_color" id="header_footer_text_color"
            value="{{ $settings['staff_header_footer_text_color'] ?? '' }}" type="text" required
            placeholder="{{ __('color') }}" class="color-picker" />
    </div>
    {{-- End Colour --}}

    {{-- Layout Type --}}
    <div class="form-group col-sm-12 col-md-4">
        <label>{{ __('layout_type') }} <span class="text-danger">*</span></label>
        <div class="col-12 d-flex row">
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" @if (isset($settings['staff_layout_type']) && $settings['staff_layout_type'] == 'vertical') checked @endif
                        name="staff_layout_type" id="layout_type" value="vertical" required>
                    {{ __('vertical') }}
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" @if (isset($settings['staff_layout_type']) && $settings['staff_layout_type'] == 'horizontal') checked @endif
                        name="staff_layout_type" id="layout_type" value="horizontal" required>
                    {{ __('horizontal') }}
                </label>
            </div>
        </div>
    </div>
    {{-- End Layout Type --}}

    {{-- Profile Image Style --}}
    <div class="form-group col-sm-12 col-md-4">
        <label>{{ __('profile_image_style') }} <span class="text-danger">*</span></label>
        <div class="col-12 d-flex row">
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" @if (isset($settings['staff_profile_image_style']) && $settings['staff_profile_image_style'] == 'round') checked @endif
                        name="staff_profile_image_style" id="profile_image_style" value="round" required>
                    {{ __('round') }}
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" @if (isset($settings['staff_profile_image_style']) && $settings['staff_profile_image_style'] == 'squre') checked @endif
                        name="staff_profile_image_style" id="profile_image_style" value="squre" required>
                    {{ __('squre') }}
                </label>
            </div>
        </div>
    </div>
    {{-- End Profile Image Style --}}

    {{-- Background Image --}}
    <div class="form-group col-sm-12 col-md-6">
        <label for="image">{{ __('background_image') }} </label>
        <input type="file" name="staff_background_image" accept="image/jpg,image/png,image/jpeg,image/svg"
            class="file-upload-default" />
        <div class="input-group col-xs-12">
            <input type="text" id="image" class="form-control file-upload-info" disabled=""
                placeholder="{{ __('image') }}" />
            <span class="input-group-append">
                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
            </span>
        </div>
        @if ($settings['staff_background_image'] ?? '')
            <div id="background">
                <img src="{{ $settings['staff_background_image'] }}" class="img-fluid w-25" alt="">

                <div class="mt-2">
                    <a href="" data-type="staff_background"
                        class="btn btn-inverse-danger btn-sm id-card-settings">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
    {{-- End Background Image --}}

    {{-- Fields --}}
    <div class="form-group col-sm-12 col-md-12">
        <label for="">{{ __('select_fields') }} <span class="text-danger">*</span></label>
    </div>

    <div class="form-group col-sm-12 col-md-3">
        <input id="staff_name" class="feature-checkbox" @if (in_array('name', $settings['staff_id_card_fields'])) checked @endif
            type="checkbox" name="staff_id_card_fields[]" value="name" />
        <label class="feature-list text-center" for="staff_name">{{ __('name') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="role" class="feature-checkbox" @if (in_array('role', $settings['staff_id_card_fields'])) checked @endif
            type="checkbox" name="staff_id_card_fields[]" value="role" />
        <label class="feature-list text-center" for="role">{{ __('role') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="contact" class="feature-checkbox" type="checkbox"
            @if (in_array('contact', $settings['staff_id_card_fields'])) checked @endif name="staff_id_card_fields[]" value="contact" />
        <label class="feature-list text-center" for="contact">{{ __('contact') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="email" class="feature-checkbox" type="checkbox"
            @if (in_array('email', $settings['staff_id_card_fields'])) checked @endif name="staff_id_card_fields[]" value="email" />
        <label class="feature-list text-center" for="email">{{ __('email') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="qualification" class="feature-checkbox" type="checkbox"
            @if (in_array('qualification', $settings['staff_id_card_fields'])) checked @endif name="staff_id_card_fields[]" value="qualification" />
        <label class="feature-list text-center" for="qualification">{{ __('qualification') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="staff_dob" class="feature-checkbox" type="checkbox"
            @if (in_array('dob', $settings['staff_id_card_fields'])) checked @endif name="staff_id_card_fields[]" value="dob" />
        <label class="feature-list text-center" for="staff_dob">{{ __('dob') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="staff_gender" class="feature-checkbox" type="checkbox"
            @if (in_array('gender', $settings['staff_id_card_fields'])) checked @endif name="staff_id_card_fields[]" value="gender" />
        <label class="feature-list text-center" for="staff_gender">{{ __('gender') }}</label>
    </div>
    <div class="form-group col-sm-12 col-md-3">
        <input id="staff_session_year" class="feature-checkbox" type="checkbox"
            @if (in_array('session_year', $settings['staff_id_card_fields'])) checked @endif name="staff_id_card_fields[]" value="session_year" />
        <label class="feature-list text-center" for="staff_session_year">{{ __('session_year') }}</label>
    </div>
    {{-- End Fields --}}

    {{-- Extra form fields --}}
    @foreach ($formFields as $field)
        @if ($field->user_type == 2) <!-- 2 => Staff -->
            <div class="form-group col-sm-12 col-md-3">
                <input id="{{ $field->id }}" class="feature-checkbox" @if ($field->display_on_id) checked @endif
                    type="checkbox" name="extra_form_fields[]" value="{{ $field->id }}" />
                <label class="feature-list text-center" for="{{ $field->id }}">{{ $field->name }}</label>
            </div>
        @endif
    @endforeach
    {{-- End Fields --}}

    <div class="form-group col-sm-12 col-md-12">

    </div>

    {{-- Page Size --}}
    <div class="form-group col-sm-12 col-md-4">
        <label for="">{{ __('page_width') }} ({{ __('mm') }})<span class="text-danger">*</span></label>
        <input name="staff_page_width" id="page_width" value="{{ $settings['staff_page_width'] ?? '' }}" type="number"
            required placeholder="{{ __('page_width') }}" class="form-control" />
    </div>

    <div class="form-group col-sm-12 col-md-4">
        <label for="">{{ __('page_height') }} ({{ __('mm') }})<span
                class="text-danger">*</span></label>
        <input name="staff_page_height" id="page_height" value="{{ $settings['staff_page_height'] ?? '' }}" type="number"
            required placeholder="{{ __('page_height') }}" class="form-control" />
    </div>
    {{-- End Page Size --}}
</div>
