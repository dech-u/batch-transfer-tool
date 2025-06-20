@extends('layouts.master')

@section('title')
    {{ __('expense_category') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('expense_category') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('expense_category') }}
                        </h4>
                        <form class="pt-3" id="create-form" action="{{ route('expense-category.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="name">{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="name" type="text" required placeholder="{{ __('name') }}" class="form-control"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="description">{{ __('description') }} </label>
                                    <textarea name="description" id="description" placeholder="{{ __('description') }}" class="form-control"></textarea>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('list') . ' ' . __('expense_category') }}</h4>

                        <div class="col-12 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('expense-category.show',[1]) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                               data-export-data-type='all' data-query-params="queryParams"
                               data-toolbar="#toolbar" data-export-options='{ "fileName": "expense-category-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                <th scope="col" data-field="description" data-sortable="true">{{ __('description') }}</th>
                                <th scope="col" data-field="operate" data-events="expenseCategoryEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('expense_category') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-form" id="" action="{{ url('expense-category') }}"
                              novalidate="novalidate">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id" value=""/>

                                <div class="form-group">
                                    <label for="edit_name">{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" required id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>

                                <div class="form-group">
                                    <label for="edit_description">{{ __('description') }}</label>
                                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
                                    <input class="btn btn-theme" type="submit" value="{{ __('submit') }}"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
