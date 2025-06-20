@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('grade') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('grade') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="page-title mb-4">
                            {{ __('create') . ' ' . __('grade') }}
                        </h4>
                        <div class="form-group">
                            <form id="create-form" action="{{ route('exam.grade.store') }}" method="POST"
                                data-success-function="formSuccessFunction">
                                <div class="grade-content">
                                    <div data-repeater-list="grade_data">
                                        <div class="row" data-repeater-item>
                                            {!! Form::hidden('id') !!}
                                            <div class="form-group col-md-4">
                                                <label>{{ __('starting_range') }} </label>
                                                {!! Form::text('starting_range', 0, [
                                                    'class' => 'starting-range form-control',
                                                    'placeholder' => trans('starting_range'),
                                                    'required' => true,
                                                    'data-convert' => 'number',
                                                ]) !!}
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>{{ __('ending_range') }} </label>
                                                {!! Form::text('ending_range', null, [
                                                    'class' => 'ending-range form-control',
                                                    'placeholder' => trans('ending_range'),
                                                    'required' => true,
                                                    'max' => 100,
                                                    'data-convert' => 'number',
                                                ]) !!}
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>{{ __('grade') }} </label>
                                                {!! Form::text('grades', null, [
                                                    'class' => 'grade form-control',
                                                    'placeholder' => trans('grade'),
                                                    'required' => true,
                                                ]) !!}
                                            </div>
                                            <div class="form-group col-md-1 pl-0 mt-4 remove-grades-div"
                                                data-repeater-delete>
                                                <button type="button" class="btn btn-icon btn-inverse-danger remove-grades"
                                                    title="Remove Grade">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-0 mb-4">
                                        <button type="button" class="btn btn-success add-grade-content" title="Add new row"
                                            data-repeater-create>
                                            {{ __('add_new_data') }}
                                        </button>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-theme float-right" value={{ __('submit') }} />
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
        $(document).ready(function() {
            @if (isset($grades) && !empty($grades->toArray()))
                gradesRepeater.setList([
                    @foreach ($grades as $data)
                        {
                            id: "{{ $data->id }}",
                            starting_range: "{{ $data->starting_range }}",
                            ending_range: "{{ $data->ending_range }}",
                            grades: "{{ $data->grade }}",
                        },
                    @endforeach
                ]);
            @endif
        });

        $(document).ready(function() {
            @foreach ($grades as $key => $data)
                $('#remove-grades-' + {{ $key }}).attr('data-id', {{ $data->id }});
            @endforeach
            $('.grade-content').find('.ending-range:last').trigger('change')
        });

        function formSuccessFunction() {
            setTimeout(() => {
                window.location.reload()
            }, 1000);
        }
    </script>
@endsection
