@extends('layouts.master')

@section('title')
    {{ __('certificate') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('certificate') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('staff') }}
                        </h4>
                        <form action="{{ url('certificate/staff-certificate') }}" class="pt-3" target="_blank" method="post">
                            @csrf
                            <div class="form-group col-sm-12 col-md-4">
                                    <label class="filter-menu">{{ __('certificate') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('certificate_template_id', $certificateTemplates, null, ['class' => 'form-control','id' => 'certificate_template_id', 'placeholder' => 'Select '.__('certificate').' '.__('template'), 'required' => 'required']) !!}
                                </div>
                            <div class="row" id="toolbar">
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <table aria-describedby="mydesc" class='table' id='table_list'
                                        data-toggle="table" data-url="{{ route('staff.show.all') }}" data-click-to-select="true"
                                        data-side-pagination="server" data-pagination="true"
                                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                        data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                                        data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                        data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                        data-export-options='{ "fileName": "students-list-<?= date('d-m-y') ?>" ,"ignoreColumn": ["operate"]}' data-query-params="studentDetailsQueryParams"
                                        data-check-on-init="true" data-escape="true" data-response-handler="responseHandler">
                                        <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                            <th scope="col" data-field="no">{{ __('no.') }}</th>
                                            <th scope="col" data-field="id" data-visible="false">{{ __('User Id') }}</th>
                                            <th scope="col" data-field="full_name">{{ __('name') }}</th>
                                            <th scope="col" data-field="dob">{{ __('dob') }}</th>
                                            <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                            
                                            
                                            
                                            <th scope="col" data-field="gender">{{ __('gender') }}</th>
                                            
                                            <th scope="col" data-field="email">{{ __('guardian') . ' ' . __('email') }}</th>
                                            
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group col-12">
                                <textarea id="user_id" name="user_id" style="display: none"></textarea>
                                <input type="submit" class="btn btn-theme mt-4 float-right" value="{{ __('Generate') }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var $tableList = $('#table_list')
        var selections = []
        var user_list = [];

        function responseHandler(res) {
            $.each(res.rows, function (i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        $(function () {
            $tableList.on('check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table',
                function (e, rowsAfter, rowsBefore) {
                    user_list = [];
                    var rows = rowsAfter
                    if (e.type === 'uncheck-all') {
                        rows = rowsBefore
                    }
                    var ids = $.map(!$.isArray(rows) ? [rows] : rows, function (row) {
                        return row.id
                    })

                    var func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference'
                    selections = window._[func](selections, ids)
                    selections.forEach(element => {
                        user_list.push(element);
                    });
                    $('textarea#user_id').val(user_list);
                })
        })
    </script>
@endsection
