@extends('layouts.master')

@section('title')
    {{ __('Session Years') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Session Years') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <span class="text-danger mt-4 ml-4">
                        {{ __('note If you change the session year the chat history will be deleted') }}
                    </span>
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create Session Years') }}
                        </h4>
                        <form action="{{ route('session-year.store') }}" class="create-form pt-3 " id="formdata" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('start_date', null, ['required', 'placeholder' => __('start_date'), 'class' => 'datepicker-popup form-control','autocomplete'=>'off']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('end_date', null, ['required', 'placeholder' => __('end_date'), 'class' => 'datepicker-popup form-control','autocomplete'=>'off']) !!}
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
                            {{ __('List Session Years') }}
                        </h4>
                        <div class="col-12 mt-4 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('session-year.show',1) }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                                       data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "session-year-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                                       data-query-params="queryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{__('id')}}</th>
                                        <th scope="col" data-field="no">{{__('no.')}}</th>
                                        <th scope="col" data-field="name">{{__('name')}}</th>
                                        <th scope="col" data-field="start_date" data-formatter="dateFormatter" data-sortable="true">{{__('start_date')}}</th>
                                        <th scope="col" data-field="end_date" data-formatter="dateFormatter" data-sortable="true">{{__('end_date')}}</th>
                                        <th scope="col" data-field="default" data-sortable="true" data-formatter="yesAndNoStatusFormatter">{{__('default')}}</th>
                                        <th data-events="sessionYearEvents" scope="col" data-field="operate" data-escape="false">{{__('action')}}</th>
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
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('Edit Session Years') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>

                <form action="{{ url('session-year') }}" class="edit-form pt-3" data-success-function="formSuccessFunction" id="formdata" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-4">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control', 'id' => 'edit-name']) !!}
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4">
                                <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('start_date', null, ['required', 'placeholder' => __('start_date'), 'class' => 'datepicker-popup form-control' , 'id' => 'edit-start-date']) !!}
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4">
                                <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('end_date', null, ['required', 'placeholder' => __('end_date'), 'class' => 'datepicker-popup form-control' , 'id' => 'edit-end-date']) !!}
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
        const formSuccessFunction = () => {
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        }
    </script>
@endsection
