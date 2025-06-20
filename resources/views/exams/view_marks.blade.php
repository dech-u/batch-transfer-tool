@extends('layouts.master')

@section('title')
    {{ __('Manage Exams') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Exams') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('List Exams') }}
                        </h4>
                        <div class="row" id="toolbar">
                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                <label for="filter_session_year_id" class="filter-menu">{{ __('Session Year') }}</label>
                                <select name="filter_session_year_id" id="filter_session_year_id" class="form-control">
                                    @foreach ($session_year_all as $sessionYear)
                                        <option value="{{ $sessionYear->id }}"
                                            {{ $sessionYear->default == 1 ? 'selected' : '' }}>{{ $sessionYear->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-3">
                                <label for="filter_medium_id" class="filter-menu">{{ __('medium') }}</label>
                                {!! Form::select('medium_id', $mediums, null, ['class' => 'form-control', 'id' => 'filter_medium_id', 'placeholder' => __('all')]) !!}
                            </div>
                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('exam.view-marks-list', 1) }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                               data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-sort-order="desc" data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "exam-list-<?= date(' d-m-y') ?>" ,"ignoreColumn":
                            ["operate"]}' data-show-export="true" data-detail-formatter="examListFormatter"
                               data-query-params="examQueryParams" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                <th scope="col" data-field="subjectWiseStatus" data-formatter="marksSubmissionStatus">{{ __('marks_submission_status') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="true" data-visible="false">{{ __('updated_at') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            {{-- Edit model --}}
            <div class="modal fade" id="editModal" tabindex="-1" data-success-function="formSuccessFunction"
                 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('exam') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3" id="edit-form" action="{{ url('exams') }}" novalidate="novalidate" method="POST">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>{{ __('Exam Name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', '', [
                                            'id' => 'edit_name',
                                            'placeholder' => trans('Exam Name'),
                                            'class' => 'form-control',
                                            'required' => true,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{ __('Exam Description') }}</label>
                                        {!! Form::textarea('description', '', [
                                            'id' => 'edit_description',
                                            'placeholder' => trans('Exam Description'),
                                            'class' => 'form-control',
                                            'rows' => '3',
                                        ]) !!}
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

            {{-- Marks status --}}
            <div class="modal fade" id="marksStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('marks_status') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <table class="table table-border">
                                    <tr>
                                        <th>{{ __('no') }}</th>
                                        <th>{{ __('subject') }}</th>
                                        <th>{{ __('date_time') }}</th>
                                        <th>{{ __('subject_teacher') }}</th>
                                        <th>{{ __('status') }}</th>
                                    </tr>
                                </table>
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
        const formSuccessFunction = () => {
            setTimeout(() => {
                $('#class-id').val('').trigger('change');
            }, 500);
        }
    </script>
@endsection
