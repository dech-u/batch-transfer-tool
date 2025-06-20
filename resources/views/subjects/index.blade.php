@extends('layouts.master')

@section('title')
    {{ __('subject') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('subject') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('subject') }}
                        </h4>
                        <small class="text-danger">{{__("Note : Subject Name,Code & Type should be Unique for Medium")}}</small>
                        <form class="pt-3 subject-create-form" id="create-form" action="{{ route('subjects.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>{{ __('medium') }} <span class="text-danger">*</span></label>
                                <div class="col-12 d-flex row">
                                    @foreach ($mediums as $medium)
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="medium_id" id="medium_{{ $medium->id }}" value="{{ $medium->id }}">
                                                {{ $medium->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                <input name="name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                            </div>

                            <div class="form-group">
                                <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="type" id="theory" value="Theory">{{__("Theory")}}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="type" id="practical" value="Practical">{{__("Practical")}}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('subject_code') }}</label>
                                <input name="code" type="text" placeholder="{{ __('subject_code') }}" class="form-control"/>
                            </div>

                            <div class="form-group">
                                <label>{{ __('bg_color') }} <span class="text-danger">*</span></label>
                                <input name="bg_color" type="text" placeholder="{{ __('bg_color') }}" class="color-picker" autocomplete="off"/>
                            </div>

                            <div class="form-group">
                                <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="file-upload-default" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/svg"/>
                                <div class="input-group col-xs-12">
                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}"/>
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                    </span>
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
                        <h4 class="card-title">{{ __('list') . ' ' . __('subject') }}</h4>
                        <div id="toolbar">
                            <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                <option value="">All</option>
                                @foreach ($mediums as $medium)
                                    <option value="{{ $medium->id }}">{{ $medium->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('subjects.show',[1]) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                               data-export-data-type='all' data-query-params="SubjectQueryParams"
                               data-toolbar="#toolbar" data-export-options='{ "fileName": "subject-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}' data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                <th scope="col" data-field="code" data-sortable="true">{{ __('subject_code') }}</th>
                                <th scope="col" data-field="bg_color" data-formatter="bgColorFormatter">{{ __('bg_color') }}</th>
                                <th scope="col" data-field="medium.name">{{ __('medium') }}</th>
                                <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                <th scope="col" data-field="type" data-sortable="true">{{ __('type') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-events="subjectEvents" data-escape="false">{{ __('action') }}</th>
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
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('subject') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 subject-edit-form" id="edit-form" action="{{ url('subject') }}"
                              novalidate="novalidate">
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                <div class="form-group">
                                    <label>{{ __('medium') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex responsive-medium-list">
                                        @foreach ($mediums as $medium)
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input edit" name="medium_id" id="edit_medium_{{ $medium->id }}" value="{{ $medium->id }}"> {{ $medium->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('type') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input edit" name="type" id="edit_theory" value="Theory">
                                                {{ __('Theory') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input edit" name="type" id="edit_practical" value="Practical">
                                                {{ __('Practical') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('subject_code') }}</label>
                                    <input name="code" id="edit_code" type="text" placeholder="{{ __('subject_code') }}" class="form-control"/>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('bg_color') }} <span class="text-danger">*</span></label>
                                    <input name="bg_color" id="edit_bg_color" type="text" placeholder="{{ __('bg_color') }}" class="color-picker" autocomplete="off"/>
                                </div>

                                <div class="form-group">
                                    <label>{{ __('image') }}</label>
                                    <input type="file" id="edit_image" name="image" class="file-upload-default" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/svg"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" id="edit_image" class="form-control" disabled="" value=""/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
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
        </div>
    </div>
@endsection
