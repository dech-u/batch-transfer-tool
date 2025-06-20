@extends('layouts.master')

@section('title')
    {{ __('privacy_policy') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('privacy_policy') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="privacyPolicyTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general"
                                    role="tab" aria-controls="general"
                                    aria-selected="true">{{ __('School Privacy Policy') }}</a>
                            </li>
                            
                           {{-- Terms & Conditions --}}

                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="terms-condition-tab" data-bs-toggle="tab" href="#terms-condition"
                                    role="tab" aria-controls="terms-condition"
                                    aria-selected="false">{{ __('Terms & Conditions') }}</a>
                            </li>

                            {{-- Refund  --}}
                         
                                
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="privacyPolicyTabsContent">
                            <div class="tab-pane fade show active py-4" id="general" role="tabpanel"
                                aria-labelledby="general-tab">
                                <div class="mt-3">
                                    <h5>{{ __('School Privacy Policy') }}</h5>
                                    <!-- Privacy Policy Form -->
                                    {{-- <div class="mb-3 d-flex mt-4">
                                        {{ __('Public URL') }} :&nbsp;&nbsp;<a
                                            href="{{ route('school-settings.get-privacy-policy', Auth::user()->school_id) }}"
                                            target="_blank">{{ route('school-settings.get-privacy-policy', Auth::user()->school_id) }}</a>
                                    </div> --}}

                                    <form id="formdata" class="setting-form"
                                        action="{{ route('school-settings.update', 1) }}" method="POST"
                                        novalidate="novalidate">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="name" id="name" value="privacy_policy">
                                            <label for="data"></label>
                                            <div class="form-group col-md-12 col-sm-12">
                                                <textarea id="tinymce_message" name="data" class="data_privacy_policy" required
                                                    placeholder="{{ __('privacy_policy') }}">{{ $privacy_policy_data }}</textarea>
                                            </div>
                                        </div>
                                        <input class="btn btn-theme float-right" type="submit"
                                            value="{{ __('submit') }}">
                                    </form>
                                </div>
                            </div>

                            {{-- <div class="tab-pane fade py-4" id="teacher-staff" role="tabpanel"
                                aria-labelledby="teacher-staff-tab">
                                <div class="mt-3">
                                    <h5>{{ __('Teacher/Staff Privacy Policy') }}</h5>
                                    <!-- Privacy Policy Form -->
                                    <div class="mb-3 d-flex mt-4">
                                        {{ __('Public URL') }} :&nbsp;&nbsp;<a
                                            href="{{ route('school-settings.privacy-policy', [Auth::user()->school_id]) }}"
                                            target="_blank">{{ route('school-settings.privacy-policy', [Auth::user()->school_id]) }}</a>
                                    </div>

                                    <form id="formdata" class="setting-form"
                                        action="{{ route('system-settings.update', 1) }}" method="POST"
                                        novalidate="novalidate">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="name" id="name"
                                                value="teacher_staff_privacy_policy">
                                            <label for="data"></label>
                                            <div class="form-group col-md-12 col-sm-12">
                                                <textarea id="tinymce_message" name="data" class="data_teacher_staff" required
                                                    placeholder="{{ __('teacher_staff_privacy_policy') }}">{{ $teacher_staff_privacy_policy_data }}</textarea>
                                            </div>
                                        </div>
                                        <input class="btn btn-theme float-right" type="submit"
                                            value="{{ __('submit') }}">
                                    </form>
                                </div>
                            </div> --}}

                            {{-- <div class="tab-pane fade py-4" id="student-parent" role="tabpanel"
                                aria-labelledby="student-parent-tab">
                                <div class="mt-3">
                                    <h5>{{ __('Student/Parent Privacy Policy') }}</h5>
                                    <!-- Privacy Policy Form -->
                                    <div class="mb-3 d-flex mt-4">
                                        {{ __('Public URL') }} :&nbsp;&nbsp;<a
                                            href="{{ route('public.school-student-parent-privacy-policy') }}"
                                            target="_blank">{{ route('public.school-student-parent-privacy-policy') }}</a>
                                    </div>

                                    <form id="formdata" class="setting-form"
                                        action="{{ route('system-settings.update', 1) }}" method="POST"
                                        novalidate="novalidate">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="name" id="name"
                                                value="student_parent_privacy_policy">
                                            <label for="data"></label>
                                            <div class="form-group col-md-12 col-sm-12">
                                                <textarea class="data" id="tinymce_message" name="data" class="data_student_parent" required
                                                    placeholder="{{ __('student_parent_privacy_policy') }}">{{ $student_parent_privacy_policy_data }}</textarea>
                                            </div>
                                        </div>
                                        <input class="btn btn-theme float-right" type="submit"
                                            value="{{ __('submit') }}">
                                    </form>
                                </div>
                            </div> --}}
                        </div>
                        <!-- End Tab Content -->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            $('#general-tab').on('click', function() {
                var policyName = 'privacy_policy';
                var generalTab = new bootstrap.Tab(document.getElementById('general-tab'));

                $('#name').val(policyName);

                generalTab.show();
            });

            $('#teacher-staff-tab').on('click', function() {
                var policyName = 'teacher_staff_privacy_policy';
                var teacherStaffTab = new bootstrap.Tab(document.getElementById('teacher-staff-tab'));

                $('#name').val(policyName);
                
                teacherStaffTab.show();
            });

            $('#student-parent-tab').on('click', function() {
                var policyName = 'student_parent_privacy_policy';
                var studentParentTab = new bootstrap.Tab(document.getElementById('student-parent-tab'));

                $('#name').val(policyName);
                studentParentTab.show();
            });

        });

    </script>
@endsection
