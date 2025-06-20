@extends('layouts.master')

@section('title')
    {{ __('faqs') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('faqs') }}
            </h3>
        </div>

        <div class="row">
            @if (Auth::user()->can('faqs-create'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('create') . ' ' . __('faqs') }}
                            </h4>
                            <form class="create-form pt-3" id="create-form" action="{{route('faqs.store')}}" method="POST" novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('description', null, ['required','rows' => '2', 'placeholder' => __('description'), 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                                {{-- <input class="btn btn-theme" type="submit" value={{ __('submit') }}> --}}
                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if (Auth::user()->can('faqs-list'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('list') . ' ' . __('faqs') }}
                            </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                           data-url="{{ route('faqs.show',1) }}" data-click-to-select="true"
                                           data-side-pagination="server" data-pagination="true"
                                           data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                           data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                           data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                                           data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                           data-sort-order="desc" data-maintain-selected="true"
                                           data-export-data-type='all' data-show-export="true"
                                           data-export-options='{ "fileName": "faqs-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                                           data-query-params="queryParams" data-escape="true">
                                        <thead>
                                        <tr>
                                            <th scope="col" data-field="id" data-sortable="true" data-visible="false"> {{ __('id') }} </th>
                                            <th scope="col" data-field="no"> {{ __('no.') }} </th>
                                            <th scope="col" data-field="title">{{ __('title') }} </th>
                                            <th scope="col" data-field="description">{{ __('description') }}</th>
                                            @if (Auth::user()->can('faqs-edit') || Auth::user()->can('faqs-delete'))
                                                <th data-events="faqsEvents" data-width="150" scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
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
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit') . ' ' . __('faqs') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="edit-form" action="{{url('faqs')}}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control', 'id' => 'edit-title']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('description') }}</label>
                                {!! Form::textarea('description', null, ['placeholder' => __('description'), 'class' => 'form-control', 'id' => 'edit-description']) !!}
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
