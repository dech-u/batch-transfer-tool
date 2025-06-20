@extends('layouts.master')

@section('title')
    {{ __('feature_sections') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') }}  {{ __('feature_sections') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title float-left">
                            {{ __('edit') . ' ' . __('feature_sections') }}
                        </h4>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 text-right">
                                <a href="{{ route('web-settings.feature.sections') }}" class="btn btn-theme btn-sm">{{ __('back') }}</a>
                            </div>
                        </div>
                        <hr>
                        {!! Form::model($featureSection, [
                            'route' => ['web-settings-section.update', $featureSection->id],
                            'method' => 'post',
                            'class' => 'edit-form pt-3',
                            'novalidate' => 'novalidate',
                            'enctype' => 'multipart/form-data',
                            'data-success-function' => 'formSuccessFunction'
                        ]) !!}
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-8">
                                    <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('heading', null, ['required', 'placeholder' => __('heading'), 'class' => 'form-control']) !!}
                                </div>                                
                            </div>
                            <hr>
                            <div class="form-group sections_data">
                                <div data-repeater-list="section_data">
                                    @foreach ($featureSection->feature_section_list as $section)
                                        <div class="row file_type_div" id="file_type_div" data-repeater-item>
                                            {!! Form::hidden('id', $section->id, ['id' => 'edit-id']) !!}
                                            <div class="form-group col-sm-12 col-md-4" id="file_name_div">
                                                <label>{{ __('feature') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="section[0][feature]" class="feature form-control"
                                                    placeholder="{{ __('feature') }}" value="{{ $section->feature }}" required>
                                            </div>
                                            <div class="form-group col-sm-12 col-md-4" id="file_thumbnail_div">
                                                <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                                <textarea name="section[0][description]" required rows="5" class="form-control">{{ $section->description }}</textarea>
                                            </div>
                                            <div class="form-group col-sm-12 col-md-3 file-input" id="file_div">
                                                <label class="image-lable">{{ __('image') }} </label>
                                                <input type="file" name="section[0][image]" class="file form-control" placeholder="" value="image">
                                                @if (!empty($section->image))
                                                    <input type="hidden" name="section[0][old_image]" class="file form-control" value="{{ $section->image }}">
                                                    <div class="preview-image">
                                                        <img src="{{ $section->image }}" class="img-fluid" alt="">
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-1 mt-4 mb-5">
                                                <button type="button" class="btn btn-inverse-danger btn-icon" data-repeater-delete> <i class="fa fa-times"></i> </button>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                </div>
                                <div class="form-group mt-4">
                                    <button type="button" class="btn btn-inverse-success"
                                            data-repeater-create>
                                        <i class="fa fa-plus"></i> {{ __('sections') }}
                                    </button>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right" type="submit" value={{ __('submit') }}>
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
        $('.sections_data').repeater({
            show: function () {
                $(this).slideDown();
                $(this).find('.preview-image').html('');
                $(this).find('.file-input').find('.file').attr('required',true);
                $(this).find('.file-input').find('.image-lable').append('<span class="text-danger">*</span>');
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            },
        })
    });
    function formSuccessFunction(response) {
        setTimeout(() => {
            window.location.href = baseUrl + '/web-settings/feature-section';
        }, 2000);
    }
    </script>
@endsection

