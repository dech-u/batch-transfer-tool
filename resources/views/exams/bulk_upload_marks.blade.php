@extends('layouts.master')

@section('title')
    {{ __('exam_marks') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('exam_marks') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('exam_marks') }}
                        </h4>
                        <form action="{{ route('exam.store-bulk-data') }}" class="create-form" id="formdata" data-success-function="formSuccessFunction">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="">{{ __('class_section') }}<span class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section_id" required class="form-control">
                                        <option value="">-- {{ __('select_class_section') }} --</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" data-classId="{{ $class->class_id }}">{{ $class->full_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="">{{ __('exams') }}<span class="text-danger">*</span></label>
                                    <select required name="exam_id" id="exam_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select') . ' ' . __('exam') }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="">{{ __('subject') }}<span class="text-danger">*</span></label>
                                    <select required name="class_subject_id" id="subject_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{ __('select') . ' ' . __('subject')}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="file-upload-default">{{ __('file_upload') }} <span class="text-danger">*</span></label>
                                    <input type="file" name="file" class="file-upload-default" />
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" id="file-upload-default" disabled="" placeholder="{{ __('file_upload') }}" required="required" />
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-xs-12">
                                    <input class="btn btn-theme submit_bulk_file float-right" type="submit" value="{{ __('submit') }}" name="submit" id="submit_bulk_file">
                                </div>
                            </div>
                        </form>    
                       
                        <div id="downloadDummyFile" style="display: none">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4 mt-5">
                                    <a class="btn btn-theme form-control" id="download-dummy-file" type="submit">
                                        <strong>{{ __('download_dummy_file') }}</strong>
                                    </a>
                                </div>
                            </div>
                            <div class="row col-sm-12 col-xs-12">
                                <span style="font-size: 14px">
                                    <b>{{ __('note') }} :- </b>{{ __('First download dummy file and convert to .csv file then upload it') }}.</span>
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
        $('#search').on('click , input', function () {
            $('.show_student_list').show();
            $('.student_table').bootstrapTable('refresh');
        });

        function formSuccessFunction(response) {
            setTimeout(() => {
                $('.student_table').bootstrapTable('refresh');
            }, 500);
        }

        document.getElementById('download-dummy-file').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default anchor behavior

            // Get form data
            const classSectionId = document.getElementById('class_section_id').value;
            const examId = document.getElementById('exam_id').value;
            const classSubjectId = document.getElementById('subject_id').value;

            const downloadUrl = `{{ route('exam.download-sample-file') }}?class_section_id=${classSectionId}&exam_id=${examId}&class_subject_id=${classSubjectId}`;
           
            const anchor = document.createElement('a');
            anchor.href = downloadUrl;
            document.body.appendChild(anchor);
            anchor.click();
            document.body.removeChild(anchor);

        });

    </script>
@endsection
