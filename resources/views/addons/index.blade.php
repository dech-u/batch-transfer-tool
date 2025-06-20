@extends('layouts.master')

@section('title')
    {{ __('addons') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('addons') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create').' '.__('addons') }}
                        </h4>
                        <span class="text-danger">
                            * {{ __('The validity of add-on is determined by the expiration date of package') }}.
                        </span>
                        <form class="pt-3 mt-2 create-form" id="formdata" action="{{ route('addons.store') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-6 col-md-4">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" placeholder="{{ __('name') }}" class="form-control" required/>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="">{{ __('price') }} <span class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control" required placeholder="{{ __('price') }}" min="1">
                                </div>
                                <div class="col-sm-12 col-md-12 mt-3">
                                    <label for="">{{ __('features') }}</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                @foreach ($features as $feature)
                                    <div class="form-group col-sm-12 col-md-3">
                                        <input id="{{ __($feature->name) }}" class="feature-radio" type="radio" name="feature_id" value="{{ $feature->id }}"/>
                                        <label class="feature-list text-center" for="{{ __($feature->name) }}">{{ __($feature->name) }}</label>
                                    </div>
                                @endforeach
                            </div>
                            {{-- <input class="btn btn-theme" id="create-btn" type="submit" value={{ __('submit') }}> --}}
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list').' '.__('addons') }}
                        </h4>
                        <div class="col-12 text-right">
                            <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('addons.show',1) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true"
                               data-query-params="queryParams" data-show-export="true"
                               data-export-options='{"fileName": "addons-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                                data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{__('id')}}</th>
                                <th scope="col" data-field="no">{{__('no.')}}</th>
                                <th scope="col" data-field="name">{{__('name')}}</th>
                                <th scope="col" data-field="feature.name">{{__('feature')}}</th>
                                <th scope="col" data-field="price">{{__('price')}}</th>
                                <th scope="col" data-field="status" data-formatter="yesAndNoStatusFormatter">{{__('status')}}</th>
                                <th scope="col" data-field="operate" data-events="addonEvents" data-escape="false">{{__('action')}}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{__('edit').' '.__('addon')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-form" id="formdata" action="{{ url('addon') }}" novalidate="novalidate">
                            <input type="hidden" name="id" id="edit_id" value=""/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{__('name')}} <span class="text-danger">*</span></label>
                                        <input name="name" id="edit_name" type="text" placeholder="{{__('name')}}" class="form-control" required/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="">{{ __('price') }} <span class="text-danger">*</span></label>
                                        <input type="number" min="0" name="price" id="edit_price" class="form-control" placeholder="{{ __('price') }}" required>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('feature') }}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach ($features as $feature)
                                    <div class="form-group col-sm-12 col-md-4">
                                        <input id="{{ __($feature->id) }}" class="feature-radio" type="radio" class="feature_id" name="feature_id" value="{{ $feature->id }}"/>
                                        <label class="feature-list text-center" for="{{ __($feature->id) }}">{{ __($feature->name) }}</label>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('close')}}</button>
                                <input class="btn btn-theme" type="submit" value={{ __('submit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
