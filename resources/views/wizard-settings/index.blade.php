@extends('layouts.master')

@section('title')
    {{ __('wizard_settings') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('wizard_settings') }}</h3>
                    </div>
                    <div class="card-body">
                        <div role="application" class="wizard" id="steps-uid-0">
                            {{-- Steps --}}
                            <div class="steps clearfix">
                                <ul role="tablist">
                                    <li role="tab" class="first" aria-disabled="false" aria-selected="true">
                                        <a id="steps-1"  class="system_settings_wizard_checkMark {{ $settings['system_settings_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-1" aria-controls="steps-1">
                                            <span class="number">1.</span> {{ __('system_settings') }}
                                        </a>
                                    </li>
                                    <li role="tab" class="disabled" aria-disabled="true">
                                        <a id="steps-2" class="notification_settings_wizard_checkMark {{  $settings['notification_settings_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-2" aria-controls="steps-2">
                                            <span class="number">2.</span> {{ __('notification_settings') }}
                                        </a>
                                    </li>
                                    <li role="tab" class="disabled" aria-disabled="true">
                                        <a id="steps-3" class="email_settings_wizard_checkMark {{  $settings['email_settings_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-3" aria-controls="steps-3">
                                            <span class="number">3.</span> {{ __('email_settings') }}
                                        </a>
                                    </li>
                                    <li role="tab" class="disabled" aria-disabled="true">
                                        <a id="steps-3" class="verify_email_wizard_checkMark {{  $settings['verify_email_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-3" aria-controls="steps-3">
                                            <span class="number">4.</span> {{ __('verify_email') }}
                                        </a>
                                    </li>
                                    <li role="tab" class="disabled" aria-disabled="true">
                                        <a id="steps-4" class="email_template_settings_wizard_checkMark {{  $settings['email_template_settings_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-4" aria-controls="steps-4">
                                            <span class="number">5.</span> {{ __('email_template_settings') }}
                                        </a>
                                    </li>
                                    <li role="tab" class="disabled" aria-disabled="true">
                                        <a id="steps-5" class="payment_settings_wizard_checkMark {{  $settings['payment_settings_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-5" aria-controls="steps-5">
                                            <span class="number">6.</span> {{ __('payment_settings') }}
                                        </a>
                                    </li>
                                    <li role="tab" class="disabled" aria-disabled="true">
                                        <a id="steps-6" class="third_party_api_settings_wizard_checkMark {{ $settings['third_party_api_settings_wizard_checkMark'] == 1 ? 'bg-success' : 'bg-danger' }}" href="#steps-6" aria-controls="steps-6">
                                            <span class="number">7.</span> {{ __('third_party_api_settings') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            {{-- Content --}}
                            <div class="clearfix">
                                <!-- Step 1: System Settings -->
                                <div id="step-1" class="step-form active">
                                    <form id="formdata" class="create-form-without-reset" action="{{ route('system-settings.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                        @csrf
                                        @include('settings.forms.system-settings-form')
                                        <input class="btn btn-theme float-right ml-3 btn-next" id="next-btn-system" type="submit" value="{{ __('Next') }}">
                                    </form>
                                </div>

                                <!-- Step 2: Notification Settings -->
                                <div id="step-2" class="step-form">
                                    <form id="formdata" class="edit-form" action="{{route('notification-setting.update')}}" method="POST" novalidate="novalidate">
                                        @csrf
                                        @include('settings.forms.fcm-form')
                                        <input class="btn btn-theme float-right ml-3 btn-next" type="submit" value="{{ __('Next') }}">
                                        <input class="btn btn-theme float-right ml-3 btn-skip" id="skip-btn-email-template" type="button" value="{{ __('Skip') }}">
                                        <input class="btn btn-secondary float-right btn-previous" type="button" id="previous-btn-notification" value="{{ __('Previous') }}">
                                    </form>
                                </div>

                                <!-- Step 3: Email Settings -->
                                <div id="step-3" class="step-form">
                                    <form id="verify_email" action="{{route('system-settings.email.update')}}" method="POST" novalidate="novalidate">
                                        @csrf
                                        @include('settings.forms.email-form')
                                        <input class="btn btn-theme float-right ml-3 btn-next" id="next-btn-email" type="button" value="{{ __('Next') }}">
                                        <input class="btn btn-secondary float-right btn-previous" type="button" id="previous-btn-email" value="{{ __('Previous') }}">
                                    </form>
                                </div>

                                <!-- Step 4: Verify Email -->
                                <div id="step-4" class="step-form">
                                    <form id="send_verification_email" action="{{route('system-settings.email.verify')}}" method="POST">
                                        @csrf
                                            <div class="form-group col-md-4 col-sm-12">
                                                <label for="verify_email_address">{{__('email')}}</label>
                                                <input name="verify_email" id="verify_email_address" type="email" required placeholder="{{__('email')}}" class="form-control"/>
                                            </div>

                                            <input class="btn btn-theme float-right ml-3 btn-next" id="next-btn-email-template" type="button" value="{{ __('Next') }}">
                                            <input class="btn btn-secondary float-right btn-previous" type="button" id="previous-btn-email-template" value="{{ __('Previous') }}">
            
                                            <div id="error-div" style="display: none;" class="col-12">
                                                <h6>Error : </h6>
                                                <pre id="error"></pre>
                                                <h6>Stacktrace : </h6>
                                                <pre id="stacktrace"></pre>
                                            </div>
            
                                    </form>
                                </div>

                                <!-- Step 5: Email Template Settings -->
                                <div id="step-5" class="step-form">
                                    <form id="formdata" class="email-template-setting-form" action="{{ route('system-settings.email-template.update', 1) }}" method="PUT" novalidate="novalidate">
                                        @csrf
                                        @include('settings.forms.email-template-form')
                                        <input class="btn btn-theme float-right ml-3 btn-next" id="next-btn-email-template" type="button" value="{{ __('Next') }}">
                                        <input class="btn btn-theme float-right ml-3 btn-skip" id="skip-btn-email-template" type="button" value="{{ __('Skip') }}">
                                        <input class="btn btn-secondary float-right btn-previous" type="button" id="previous-btn-email-template" value="{{ __('Previous') }}">
                                    </form>
                                </div>

                                <!-- Step 6: Payment Settings -->
                                <div id="step-6" class="step-form">
                                    <form class="create-form-without-reset" action="{{ route('system-settings.payment.update') }}"
                                    method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                        @csrf
                                        
                                        @include('settings.forms.payment-form')
                                        <input class="btn btn-theme float-right ml-3 btn-next" id="next-btn-payment" type="button" value="{{ __('Next') }}">
                                        <input class="btn btn-theme float-right ml-3 btn-skip" id="skip-btn-email-template" type="button" value="{{ __('Skip') }}">
                                        <input class="btn btn-secondary float-right btn-previous" type="button" id="previous-btn-payment" value="{{ __('Previous') }}">
                                    </form>
                                </div>

                                <!-- Step 7: Third Party API Settings -->
                                <div id="step-7" class="step-form">
                                    <form id="formdata" class="create-form-without-reset" action="{{ route('system-settings.third-party.update') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                                        @csrf
                                        @include('settings.forms.third-party-apis-form')
                                        <input class="btn btn-theme float-right ml-3 btn-next btn-finish" id="next-btn-third-party-api" type="button" value="{{ __('Finish') }}">
                                        <input class="btn btn-secondary float-right btn-previous" type="button" id="previous-btn-third-party-api" value="{{ __('Previous') }}">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    window.onload = setTimeout(() => {
        $('.email-template').trigger('change');
    }, 500);
    
    $('.email-template').change(function (e) { 
        e.preventDefault();
        let type = $('input[name="template"]:checked').val();

        if (type == 'school-email-template') {
            $('.school-email-template').show(500);
            $('.school-reject-template').hide(500);
            // Remove required attribute from hidden form
            $('.school-reject-template :input').prop('required', false);
            // Add required attribute to visible form
            $('.school-email-template :input[data-required]').prop('required', true);
        } else if(type == 'school-reject-template') {
            $('.school-reject-template').show(500);
            $('.school-email-template').hide(500);
            // Remove required attribute from hidden form
            $('.school-email-template :input').prop('required', false);
            // Add required attribute to visible form
            $('.school-reject-template :input[data-required]').prop('required', true);
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Cache selectors
        const $nextButtons = $('.btn-next');
        const $prevButtons = $('.btn-previous');
        const $skipButtons = $('.btn-skip');
        const $finishButtons = $('.btn-finish');
        const $forms = $('.step-form');
        const $tabs = $('.steps ul li');
        
        // Get current step from PHP
        let currentFormIndex = {{ $currentStep ?? 0 }};
        
        // Get completed steps from database
        const completedSteps = {
            'system_settings_wizard_checkMark': {{ $settings['system_settings_wizard_checkMark'] ?? 0 }},
            'notification_settings_wizard_checkMark': {{ $settings['notification_settings_wizard_checkMark'] ?? 0 }},
            'email_settings_wizard_checkMark': {{ $settings['email_settings_wizard_checkMark'] ?? 0 }},
            'verify_email_wizard_checkMark': {{ $settings['verify_email_wizard_checkMark'] ?? 0 }},
            'email_template_settings_wizard_checkMark': {{ $settings['email_template_settings_wizard_checkMark'] ?? 0 }},
            'payment_settings_wizard_checkMark': {{ $settings['payment_settings_wizard_checkMark'] ?? 0 }},
            'third_party_api_settings_wizard_checkMark': {{ $settings['third_party_api_settings_wizard_checkMark'] ?? 0 }}
        };
        
        // Step names mapping
        const stepNames = [
            'system_settings_wizard_checkMark',
            'notification_settings_wizard_checkMark',
            'email_settings_wizard_checkMark',
            'verify_email_wizard_checkMark',
            'email_template_settings_wizard_checkMark',
            'payment_settings_wizard_checkMark',
            'third_party_api_settings_wizard_checkMark'
        ];

        // Initialize wizard
        initializeWizard();

        function initializeWizard() {
            $forms.hide();
            showStep(currentFormIndex);
            updateTabStates();
            
            // Convert all next buttons to type="button"
            $nextButtons.attr('type', 'button');
            
            // Initialize step states based on database values
            stepNames.forEach((stepName, index) => {
                const $tab = $($tabs[index]);
                const $link = $tab.find('a');
                
                if (completedSteps[stepName] === 1) {
                    $link.addClass('bg-success').removeClass('bg-danger');
                } else {
                    $link.addClass('bg-danger').removeClass('bg-success');
                }
            });

            // Initialize form validation states
            $forms.each(function() {
                const $form = $(this).find('form');
                if ($form.length) {
                    // Store required fields data attribute
                    $form.find(':input[required]').attr('data-required', 'true');
                }
            });

            // Trigger initial template change
            $('.email-template:checked').trigger('change');
        }

        function validateForm($form) {
            let isValid = true;
            
            // Only validate visible inputs
            $form.find(':input:visible').each(function() {
                const $input = $(this);
                if ($input.prop('required') && !$input.val()) {
                    isValid = false;
                    $input.addClass('is-invalid');
                } else {
                    $input.removeClass('is-invalid');
                }
            });
            
            return isValid;
        }

        function handleStepCompletion(stepIndex, formData) {
            const stepName = stepNames[stepIndex];
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/update-wizard-session',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    name: stepName
                },
                success: function(response) {
                    // Update completed steps tracking
                    completedSteps[stepName] = 1;
                    $($tabs[stepIndex]).find('a').addClass('bg-success').removeClass('bg-danger');
                    
                    if (formData) {
                        const $form = $($forms[stepIndex]).find('form');
                        $.ajax({
                            url: $form.attr('action'),
                            method: $form.attr('method'),
                            data: formData,
                            success: function() {
                                const nextStep = findNextIncompleteStep();
                                currentFormIndex = nextStep;
                                showStep(currentFormIndex);
                            },
                            error: function(error) {
                                console.error('Error submitting form:', error);
                                // Show error message to user
                                alert('Error saving form data. Please try again.');
                            }
                        });
                    } else {
                        const nextStep = findNextIncompleteStep();
                        currentFormIndex = nextStep;
                        showStep(currentFormIndex);
                    }
                },
                error: function(error) {
                    console.error('Error updating step:', error);
                    alert('Error updating step. Please try again.');
                }
            });
        }

        function showStep(index) {
            $forms.hide();
            $($forms[index]).show();
            updateTabStates();
        }

        function updateTabStates() {
            $tabs.each(function(i) {
                const $tab = $(this);
                const $link = $tab.find('a');
                const stepName = stepNames[i];
                
                if (i < currentFormIndex) {
                    // Previous steps - keep their completed/incomplete state
                    $tab.removeClass('disabled').attr('aria-disabled', 'false');
                } else if (i === currentFormIndex) {
                    // Current step
                    $tab.removeClass('disabled').attr('aria-disabled', 'false');
                    $link.removeClass('bg-success bg-danger');
                } else {
                    // Future steps - keep their completed/incomplete state
                    $tab.addClass('disabled').attr('aria-disabled', 'true');
                }
            });
        }

        function findNextIncompleteStep() {
            for (let i = currentFormIndex + 1; i < stepNames.length; i++) {
                const stepName = stepNames[i];
                if (completedSteps[stepName] === 0) {
                    return i;
                }
            }
            // If no incomplete steps found, move to next step
            return Math.min(currentFormIndex + 1, stepNames.length - 1);
        }

        // Next button click handler
        $nextButtons.on('click', function(e) {
            e.preventDefault();
            const $currentForm = $($forms[currentFormIndex]).find('form');
            
            if ($currentForm.length) {
                if (validateForm($currentForm)) {
                    const formData = $currentForm.serialize();
                    handleStepCompletion(currentFormIndex, formData);
                }
            } else {
                handleStepCompletion(currentFormIndex);
            }
        });

        // Skip button click handler
        $skipButtons.on('click', function(e) {
            e.preventDefault();
            // Keep current step as incomplete
            completedSteps[stepNames[currentFormIndex]] = 0;
            $($tabs[currentFormIndex]).find('a').removeClass('bg-success').addClass('bg-danger');
            
            const nextIncomplete = findNextIncompleteStep();
            currentFormIndex = nextIncomplete;
            showStep(currentFormIndex);
        });

        // Previous button click handler
        $prevButtons.on('click', function(e) {
            e.preventDefault();
            if (currentFormIndex > 0) {
                currentFormIndex--;
                showStep(currentFormIndex);
            }
        });

        // Finish button click handler
        $finishButtons.on('click', function(e) {
            e.preventDefault();
            window.location.href = '/dashboard';
        });
    });
</script>
@endsection