@extends('layouts.master')

@section('title')
    {{ __('Class Subject') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Class Subject') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('Class Subject') }}
                        </h4>
                        <div class="d-block">
                            <div class="">
                                <div class="col-12 text-right d-flex justify-content-end text-right align-items-end">
                                    <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                                </div>
                            </div>
                        </div>
                        <div id="toolbar">
                            <label for="filter_medium_id" class="filter-menu">{{__("Medium")}}</label>
                            <select name="medium_id" id="filter_medium_id" class="form-control">
                                <option value="">{{ __('all') }}</option>
                                @foreach ($mediums as $medium)
                                    <option value="{{ $medium->id }}">{{ $medium->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('class.subject.list') }}" data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                               data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                               data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id" data-toolbar="#toolbar" data-sort-order="desc"
                               data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "class-subject-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-detail-filter="classSubjectsDetailFilter" data-detail-view="true" data-detail-formatter="classSubjectsDetailFormatter" data-query-params="classQueryParams"
                               data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="full_name">{{ __('name') }}</th>
                                <th scope="col" data-field="shift.name">{{ __('Shift') }}</th>
                                <th scope="col" data-field="include_semesters" data-formatter="yesAndNoStatusFormatter">{{ __('Semester') }}</th>
                                <th scope="col" data-field="medium.name">{{ __('medium') }}</th>
                                <th scope="col" data-field="section_names">{{ __('section') }}</th>
                                <th scope="col" data-field="core_subjects" data-formatter="coreSubjectFormatter">{{ __('Core Subjects') }}</th>
                                <th scope="col" data-field="elective_subject_groups" data-formatter="electiveSubjectFormatter">{{ __('elective_subject') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
