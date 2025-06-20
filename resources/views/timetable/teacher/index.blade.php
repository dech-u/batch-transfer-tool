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
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('timetable.teacher.list') }}"
                                       data-click-to-select="true" data-side-pagination="server"
                                       data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                       data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true"
                                       data-fixed-columns="false" data-fixed-number="2"
                                       data-fixed-right-number="1" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id"
                                       data-query-params="AssignTeacherQueryParams" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "data-list-<?= date(' d-m-y') ?>" }'>
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="full_name">{{ __('name') }}</th>
                                        <th scope="col" data-field="Monday" data-formatter="teacherTimetableDayFormatter">{{ __('Monday') }}</th>
                                        <th scope="col" data-field="Tuesday" data-formatter="teacherTimetableDayFormatter">{{ __('Tuesday') }}</th>
                                        <th scope="col" data-field="Wednesday" data-formatter="teacherTimetableDayFormatter">{{ __('Wednesday') }}</th>
                                        <th scope="col" data-field="Thursday" data-formatter="teacherTimetableDayFormatter">{{ __('Thursday') }}</th>
                                        <th scope="col" data-field="Friday" data-formatter="teacherTimetableDayFormatter">{{ __('Friday') }}</th>
                                        <th scope="col" data-field="Saturday" data-formatter="teacherTimetableDayFormatter">{{ __('Saturday') }}</th>
                                        <th scope="col" data-field="Sunday" data-formatter="teacherTimetableDayFormatter">{{ __('Sunday') }}</th>
                                        <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
