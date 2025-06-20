@extends('layouts.master')

@section('title')
    {{ __('Fees Type')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Fees Type')}}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create Fees Type')}}
                        </h4>
                        <form id="create-form" class="pt-3 create-form" action="{{ url('fees-type') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12 col-lg-6">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-md-12 col-lg-6">
                                    <label>{{ __('description') }} </label>
                                    {!! Form::textarea('description', null, ['placeholder' => __('description'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            {{-- <input class="btn btn-theme" type="submit" value={{ __('submit') }}> --}}
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('List Fees')}}
                        </h4>
                        <div class="row">
                            <div class="col-12 text-right">
                                <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                            </div>
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('fees-type.show',1) }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                                       data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-query-params="queryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{__('id')}}</th>
                                        <th scope="col" data-field="no">{{__('no.')}}</th>
                                        <th scope="col" data-field="name" data-sortable="true">{{__('name')}}</th>
                                        <th scope="col" data-field="description" data-sortable="true">{{__('description')}}</th>
                                        <th scope="col" data-events="FeesTypeActionEvents" data-field="operate" data-escape="false">{{__('action')}}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{__('Edit Fees Type')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-close"></i></span>
                        </button>
                    </div>
                    <form id="edit-form" class="pt-3 edit-form" action="{{ url('fees-type') }}">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{__('name')}} <span class="text-danger">*</span></label>
                                {!! Form::text('edit_name',null,['required','class' => 'form-control edit_name','id' => 'edit_name','placeholder' => __('name')]) !!}
                            </div>

                            <div class="form-group">
                                <label for="name">{{__('description')}} </label>
                                {!! Form::textarea('edit_description',null,array('class'=>'form-control edit_description','id'=>'edit_description','placeholder'=>__('description'))) !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
                            <input class="btn btn-theme" type="submit" value={{ __('submit') }} />
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection
