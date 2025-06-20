@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('exam') . ' ' . __('timetable') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('exam') . ' ' . __('timetable') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('exams.index') }}">{{ __('back') }}</a>
                        </div>
                        <h4 class="page-title mb-4">
                            {{ __('create') . ' ' . __('exam') . ' ' . __('timetable') }}
                        </h4>
                        <div class="form-group">
                            <form class="edit-form" data-success-function="formSuccessFunction" action="{{ route('exam.timetable.update',$exam->id) }}" data-pre-submit-function="classValidation" method="POST">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>{{ __('exam') }} </label>
                                        {!! Form::hidden('semester_id', $exam->semester_id ?? null) !!}
                                        {!! Form::hidden('session_year_id', $exam->session_year_id) !!}
                                        {!! Form::text('', $exam->name, ['readonly' => true ,'class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('Class') }} </label>
                                        {!! Form::text('', $exam->class->full_name, ['readonly' => true ,'class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('Exam Result Submission Date') }} <span class="text-danger">*</span></label>
                                            {!! Form::text('last_result_submission_date', $last_result_submission_date, ['class' => 'timetable-date form-control', 'placeholder' => __('Exam Result Submission Date'), 'required',]) !!}
                                    </div>
                                </div>

                                <div class="exam-timetable-content">
                                    <div data-repeater-list="timetable">
                                        <div data-repeater-item>
                                            <div class="row">
                                                {!! Form::hidden('id', null, ['class' => 'timetable_id']) !!}
                                                <div class="form-group col-md-4">
                                                    <label for="subject_id">{{ __('subject') }} </label>
                                                    <select name="class_subject_id" id="subject_id" class="form-control exam-subjects-options subject" required>
                                                        @if(!empty($exam->class->all_subjects))
                                                            <option value="">-- {{ __('select') }} --</option>
                                                            @foreach($exam->class->all_subjects as $subject)
                                                                <option value="{{$subject->class_subject_id}}">{{$subject->name_with_type}}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">-- {{ __('no_data_found') }} --</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>{{ __('total_marks') }} <span class="text-danger">*</span></label>
                                                    {!! Form::text('total_marks', null, ['class' => 'total-marks form-control', 'placeholder' => __('total_marks'), 'min' => 1, 'required' , "data-convert" => "number"]) !!}
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>{{ __('passing_marks') }} <span class="text-danger">*</span></label>
                                                    {!! Form::text('passing_marks', null, ['class' => 'passing-marks form-control', 'placeholder' => __('passing_marks'), 'min' => 1, 'required', "data-convert" => "number"]) !!}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label>{{ __('start_time') }} <span class="text-danger">*</span></label>
                                                    {!! Form::text('start_time', null, ['class' => 'start-time form-control', 'placeholder' => __('start_time'), 'autocomplete' => 'off', 'required' , "data-convert" => "time"]) !!}
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>{{ __('end_time') }} <span class="text-danger">*</span></label>
                                                    {!! Form::text('end_time', null, ['class' => 'end-time form-control', 'placeholder' => __('end_time'), 'autocomplete' => 'off', 'required' , "data-convert" => "time"]) !!}
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                                    {!! Form::text('date', null, ['class' => 'timetable-date form-control', 'placeholder' => __('date'), 'required']) !!}
                                                </div>
                                                <div class="form-group col-md-1 pl-0 mt-4" data-repeater-delete>
                                                    <button type="button" {{ $disabled }} class="btn btn-inverse-danger btn-icon remove-exam-timetable-content">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-md-4 mt-3 mb-3">
                                        <button type="button" {{ $disabled }} class="btn btn-success add-exam-timetable-content" title="Add new row" data-repeater-create>
                                            {{ __('Add New Data') }}
                                        </button>
                                    </div>
                                </div>
                                <input class="btn btn-theme float-right ml-3" id="create-btn" {{ $disabled }} type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        @if(isset($exam->timetable) && $exam->timetable->isNotEmpty())
            examTimetableRepeater.setList([
                @foreach ($exam->timetable as $timetable)
                {
                    id: "{{ $timetable->id }}",
                    class_subject_id: "{{ $timetable->class_subject_id }}",
                    total_marks: "{{ $timetable->total_marks }}",
                    passing_marks: "{{ $timetable->passing_marks }}",
                    start_time: "{{ $timetable->start_time }}",
                    end_time: "{{ $timetable->end_time }}",
                    date: moment("{{ $timetable->date }}", 'YYYY-MM-DD').format('DD-MM-YYYY')
                },
                @endforeach
            ])
        @else
            $('.add-exam-timetable-content').trigger('click')
        @endif

        $(document).ready(function () {
            @foreach ($exam->timetable as $key=>$timetable)
            $('#remove-exam-timetable-' + {{$key}}).attr('data-id', {{$timetable->id}});
            @endforeach

            $('body').on('focus', ".timetable-date", function () {
                let minDate = moment("{{ $currentSessionYear->start_date }}", 'YYYY-MM-DD').format('DD-MM-YYYY') ;
                let maxDate = moment("{{ $currentSessionYear->end_date }}", 'YYYY-MM-DD').format('DD-MM-YYYY');

                $(this).datepicker({
                    enableOnReadonly: false,
                    format: "dd-mm-yyyy",
                    todayHighlight: true,
                    startDate: minDate,
                    endDate: maxDate,
                    rtl: isRTL()
                });
            });
        });

        function formSuccessFunction(response) {
            if (!response.error) {
                setTimeout(() => {
                    window.location.href = "{{route('exams.index')}}"
                }, 1000);
            }
        }
    </script>
@endsection
