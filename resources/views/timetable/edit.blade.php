@extends('layouts.master')

@section('title')
    {{ __('timetable') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('create') . ' ' . __('timetable') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-2 col-sm-12 col-12 p-0">
                                <a href="{{ route('timetable.index') }}"
                                   class="btn btn-theme btn-block">{{ __('back') }}</a>
                            </div>
                            <div class="text-center col-md-10 col-sm-12 col-12">
                                <h3>{{$classSection->full_name}}</h3>
                                <input type="hidden" id="class_section_id" value="{{$classSection->id}}"/>
                                {!! Form::hidden('semester_id', $classSection->class->include_semesters ? $currentSemester->id : null, ['id' => 'semester_id']) !!}

                            </div>
                        </div>

                        <div class="row">
                            <div id='external-events' class="col-md-2 col-sm-12 col-12">
                                <p><strong>{{ __('Subject') }}</strong></p>

                                @if (!empty($subjectTeachers) && count($subjectTeachers) > 0)
                                    @foreach ($subjectTeachers as $subjectTeacher)
                                        <div class='fc-event fc-h-event fc-div-color fc-daygrid-event fc-daygrid-block-event text-wrap'
                                            style="background-color: {{ $subjectTeacher->subject->bg_color }}"
                                            data-color="{{ $subjectTeacher->subject->bg_color }}"
                                            data-subject_teacher_id="{{ $subjectTeacher->id }}"
                                            data-subject_id="{{ $subjectTeacher->subject_id }}"
                                            data-duration='{{ $timetableSettingsData['timetable_duration'] ?? '01:00:00' }}'
                                            data-note="">
                                            <div class='fc-event-main px-2' style="width: -webkit-fill-available">
                                                {{ Str::limit($subjectTeacher->subject->name .' ( '.$subjectTeacher->subject->type.' ) '.' - ' . $subjectTeacher->teacher->full_name, 25, ' ...') }}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if (!empty($subjectWithoutTeacherAssigned) && count($subjectWithoutTeacherAssigned) > 0 && isset($subjectTeacher))       
                                    @foreach ($subjectWithoutTeacherAssigned as $subject)
                                        <div class='fc-event fc-h-event fc-div-color fc-daygrid-event fc-daygrid-block-event'
                                            style="background-color: {{ $subject->bg_color }}"
                                            data-color="{{ $subject->bg_color }}"
                                            data-duration='{{ $timetableSettingsData['timetable_duration'] ?? '01:00:00' }}'
                                            data-subject_id="{{ $subject->id }}" data-note="">
                                            <div class='fc-event-main'>{{  Str::limit($subject->name.' ( '.$subjectTeacher->subject->type.' )', 25, ' ...') }}</div>
                                        </div>
                                    @endforeach
                                @endif

                                <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'
                                     style="background-color:rgb(11, 11, 11)" data-color="black" data-duration='00:30:00'
                                     data-note="Break">
                                    <div class='fc-event-main px-2'>{{ __('Break') }}</div>
                                </div>
                            </div>
                            <div id='calendar' class="col-md-10 col-sm-12 col-12 no-header-toolbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        @foreach ($timetables as $timetable)
        createTimetable.addEvent({
            title: "{{ $timetable->title }}",
            daysOfWeek: [days.indexOf("{{ $timetable->day }}")],
            startTime: "{{ $timetable->start_time }}",
            endTime: "{{ $timetable->end_time }}",
            color: "{{ $timetable->subject->bg_color ?? 'Black' }}",
            id: "{{ $timetable->id }}",
        });
        @endforeach
        $(document).ready(function () {
            createTimetable.setOption("slotMinTime",
                "{{ $timetableSettingsData['timetable_start_time'] ?? '00:00:00' }}");
            createTimetable.setOption("slotMaxTime",
                "{{ $timetableSettingsData['timetable_end_time'] ?? '00:00:00' }}");
            createTimetable.setOption("slotDuration",
                "{{ $timetableSettingsData['timetable_duration'] ?? '00:00:00' }}");
        })
    </script>
@endsection
