@extends('layouts.master')

@section('title')
    {{ __('class_group') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('class_group') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('class_group') }}
                        </h4>
                        <form class="pt-3" id="create-form" action="{{ route('class-group.store') }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" placeholder="{{ __('name') }}" required class="form-control"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                    <textarea name="description" required class="form-control"></textarea>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('image') }} <span class="text-danger">*</span> <span class="text-info text-small">(308px*397px)</span> </label>
                                    <input type="file" required name="image" class="file-upload-default" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/svg"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}"/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('Classes') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('class_ids[]', $classes, null, ['class' => 'form-control select2-dropdown select2-hidden-accessible','multiple','required']) !!}
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
                        <h4 class="card-title">{{ __('list') . ' ' . __('class_group') }}</h4>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('class-group.show',[1]) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true"
                               data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                               data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                               data-export-data-type='all' data-query-params="SubjectQueryParams"
                               data-toolbar="#toolbar" data-export-options='{ "fileName": "class-group-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}' data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="image" data-formatter="imageFormatter">{{ __('image') }}</th>
                                <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                <th scope="col" data-field="description" data-events="tableDescriptionEvents" data-formatter="descriptionFormatter" data-sortable="false">{{ __('description') }}</th>
                                <th scope="col" data-field="classes" data-sortable="false">{{ __('Classes') }}</th>
                                <th scope="col" data-field="operate" data-events="classGroupEvents" data-escape="false">{{ __('action') }}</th>
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
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('class_group') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 subject-edit-form" id="edit-form" action="{{ url('class-group') }}"
                              novalidate="novalidate">
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                

                                <div class="form-group">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>


                                <div class="form-group">
                                    <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                                </div>


                                <div class="form-group">
                                    <label>{{ __('image') }} <span class="text-info text-small">(308px*397px)</span></label>
                                    <input type="file" id="image" name="image" class="file-upload-default" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/svg"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" id="image" class="form-control" disabled="" value=""/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                    <img src="" id="edit_image" class="img-fluid w-25" alt="">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('Classes') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('class_ids[]', $classes, null, ['class' => 'form-control select2-dropdown select2-hidden-accessible','multiple','required','id' => 'edit_class_ids']) !!}
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
