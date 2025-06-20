@extends('layouts.master')

@section('title')
    {{ __('leave') }} {{ __('settings') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('leave') }} {{ __('settings') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3 section-create-form" id="create-form" action="{{ route('leave-master.store') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('total_leaves_per_month') }} <span class="text-danger">*</span></label>
                                    <input name="leaves" type="number" min="0" max="30" placeholder="{{ __('total_leaves_per_month') }}" class="form-control" required/>
                                </div>

                                <div class="form-group col-md-4 col-sm-12">
                                    <label for="roll-number-order">{{__("holiday_days")}} <span class="text-danger">*</span></label>
                                    {!! Form::select('holiday_days[]', ["Sunday" => "Sunday", "Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday"], $settings['holiday_days'] ?? '', ['required','class' => 'form-control select2-dropdown select2-hidden-accessible','multiple']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="">{{ __('session_year') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('session_year_id', $sessionYear, null, ['required','class' => 'form-control', 'placeholder' => __('session_year')]) !!}
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
                        <h4 class="card-title">
                            {{ __('list') }} {{ __('leaves') }} {{ __('settings') }}
                        </h4>
                        <table aria-describedby="mydesc" class='table' id='table_list'
                               data-toggle="table" data-url="{{ route('leave-master.show',[1]) }}"
                               data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true"
                               data-show-refresh="true" data-fixed-columns="false" data-fixed-number="2"
                               data-fixed-right-number="1" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true"
                               data-query-params="queryParams" data-show-export="true"
                               data-export-options='{"fileName": "leave-master-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'>
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{__('id')}}</th>
                                <th scope="col" data-field="no">{{__('no.')}}</th>
                                <th scope="col" data-field="leaves">{{__('leaves')}}</th>
                                <th scope="col" data-field="holiday">{{__('holiday')}}</th>
                                <th scope="col" data-field="session_year.name">{{__('session_year')}}</th>
                                <th scope="col" data-field="operate" data-events="leaveSettingsEvents">{{__('action')}}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{__('edit').' '.__('leave')}} {{ __('settings') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 section-edit-form" id="edit-form" action="{{ url('section') }}" novalidate="novalidate">
                            <input type="hidden" name="id" id="id" value=""/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('total_leaves_per_month') }} <span class="text-danger">*</span></label>
                                        <input name="leaves" type="number" min="0" max="30" placeholder="{{ __('total_leaves_per_month') }}" id="edit_leaves" class="form-control" required/>
                                    </div>
    
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="roll-number-order">{{__("holiday_days")}} <span class="text-danger">*</span></label>
                                        {!! Form::select('holiday_days[]', ["Sunday" => "Sunday", "Monday" => "Monday", "Tuesday" => "Tuesday", "Wednesday" => "Wednesday", "Thursday" => "Thursday", "Friday" => "Friday", "Saturday" => "Saturday"], $settings['holiday_days'] ?? '', ['required','class' => 'form-control select2-dropdown select2-hidden-accessible','multiple', 'id' => 'edit_holiday_days']) !!}
                                    </div>
    
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('session_year') }} <span class="text-danger">*</span></label>
                                        {!! Form::select('session_year_id', $sessionYear, null, ['required','class' => 'form-control', 'placeholder' => __('session_year'), 'id' => 'edit_session_year_id']) !!}
                                    </div>
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
