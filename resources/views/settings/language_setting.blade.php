@extends('layouts.master')

@section('title')
    {{ __('language_settings') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('language_settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="create-form" data-success-function="formSuccessFunction" action="{{ route('language.store') }}" novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="name">{{ __('language_name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="name" type="text" required placeholder="{{ __('language_name') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="code">{{ __('language_code') }} <span class="text-danger">*</span></label>
                                    <input name="code" id="code" type="text" required placeholder="{{ __('language_code') }}" class="form-control"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>{{ __('upload_file') }} <span class="text-danger">*</span></label>
                                    <input type="file" name="file" class="file-upload-default" accept="application/json"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" accept="application/json" disabled="" placeholder="{{ __('upload_file') }}" aria-label=""/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <br>
                                    <a class="btn btn-success" href="{{ url('language-sample') }}">{{ __('download_sample') }}</a>

                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <input class="form-check-input mt-0 mx-1" type="checkbox" value="1" name="rtl" id="rtl" aria-label="Checkbox for following text input">
                                    <label class="mx-4" for="rtl">{{ __('Is RTL') }}</label>
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
                            {{ __('list') . ' ' . __('language') }}
                        </h4>

                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                       data-url="{{ url('language-list') }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200,All]" data-search="true"
                                       data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                       data-fixed-columns="false" data-fixed-number="1" data-fixed-right-number="1"
                                       data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                       data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "language-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                                       data-query-params="queryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="false" data-visible="false"> {{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="name">{{ __('name') }}</th>
                                        <th scope="col" data-field="code" data-sortable="false">{{ __('code') }}</th>
                                        <th scope="col" data-field="is_rtl" data-sortable="false" data-formatter="yesAndNoStatusFormatter">{{ __('Is RTL') }}</th>
                                        <th scope="col" data-field="status" data-formatter="activeStatusFormatter" data-sortable="false" data-visible="false">{{ __('status') }}</th>
                                        <th data-events="languageSettingsEvents" scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
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

    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit') . ' ' . __('language') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form class="edit-form" data-success-function="formSuccessFunction" action="{{ url('language') }}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('language_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('language_name'), 'class' => 'form-control', 'id' => 'edit_name']) !!}

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('language_code') }} <span class="text-danger">*</span></label>
                                {!! Form::text('code', null, ['required', 'placeholder' => __('language_code'), 'class' => 'form-control', 'id' => 'edit_code']) !!}

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('upload_file') }}</label><br>
                                <input type="file" name="file" class="file-upload-default"/>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('upload_file') }}" aria-label=""/>
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 mx-1.5">
                                {!! Form::checkbox('rtl', 1,false, ['id' => 'edit_rtl']) !!}
                                <label for="edit_rtl">{{ __('Is RTL') }}</label>
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
@section('script')
    <script>
        function formSuccessFunction() {
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    </script>
@endsection
