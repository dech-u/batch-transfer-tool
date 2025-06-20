@extends('layouts.master')

@section('title')
    {{ __('exam_timetable') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('exam_timetable') }}
            </h3>
        </div>
        
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        
                        <div class="row" id="toolbar">
                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                <label for="filter_session_year_id" class="filter-menu">{{ __('class_section') }}</label>
                                {!! Form::select('class_id', $class_sections, null, ['class' => 'form-control','id' => 'filter-class-id']) !!}
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                <label for="filter_session_year_id" class="filter-menu">{{ __('exam') }}</label>
                                <select name="exam_id" id="filter-exam-id" class="form-control">
                                    <option value="">-- {{ __('select') }} {{ __('exam') }} --</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}" data-class-id="{{ $exam->class_id }}">{{ $exam->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table" data-url="{{ route('exams.timetable.show',1) }}" data-click-to-select="true" data-pagination="false" data-search="false" data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true" data-fixed-columns="false" data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all' data-export-options='{ "fileName": "exam-list-<?= date(' d-m-y') ?>" ,"ignoreColumn": ["operate"]}' data-show-export="true" data-detail-formatter="examListFormatter" data-query-params="examTimetableQueryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th> <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="class_subject.subject.name_with_type" data-sortable="false">{{ __('subject') }}</th>
                                <th scope="col" data-field="date" data-formatter="dateFormatter">{{ __('date') }}</th>
                                <th scope="col" data-field="start_time" data-formatter="timeFormatter">{{ __('start_time') }}</th>
                                <th scope="col" data-field="end_time" data-formatter="timeFormatter">{{ __('end_time') }}</th>
                                <th scope="col" data-field="total_marks">{{ __('total_marks') }}</th>
                                <th scope="col" data-field="passing_marks">{{ __('passing_marks') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>            
        </div>
    </div>
@endsection
@section('script')
    <script>
        window.onload = setTimeout(() => {
            $('#filter-class-id').trigger('change');
        }, 500);
    </script>
@endsection
