@extends('layouts.master')

@section('title')
    {{ __('View Teacher Timetable') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('View Teacher Timetable') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">

                        <div class="text-center mb-3"><h3>{{$teacher->full_name}}</h3></div>
                        <div class="row">
                            <div id='calendar' class="col-md-12 col-sm-12 col-12 no-header-toolbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            @foreach($timetables as $timetable)
            teacherTimetable.addEvent({
                title: "{{$timetable->title}}",
                daysOfWeek: [days.indexOf("{{$timetable->day}}")],
                startTime: "{{$timetable->start_time}}",
                endTime: "{{$timetable->end_time}}",
                color: "{{$timetable->subject->bg_color??'Black'}}",
                id: "{{$timetable->id}}",
                class_section: "{{$timetable->class_section->full_name}}"
            });
            @endforeach

            $(document).ready(function () {
                teacherTimetable.setOption("slotMinTime", "{{$timetableSettingsData['timetable_start_time']??"00:00:00"}}");
                teacherTimetable.setOption("slotMaxTime", "{{$timetableSettingsData['timetable_end_time']??"00:00:00"}}");
                teacherTimetable.setOption("slotDuration", "{{$timetableSettingsData['timetable_duration']??"00:00:00"}}");
            })

        });


    </script>
@endsection
