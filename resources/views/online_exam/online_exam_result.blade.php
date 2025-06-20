@extends('layouts.master')

@section('title')
    {{ __('online').' '.__('exam').' '.__('result') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('online').' '.__('exam').' '.__('result') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('online-exam.index') }}">{{ __('back') }}</a>
                        </div>
                        <div class="row">
                            <input type="hidden" name="online_exam_id" value="{{$onlineExamData->id}}">
                            <div class="form-group col-md-4">
                                <label>{{ __('class_section').' '.__('name') }}</label>
                                <input type="text" id="class-section-name" value="{{$onlineExamData->class_section_with_medium}}" placeholder="{{ __('class_section').' '.__('name') }}" class="form-control" readonly/>
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ __('subject') }}</label>
                                <input type="text" id="subject-name" value="{{$onlineExamData->subject_with_name}}" placeholder="{{ __('subject') }}" class="form-control" readonly/>
                            </div>
                            <div class="form-group col-md-4">
                                <label>{{ __('online').' '.__('exam').' '.__('title') }}</label>
                                <input type="text" id="online-exam-title" value="{{$onlineExamData->title}}" placeholder="{{ __('title') }}" class="form-control" readonly/>
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
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('online-exam.result.show',$onlineExamData->id) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                               data-toolbar="#toolbar" data-show-columns="false" data-show-refresh="true"
                               data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "{{__('online').' '.__('exam')}}-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="queryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="student_id" data-sortable="true" data-visible="false">{{ __('student_id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="student_name">{{ __('student_name')}}</th>
                                <th scope="col" data-field="marks">{{ __('marks') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
