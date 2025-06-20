<div class="form-group">
    <label>{{ __('template') }} <span class="text-danger">*</span></label>
    <div class="col-12 d-flex row">
        <div class="form-check form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input email-template" checked name="template" id="email-template" value="school-email-template" required="required">
                {{ __('school_register_email_template') }}
            </label>
        </div>

        <div class="form-check form-check-inline">
            <label class="form-check-label">
                <input type="radio" class="form-check-input email-template" name="template" id="email-template" value="school-reject-template" required="required">
                {{ __('school_application_reject_email_template') }}
            </label>
        </div>
    </div>
</div>

<div class="row school-email-template">
    <div class="form-group col-md-12 col-sm-12">
        <textarea id="tinymce_message" name="email_template_school_registration" required placeholder="{{ __('email_template') }}">{{ htmlspecialchars_decode($settings['email_template_school_registration'] ?? '') }}</textarea>
    </div>  
    <div class="form-group col-sm-12 col-md-12">
        <a data-value="{school_admin_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_admin_name') }} }</a>
        <a data-value="{code}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('code') }} }</a>
        <a data-value="{email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('email') }} }</a>
        <a data-value="{password}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('password') }} }</a>
        <a data-value="{school_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_name') }} }</a>
    </div>
    

    <div class="form-group col-sm-12 col-md-12">
        <hr>
        <a data-value="{super_admin_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('super_admin_name') }} }</a>
        <a data-value="{support_email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('support_email') }} }</a>
        <a data-value="{contact}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('contact') }} }</a>
        <a data-value="{system_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('system_name') }} }</a>
        <a data-value="{url}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('url') }} }</a>
    </div>
</div>

<div class="row school-reject-template">
    <div class="form-group col-md-12 col-sm-12">
        <textarea id="tinymce_message" name="school_reject_template" required placeholder="{{ __('email_template') }}">{{ htmlspecialchars_decode($settings['school_reject_template'] ?? '') }}</textarea>
    </div>

    <div class="form-group col-sm-12 col-md-12">
        <a data-value="{school_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('school_name') }} }</a>
        <a data-value="{super_admin_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('super_admin_name') }} }</a>
        <a data-value="{support_email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('support_email') }} }</a>
        <a data-value="{contact}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('contact') }} }</a>
        <a data-value="{system_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('system_name') }} }</a>
        <a data-value="{url}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('url') }} }</a>
    </div>        
</div>
