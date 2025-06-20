@extends('layouts.master')
@section('title')
    {{ __('dashboard') }}
@endsection
@section('content')

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-theme text-white mr-2">
                    <i class="fa fa-home"></i>
                </span> {{ __('dashboard') }}
            </h3>
        </div>
        {{-- School Dashboard --}}
        @if (Auth::user()->hasRole('School Admin') || Auth::user()->hasRole('Teacher') || Auth::user()->school_id)
            <div class="row">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <h4 class="card-title">{{ __('holiday') }}</h4>
                            <div class="v-scroll dashboard-description">
                                @if (count($holiday))
                                    @foreach ($holiday as $holiday)
                                        <div class="col-md-12 bg-light p-2 mb-2">
                                            <span>{{ $holiday->title }}</span>
                                            <span class="float-right text-muted">{{ date('d - M',strtotime($holiday->date)) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-md-12 text-center bg-light p-2 mb-2">
                                        <span>{{ __('no_holiday_found') }}.</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">{{ __('leaves') }}</h3>
                                </div>
                                <div class="col-md-6 dropdown text-right">
                                    {!! Form::select('leave_filter', ['Today' => __('today'), 'Tomorrow' => __('tomorrow'), 'Upcoming' => __('upcoming')], 'today', ['class' => 'form-control form-control-sm filter_leaves']) !!}
                                </div>
                            </div>
        
                            <div class="v-scroll mt-2">
                                <table class="table custom-table">
                                    @hasNotFeature('Staff Leave Management')
                                    <tbody class="leave-list">
                                        <tr>
                                            <td colspan="2" class="text-center text-small">
                                                {{ __('Purchase') . ' ' . __('Staff Leave Management') .' '. __('to Continue using this functionality') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                    @endHasNotFeature
        
                                    @hasFeature('Staff Leave Management')
                                    <tbody class="leave-list">
        
                                    </tbody>
                                    @endHasFeature
                                    
                                    
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <h4 class="card-title">{{ __('student_gender') }}</h4>
                            <div id="gender-ratio-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Expense Graph --}}
                @if (Auth::user()->can('expense-create'))
                    <div class="col-md-6 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <h4 class="card-title">{{ __('expense') }}</h4>
                                <div class="chartjs-wrapper mt-5" style="height: 330px">
                                    <canvas id="expenseChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Teacher's Today Schedule #Timetable --}}
                @if (Auth::user()->hasRole('Teacher'))
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body custom-card-body">
                                <div class="clearfix">
                                    <h4 class="card-title float-left">{{ __('today_schedule') }}</h4>
                                </div>
                                <div class="v-scroll dashboard-description">
                                    @if (count($timetables))
                                        @foreach ($timetables as $timetable)
                                            <div
                                                class="wrapper mb-2 d-flex align-items-center justify-content-between py-2 border-bottom">
                                                <div class="d-flex">
                                                    <div class="wrapper ms-3">
                                                        <h5>{{ $timetable->start_time }} - {{ $timetable->end_time }}</h5>
                                                        <span
                                                            class="text-small text-muted">{{ $timetable->subject->name_with_type }}</span>
                                                    </div>
                                                </div>
                                                <span class="text-muted mr-2">{{ $timetable->class_section->full_name }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-md-12 text-center bg-light p-2 mb-2">
                                            <span>{{ __('no_timetable_found') }}.</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Class section wise attendance --}}
                <div class="col-md-4 d-flex flex-column">
                    <div class="row flex-grow">
                        <div class="col-12 col-lg-4 col-lg-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                                <div class="card-body custom-card-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <h3 class="card-title">{{ __('attendance') }}</h3>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            {!! Form::select('class_id', $class_names, null, ['class' => 'form-control form-control-sm class-section-attendance']) !!}
                                        </div>
                                    </div>
                                    <div id="attendanChart">
                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body custom-card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <h3 class="card-title">
                                        {{ __('exam_result') }}
                                    </h3>
                                </div>
                                <div class="col-md-3">
                                    <select name="session_year_id" id="exam_result_session_year_id" class="form-control form-control-sm">
                                        @foreach ($sessionYear as $session)
                                            @if ($session->default == 1)
                                                <option value="{{ $session->id }}" selected>{{ $session->name }}</option>
                                            @else
                                                <option value="{{ $session->id }}">{{ $session->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="exam_name" id="exam_reuslt_exam_name" class="form-control form-control-sm">
                                        <option value="">{{ __('select') .' '. __('exam') }}</option>
                                        @foreach ($exams as $exam)
                                            <option value="{{ $exam->name }}"
                                                    data-session-year="{{ $exam->session_year_id }}">
                                                {{ $exam->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-1 mb-3 v-scroll">
                                <div class="exam-report" id="class-progress-report">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12 grid-margin stretch-card search-container">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">{{ __('announcement') }}</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th> {{ __('no.') }}</th>
                                        <th class="col-md-2"> {{ __('title') }}</th>
                                        <th> {{ __('description') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($announcement))
                                        @foreach ($announcement as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->title }}</td>
                                                <td>{{ $row->description }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center text-small">
                                                {{ __('no_announcement_found') }}
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
    </div>
@endsection
@section('script')
@if ($boys || $girls)
<script>

    window.onload = setTimeout(() => {
        $('.class-section-attendance').trigger('change');
        $('.filter_leaves').trigger('change');
        $('#exam_result_session_year_id').trigger('change');
        const selectElement = document.getElementById('exam_reuslt_exam_name');
        if (selectElement) {
            var selectedIndex = selectElement.selectedIndex || 0;
            var options = selectElement.options;
            
            // Iterate through options starting from the next index
            for (var i = selectedIndex + 1; i < options.length; i++) {
                if (options[i].style.display !== "none") {
                    // Set the next visible option as selected
                    selectElement.selectedIndex = i;
                    break;
                }
            }
        }
        $('#exam_reuslt_exam_name').trigger('change');
    }, 500);

    gender_ratio(<?php echo $boys; ?>, <?php echo $girls; ?>,<?php echo $total_students; ?>);
</script>
@endif

@endsection