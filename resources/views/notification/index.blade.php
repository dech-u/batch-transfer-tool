@extends('layouts.master')

@section('title')
    {{ __('notification') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_notification') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_notification') }}
                        </h4>
                        <form id="create-form" class="pt-3" action="{{ url('notifications') }}" method="POST"
                              novalidate="novalidate" data-success-function="formSuccessFunction">
                            @csrf
                            <div class="row">

                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('roles') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {{ Form::radio('type', 'Roles', true, ['id' => 'roles_type', 'class' => 'form-check-input type']) }}
                                                {{ __('Roles') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {{ Form::radio('type', 'OverDueFees', false, ['id' => 'over_due_fees_type', 'class' => 'form-check-input type']) }}
                                                {{ __('Over Due Fees') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-12 roles">
                                    <label for="">{{ __('roles') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('roles[]', $roles, null, ['class' => 'form-control select2-dropdown select2-hidden-accessible','multiple', 'id' => 'roles']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-12 over_due_fees_roles" style="display: none;">
                                    <label for="">{{ __('roles') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('roles[]', $over_due_fees_roles, null, ['class' => 'form-control select2-dropdown select2-hidden-accessible','multiple', 'id' => 'over_due_fees_roles']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label for="">{{ __('title') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('title', null, ['required','class' => 'form-control','placeholder' => __('title')]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label for="">{{ __('message') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('message', null, ['required','class' => 'form-control','placeholder' => __('message'), 'rows' => 3]) !!}
                                </div>

                                <textarea id="user_id" name="user_id" style="display: none"></textarea>

                                {{-- <textarea name="all_users" id="" cols="30" rows="10" hidden>{{ $all_users }}</textarea> --}}

                                <div class="form-group col-sm-6 col-md-12">
                                    <label>{{ __('image') }} </label>
                                    <input type="file" name="image" class="file-upload-default"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" id="image" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}"/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div>

                            </div>
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" id="reset" type="reset" value={{ __('reset') }}>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        
                        {{-- <div class="row" id="toolbar-user">
                            <div class="form-group col-sm-12 col-md-12">
                                <label class="filter-menu">{{ __('Class Section') }} <span class="text-danger">*</span></label>
                                
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                
                            </div>
                        </div> --}}
                        <table aria-describedby="mydesc" class='table' id='table_user_list' data-toggle="table"
                               data-url="{{ route('notifications.user.show') }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="false" data-show-refresh="true"
                               data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all' data-show-export="false" data-check-on-init="true" data-response-handler="responseHandler"
                               data-export-options='{ "fileName": "notification-list-<?= date('d-m-y') ?>","ignoreColumn":["operate"]}'
                               data-escape="true" data-query-params="NotificationUserqueryParams">
                            <thead>
                            <tr>
                                <th data-field="state" data-checkbox="true"></th>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="full_name">{{ __('name') }}</th>
                                
                                {{-- <th scope="col" data-field="operate" data-escape="false">{{ __('action') }} --}}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list_notification') }}
                        </h4>

                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('notifications.show', [1]) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                               data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                               data-export-options='{ "fileName": "notification-list-<?= date('d-m-y') ?>","ignoreColumn":["operate"]}'
                               data-escape="true" data-query-params="queryParams">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                <th scope="col" data-field="title">{{ __('title') }}</th>
                                <th scope="col" data-field="message" data-events="tableDescriptionEvents" data-formatter="descriptionFormatter">{{ __('message') }}</th>
                                <th scope="col" data-visible="false" data-field="send_to">{{ __('type') }}</th>
                                <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('.role-list').hide(500);
            $('.user-list').hide(500);
            $('.type').trigger('change');
        });
        function formSuccessFunction(response) {
            setTimeout(() => {
                // Reset selections
                selections = [];
                user_list = [];
                $('.roles').show();
                $('.over_due_fees_roles').hide();
                $('.type').trigger('change');
                $('#table_user_list').bootstrapTable('refresh');

                // reset form fields
                $('.form-control').val('');
            }, 500);
        }
        
        $('#reset').click(function (e) { 
            // e.preventDefault();
            $('.default-all').prop('checked', true);
            $('.type').trigger('change');
        });
        

        $('.type').change(function (e) {
            var selectedType = $('input[name="type"]:checked').val();
            e.preventDefault();
            $('.user_id').val('').trigger('change');

            $('.roles').hide();
            $('.over_due_fees_roles').hide();
            $('.user-list').hide();
            $('.role-list').hide();
            
            $('#table_user_list').bootstrapTable('uncheckAll');
            
            if (selectedType == 'Roles') {
                $('.roles').show();
                $('.role-list').show();

                $("#roles").prop("disabled", false); 
                $("#over_due_fees_roles").prop("disabled", true);

                // reset roles
                $("#roles").val('').trigger('change');

            } else if (selectedType == 'OverDueFees') {
                $('.over_due_fees_roles').show();
                $('.user-list').show();

                $("#roles").prop("disabled", true); 
                $("#over_due_fees_roles").prop("disabled", false);
                
                // reset roles
                $("#over_due_fees_roles").val('').trigger('change');
            }
            
        });

        $('#roles').change(function (e) { 
            e.preventDefault();
            $('#table_user_list').bootstrapTable('refresh');
        });

        $('#over_due_fees_roles').change(function (e) { 
            e.preventDefault();
            $('#table_user_list').bootstrapTable('refresh');
        });

        $('.type').change(function (e) {
            e.preventDefault();
            $('#table_user_list').bootstrapTable('refresh');
            
        });

        var $tableList = $('#table_user_list')
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
