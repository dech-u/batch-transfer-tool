<div id="student_tags">
    <a data-value="{full_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('full_name') }} }</a>
    <a data-value="{first_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('first_name') }} }</a>
    <a data-value="{last_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('last_name') }} }</a>
    <a data-value="{class_section}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('class_section') }} }</a>
    <a data-value="{student_mobile}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('student_mobile') }} }</a>
    <a data-value="{dob}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('dob') }} }</a>
    <a data-value="{roll_no}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('roll_no') }} }</a>
    <a data-value="{admission_no}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('admission_no') }} }</a>
    <a data-value="{current_address}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('current_address') }} }</a>
    <a data-value="{permanent_address}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('permanent_address') }} }</a>
    <a data-value="{gender}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('gender') }} }</a>
    <a data-value="{admission_date}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('admission_date') }} }</a>
    <a data-value="{guardian_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('guardian_name') }} }</a>
    <a data-value="{guardian_mobile}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('guardian_mobile') }} }</a>
    <a data-value="{guardian_email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('guardian_email') }} }</a>
    <a data-value="{exam}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('exam') }} }</a>
    <a data-value="{total_marks}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('total_marks') }} }</a>
    <a data-value="{obtain_marks}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('obtain_marks') }} }</a>
    <a data-value="{grade}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('grade') }} }</a>
    <a data-value="{session_year}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('session_year') }} }</a>

    @foreach ($formFields as $formField)
        @if ($formField->user_type == 1) <!-- 1 => Student -->
            <a data-value="{{ '{'.$formField->name.'}' }}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __($formField->name) }} }</a>
        @endif
    @endforeach
</div>
<div id="staff_tags">
    <a data-value="{full_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('full_name') }} }</a>
    <a data-value="{first_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('first_name') }} }</a>
    <a data-value="{last_name}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('last_name') }} }</a>
    <a data-value="{gender}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('gender') }} }</a>
    <a data-value="{joining_date}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('joining_date') }} }</a>
    <a data-value="{role}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('role') }} }</a>
    <a data-value="{qualification}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('qualification') }} }</a>
    <a data-value="{dob}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('dob') }} }</a>
    <a data-value="{email}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('email') }} }</a>
    <a data-value="{mobile}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('mobile') }} }</a>
    <a data-value="{current_address}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('current_address') }} }</a>
    <a data-value="{permanent_address}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('permanent_address') }} }</a>
    <a data-value="{experience}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('experience') }} }</a>
    <a data-value="{session_year}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __('session_year') }} }</a>
    
    @foreach ($formFields as $formField)
        @if ($formField->user_type == 2) <!-- 2 => Staff -->
            <a data-value="{{ '{'.$formField->name.'}' }}" class="btn btn-gradient-light btn_tag mt-2">{ {{ __($formField->name) }} }</a>
        @endif
    @endforeach
</div>