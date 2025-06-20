@extends('layouts.master')

@section('title') {{ __('terms_condition') }} @endsection

@section('content')

<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title">
      {{ __('terms_condition') }}
    </h3>
  </div>
  <div class="row grid-margin">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          
          <!-- Tabs Navigation -->
          <ul class="nav nav-tabs" id="termsConditionTabs" role="tablist">
            <!-- General Terms Tab -->
            <li class="nav-item" role="presentation">
              <a class="nav-link active" id="terms-condition-tab" data-bs-toggle="tab" href="#terms-condition" role="tab" aria-controls="terms-condition" aria-selected="true">{{ __('Terms Condition') }}</a>
            </li>
            <!-- Student Terms Tab -->
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="student-terms-condition-tab" data-bs-toggle="tab" href="#student-terms-condition" role="tab" aria-controls="student-terms-condition" aria-selected="false">{{ __('Student Terms Condition') }}</a>
            </li>
            <!-- Teacher Terms Tab -->
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="teacher-terms-condition-tab" data-bs-toggle="tab" href="#teacher-terms-condition" role="tab" aria-controls="teacher-terms-condition" aria-selected="false">{{ __('Teacher Terms Condition') }}</a>
            </li>
            <!-- Refund & Cancellation Tab -->
            <li class="nav-item" role="presentation">
              <a class="nav-link" id="refund-cancellation-tab" data-bs-toggle="tab" href="#refund-cancellation" role="tab" aria-controls="refund-cancellation" aria-selected="false">{{ __('Refund & Cancellation') }}</a>
            </li>
          </ul>

          <!-- Tabs Content -->
          <div class="tab-content" id="termsConditionTabsContent">
            
            <!-- Terms Condition Tab -->
            <div class="tab-pane fade show active py-4" id="terms-condition" role="tabpanel" aria-labelledby="terms-condition-tab">
              <div class="mt-3">
                <h5>{{ __('Terms Condition') }}</h5>
                <div class="mb-3 d-flex mt-4">
                  {{ __("Public URL") }} :&nbsp;&nbsp;<a href="{{ route('public.terms-conditions') }}" target="_blank">{{ route('public.terms-conditions') }}</a>
                </div>
                <form id="formdata" class="setting-form" action="{{ route('system-settings.update', 1) }}" method="POST" novalidate="novalidate">
                  @csrf
                  <div class="row">
                    <input type="hidden" name="name" id="name" value="terms_condition">
                    <label for="data"></label>
                    <div class="form-group col-md-12 col-sm-12">
                      <textarea id="tinymce_message" name="data" required placeholder="{{ __('terms_condition') }}">{{ $terms_condition_data ?? '' }}</textarea>
                    </div>
                  </div>
                  <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
                </form>
              </div>
            </div>

            <!-- Student Terms Condition Tab -->
            <div class="tab-pane fade py-4" id="student-terms-condition" role="tabpanel" aria-labelledby="student-terms-condition-tab">
              <div class="mt-3">
                <h5>{{ __('Student Terms Condition') }}</h5>
                <div class="mb-3 d-flex mt-4">
                  {{ __("Public URL") }} :&nbsp;&nbsp;<a href="{{ route('public.student-terms-conditions') }}" target="_blank">{{ route('public.student-terms-conditions') }}</a>
                </div>
                <form id="formdata" class="setting-form" action="{{ route('system-settings.update', 1) }}" method="POST" novalidate="novalidate">
                  @csrf
                  <div class="row">
                    <input type="hidden" name="name" id="name" value="student_terms_condition">
                    <label for="data"></label>
                    <div class="form-group col-md-12 col-sm-12">
                      <textarea id="tinymce_message" name="data" required placeholder="{{ __('student_terms_condition') }}">{{ $student_terms_condition_data ?? '' }}</textarea>
                    </div>
                  </div>
                  <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
                </form>
              </div>
            </div>

            <!-- Teacher Terms Condition Tab -->
            <div class="tab-pane fade py-4" id="teacher-terms-condition" role="tabpanel" aria-labelledby="teacher-terms-condition-tab">
              <div class="mt-3">
                <h5>{{ __('Teacher Terms Condition') }}</h5>
                <div class="mb-3 d-flex mt-4">
                  {{ __("Public URL") }} :&nbsp;&nbsp;<a href="{{ route('public.teacher-terms-conditions') }}" target="_blank">{{ route('public.teacher-terms-conditions') }}</a>
                </div>
                <form id="formdata" class="setting-form" action="{{ route('system-settings.update', 1) }}" method="POST" novalidate="novalidate">
                  @csrf
                  <div class="row">
                    <input type="hidden" name="name" id="name" value="teacher_terms_condition">
                    <label for="data"></label>
                    <div class="form-group col-md-12 col-sm-12">
                      <textarea id="tinymce_message" name="data" required placeholder="{{ __('teacher_terms_condition') }}">{{ $teacher_terms_condition_data ?? '' }}</textarea>
                    </div>
                  </div>
                  <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
                </form>
              </div>
            </div>

            <!-- refund-cancellation Tab -->
            <div class="tab-pane fade py-4" id="refund-cancellation" role="tabpanel" aria-labelledby="refund-cancellation-tab">
              <div class="mt-3">
                <h5>{{ __('Refund & Cancellation') }}</h5>
                <div class="mb-3 d-flex mt-4">
                  {{ __("Public URL") }} :&nbsp;&nbsp;<a href="{{ route('public.refund-cancellation') }}" target="_blank">{{ route('public.refund-cancellation') }}</a>
                </div>
                <form id="formdata" class="setting-form" action="{{ route('system-settings.update', 1) }}" method="POST" novalidate="novalidate">
                  @csrf
                  <div class="row">
                    <input type="hidden" name="name" id="name" value="refund_cancellation">
                    <label for="data"></label>
                    <div class="form-group col-md-12 col-sm-12">
                      <textarea id="tinymce_message" name="data" required placeholder="{{ __('refund_cancellation') }}">{{ $refund_cancellation_data ?? '' }}</textarea>
                    </div>
                  </div>
                  <input class="btn btn-theme float-right" type="submit" value="{{ __('submit') }}">
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
        $(document).ready(function() {

            $('#terms-condition-tab').on('click', function() {
                var policyName = 'terms_condition';
                var termsConditionTab = new bootstrap.Tab(document.getElementById('terms-condition-tab'));

                $('#name').val(policyName);

                termsConditionTab.show();
            });

            $('#student-terms-condition-tab').on('click', function() {
                var policyName = 'student_terms_condition';
                var studentTermsConditionTab = new bootstrap.Tab(document.getElementById('student-terms-condition-tab'));

                $('#name').val(policyName);
                
                studentTermsConditionTab.show();
            });

            $('#teacher-terms-condition-tab').on('click', function() {
                var policyName = 'teacher_terms_condition';
                var teacherTermsConditionTab = new bootstrap.Tab(document.getElementById('teacher-terms-condition-tab'));

                $('#name').val(policyName);
                teacherTermsConditionTab.show();
            });

            $('#refund-cancellation-tab').on('click', function() {
                var policyName = 'refund_cancellation';
                var refundCancellationTab = new bootstrap.Tab(document.getElementById('refund-cancellation-tab'));

                $('#name').val(policyName);
                refundCancellationTab.show();
            });

        });
    </script>
@endsection