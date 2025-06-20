@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('lesson') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('lesson') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title float-left">
                            {{ __('edit') . ' ' . __('lesson') }}
                        </h4>
                        <div class="row text-right">
                            <div class="col-sm-12 col-md-12">
                                <a class="btn btn-sm btn-theme" href="{{ route('lesson.index') }}">{{ __('back') }}</a>
                            </div>
                        </div>
                        <form class="pt-3 edit-lesson-form" id="edit-form" data-success-function="formSuccessFunction" action="{{ route('lesson.update',[$lesson->id]) }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('Class') . ' ' . __('section') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id[]" id="class-section-id" class="class_section_id form-control select2-dropdown select2-hidden-accessible" multiple>
                                        {{-- <option value="">{{ __('Select Class Section') }}</option> --}}
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {!! Form::hidden('class_section_id',"", ["id" => "class_section_id_value"]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="class_subject_id" id="class-subject-id" class="form-control">
                                        <option value="">-- {{ __('Select Subject') }} --</option>
                                        <option value="data-not-found">-- {{ __('no_data_found') }} --</option>
                                        @foreach ($subjectTeachers as $item)
                                            <option value="{{ $item->class_subject_id }}" data-class-section="{{ $item->class_section_id }}" data-user="{{ Auth::user()->id }}">{{ $item->subject_with_name}}</option>
                                        @endforeach
                                    </select>
                                    {!! Form::hidden('class_subject_id',"", ["id" => "class_subject_id_value"]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" value="{{ $lesson->name }}" name="name" placeholder="{{ __('lesson_name') }}" class="form-control"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson_description') }} <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" placeholder="{{ __('lesson_description') }}" class="form-control">{{ $lesson->description }}</textarea>
                                </div>
                            </div>
                            <hr>

                            <h4 class="mb-4 mt-4">{{ __('files') }}</h4>

                            <div class="form-group files_data">
                                <div data-repeater-list="file_data">
                                    <div class="row file_type_div" id="file_type_div" data-repeater-item>
                                        <input type="hidden" name="id" class="fileId">
                                        <div class="form-group col-xl-2">
                                            <label>{{ __('type') }} </label>
                                            <select name="type" class="form-control file_type">
                                                <option value="">--{{ __('select') }}--</option>
                                                <option value="file_upload">{{ __('file_upload') }}</option>
                                                <option value="youtube_link">{{ __('youtube_link') }}</option>
                                                <option value="video_upload">{{ __('video_upload') }}</option>
                                                <option value="other_link">{{ __('other_link') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_name_div" style="display: none">
                                            <label>{{ __('file_name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="file_name form-control" placeholder="{{ __('file_name') }}" required>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_thumbnail_div" style="display: none">
                                            <label>{{ __('thumbnail') }} <span class="text-danger thumbnail-required">*</span></label>
                                            <input type="file" name="thumbnail" accept="image/*" class="file_thumbnail form-control" required>
                                            <div style="width: 70px;">
                                                <a data-toggle='lightbox' href=><img class="img-fluid w-70 thumbnail-preview mt-1" alt=""/></a>
                                            </div>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_div" style="display: none">
                                            <label>{{ __('file_upload') }} <span class="text-danger file-upload-required">*</span></label>
                                            <input type="file" name="file" class="file form-control" placeholder="" required>
                                            <a class="file-preview mt-2" target="_blank"></a>
                                        </div>
                                        <div class="form-group col-xl-3" id="file_link_div" style="display: none">
                                            <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="link" class="file_link form-control" placeholder="{{ __('link') }}" required>
                                        </div>

                                        <div class="form-group col-xl-1 mt-4">
                                            <button type="button" class="btn btn-inverse-danger btn-icon remove-lesson-topic-file" data-repeater-delete>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-inverse-success add-lesson-topic-file" data-repeater-create>
                                        <i class="fa fa-plus"></i> {{__('add_new_files')}}
                                    </button>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
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
            setTimeout(() => {
            $('#class-section-id').val({{ $lesson->class_section_id }}).trigger('change').attr('disabled',false)
            $('#class_section_id_value').val({{ $lesson->class_section_id }})
            $('#class-subject-id').val({{ $lesson->class_subject_id }}).trigger('change').attr('disabled',true)
            $('#class_subject_id_value').val({{ $lesson->class_subject_id }})

            @if(isset($lesson->file) && empty($lesson->file->toArray()))
            $('.remove-lesson-topic-file').click();
            @endif

            addNewLessonTopicFileRepeater.setList([
                    @foreach($lesson->file as $key => $fileData)
                {
                    id: "{{$fileData->id}}",
                    name: "{{$fileData->file_name}}",
                    @if($fileData->type == 1)
                    type: "file_upload",
                    @elseif($fileData->type == 2)
                    type: "youtube_link",
                    link: "{{$fileData->file_url}}",
                    @elseif($fileData->type == 3)
                    type: "video_upload",
                    @endif
                },
                @endforeach
            ]);
            $('.file_type').trigger('change');


            @foreach($lesson->file as $key => $fileData)
            $('#remove-lesson-topic-file-{{ $key }}').attr("data-id", "{{ $fileData->id }}") // Add Data id attribute to Remove Files
            @if($fileData->type == 1)

            // if File Url Exists
            @if ($fileData->getOriginal('file_url'))
            $('#file-upload-required-{{ $key }}').html("") // remove * from label
            $('#file-upload-required-{{ $key }}').parent().siblings().removeAttr('required') // Remove Required from file input
            $('#file-preview-{{ $key }}').addClass('btn btn-sm btn-outline-info').attr('href', '{{ $fileData->file_url }}').html(window.trans['File Preview']) // Add File Url in Anchor Tag
            @else
            $('#file-upload-required-{{ $key }}').html("*") // Add * in label
            $('#file-upload-required-{{ $key }}').parent().siblings().attr('required', true) // Add Required attribute in file input
            @endif
            @elseif($fileData->type == 2)

            // if Thumbnail Exists
            @if($fileData->getOriginal('file_thumbnail'))
            $('#thumbnail-required-{{ $key }}').html("") // remove * from label
            $('#thumbnail-required-{{ $key }}').parent().siblings().removeAttr('required') // Remove Required from file input
            $('#thumbnail-preview-{{ $key }}').attr('src', "{{ $fileData->file_thumbnail }}") // Add Thumbnail in Image Tag
            $('#thumbnail-preview-{{ $key }}').parent().attr('href', "{{ $fileData->file_thumbnail }}") // Add Thumbnail in image Tag
            @else
            $('#thumbnail-required-{{ $key }}').parent().siblings().attr('required', true) // Add * in label
            $('#thumbnail-required-{{ $key }}').html("*") // Add Required attribute in file input
            @endif
            @elseif($fileData->type == 3)

            // if Thumbnail Exists
            @if($fileData->getOriginal('file_thumbnail'))
            $('#thumbnail-required-{{ $key }}').html("") // remove * from label
            $('#thumbnail-required-{{ $key }}').parent().siblings().removeAttr('required') // Remove Required from file input
            $('#thumbnail-preview-{{ $key }}').attr('src', "{{ $fileData->file_thumbnail }}") // Add Thumbnail in Image Tag
            $('#thumbnail-preview-{{ $key }}').parent().attr('href', "{{ $fileData->file_thumbnail }}") // Add Thumbnail in image Tag
            @else
            $('#thumbnail-required-{{ $key }}').parent().siblings().attr('required', true) // Add * in label
            $('#thumbnail-required-{{ $key }}').html("*") // Add Required attribute in file input
            @endif

            // if File Url Exists
            @if ($fileData->getOriginal('file_url'))
            $('#file-upload-required-{{ $key }}').html("") // remove * from label
            $('#file-upload-required-{{ $key }}').parent().siblings().removeAttr('required') // Remove Required from file input
            $('#file-preview-{{ $key }}').addClass('btn btn-sm btn-outline-info').attr('href', '{{ $fileData->file_url }}').html(window.trans['File Preview']) // Add File Url in Anchor Tag
            @else
            $('#file-upload-required-{{ $key }}').html("*") // Add * in label
            $('#file-upload-required-{{ $key }}').parent().siblings().attr('required', true) // Add Required attribute in file input
            @endif


            @endif
            @endforeach
            }, 1000);

        });

        function formSuccessFunction(response) {
            if (!response.error) {
                setTimeout(() => {
                    window.location.href = "{{route('lesson.index')}}"
                }, 1000);
            }
        }
    </script>
@endsection
