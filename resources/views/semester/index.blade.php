@extends('layouts.master')

@section('title')
    {{ __('Semester') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create').' '.__('Semester') }}
                        </h4>
                        <form action="{{ route('semester.store') }}" class="create-form pt-3 " id="formdata" method="POST" novalidate="novalidate" data-success-function="formSuccessFunction">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="start_month">{{ __('Start Month') }} <span class="text-danger">*</span></label>
                                    <select name="start_month" id="start_month" class="form-control" required>
                                        <option value="1">{{__("January")}}</option>
                                        <option value="2">{{__("February")}}</option>
                                        <option value="3">{{__("March")}}</option>
                                        <option value="4">{{__("April")}}</option>
                                        <option value="5">{{__("May")}}</option>
                                        <option value="6">{{__("June")}}</option>
                                        <option value="7">{{__("July")}}</option>
                                        <option value="8">{{__("August")}}</option>
                                        <option value="9">{{__("September")}}</option>
                                        <option value="10">{{__("October")}}</option>
                                        <option value="11">{{__("November")}}</option>
                                        <option value="12">{{__("December")}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="end_month">{{ __('End Month') }} <span class="text-danger">*</span></label>
                                    <select name="end_month" id="end_month" class="form-control" required>
                                        <option value="1">{{__("January")}}</option>
                                        <option value="2">{{__("February")}}</option>
                                        <option value="3">{{__("March")}}</option>
                                        <option value="4">{{__("April")}}</option>
                                        <option value="5">{{__("May")}}</option>
                                        <option value="6">{{__("June")}}</option>
                                        <option value="7">{{__("July")}}</option>
                                        <option value="8">{{__("August")}}</option>
                                        <option value="9">{{__("September")}}</option>
                                        <option value="10">{{__("October")}}</option>
                                        <option value="11">{{__("November")}}</option>
                                        <option value="12">{{__("December")}}</option>
                                    </select>
                                </div>
                            </div>
                             <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list').' '.__('Semester') }}
                        </h4>
                        <div class="col-12 mt-4 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('semester.show',1) }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                                       data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id" data-sort-order="asc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "semester-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                                       data-query-params="queryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{__('id')}}</th>
                                        <th scope="col" data-field="no">{{__('no.')}}</th>
                                        <th scope="col" data-field="name">{{__('name')}}</th>
                                        <th scope="col" data-field="start_month_name">{{__('Start Month')}}</th>
                                        <th scope="col" data-field="end_month_name">{{__('End Month')}}</th>
                                        <th scope="col" data-field="current" data-formatter="yesAndNoStatusFormatter">{{__('Current')}}</th>
                                        <th data-events="semesterEvents" scope="col" data-field="operate" data-escape="false">{{__('action')}}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit').' '.__('Semester') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>

                <form action="{{ url('semester') }}" class="edit-form pt-3 " id="formdata" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-4">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control', 'id' => 'edit-name']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="edit-start-month">{{ __('Start Month') }} <span class="text-danger">*</span></label>
                                <select name="start_month" id="edit-start-month" class="form-control">
                                    <option value="1">{{__("January")}}</option>
                                    <option value="2">{{__("February")}}</option>
                                    <option value="3">{{__("March")}}</option>
                                    <option value="4">{{__("April")}}</option>
                                    <option value="5">{{__("May")}}</option>
                                    <option value="6">{{__("June")}}</option>
                                    <option value="7">{{__("July")}}</option>
                                    <option value="8">{{__("August")}}</option>
                                    <option value="9">{{__("September")}}</option>
                                    <option value="10">{{__("October")}}</option>
                                    <option value="11">{{__("November")}}</option>
                                    <option value="12">{{__("December")}}</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label for="edit-end-month">{{ __('End Month') }} <span class="text-danger">*</span></label>
                                <select name="end_month" id="edit-end-month" class="form-control">
                                    <option value="1">{{__("January")}}</option>
                                    <option value="2">{{__("February")}}</option>
                                    <option value="3">{{__("March")}}</option>
                                    <option value="4">{{__("April")}}</option>
                                    <option value="5">{{__("May")}}</option>
                                    <option value="6">{{__("June")}}</option>
                                    <option value="7">{{__("July")}}</option>
                                    <option value="8">{{__("August")}}</option>
                                    <option value="9">{{__("September")}}</option>
                                    <option value="10">{{__("October")}}</option>
                                    <option value="11">{{__("November")}}</option>
                                    <option value="12">{{__("December")}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                        <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        function formSuccessFunction() {
            $('[data-repeater-item]').slice(2).remove();
        }
    </script>
@endsection
