@extends('layouts.master')

@section('title')
    {{ __('guidance') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('guidance') }}
            </h3>
        </div>

        <div class="row">
            @if (Auth::user()->can('guidance-create'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('create') . ' ' . __('guidance') }}
                            </h4>
                            <form class="create-form pt-3" id="create-form" action="{{route('guidances.store')}}" method="POST" novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control', 'maxlength' => '30']) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-9">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('link', null, ['required', 'placeholder' => __('link'), 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if (Auth::user()->can('guidance-list'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('list') . ' ' . __('guidance') }}
                            </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                           data-url="{{ route('guidances.show',1) }}" data-click-to-select="true"
                                           data-side-pagination="server" data-pagination="true"
                                           data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                           data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                           data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                                           data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                           data-sort-order="desc" data-maintain-selected="true"
                                           data-export-types='["txt","excel"]'
                                           data-export-options='{ "fileName": "guidance-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                                           data-query-params="queryParams" data-escape="true">
                                        <thead>
                                        <tr>
                                            <th scope="col" data-field="id" data-sortable="true" data-visible="false"> {{ __('id') }} </th>
                                            <th scope="col" data-field="no"> {{ __('no.') }} </th>
                                            <th scope="col" data-field="name">{{ __('name') }} </th>
                                            <th scope="col" data-formatter="linkFormatter" data-field="link">{{ __('link') }}</th>
                                            @if (Auth::user()->can('guidance-edit') || Auth::user()->can('guidance-delete'))
                                                <th data-events="guidanceEvents" data-width="150" scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                                            @endif
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit') . ' ' . __('guidance') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="edit-form" action="{{url('guidances')}}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control', 'id' => 'edit-name', 'maxlength' => '30']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                {!! Form::text('link', null, ['required','placeholder' => __('link'), 'class' => 'form-control', 'id' => 'edit-link']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
