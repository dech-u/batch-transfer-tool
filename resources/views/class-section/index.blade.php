@extends('layouts.master')

@section('title')
    {{ __('Class Section') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Class Section & Teachers') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('Class') }}
                        </h4>
                        @canany(['class-section-edit','class-section-delete'])
                            <div class="d-block">

                                <div class="">
                                    <div class="col-12 text-right d-flex justify-content-end text-right align-items-end">
                                        <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                                    </div>
                                </div>
                            </div>
                        @endcanany
                        <div id="toolbar">
                            <label for="filter_class_id" class="filter-menu">{{__("Class")}}</label>
                            <select name="class_id" id="filter_class_id" class="form-control">
                                <option value="">{{ __('all') }}</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('class-section.show',[1]) }}" data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                               data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                               data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id" data-toolbar="#toolbar" data-sort-order="desc"
                               data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "class-section-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true"
                               data-detail-filter="subjectTeachersDetailFilter" data-detail-view="true" data-detail-formatter="SubjectTeachersDetailFormatter"
                               data-query-params="classQueryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="full_name">{{ __('Class') }}</th>
                                <th scope="col" data-field="class_teachers_list" data-formatter="classTeacherListFormatter">{{ __('Class Teacher') }}</th>
                                <th scope="col" data-field="subject_teachers_list" data-formatter="subjectTeacherListFormatter">{{ __('Subject Teacher') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>

                                @canany(['class-section-edit','class-section-delete'])
                                    <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                                @endcanany
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
