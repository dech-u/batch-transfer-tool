@extends('layouts.master')

@section('title')
    {{ __('Manage Online Exam Questions') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Edit Online Exam Questions') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('online-exam-question.index') }}">{{ __('back') }}</a>
                        </div>
                        <form class="pt-3 mt-6" id="edit-online-exam-questions-form" method="POST" action="{{ route('online-exam-question.index') }}">
                            <input type="hidden" name="edit_id" id="edit_id" value="{{$onlineExamQuestion->id}}">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('class_section') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('', $onlineExamQuestion->class_section_with_medium, ['readonly' => true,'class' => 'form-control',]) !!}
                                    {{--  {!! Form::hidden('class_section', $onlineExamQuestion->class_section_id) !!}  --}}
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('', $onlineExamQuestion->subject_with_name, ['readonly' => true,'class' => 'form-control',]) !!}
                                    {{--  {!! Form::hidden('subject_id', $onlineExamQuestion->subject_id) !!}  --}}
                                </div>
                            </div>
                            <div class="bg-light p-4">
                                <div class="form-group">
                                    <label>{{ __('question') }} <span class="text-danger">*</span></label>
                                    <textarea class="editor_question" name="question" required placeholder="{{__('enter').' '.__('question')}}">{{htmlspecialchars_decode($onlineExamQuestion->question)}}</textarea>
                                </div>
                                <div class="options-data">
                                    <div data-repeater-list="option_data" class="row">
                                        <div class="form-group col-lg-6 col-md-12" data-repeater-item>
                                            {!! Form::hidden('id','', ['class'=>'option-id']) !!}
                                            <label>{{ __('option') }} <span class="option-number">0</span> <span class="text-danger">*</span></label>
                                            <textarea class="edit_editor_options" name="option" required placeholder="{{__('enter').' '.__('option')}}"></textarea>
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
                                        <div style="width: 70px; margin:10px 0">
                                            <img src="{{$onlineExamQuestion->image_url}}" class="img-fluid w-100"  alt=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group p-1">
                                    <label>{{ __('note') }}</label>
                                    <input type="text" name="note" value="{!! htmlspecialchars_decode($onlineExamQuestion->note) !!}" class="form-control">
                                </div>
                            </div>
                            <input class="btn btn-theme mt-4" id="new-question-add" type="submit" value={{__('submit')}}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function () {
        
        // Initialize the options repeater with their values
        var optionsList = [
            @foreach($onlineExamQuestion->options as $option)
            {
                id: "{{ $option->id }}",
            },
            @endforeach
        ];
        addNewOptionRepeater.setList(optionsList);

        // Add the answers to be selected
        var selectedOptions = [];
        @if($onlineExamQuestion->options->isNotEmpty())
            @foreach($onlineExamQuestion->options as $key => $option)
                {{$key++}}
                $("#remove-option-{{$key}}").attr('data-id',{{$option->id}});
                if({{$option->is_answer}}){
                    selectedOptions.push({{$key}});
                }

                // Initialize Option
                CKEDITOR.replace("option-{{ $key }}",{
                    mathJaxLib: '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML',
                    extraPlugins: 'mathjax',
                    height: 100
                });

                // Add Data of Option to Initialized Option
                CKEDITOR.instances["option-{{ $key }}"].setData(@json($option->option));
            @endforeach
        @else
            createCkeditor();
        @endif
        $("#answer_select").val(selectedOptions).trigger('change');
    });

</script>
@endsection
