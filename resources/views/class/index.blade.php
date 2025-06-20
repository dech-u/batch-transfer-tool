@extends('layouts.master')

@section('title')
    {{ __('Class') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Class') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('Class') }}
                        </h4>
                        <form class="pt-3 class-create-form" id="create-form" action="{{ route('class.store') }}" method="POST" data-pre-submit-function="classValidation" data-success-function="successFunction" novalidate="novalidate">
                            <div class="form-group">
                                <label>{{ __('medium') }} <span class="text-danger">*</span></label>
                                <div class="col-12 d-flex row">
                                    @foreach ($mediums as $medium)
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" name="medium_id" id="medium_{{ $medium->id }}" value="{{ $medium->id }}" required="required">
                                                {{ $medium->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name">{{ __('name') }} <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" placeholder="{{ __('name') }}" class="form-control" required="required"/>
                            </div>
                            <div class="form-group">
                                <label for="shift_id">{{ __('Shift') }} <span class="text-info"> ({{__("Optional")}})</span></label>
                                <select name="shift_id" id="shift_id" class="form-control select2-dropdown select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                    <option value="">--- {{__('Select Shift')}} ---</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{$shift->id}}">{{$shift->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="stream_id">{{ __('Stream') }} <span class="text-info"> ({{__("Optional")}})</span></label>
                                <select name="stream_id[]" id="stream_id" class="stream_id form-control select2-dropdown select2-hidden-accessible" tabindex="-1" aria-hidden="true" data-placeholder=" --- {{__("Select Stream")}} --- " multiple>
                                    @foreach ($streams as $stream)
                                        <option value="{{$stream->id}}">{{$stream->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="default-section-div">
                                <label class="mb-0 mt-3">{{ __('section') }} <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    @foreach ($sections as $section)
                                        <div class="form-check w-fit-content ml-3">
                                            <label class="form-check-label ml-4">
                                                <input type="checkbox" class="form-check-input" name="section_id[0][]" value="{{ $section->id }}" required="required">{{ $section->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @if (count($semesters) > 0)
                                    <div class="form-check w-fit-content ml-3 mt-0">
                                        <label class="form-check-label d-inline">
                                            <input type="checkbox" class="form-check-input include_semesters[0][]" name="include_semesters[0]" value="1">{{ __('Include Semesters') }}
                                        </label>
                                    </div>
                                @endif

                            </div>
                            <div class="form-group" id="stream-wise-section-div" style="display: none;">
                                @foreach ($streams as $stream)
                                    <div id="{{ preg_replace('/\s+/', '-', trim($stream->name)) }}-section-div" class="stream-divs" style="display: none;">
                                        <label class="mb-0 mt-3">{{ __('section').' ('.$stream->name.')'}} <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            @foreach ($sections as $section)
                                                <div class="form-check w-fit-content ml-3">
                                                    <label class="form-check-label ml-4">
                                                        <input type="checkbox" class="form-check-input" name="section_id[{{$stream->id}}][]" value="{{ $section->id }}" required="required">{{ $section->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if (count($semesters) > 0)
                                            <div class="form-check w-fit-content ml-3 mt-0">
                                                <label class="form-check-label d-inline">
                                                    <input type="checkbox" class="form-check-input include_semesters" name="include_semesters[{{$stream->id}}]" value="1">{{ __('Include Semesters') }}
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="row mt-4">
                                <div class="col-md-12 col-sm-12 col-12">
                                    <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                    <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('Class') }}
                        </h4>
                        <div class="d-block">
                            <div class="">

                                <div class="col-12 text-right d-flex justify-content-end text-right align-items-end">
                                    <b><a href="#" class="table-list-type active mr-2" data-id="0">{{__('all')}}</a></b> | <a href="#" class="ml-2 table-list-type" data-id="1">{{__("Trashed")}}</a>
                                </div>
                            </div>
                        </div>
                        <div id="toolbar">
                            <label for="filter_medium_id" class="filter-menu">{{__("Medium")}}</label>
                            <select name="medium_id" id="filter_medium_id" class="form-control">
                                <option value="">{{ __('all') }}</option>
                                @foreach ($mediums as $medium)
                                    <option value="{{ $medium->id }}">{{ $medium->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('class.show',[1]) }}" data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                               data-show-columns="true" data-show-refresh="true" data-fixed-columns="false"
                               data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                               data-mobile-responsive="true" data-sort-name="id" data-toolbar="#toolbar" data-sort-order="desc"
                               data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "class-list-<?= date('d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                               data-show-export="true" data-query-params="classQueryParams"
                               data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="full_name" data-sortable="false">{{ __('name') }}</th>
                                <th scope="col" data-field="shift.name">{{ __('Shift') }}</th>
                                <th scope="col" data-field="include_semesters" data-formatter="yesAndNoStatusFormatter">{{ __('Semester') }}</th>
                                <th scope="col" data-field="section_names">{{ __('section') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function successFunction() {
            $('#default-section-div').show();
            $('#stream-wise-section-div').hide();
        }
    </script>
@endsection
