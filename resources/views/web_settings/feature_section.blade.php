@extends('layouts.master')

@section('title')
    {{ __('feature_sections') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') }}  {{ __('feature_sections') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') }} {{ __('feature_sections') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" action="{{ route('web-settings.feature.sections.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-8">
                                    <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('heading', null, ['required', 'placeholder' => __('heading'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="form-group sections_data">
                                <div data-repeater-list="section_data">
                                    <div class="row file_type_div" id="file_type_div" data-repeater-item>
                                        <div class="form-group col-sm-12 col-md-4" id="file_name_div">
                                            <label>{{ __('feature') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="section[0][feature]" class="feature form-control"
                                                   placeholder="{{ __('feature') }}" required>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4" id="file_thumbnail_div">
                                            <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                            <textarea name="section[0][description]" required rows="5" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-3" id="file_div">
                                            <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                            <input type="file" name="section[0][image]" class="file form-control"
                                                   placeholder="" required>
                                        </div>

                                        <div class="form-group col-md-1 mt-4 mb-5">
                                            <button type="button" class="btn btn-inverse-danger btn-icon" data-repeater-delete><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-inverse-success"
                                            data-repeater-create>
                                        <i class="fa fa-plus"></i> {{ __('sections') }}
                                    </button>
                                </div>
                            </div>

                            <input class="btn btn-theme float-right" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') }} {{ __('sections') }}
                        </h4>

                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table reorder-table-row' id='table_list' data-toggle="table"
                                       data-url="{{ route('web-settings-section.show') }}" data-click-to-select="true"
                                       data-side-pagination="server" data-pagination="true"
                                       data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="rank" data-sort-order="asc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-use-row-attr-func="true" data-reorderable-rows="true" data-export-options='{ "fileName": "web-settings-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                                       data-query-params="webSettingsQueryParams" data-escape="true">
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="title">{{ __('title') }}</th>
                                        <th scope="col" data-field="heading">{{ __('heading') }}</th>
                                        <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
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
        $(document).ready(function() {
        
            function checkTableRows() {
                var rowCount = $('#table_list tbody tr:not(.no-records-found)').length;
                
                if (rowCount === 0) {
                    $('#reorder').addClass('d-none');
                } else {
                    $('#reorder').removeClass('d-none');
                }
            }
            
            checkTableRows();
        
            $('#table_list').on('load-success.bs.table', function() {
                checkTableRows();
            });
        });

        $(document).ready(function () {
            $('.sections_data').repeater({
                show: function () {
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                },

                isFirstItemUndeletable: true
            })
        });

        $(function () {
            $('#table_list').bootstrapTable()
            $('#reorder').click(function () {
                
                let idByOrder = JSON.stringify($('#table_list').bootstrapTable('getData').map((row) => row.id));
                let data = new FormData();
                data.append('ids', idByOrder);
                data.append('_method', 'PATCH');
                ajaxRequest('POST', baseUrl + '/web-settings/feature-section/change/rank', data, null, (response) => {
                    $('#table_list').bootstrapTable('refresh');
                    showSuccessToast(response.message)
                }, (response) => {
                    showErrorToast(response.message);
                })
            })
        })
    </script>
@endsection

