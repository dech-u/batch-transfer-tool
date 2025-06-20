@extends('layouts.master')

@section('title')
    {{ __('expense') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('expense') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('expense') }}
                        </h4>
                        <form class="pt-3" id="create-form" action="{{ route('expense.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-5">
                                    <label>{{ __('select') }} {{ __('category') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('category_id', $expenseCategory, null, ['required','class' => 'form-control','placeholder' => __('select') .' '. __('category')]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-5">
                                    <label for="title">{{ __('title') }} <span class="text-danger">*</span></label>
                                    <input name="title" id="title" type="text" placeholder="{{ __('title') }}" class="form-control" required/>
                                </div>

                                <div class="form-group col-sm-12 col-md-2">
                                    <label for="ref_no">{{ __('reference_no') }}</label>
                                    <input name="ref_no" id="ref_no" type="text" placeholder="{{ __('reference_no') }}" class="form-control"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                    <input name="amount" id="amount" type="number" placeholder="{{ __('Amount') }}" class="form-control" required/>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="date">{{ __('date') }} <span class="text-danger">*</span></label>
                                    <input name="date" id="date" type="text" placeholder="{{ __('date') }}" class="datepicker-popup-no-future form-control" autocomplete="off" required/>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="description">{{ __('description') }} </label>
                                    <textarea name="description" id="description" placeholder="{{ __('description') }}" class="form-control"></textarea>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="">{{ __('select') }} {{ __('session_year') }}</label>
                                    {!! Form::select('session_year_id', $sessionYear, $current_session_year->id, ['required','class' => 'form-control']) !!}
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
                        <h4 class="card-title">{{ __('list') . ' ' . __('expense') }}</h4>
                        <div class="row" id="toolbar">

                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu">{{ __('session_year') }}</label>
                                {!! Form::select('session_year_id', $sessionYear, $current_session_year->id, ['class' => 'form-control', 'id' => 'filter_session_year_id']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu">{{ __('category') }}</label>
                                {!! Form::select('category_id', $expenseCategory + ['salary' => __('salary')], null, ['class' => 'form-control', 'id' => 'filter_category_id', 'placeholder' => __('all')]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-4">
                                <label class="filter-menu"> {{ __('month') }}</label>
                                {!! Form::select('month', $months, null, ['class' => 'form-control', 'id' => 'filter_month', 'placeholder' => __('all')]) !!}
                            </div>

                        </div>

                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('expense.show',[1]) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="date" data-sort-order="desc" data-maintain-selected="true"
                               data-export-data-type='all' data-query-params="ExpenseQueryParams"
                               data-toolbar="#toolbar" data-export-options='{ "fileName": "expense-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-show-footer="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="ref_no" data-sortable="false">{{ __('reference_no') }}</th>
                                <th scope="col" data-field="title" data-sortable="false">{{ __('title') }}</th>
                                <th scope="col" data-field="category.name" data-sortable="false">{{ __('category') }}</th>
                                <th scope="col" data-field="description" data-sortable="false">{{ __('description') }}</th>
                                <th scope="col" data-field="date" data-formatter="dateFormatter" data-sortable="false" data-footer-formatter="totalFormatter">{{ __('date') }}</th>
                                <th scope="col" data-field="amount" data-sortable="false" data-formatter="amountFormatter" data-footer-formatter="totalAmountFormatter">{{ __('Amount') }}</th>
                                <th scope="col" data-field="operate" data-events="expenseEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('expense') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-form" id="" action="{{ url('expense') }}"
                              novalidate="novalidate">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="edit_id" value=""/>
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-5">
                                        <label>{{ __('select') }} {{ __('category') }} <span class="text-danger">*</span></label>
                                        {!! Form::select('category_id', $expenseCategory, null, ['required','class' => 'form-control','placeholder' => __('select') .' '. __('category'), 'id' => 'edit_category_id']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-5">
                                        <label for="edit_title">{{ __('title') }} <span class="text-danger">*</span></label>
                                        <input name="title" id="edit_title" type="text" placeholder="{{ __('title') }}" class="form-control" required/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-2">
                                        <label for="edit_ref_no">{{ __('reference_no') }}</label>
                                        <input name="ref_no" id="edit_ref_no" type="text" placeholder="{{ __('reference_no') }}" class="form-control"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label for="edit_amount">{{ __('Amount') }} <span class="text-danger">*</span></label>
                                        <input name="amount" id="edit_amount" type="number" placeholder="{{ __('Amount') }}" class="form-control" required/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label for="edit_date">{{ __('date') }} <span class="text-danger">*</span></label>
                                        <input name="date" id="edit_date" type="text" placeholder="{{ __('date') }}" class="datepicker-popup-no-future form-control" required/>
                                    </div>


                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="edit_description">{{ __('description') }}</label>
                                        <textarea name="description" id="edit_description" class="form-control"></textarea>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label for="">{{ __('select') }} {{ __('session_year') }}</label>
                                        {!! Form::select('session_year_id', $sessionYear, $current_session_year->id, ['required','class' => 'form-control','id' => 'edit_session_year_id']) !!}
                                    </div>
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
@section('js')
    <script>

    </script>
@endsection
