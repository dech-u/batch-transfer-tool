<!-- partial:../../partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">

    <div class="sidebar-search pl-4 pr-4">
        <input type="text" id="menu-search" placeholder="{{ __('search_menu') }}" class="form-control menu-search border-theme form-control-sm">
    </div>

    <div class="sidebar-search pl-4 pr-4 mt-2">
        <input type="text" id="menu-search-mini" placeholder="{{ __('search_menu') }}" class="form-control d-lg-none border-theme">
    </div>

    <ul class="nav">
        {{-- dashboard --}}
        <li class="nav-item">
            <a href="{{ url('/dashboard') }}" class="nav-link">
                <i class="fa fa-home menu-icon"></i>
                <span class="menu-title">{{ __('dashboard') }}</span>
            </a>
        </li>
        {{-- Academics --}}
        @canany(['medium-create','section-create','subject-create','class-create','subject-create','promote-student-create','transfer-student-create'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#academics-menu" aria-expanded="false" aria-controls="academics-menu">
                    <i class="fa fa-university menu-icon"></i>
                    <span class="menu-title">{{ __('academics') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="academics-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('medium-create')
                            <li class="nav-item"><a href="{{ route('mediums.index') }}" class="nav-link"> {{ __('medium') }} </a></li>
                        @endcan

                        @can('section-create')
                            <li class="nav-item"><a href="{{ route('section.index') }}" class="nav-link"> {{ __('section') }} </a></li>
                        @endcan

                        @can('subject-create')
                            <li class="nav-item"><a href="{{ route('subjects.index') }}" class="nav-link"> {{ __('subject') }} </a></li>
                        @endcan

                        @can('semester-create')
                            <li class="nav-item"><a href="{{ route('semester.index') }}" class="nav-link"> {{ __('Semester') }} </a></li>
                        @endcan

                        @can('stream-create')
                            <li class="nav-item"><a class="nav-link" href="{{ route('stream.index') }}"> {{ __('Stream') }} </a></li>
                        @endcan

                        @can('shift-create')
                            <li class="nav-item"><a class="nav-link" href="{{ route('shift.index') }}"> {{ __('Shift') }} </a></li>
                        @endcan

                        @can('class-create')
                            <li class="nav-item"><a href="{{ route('class.index') }}" class="nav-link"> {{ __('Class') }} </a></li>
                            <li class="nav-item"><a href="{{ route('class.subject.index') }}" class="nav-link"> {{ __('Class Subject') }} </a></li>
                        @endcan

                        @can('class-group-create')
                            <li class="nav-item"><a href="{{ route('class-group.index') }}" class="nav-link"> {{ __('class_group') }} </a></li>
                        @endcan 
                        


                        @can('class-section-create')
                            <li class="nav-item"><a href="{{ route('class-section.index') }}" class="nav-link">{{ __('Class Section & Teachers') }} </a></li>
                        @endcan

                        @canany('promote-student-create','transfer-student-create')
                            <li class="nav-item"><a href="{{ route('promote-student.index') }}" class="nav-link text-wrap">{{ __('Transfer & Promote Students') }}</a></li>
                        @endcan

                        @can('student-create')
                            <li class="nav-item"><a href="{{ route('students.roll-number.index') }}" class="nav-link">{{ __('assign') }} {{ __('roll_no') }}</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- Custom Form Fields --}}
        @role('School Admin')
            @canany(['form-fields-list', 'form-fields-create', 'form-fields-edit', 'form-fields-delete'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('form-fields.index') }}">
                        <i class="fa fa-list-alt menu-icon"></i>
                        <span class="menu-title"> {{ __('custom_fields') }} </span>
                    </a>
                </li>
            @endcan
        @endrole

        {{-- Class Section For Teacher --}}
        @role('Teacher')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('class-section.index') }}">
                <i class="fa fa-university menu-icon"></i>
                <span class="menu-title"> {{ __('Class Section') }} </span>
            </a>
        </li>
        @endrole

        {{-- student --}}
        @canany(['student-create', 'student-list', 'student-reset-password', 'class-teacher','form-fields-list', 'form-fields-create', 'form-fields-edit', 'form-fields-delete','guardian-create'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#student-menu" aria-expanded="false" aria-controls="academics-menu">
                    <i class="fa fa-graduation-cap menu-icon"></i>
                    <span class="menu-title">{{ __('students') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="student-menu">
                    <ul class="nav flex-column sub-menu">
                        {{-- Student Addmission Form Manage --}}
                        {{-- @canany(['form-fields-list', 'form-fields-create', 'form-fields-edit', 'form-fields-delete'])
                            <li class="nav-item">
                                <a href="{{ route('form-fields.index') }}" class="nav-link">{{ __('admission_form_fields') }}</i></a>
                            </li>
                        @endcan --}}
                        @can('student-create')
                            <li class="nav-item"><a href="{{ route('students.create') }}" class="nav-link">{{ __('student_admission') }}</a></li>
                        @endcan
                        @can('student-create')
                            <li class="nav-item"><a href="{{ route('online-registration.index') }}" class="nav-link" data-access="@hasFeatureAccess('Website Management')">{{ __('admission_inquiries') }}</a></li>
                        @endcan
                        @canany(['student-list', 'class-teacher'])
                            <li class="nav-item"><a href="{{ route('students.index') }}" class="nav-link">{{ __('student_details') }}</a></li>
                        @endcanany

                        @can('student-reset-password')
                            <li class="nav-item"><a href="{{ route('students.reset-password.index') }}" class="nav-link">{{ __('students') . ' ' . __('reset_password') }}</a></li>
                        @endcan
                        
                        @can('student-create')
                            <li class="nav-item"><a href="{{ route('students.create-bulk-data') }}" class="nav-link">{{ __('add_bulk_data') }}</a></li>
                        @endcan

                        @can('student-edit')
                            <li class="nav-item"><a href="{{ route('students.upload-profile') }}" class="nav-link">{{ __('upload_profile_images') }}</a></li>
                        @endcan

                        {{-- parents --}}
                        @can('guardian-create')
                            <li class="nav-item">
                                <a href="{{ route('guardian.index') }}" class="nav-link"> {{ __('Guardian') }} </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- teacher --}}
        @can('teacher-create')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#teacher-menu" aria-expanded="false" aria-controls="academics-menu">
                    <i class="fa fa-user menu-icon"></i>
                    <span class="menu-title">{{ __('teacher') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="teacher-menu">
                    <ul class="nav flex-column sub-menu">
                        {{-- Teacher Registration --}}
                        <li class="nav-item">
                            <a href="{{ route('teachers.index') }}" class="nav-link">
                                <span class="menu-title">{{ __('manage_teacher') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teachers.create-bulk-upload') }}" class="nav-link">
                                <span class="menu-title">{{ __('bulk upload') }}</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>
        @endcan



        {{-- timetable --}}
        @if(Auth::user()->hasRole('Teacher'))
            <li class="nav-item">
                <a href="{{ route('timetable.teacher.show', Auth::user()->id) }}" class="nav-link" data-access="@hasFeatureAccess('Timetable Management')">
                    <i class="fa fa-calendar menu-icon"></i>
                    <span class="menu-title">{{ __('timetable') }}</span>
                </a>
            </li>
        @else
            @canany(['timetable-create', 'timetable-list'])
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#timetable-menu" aria-expanded="false" aria-controls="timetable-menu" data-access="@hasFeatureAccess('Timetable Management')">
                        <i class="fa fa-calendar menu-icon"></i>
                        <span class="menu-title">{{ __('timetable') }}</span>
                        <i class="menu-arrow"></i>
                    </a>

                    <div class="collapse" id="timetable-menu">
                        <ul class="nav flex-column sub-menu">
                            @can('timetable-create')
                                <li class="nav-item">
                                    <a href="{{ route('timetable.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Timetable Management')">{{ __('create_timetable') }} </a>
                                </li>
                            @endcan

                            @can('timetable-list')
                                <li class="nav-item">
                                    <a href="{{ route('timetable.teacher.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Timetable Management')">
                                        {{ __('teacher_timetable') }}
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcanany
        @endif

        {{-- Holiday --}}
        @canany(['holiday-create', 'holiday-list'])
            <li class="nav-item">
                @can('holiday-list')
                    <a href="{{ route('holiday.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Holiday Management')">
                        <i class="fa fa-calendar-check-o menu-icon"></i>
                        <span class="menu-title">{{ __('holiday_list') }}</span>
                    </a>
                @endcan
            </li>
        @endcanany
        {{-- subject lesson --}}
        @canany(['lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete', 'topic-list', 'topic-create',
            'topic-edit', 'topic-delete'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#subject-lesson-menu" aria-expanded="false" aria-controls="subject-lesson-menu" data-access="@hasFeatureAccess('Lesson Management')">
                    <i class="fa fa-book menu-icon"></i>
                    <span class="menu-title">{{ __('subject_lesson') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="subject-lesson-menu">
                    <ul class="nav flex-column sub-menu">
                        @canany(['lesson-list', 'lesson-create', 'lesson-edit', 'lesson-delete'])
                            <li class="nav-item">
                                <a href="{{ url('lesson') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Lesson Management')"> {{ __('create_lesson') }}</a>
                            </li>
                        @endcanany

                        @canany(['topic-list', 'topic-create', 'topic-edit', 'topic-delete'])
                            <li class="nav-item">
                                <a href="{{ url('lesson-topic') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Lesson Management')"> {{ __('create_topic') }}</a>
                            </li>
                        @endcanany
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- student assignment --}}
        @canany(['assignment-create', 'assignment-submission'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#student-assignment-menu" aria-expanded="false"
                   aria-controls="student-assignment-menu" data-access="@hasFeatureAccess('Assignment Management')">
                   <i class="fa fa-tasks menu-icon"></i>
                    <span class="menu-title">{{ __('student_assignment') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="student-assignment-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('assignment-create')
                            <li class="nav-item">
                                <a href="{{ route('assignment.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Assignment Management')">
                                    {{ __('create_assignment') }}
                                </a>
                            </li>
                        @endcan
                        @can('assignment-submission')
                            <li class="nav-item">
                                <a href="{{ route('assignment.submission') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Assignment Management')">
                                    {{ __('assignment_submission') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- Slider --}}
        @can('slider-create')
            <li class="nav-item">
                <a href="{{ route('sliders.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Slider Management')">
                    <i class="fa fa-list menu-icon"></i>
                    <span class="menu-title">{{ __('sliders') }}</span>
                </a>
            </li>
        @endcan

        @canany(['notification-create','notification-list','notification-delete'])
            <li class="nav-item">
                <a href="{{ route('notifications.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Announcement Management')">
                    <i class="fa fa-bell menu-icon"></i>
                    <span class="menu-title">{{ __('notification') }}</span>
                </a>
            </li>
        @endcanany

        {{-- Attendance --}}
        @canany(['class-teacher','attendance-list','attendance-create','attendance-edit','attendance-delete'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#attendance-menu" data-access="@hasFeatureAccess('Attendance Management')" aria-expanded="false"
                   aria-controls="attendance-menu">
                   <i class="fa fa-check menu-icon"></i>
                   <span class="menu-title">{{ __('attendance') }}</span>
                   <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="attendance-menu">
                    <ul class="nav flex-column sub-menu">
                        @canany(['class-teacher','attendance-create'])
                            <li class="nav-item">
                                <a href="{{ route('attendance.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Attendance Management')">
                                    {{ __('add_attendance') }}
                                </a>
                            </li>
                        @endcan

                        {{-- view attendance --}}
                        @canany(['class-teacher','attendance-list'])
                            <li class="nav-item">
                                <a href="{{ route('attendance.view') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Attendance Management')">
                                    {{ __('view_attendance') }}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('attendance.month') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Attendance Management')">
                                    {{ __('month_wise') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- announceent --}}
        @can('announcement-create')
            <li class="nav-item">
                <a href="{{ route('announcement.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Announcement Management')">
                    <i class="fa fa-bullhorn menu-icon"></i>
                    <span class="menu-title">{{ __('announcement') }}</span>
                </a>
            </li>
        @endcan

        {{-- exam --}}
        @canany(['exam-create', 'exam-upload-marks', 'grade-create', 'exam-result','view-exam-marks'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#exam-menu" aria-expanded="false"
                   aria-controls="exam-menu" data-access="@hasFeatureAccess('Exam Management')">
                   <i class="fa fa-book menu-icon"></i>
                    <span class="menu-title">{{ __('Offline Exam') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="exam-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('exam-create')
                            <li class="nav-item">
                                <a href="{{ route('exams.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')"> {{ __('manage_offline_exam') }}
                                </a>
                            </li>
                        @endcan
                        @can('view-exam-marks')
                            <li class="nav-item">
                                <a href="{{ route('exam.view-marks') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')"> {{ __('Unpublished Exam Marks') }}
                                </a>
                            </li>
                        @endcan
                      
                        @can('exam-upload-marks')

                            <li class="nav-item">
                                <a href="{{ route('exams.timetable') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')">
                                    {{ __('timetable') }}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('exams.upload-marks') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')">
                                    {{ __('upload') }} {{ __('Exam Marks') }}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('exam.bulk-upload-marks') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')">
                                    {{ __('bulk upload') }} {{ __('Exam Marks') }}
                                </a>
                            </li>
                            
                        @endcan
                        @can('exam-result')
                            <li class="nav-item">
                                <a href="{{ route('exams.get-result') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')">
                                    {{ __('Published Exam Result') }}
                                </a>
                            </li>
                        @endcan

                        @can('grade-create')
                            <li class="nav-item">
                                <a href="{{ route('exam.grade.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')">
                                    {{ __('exam_grade') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        {{-- Online Exam --}}
        @canany(['online-exam-create', 'online-exam-list', 'online-exam-edit', 'online-exam-delete'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#online-exam-menu" aria-expanded="false"
                   aria-controls="online-exam-menu" data-access="@hasFeatureAccess('Exam Management')">
                   <i class="fa fa-laptop menu-icon"></i>
                    <span class="menu-title">{{ __('online_exam') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="online-exam-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('online-exam-list')
                            <li class="nav-item">

                                <a href="{{ route('online-exam.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')"> {{ __('manage_online_exam') }}
                                </a>
                            </li>
                        @endcan
                        @can('online-exam-create')
                            <li class="nav-item">
                                <a href="{{ route('online-exam-question.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')"> {{ __('manage_questions') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- Fees --}}

        @canany(['fees-list', 'fees-type-list', 'fees-classes-list', 'fees-paid'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#fees-menu" aria-expanded="false" aria-controls="fees-menu" data-access="@hasFeatureAccess('Fees Management')">
                    <i class="fa fa-dollar menu-icon"></i>
                    <span class="menu-title">{{ __('Fees') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="fees-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('fees-type-list')
                            <li class="nav-item">
                                <a href="{{ route('fees-type.index') }}" class="nav-link" data-access="@hasFeatureAccess('Fees Management')"> {{ __('Fees Type') }}
                                </a>
                            </li>
                        @endcan
                        @can('fees-list')
                            <li class="nav-item">
                                <a href="{{ route('fees.index') }}" class="nav-link" data-access="@hasFeatureAccess('Fees Management')"> {{ __('Manage Fee') }}</a>
                            </li>
                        @endcan
                        @can('fees-paid')
                            <li class="nav-item">
                                <a href="{{ route('fees.paid.index') }}" class="nav-link" data-access="@hasFeatureAccess('Fees Management')"> {{ __('Student Fees') }}
                                </a>
                            </li>
                        @endcan
                        @can('fees-paid')
                            <li class="nav-item">
                                <a href="{{ route('fees.transactions.log.index') }}" class="nav-link" data-access="@hasFeatureAccess('Fees Management')"> {{__('Fees Transaction Logs') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        {{-- Leave --}}
        @canany(['leave-list', 'leave-create', 'leave-edit', 'leave-delete'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#staff-leave-menu" data-access="@hasFeatureAccess('Staff Leave Management')" aria-expanded="false"
                   aria-controls="staff-leave-menu">
                   <i class="fa fa-plane menu-icon"></i>
                   <span class="menu-title">{{ __('leave') }}</span>
                   <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="staff-leave-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('leave.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Leave Management')">
                                {{ __('apply_leave') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('leave.report') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Leave Management')">
                                {{ __('leave_report') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endcanany

        @if (Auth::user()->school_id && Auth::user()->staff)
            <li class="nav-item">
                <a href="{{ route('payroll.slip.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Expense Management')">
                    <i class="fa fa-money menu-icon"></i>
                    <span class="menu-title">{{ __('payroll') }} {{ __('slips') }}</span>
                </a>
            </li>
        @endif

        {{-- Schools --}}
        @canany(['schools-list','schools-create', 'schools-edit', 'schools-delete','school-custom-field-list','school-custom-field-create','school-custom-field-edit','school-custom-field-delete'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#school-menu" aria-expanded="false" aria-controls="school-menu">
                    <i class="fa fa-university menu-icon"></i>
                    <span class="menu-title">{{ __('schools') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="school-menu">
                    <ul class="nav flex-column sub-menu">
                        @canany(['school-custom-field-list','school-custom-field-create','school-custom-field-edit','school-custom-field-delete'])
                            <li class="nav-item">
                                <a href="{{ route('school-custom-fields.index') }}" class="nav-link">
                                    {{ __('school_register_form_fields') }}
                                </a>
                            </li>
                            @if(isset($systemSettings['school_inquiry']) && $systemSettings['school_inquiry'] == 1)
                                <li class="nav-item">
                                    <a href="{{ route('school-inquiry.index') }}" class="nav-link">
                                        {{ __('school_inquires') }}
                                    </a>
                                </li>  
                            @endif
                        @endcanany 
                        @canany(['schools-list','schools-create', 'schools-edit', 'schools-delete']) 
                            <li class="nav-item">
                                <a href="{{ route('schools.index') }}" class="nav-link">
                                    {{ __('schools_details') }}
                                </a>
                            </li> 
                        @endcanany
                    </ul>
                </div>
            </li>
        @endcanany


        {{-- package --}}
        @canany(['package-list', 'package-create', 'package-edit', 'package-delete'])
            <li class="nav-item">
                <a href="{{ route('package.index') }}" class="nav-link">
                    <i class="fa fa-codepen menu-icon"></i>
                    <span class="menu-title">{{ __('package') }}</span>
                </a>
            </li>
        @endcan
        {{-- package --}}
        @canany(['addons-list', 'addons-create', 'addons-edit', 'addons-delete'])
            <li class="nav-item">
                <a href="{{ route('addons.index') }}" class="nav-link">
                    <i class="fa fa-puzzle-piece menu-icon"></i>
                    <span class="menu-title">{{ __('addons') }}</span>
                </a>
            </li>
        @endcan

        {{-- Features list --}}
        @canany(['addons-list', 'addons-create', 'addons-edit', 'addons-delete','package-list', 'package-create', 'package-edit', 'package-delete'])
            <li class="nav-item">
                <a href="{{ url('features') }}" class="nav-link">
                    <i class="fa fa-list-ul menu-icon"></i>
                    <span class="menu-title">{{ __('features') }}</span>
                </a>
            </li>
        @endcan

        {{-- subscription-view --}}
        @can('subscription-view')
            <li class="nav-item">
                <a href="{{ url('subscriptions/report') }}" class="nav-link">
                    <i class="fa fa-puzzle-piece menu-icon"></i>
                    <span class="menu-title">{{ __('subscription') }}</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('subscriptions/transactions') }}" class="nav-link">
                    <i class="fa fa-money menu-icon"></i>
                    <span class="menu-title">{{ __('subscription_transaction') }}</span>
                </a>
            </li>
        @endcan


        {{-- Expense --}}
        @canany(['expense-category-create', 'expense-category-list','expense-category-edit', 'expense-category-delete','expense-create', 'expense-list','expense-edit', 'expense-delete'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#expense-menu" aria-expanded="false" aria-controls="expense-menu" data-access="@hasFeatureAccess('Expense Management')">
                    <i class="fa fa-money menu-icon"></i>
                    <span class="menu-title">{{ __('expense') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="expense-menu">
                    <ul class="nav flex-column sub-menu">
                        @canany(['expense-category-create', 'expense-category-list','expense-category-edit', 'expense-category-delete'])
                            <li class="nav-item">
                                <a href="{{ route('expense-category.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Expense Management')">{{ __('manage_category') }} </a>
                            </li>
                        @endcanany

                        @canany(['expense-create', 'expense-list','expense-edit', 'expense-delete'])
                            <li class="nav-item">
                                <a href="{{ route('expense.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Expense Management')">
                                    {{ __('manage_expense') }}
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- Payroll --}}
        @canany(['payroll-create', 'payroll-list', 'payroll-edit', 'payroll-delete','payroll-settings-list','payroll-settings-create','payroll-settings-edit', 'payroll-settings-delete'])
            <li class="nav-item">
                <a href="#payroll-menu" class="nav-link" data-toggle="collapse" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Expense Management')">
                    <i class="fa fa-credit-card-alt menu-icon"></i>
                    <span class="menu-title">{{ __('payroll') }}</span>    
                    <i class="menu-arrow"></i>       
                </a>
                <div class="collapse" id="payroll-menu">
                    <ul class="nav flex-column sub-menu">
                        @canany(['payroll-create', 'payroll-list', 'payroll-edit', 'payroll-delete'])
                            <li class="nav-item">
                                <a href="{{ route('payroll.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Expense Management')">{{ __('manage_payroll') }} </a>
                            </li>
                        @endcanany

                        @canany(['payroll-settings-list','payroll-settings-create','payroll-settings-edit', 'payroll-settings-delete'])
                            <li class="nav-item">
                                <a href="{{ route('payroll-setting.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Expense Management')">
                                    {{ __('payroll_setting') }}
                                </a>
                            </li>
                        @endcanany
                    </ul>
                </div>
            </li>
        @endcanany

        {{-- session-year --}}
        {{-- @can('session-year-create')
            <li class="nav-item">
                <a href="{{ route('session-year.index') }}" class="nav-link">
                    <i class="fa fa-calendar-o menu-icon"></i>
                    <span class="menu-title">{{ __('Session Years') }}</span>
                </a>
            </li>
        @endcan --}}

        {{-- gallery --}}
        @canany(['gallery-create','gallery-list','gallery-edit','gallery-delete'])
            <li class="nav-item">
                <a href="{{ route('gallery.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('School Gallery Management')">
                    <i class="fa fa-picture-o menu-icon"></i>
                    <span class="menu-title">{{ __('gallery') }}</span>
                </a>
            </li>
        @endcan

        {{-- Certificate --}}
        @canany(['certificate-create', 'certificate-list','certificate-edit', 'certificate-delete','student-list', 'class-teacher','id-card-settings'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#certificate-menu" aria-expanded="false" aria-controls="certificate-menu" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">
                    <i class="fa fa-trophy menu-icon"></i>
                    <span class="menu-title">{{ __('certificate_id_card') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="certificate-menu">
                    <ul class="nav flex-column sub-menu">
                        
                        @canany(['certificate-create', 'certificate-list','certificate-edit', 'certificate-delete'])
                            <li class="nav-item">
                                <a href="{{ url('certificate-template') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">
                                    {{ __('certificate_template') }}
                                </a>
                            </li>
                        @endcanany

                        @canany(['certificate-list'])
                            <li class="nav-item">
                                <a href="{{ url('certificate') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">
                                    {{ __('student_certificate') }}
                                </a>
                            </li>
                        @endcanany

                        @canany(['certificate-list'])
                            <li class="nav-item">
                                <a href="{{ url('certificate/staff-certificate') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">
                                    {{ __('staff_certificate') }}
                                </a>
                            </li>
                        @endcanany

                        @can('id-card-settings')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('id-card-settings') }}" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">{{ __('id_card_settings') }}</a>
                            </li>
                        @endcan

                        @canany(['student-list', 'class-teacher'])
                            <li class="nav-item"><a href="{{ route('students.generate-id-card-index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">{{ __('student_id_card') }}</a></li>
                        @endcanany

                        @can('staff-list')
                                <li class="nav-item">
                                    <a href="{{ route('staff.id-card') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('ID Card - Certificate Generation')">{{ __('staff_id_card') }}</a>
                                </li>
                            @endcan
                    </ul>
                </div>
            </li>
        @endcanany

        @if (Auth::user()->school_id)
            @canany(['role-list', 'role-create', 'role-edit', 'role-delete', 'staff-list', 'staff-create', 'staff-edit','staff-delete'])
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#staff-management" aria-expanded="false" aria-controls="staff-management-menu" data-access="@hasFeatureAccess('Staff Management')">
                        <i class="fa fa-user-secret menu-icon"></i>
                        <span class="menu-title">{{ __('Staff Management')  }}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="staff-management">
                        <ul class="nav flex-column sub-menu">
                            @canany(['role-list', 'role-create', 'role-edit', 'role-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Management')">{{ __('Role & Permission') }}</a>
                                </li>
                            @endcanany
                            @canany(['staff-list', 'staff-create', 'staff-edit', 'staff-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('staff.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Management')">{{ __('staff') }}</a>
                                </li>
                            @endcanany
                            @canany(['staff-list', 'staff-create', 'staff-edit', 'staff-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('staff.create-bulk-upload') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Management')">{{ __('bulk upload') }}</a>
                                </li>
                            @endcanany
                        </ul>
                    </div>
                </li>
            @endcan

            {{-- Staff Leave Management --}}
            @canany(['approve-leave'])
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#staff-leave-management" aria-expanded="false" aria-controls="staff-leave-management-menu" data-access="@hasFeatureAccess('Staff Leave Management')">
                        <i class="fa fa-plane menu-icon"></i>
                        <span class="menu-title">{{ __('Staff Leave')  }}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="staff-leave-management">
                        <ul class="nav flex-column sub-menu">

                            @can('approve-leave')
                                <li class="nav-item">
                                    <a href="{{ route('leave.request') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Leave Management')">{{ __('staff') }} {{ __('leave') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('leave/report') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Leave Management')">{{ __('leave_report') }}</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endcan
        @else
            @canany(['role-list', 'role-create', 'role-edit', 'role-delete', 'staff-list', 'staff-create', 'staff-edit','staff-delete'])
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#staff-management" aria-expanded="false" aria-controls="staff-management-menu">
                        <i class="fa fa-user-secret menu-icon"></i>
                        <span class="menu-title">{{ __('Staff Management')  }}</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="staff-management">
                        <ul class="nav flex-column sub-menu">
                            @canany(['role-list', 'role-create', 'role-edit', 'role-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link">{{ __('Role & Permission') }}</a>
                                </li>
                            @endcanany
                            @canany(['staff-list', 'staff-create', 'staff-edit', 'staff-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('staff.index') }}" class="nav-link">{{ __('staff') }}</a>
                                </li>
                            @endcanany
                        </ul>
                    </div>
                </li>
            @endcan
        @endif

        @canany(['custom-school-email'])
            <li class="nav-item">
                <a href="{{ route('schools.send.mail') }}" class="nav-link">
                    <i class="fa fa-envelope menu-icon"></i>
                    <span class="menu-title">{{ __('email_schools') }}</span>
                </a>
            </li>
        @endcan


        {{-- Subscription Plans & Addons --}}
        @role('School Admin')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#subscription" aria-expanded="false"
               aria-controls="subscription-menu">
               <i class="fa fa-puzzle-piece menu-icon"></i>
                <span class="menu-title">{{ __('subscription') }}</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="subscription">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('subscriptions.history') }}">{{ __('subscription') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('subscriptions.index') }}">{{ __('plans') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('addons.plan') }}">{{ __('addons') }}</a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Support --}}
        <li class="nav-item">
            <a href="{{ url('staff/support') }}" class="nav-link">
                <i class="fa fa-question menu-icon"></i>
                <span class="menu-title">{{ __('support') }}</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('features') }}" class="nav-link">
                <i class="fa fa-list-ul menu-icon"></i>
                <span class="menu-title">{{ __('features') }}</span>
            </a>
        </li>

        @endrole

        {{-- Super admin web settings --}}
        @can('web-settings')
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#web_settings" aria-expanded="false"
               aria-controls="web_settings-menu">
               <i class="fa fa-cogs menu-icon"></i>
                <span class="menu-title">{{ __('web_settings') }}</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="web_settings">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('web-settings.index') }}">{{ __('general_settings') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('web-settings.feature.sections') }}">{{ __('feature_sections') }}</a>
                    </li>

                    @canany(['faqs-create','faqs-list','faqs-edit','faqs-delete'])
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('faqs.index') }}">{{ __('faqs') }}</a>
                        </li>
                    @endcanany

                </ul>
            </div>
        </li>
        @endcan

        {{-- School web page setttings --}}
        @can('school-web-settings')
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#web_settings" aria-expanded="false"
                aria-controls="web_settings-menu" data-access="@hasFeatureAccess('Website Management')">
                <i class="fa fa-cogs menu-icon"></i>
                    <span class="menu-title">{{ __('web_settings') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="web_settings">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school.web-settings.index') }}" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Website Management')">{{ __('content') }}</a>
                        </li>
                        
                        @canany(['faqs-create','faqs-list','faqs-edit','faqs-delete'])
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('faqs.index') }}" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Website Management')">{{ __('faqs') }}</a>
                            </li>
                        @endcanany
                    </ul>
                </div>
            </li>
        @endcan
    
        


        {{-- settings --}}
        @canany(['app-settings', 'language-list', 'school-setting-manage', 'system-setting-manage',
            'fcm-setting-manage', 'email-setting-create', 'privacy-policy', 'contact-us', 'about-us','guidance-create','guidance-list','guidance-edit','guidance-delete', 'email-template'])
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#settings-menu" aria-expanded="false" aria-controls="settings-menu">
                    <i class="fa fa-cog menu-icon"></i>
                    <span class="menu-title">{{ __('system_settings') }}</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="settings-menu">
                    <ul class="nav flex-column sub-menu">
                        @can('app-settings')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.app') }}">{{ __('app_settings') }}</a>
                            </li>
                        @endcan
                        @can('school-setting-manage')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-settings.index') }}">{{ __('general_settings') }}</a>
                            </li>

                            {{-- session-year.index --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('session-year.index') }}">{{ __('session_year') }}</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('leave-master.index') }}" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Staff Leave Management')">{{ __('leave') }} {{ __('settings') }}</a>
                            </li>
                        @endcan

                        @can('system-setting-manage')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.index') }}">{{ __('general_settings') }}</a>
                            </li>
                        @endcan

                        @can('subscription-settings')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.subscription-settings') }}">{{ __('subscription_settings') }}</a>
                            </li>
                        @endcan

                        {{-- @can('front-site-setting')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.front-site-settings') }}">{{ __('front_site_settings') }}</a>
                            </li>
                        @endcan --}}
                        @canany(['guidance-create','guidance-list','guidance-edit','guidance-delete'])
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('guidances.index') }}">{{ __('guidance') }}</a>
                            </li>
                        @endcanany

                        @can('language-list')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('language') }}">
                                    {{ __('language_settings') }}</a>
                            </li>
                        @endcan
                        @can('fcm-setting-manage')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.fcm') }}"> {{ __('notification_settings') }}</a>
                            </li>
                        @endcan

                        {{-- @can('fees-config')
                            <li class="nav-item">
                                <a href="{{ route('fees.config.index') }}" class="nav-link" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Fees Management')">
                                    {{ __('Fees Settings') }}</a>
                            </li>
                        @endcan --}}

                        @can('school-setting-manage')
                            <li class="nav-item">
                                <a href="{{ route('school-settings.online-exam.index') }}" class="nav-link text-wrap" data-name="{{ Auth::user()->getRoleNames()[0] }}" data-access="@hasFeatureAccess('Exam Management')">
                                    {{ __('online_exam_terms_condition') }}
                                </a>
                            </li>
                        @endcan

                        @can('email-setting-create')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.email.index') }}">{{ __('email_configuration') }}</a>
                            </li>
                        @endcan

                        {{-- Super admin panel --}}
                        @can('email-setting-create')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.email.template') }}">{{ __('email_template') }}</a>
                            </li>
                        @endcan

                        {{-- School admin panel --}}
                        @can('email-template')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school-settings.email.template') }}">{{ __('email_template') }}</a>
                            </li>
                        @endcan

                        {{--Payment Configuration Menu For Superadmin--}}
                        @hasanyrole(['Super Admin','School Admin'])
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('system-settings.payment.index') }}">{{ __('Payment Settings') }}</a>
                        </li>
                        @endrole

                        @can('school-setting-manage')
                        <li class="nav-item">
                            <a class="nav-link" data-access="@hasFeatureAccess('Website Management')" href="{{ route('school-settings.third-party') }}">{{ __('Third-Party APIs') }}</a>
                        </li>
                        @endcan

                        @can('system-setting-manage')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('system-settings.third-party') }}">{{ __('Third-Party APIs') }}</a>
                        </li>
                        @endcan

                        {{-- @can('database-backup')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('database-backup') }}">{{ __('database_backup') }}</a>
                            </li>
                        @endcan --}}

                        

                        @can('contact-us')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.contact-us') }}"> {{ __('contact_us') }}</a>
                            </li>
                        @endcan
                        @can('about-us')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.about-us') }}"> {{ __('about_us') }}
                                </a>
                            </li>
                        @endcan

                        @hasrole('School Admin')
                        
                        {{-- Privacy Policy --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-settings.privacy-policy') }}">{{ __('privacy_policy') }}</a>
                        </li>

                        {{-- Terms & Conditions --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-settings.terms-condition') }}">{{ __('terms_condition') }}</a>
                        </li>

                        {{-- Refund Cancellation --}}

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('school-settings.refund-cancellation') }}">{{ __('refund_cancellation') }}</a>
                        </li>

                        @endrole

                        @can('privacy-policy')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.privacy-policy') }}">{{ __('privacy_policy') }}</a>
                            </li>
                        @endcan

                        @can('terms-condition')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('system-settings.terms-condition') }}">{{ __('terms_condition') }}</a>
                            </li>
                        @endcan

                    </ul>
                </div>
            </li>
        @endcanany

        @if (Auth::user()->hasRole('Super Admin'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('system-update.index') }}">
                    <i class="fa fa-cloud-download menu-icon"></i>
                    <span class="menu-title">{{ __('system_update') }}</span>
                </a>
            </li>
        @endif

        @if (Auth::user()->hasRole(['Super Admin','School Admin']))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('database-backup.index') }}">
                    <i class="fa fa-database menu-icon"></i>
                    <span class="menu-title">{{ __('database_backup') }}</span>
                </a>
            </li>
        @endif

    </ul>
</nav>
