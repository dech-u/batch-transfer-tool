@extends('layouts.master')

@section('title')
    {{ __('assign').' '.__('questions') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('assign').' '.__('questions') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('online-exam.index') }}">{{ __('back') }}</a>
                        </div>
                        <form class="pt-3 mt-6" id="add-new-question-online-exam" method="POST" action="{{ route('online-exam.add-new-question') }}">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>{{ __('class_section') }}</label>
                                    <input type="text" value="{{$onlineExam->class_section_with_medium}}" placeholder="{{ __('class_section') }}" class="form-control" readonly/>
                                    <input type="hidden" name="class_section_id" value={{$onlineExam->class_section_id}}>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>{{ __('subject') }}</label>
                                    <input type="text" id="add-question-subject" value="{{$onlineExam->subject_with_name}}" placeholder="{{ __('subject') }}" class="form-control" readonly/>
                                    <input type="hidden" name="class_subject_id" value={{$onlineExam->class_subject_id}}>
                                </div>
                                <input type="hidden" name="online_exam_id" value="{{$onlineExam->id}}">
                                <div class="form-group col-md-4">
                                    <label>{{ __('online') }} {{__('exam')}} {{__('title')}}</label>
                                    <input type="text" value="{{$onlineExam->title}}" placeholder="{{ __('title') }}" class="form-control" readonly/>
                                </div>
                            </div>
                            <hr>
                            <div class="add-new-question-container" style="display:none">
                                <div class="form-group">
                                    <label></label>
                                    <button type="button" class="btn btn-danger btn-sm d-flex float-right remove-add-new-question"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('question') }} <span class="text-danger">*</span></label>
                                    <textarea class="editor_question" name="question" required placeholder="{{__('enter').' '.__('question')}}"></textarea>
                                </div>
                                <div class="options-data">
                                    <div data-repeater-list="option_data" class="row">
                                        <div class="form-group col-lg-6 col-md-12" data-repeater-item>
                                            <label>{{ __('option') }} <span class="option-number">0</span> <span class="text-danger">*</span></label>
                                            <textarea class="editor_options" name="option" required placeholder="{{__('enter').' '.__('option')}}"></textarea>
                                            {!! Form::hidden('number','', ['class'=>'option-number']) !!}
                                            <button type="button" class="btn btn-inverse-danger mt-2 btn-icon remove-option" data-repeater-delete>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-dark btn-sm" type="button" id="add-new-option" data-repeater-create>
                                            <i class="fa fa-plus-circle fa-3x mr-2" aria-hidden="true"></i>
                                            {{__('add_option')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="form-group col-md-6 mt-2">
                                        <div class="form-group">
                                            <label>{{ __('answer') }} <span class="text-danger">*</span></label>
                                            <select multiple required name="answer[]" id="answer_select" class="form-control select2-dropdown select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('image') }}</label>
                                        <input type="file" name="image" class="file-upload-default"/>
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}"/>
                                            <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group p-1">
                                    <label>{{ __('note') }}</label>
                                    <input type="text" name="note" class="form-control">
                                </div>
                                <input class="btn btn-theme mt-4" id="new-question-add" type="submit" value={{__('add')}}>
                            </div>
                        </form>
                        <div class="row">
                            <button type="buttton" class="btn btn-theme ml-3 add-new-question-button">{{__('add_new_question')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list').' '.__('questions') }}
                        </h4>

                        <table aria-describedby="mydesc" class='table' id='table_list_exam_questions'
                               data-toggle="table" data-url="{{ route('online-exam-question.get-class-questions',$onlineExam->id) }}"
                               data-checkbox-header="false" data-click-to-select="true" data-side-pagination="server"
                               data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                               data-show-columns="true" data-fixed-columns="false" data-trim-on-search="true"
                               data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                               data-query-params="onlineExamQuestionsQueryParams" data-show-refresh="true" data-escape="true">
                            <thead>
                            <tr>
                                <th data-field="state" data-checkbox="true"></th>
                                <th scope="col" data-field="question_id" data-sortable="true" data-visible="false">{{ __('id') }}</th>
                                <th scope="col" data-field="no">{{ __('no.') }}</th>
                                <th scope="col" data-field="question" data-escape="false">{{ __('question')}}</th>
                                <th scope="col" data-field="image" data-formatter="imageFormatter" data-align="center">{{ __('image') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12 col-xl-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('assign').' '.__('questions') }}
                        </h4>
                        <form class="pt-3 mt-6" id="store-assign-questions-form" method="POST" action="{{ route('online-exam.store-choice-question') }}">
                            <input type="hidden" name="exam_id" value="{{$onlineExam->id}}"/>
                            <div id='questions_block' class="form-group mt-4" style="overflow-y:scroll;height:700px;">
                                <ol id="sortable-row">
                                    @if(isset($examQuestions) && !empty($examQuestions))
                                        @foreach ($examQuestions as $data)
                                            <div class="list-group">
                                                <input type="hidden" name="assign_questions[{{$data->question_id}}][edit_id]" value="{{$data->id}}">
                                                <input type="hidden" name="assign_questions[{{$data->question_id}}][question_id]" value="{{$data->question_id}}">
                                                <li id="q{{$data->question_id}}" class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">
                                                    {{$data->question_id}}
                                                    <div>
                                                        <textarea class="equation-editor-inline" name="q{{$data->question_id}}">{{htmlspecialchars_decode($data->questions->question)}}</textarea>
                                                    </div>
                                                    <span class="text-right row mx-0">
                                                        <input type="number" min="1" class="list-group-item form-control-sm mb-2 mr-2 col-md-3 col-sm-12" placeholder="{{ __('enter_marks') }}" name="assign_questions[{{$data->question_id}}][marks]" value="{{$data->marks}}" min="0">
                                                        <a class="btn btn-danger btn-sm remove-row mb-2" data-edit_id="{{$data->id}}" data-id="{{$data->question_id}}">
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            </div>
                                        @endforeach
                                    @endif
                                </ol>
                                <input class="btn btn-theme ml-4 submit_questions_btn" type="submit" value={{ __('submit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // $(document).ready(function () {
        //     setTimeout(() => {
        //         createCkeditor();
        //     }, 1000);
        // });
    </script>
@endsection
