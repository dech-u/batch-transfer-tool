@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('assignment') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('assignment_submission') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('assignment_submission') }}
                        </h4>

                        <div class="row" id="toolbar">

                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-6">
                                <label for="filter-class-section-id" class="filter-menu">{{__("class_section")}}</label>
                                <select name="class_section_id" id="filter-class-section-id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($classSections as $data)
                                        <option value="{{ $data->id }}">
                                            {{ $data->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-3 col-lg-6">
                                <label for="filter-subject-id" class="filter-menu">{{__("subject")}}</label>
                                <select name="class_subject_id" id="filter-subject-id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">-- {{ __('Select Subject') }} --</option>
                                    {{-- <option value="data-not-found">-- {{ __('no_data_found') }} --</option> --}}
                                    @foreach ($subjectTeachers as $item)
                                        <option value="{{ $item->class_subject_id }}" data-class-section="{{ $item->class_section_id }}">{{ $item->subject_with_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                               data-url="{{ route('assignment.submission.list') }}" data-click-to-select="true"
                               data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                               data-search="true" data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                               data-fixed-columns="false" data-fixed-number="2" data-fixed-right-number="1"
                               data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                               data-query-params="AssignmentSubmissionQueryParams" data-sort-order="desc"
                               data-maintain-selected="true" data-export-data-type='all'
                               data-export-options='{ "fileName": "assignment-submission-list-<?= date('d-m-y') ?>","ignoreColumn": ["operate"]}'
                               data-show-export="true" data-escape="true">
                            <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="assignment.name" data-sortable="false">{{ __('assignment_name') }}</th>
                                <th scope="col" data-field="assignment.class_section.full_name" data-sortable="false">{{ __('class_section') }}</th>
                                <th scope="col" data-field="assignment.class_subject.subject.name_with_type" data-sortable="false">{{ __('subject') }}</th>
                                <th scope="col" data-field="student.full_name" data-sortable="false">{{ __('student_name') }}</th>
                                <th scope="col" data-field="file" data-sortable="false" data-formatter="fileFormatter">{{ __('files') }}</th>
                                <th scope="col" data-field="status" data-sortable="false" data-formatter="assignmentSubmissionStatusFormatter">{{ __('status') }}</th>
                                <th scope="col" data-field="assignment.points" data-sortable="false">{{ __('points') }}</th>
                                <th scope="col" data-field="feedback" data-sortable="false">{{ __('feedback') }}</th>
                                <th scope="col" data-field="session_year.name" data-sortable="false" data-visible="false">{{ __('Session Year') }}</th>
                                <th scope="col" data-field="created_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false">{{ __('created_at') }}</th>
                                <th scope="col" data-field="updated_at" data-formatter="dateTimeFormatter" data-sortable="false" data-visible="false">{{ __('updated_at') }}</th>
                                <th scope="col" data-field="operate" data-events="assignmentSubmissionEvents" data-escape="false">{{ __('action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('assignment_submission') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 class-edit-form" id="edit-form" action="{{ url('assignment-submission') }}" novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value=""/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="assignment_name">{{ __('assignment_name') }}</label>
                                        <input type="text" name="" id="assignment_name" class="form-control" disabled>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="subject">{{ __('subject') }}</label>
                                        <input type="text" name="" id="subject" class="form-control" disabled>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="student_name">{{ __('student_name') }}</label>
                                        <input type="text" name="" id="student_name" class="form-control" disabled>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('files') }}</label>
                                        <div id="files"></div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input edit-status" name="status" id="status_accept" value="1">{{ __('accept') }}
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input edit-status" name="status" id="status_reject" value="2">{{ __('reject') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12" id="points_div">
                                        <label for="points">{{ __('points') }} <span id="assignment_points"></span></label>
                                        <input type="number" name="points" placeholder="{{ __('points') }}" id="points" class="form-control" min="0">
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="feedback">{{ __('feedback') }}</label>
                                        <textarea name="feedback" id="feedback" placeholder="{{ __('feedback') }}" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.edit-status').change(function (e) { 
            e.preventDefault();
            var status = $('input[name="status"]:checked').val();
            if (status == 1) {
                $('#points').attr('disabled', false);
            } else {
                $('#points').val(null);
                $('#points').attr('disabled', true);
            }
        });
    </script>
@endsection
