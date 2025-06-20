@extends('layouts.master')

@section('title')
    {{ __('package') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('package') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title float-left">
                            {{ __('list') . ' ' . __('package') }}
                        </h4>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 text-right">
                                <a href="{{ route('package.create') }}" class="btn btn-theme btn-sm">{{ __('create') }}
                                    {{ __('package') }}</a>
                            </div>
                        </div>
                        <hr>
                        <ul class="text-danger">
                            <li>
                                <span>{{ __('To Reorder the Package, Drag the Table Row Up and Down and then Click on Update Rank') }}.</span>
                            </li>
                        </ul>
                        <div class="row">
                            <div id="toolbar">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label class="filter-menu">{{ __('type') }}</label>
                                    {!! Form::select(
                                        'type',
                                        [
                                            '' => __('all'),
                                            '0' => __('prepaid'),
                                            '1' => __('postpaid'),
                                        ],
                                        null,
                                        ['class' => 'form-control', 'id' => 'type'],
                                    ) !!}
                                </div>
                            </div>
                            <div class="col-12 text-right mt-4">
                                <b><a href="#" class="table-list-type active mr-2" data-id="0">{{ __('all') }}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{ __('Trashed') }}</a>
                            </div>
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table reorder-table-row' id='table_list'
                                       data-toggle="table" data-url="{{ route('package.show', 1) }}"
                                       data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="rank" data-use-row-attr-func="true"
                                       data-reorderable-rows="true" data-sort-order="asc" data-maintain-selected="true"
                                       data-export-data-type='all'
                                       data-export-options='{ "fileName": "{{ __('list') . ' ' . __('package') }}-<?= date('
                                    d-m-y') ?>" ,"ignoreColumn":["operate"]}' data-show-export="true"
                                       data-query-params="packageQueryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="name">{{ __('name') }}</th>
                                        <th scope="col" data-field="description">{{ __('description') }}</th>
                                        <th scope="col" data-field="status" data-formatter="packageTypeFormatter">{{ __('type') }}</th>
                                        <th scope="col" data-field="status" data-formatter="yesAndNoStatusFormatter">{{ __('published') }}</th>
                                        <th scope="col" data-field="highlight" data-formatter="yesAndNoStatusFormatter">{{ __('highlight') }}</th>
                                        <th scope="col" data-field="days">{{ __('days') }}</th>
                                        <th scope="col" data-field="used_by">{{ __('used_by')}}</th>
                                        <th scope="col" data-field="package_feature" data-visible="false" data-formatter="packageFeatureFormatter">{{ __('features')}}</th>
                                        <th scope="col" data-field="operate" data-events="packageEvents" data-escape="false">{{ __('action') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                                <div class="form-group col-sm-12 col-md-4 mt-1 btn-update-rank d-none d-md-block">
                                    <button id="reorder" class="btn btn-theme">{{ __('update_rank') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $('#table_list').bootstrapTable()
            $('.table-list-type').click(function (e) {
                e.preventDefault();
                var dataId = $(this).data('id');

                // If "Trashed" is selected, show the reorder button
                if (dataId == 1) {
                    $('#reorder').hide(); // Hide the Update Rank button
                } else {
                    $('#reorder').show(); // Show the Update Rank button
                }

                // Highlight the active link
                $('.table-list-type').removeClass('active');
                $(this).addClass('active');

            });
            $('#reorder').click(function () {
                let idByOrder = JSON.stringify($('#table_list').bootstrapTable('getData').map((row) => row.id));
                let data = new FormData();
                data.append('ids', idByOrder);
                data.append('_method', 'PATCH');
                ajaxRequest('POST', baseUrl + '/package/change/rank', data, null, (response) => {
                    $('#table_list').bootstrapTable('refresh');
                    showSuccessToast(response.message)
                }, (response) => {
                    showErrorToast(response.message);
                })
            })
        })

    </script>
@endsection
