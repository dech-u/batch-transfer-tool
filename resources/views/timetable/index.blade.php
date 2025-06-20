@extends('layouts.master')

@section('title')
    {{ __('timetable') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('create') . ' ' . __('timetable') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="edit-form edit-form-without-reset timetable-settings-form" action="{{ route('timetable.settings') }}" method="POST">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="starting_time">{{ __('Starting Time') }} <span class="text-danger">*</span></label>
                                    <input type="time" name="timetable_start_time" id="starting_time" class="form-control" value="{{ $timetableData['timetable_start_time'] ?? ""}}"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="ending_time">{{ __('Ending Time') }} <span class="text-danger">*</span></label>
                                    <input type="time" name="timetable_end_time" id="ending_time" class="form-control" value="{{ $timetableData['timetable_end_time'] ?? ""}}"/>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="duration">{{ __('Timeslot Duration') }} <small>({{__('in Minutes')}})</small><span class="text-danger">*</span></label>
                                    <input type="number" name="timetable_duration" id="duration" class="form-control" min="1" value="{{ $timetableData['timetable_duration'] ?? ""}}"/>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" id="generate" class="btn btn-theme">{{__('Generate')}}</button>
                            </div>
                        </form>
                        <div class="row">
                            <div class="row" id="toolbar">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label class="filter-menu" for="">{{ __('medium') }}</label>
                                    {!! Form::select('medium_id', $mediums, null, ['class' => 'form-control','id' => 'filter_medium_id', 'placeholder' => __('select_medium')]) !!}
                                </div>
                            </div>
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list'
                                       data-toggle="table" data-url="{{ route('timetable.show',[1]) }}"
                                       data-click-to-select="true" data-side-pagination="server"
                                       data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                       data-search="true" data-toolbar="#toolbar"
                                       data-show-columns="true" data-show-refresh="true"
                                       data-fixed-columns="false" data-fixed-number="2"
                                       data-fixed-right-number="1" data-trim-on-search="false"
                                       data-mobile-responsive="true" data-sort-name="id"
                                       data-query-params="timetableQueryParams" data-sort-order="desc"
                                       data-maintain-selected="true" data-export-data-type='all' data-show-export="true"
                                       data-export-options='{ "fileName": "timetable-list-<?= date(' d-m-y') ?>" }'>
                                    <thead>
                                    <tr>
                                        <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                        <th scope="col" data-field="no">{{ __('no.') }}</th>
                                        <th scope="col" data-field="full_name">{{ __('Class Section') }}</th>
                                        <th scope="col" data-field="Monday" data-formatter="timetableDayFormatter">{{ __('Monday') }}</th>
                                        <th scope="col" data-field="Tuesday" data-formatter="timetableDayFormatter">{{ __('Tuesday') }}</th>
                                        <th scope="col" data-field="Wednesday" data-formatter="timetableDayFormatter">{{ __('Wednesday') }}</th>
                                        <th scope="col" data-field="Thursday" data-formatter="timetableDayFormatter">{{ __('Thursday') }}</th>
                                        <th scope="col" data-field="Friday" data-formatter="timetableDayFormatter">{{ __('Friday') }}</th>
                                        <th scope="col" data-field="Saturday" data-formatter="timetableDayFormatter">{{ __('Saturday') }}</th>
                                        <th scope="col" data-field="Sunday" data-formatter="timetableDayFormatter">{{ __('Sunday') }}</th>
                                        <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
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
@endsection
