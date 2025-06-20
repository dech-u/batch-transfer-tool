@extends('layouts.master')

@section('title')
    {{ __('web_page') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('web_page') }}
            </h3>
        </div>
        <form class="pt-3 create-form-without-reset" action="{{ route('school.web-settings.store') }}" method="POST"
            novalidate="novalidate">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            {{-- Theme color --}}
                            <div class="page-section">
                                <h4 class="card-title">
                                    {{ __('theme_color') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="primary_color">{{ __('primary_color') }} <span class="text-danger">*</span></label>
                                        <input name="primary_color" id="primary_color" value="{{ $settings['primary_color'] ?? '#22577a' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="secondary_color">{{ __('secondary_color') }} <span class="text-danger">*</span></label>
                                        <input name="secondary_color" id="secondary_color" value="{{ $settings['secondary_color'] ?? '#38a3a5' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="primary_background_color">{{ __('primary_background_color') }} <span class="text-danger">*</span></label>
                                        <input name="primary_background_color" id="primary_background_color" value="{{ $settings['primary_background_color'] ?? '#f2f5f7' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="text_secondary_color">{{ __('text_secondary_color') }} <span class="text-danger">*</span></label>
                                        <input name="text_secondary_color" id="text_secondary_color" value="{{ $settings['text_secondary_color'] ?? '#2d2c2fb5' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="primary_hover_color">{{ __('primary_hover_color') }} <span class="text-danger">*</span></label>
                                        <input name="primary_hover_color" id="primary_hover_color" value="{{ $settings['primary_hover_color'] ?? '#143449' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>

                                </div>
                            </div>

                            {{-- About Us --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('about_us') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('about_us_title', $settings['about_us_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('about_us_heading', $settings['about_us_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('about_us_description', $settings['about_us_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('image') }} <span class="text-danger">*</span> <span class="text-info text-small">(645px*555px)</span></label>
                                        <input type="file" name="about_us_image" class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled=""
                                                placeholder="{{ __('image') }}" required />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                        </div>
                                        @if ($settings['about_us_image'] ?? null)
                                            <img src="{{ $settings['about_us_image'] ?? null }}" class="img-fluid w-25" alt="">
                                        @endif

                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="about_us_status" {{ isset($settings['about_us_status']) && $settings['about_us_status'] == 1 ? 'checked' : '' }} type="radio" value="1">{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="about_us_status" {{ isset($settings['about_us_status']) && $settings['about_us_status'] == 0 ? 'checked' : '' }} type="radio" value="0">{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Education programs --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('education_program') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('education_program_title', $settings['education_program_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('education_program_heading', $settings['education_program_heading'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('heading'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('education_program_description', $settings['education_program_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="education_program_status" {{ isset($settings['education_program_status']) && $settings['education_program_status'] == 1 ? 'checked' : '' }} type="radio" value="1">{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="education_program_status" type="radio" value="0" {{ isset($settings['education_program_status']) && $settings['education_program_status'] == 0 ? 'checked' : '' }}>{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Announcement --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('announcement') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('announcement_title', $settings['announcement_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('announcement_heading', $settings['announcement_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('announcement_description', $settings['announcement_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('image') }} <span class="text-danger">*</span><span class="text-info text-small">(595px*496px)</span></label>
                                        <input type="file" name="announcement_image" class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled=""
                                                placeholder="{{ __('image') }}" required />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                        </div>
                                        @if ($settings['announcement_image'] ?? null)
                                            <img src="{{ $settings['announcement_image'] ?? null }}" class="img-fluid w-25" alt="">
                                        @endif
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="announcement_status" {{ isset($settings['announcement_status']) && $settings['announcement_status'] == 1 ? 'checked' : '' }} type="radio" value="1">{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="announcement_status" {{ isset($settings['announcement_status']) && $settings['announcement_status'] == 0 ? 'checked' : '' }} type="radio" value="0">{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Counters --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('counter') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('counter_title', $settings['counter_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('counter_heading', $settings['counter_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <div class="row">
                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                                {!! Form::textarea('counter_description', $settings['counter_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                            </div>

                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                                <div class="d-flex">
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label"> <input name="counter_status" type="radio" value="1" {{ isset($settings['counter_status']) && $settings['counter_status'] == 1 ? 'checked' : '' }}>{{ __('enable') }} <i class="input-helper"></i></label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label"> <input name="counter_status" type="radio" value="0" {{ isset($settings['counter_status']) && $settings['counter_status'] == 0 ? 'checked' : '' }}>{{ __('disable') }} <i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>


                                    <div class="form-group col-sm-12 col-md-6">
                                        <span class="text-info text-small"> {{ __('image_size') }} : (248px*210px)</span>
                                        <div class="row mt-3">
                                            <div class="form-group col-sm-12 col-md-6">
                                                <label>{{ __('teacher') }} <span class="text-danger">*</span></label>
                                                <input type="file" name="counter_teacher"
                                                    class="file-upload-default" />
                                                <div class="input-group col-xs-12">
                                                    <input type="text" class="form-control file-upload-info"
                                                        disabled="" placeholder="{{ __('image') }}" required />
                                                    <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-theme"
                                                            type="button">{{ __('upload') }}</button>
                                                    </span>
                                                </div>
                                                @if ($settings['counter_teacher'] ?? null)
                                                    <img src="{{ $settings['counter_teacher'] ?? null }}" class="img-fluid w-25" alt="">
                                                @endif
                                            </div>
                                            <div class="form-group col-sm-12 col-md-6">
                                                <label>{{ __('student') }} <span class="text-danger">*</span></label>
                                                <input type="file" name="counter_student"
                                                    class="file-upload-default" />
                                                <div class="input-group col-xs-12">
                                                    <input type="text" class="form-control file-upload-info"
                                                        disabled="" placeholder="{{ __('image') }}" required />
                                                    <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-theme"
                                                            type="button">{{ __('upload') }}</button>
                                                    </span>
                                                </div>
                                                @if ($settings['counter_student'] ?? null)
                                                    <img src="{{ $settings['counter_student'] ?? null }}" class="img-fluid w-25" alt="">
                                                @endif
                                            </div>

                                            <div class="form-group col-sm-12 col-md-6">
                                                <label>{{ __('Class') }} <span class="text-danger">*</span></label>
                                                <input type="file" name="counter_class" class="file-upload-default" />
                                                <div class="input-group col-xs-12">
                                                    <input type="text" class="form-control file-upload-info"
                                                        disabled="" placeholder="{{ __('image') }}" required />
                                                    <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-theme"
                                                            type="button">{{ __('upload') }}</button>
                                                    </span>
                                                </div>
                                                @if ($settings['counter_class'] ?? null)
                                                    <img src="{{ $settings['counter_class'] ?? null }}" class="img-fluid w-25" alt="">
                                                @endif
                                            </div>
                                            <div class="form-group col-sm-12 col-md-6">
                                                <label>{{ __('Stream') }} <span class="text-danger">*</span></label>
                                                <input type="file" name="counter_stream"
                                                    class="file-upload-default" />
                                                <div class="input-group col-xs-12">
                                                    <input type="text" class="form-control file-upload-info"
                                                        disabled="" placeholder="{{ __('image') }}" required />
                                                    <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-theme"
                                                            type="button">{{ __('upload') }}</button>
                                                    </span>
                                                </div>
                                                @if ($settings['counter_stream'] ?? null)
                                                    <img src="{{ $settings['counter_stream'] ?? null }}" class="img-fluid w-25" alt="">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Expert Teachers --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('expert_teachers') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('expert_teachers_title', $settings['expert_teachers_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('expert_teachers_heading', $settings['expert_teachers_heading'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('heading'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('expert_teachers_description', $settings['expert_teachers_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="expert_teachers_status" {{ isset($settings['expert_teachers_status']) && $settings['expert_teachers_status'] == 1 ? 'checked' : '' }} type="radio" value="1">{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="expert_teachers_status" type="radio" value="0" {{ isset($settings['expert_teachers_status']) && $settings['expert_teachers_status'] == 0 ? 'checked' : '' }}>{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Gallery --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('gallery') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('gallery_title', $settings['gallery_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('gallery_heading', $settings['gallery_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('gallery_description', $settings['gallery_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="gallery_status" {{ isset($settings['gallery_status']) && $settings['gallery_status'] == 1 ? 'checked' : '' }} type="radio" value="1">{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="gallery_status" {{ isset($settings['gallery_status']) && $settings['gallery_status'] == 0 ? 'checked' : '' }} type="radio" value="0">{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Our Mission --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('our_mission') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('our_mission_title', $settings['our_mission_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('our_mission_heading', $settings['our_mission_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('our_mission_description', $settings['our_mission_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>


                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="">{{ __('points') }}</label>
                                        <span class="text-small text-info">({{ __('please_use_commas_or_press_enter_to_add_multiple_points') }})</span></label>
                                        <input name="our_mission_points" id="tags" class="form-control" value="{{ $settings['our_mission_points'] ?? null }}"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('image') }} <span class="text-danger">*</span></label>
                                        <input type="file" name="our_mission_image"
                                            class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info"
                                                disabled="" placeholder="{{ __('image') }}" required />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                        </div>
                                        @if ($settings['our_mission_image'] ?? null)
                                            <img src="{{ $settings['our_mission_image'] ?? null }}" class="img-fluid w-25" alt="">
                                        @endif
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="our_mission_status" type="radio" value="1" {{ isset($settings['our_mission_status']) && $settings['our_mission_status'] == 1 ? 'checked' : '' }}>{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="our_mission_status" type="radio" value="0" {{ isset($settings['our_mission_status']) && $settings['our_mission_status'] == 0 ? 'checked' : '' }}>{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Contact Us --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('contact_us') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('heading') }} </label>
                                        {!! Form::text('contact_us_heading', $settings['contact_us_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading')]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('description') }} </label>
                                        {!! Form::textarea('contact_us_description', $settings['contact_us_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description') ]) !!}
                                    </div>
                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="contact_us_status" type="radio" value="1" {{ isset($settings['contact_us_status']) && $settings['contact_us_status'] == 1 ? 'checked' : '' }}>{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="contact_us_status" type="radio" value="0" {{ isset($settings['contact_us_status']) && $settings['contact_us_status'] == 0 ? 'checked' : '' }}>{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- FAQs --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('faqs') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('faqs_title', $settings['faqs_title'] ?? null, ['class' => 'form-control', 'placeholder' => __('title'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('faqs_heading', $settings['faqs_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('faqs_description', $settings['faqs_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="faqs_status" type="radio" value="1" {{ isset($settings['faqs_status']) && $settings['faqs_status'] == 1 ? 'checked' : '' }}>{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="faqs_status" type="radio" value="0" {{ isset($settings['faqs_status']) && $settings['faqs_status'] == 0 ? 'checked' : '' }}>{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Admission Form --}}
                            <div class="page-section mt-3">
                                <h4 class="card-title">
                                    {{ __('online_registration') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('online_registration_title', $settings['online_registration_title'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('title'), ' required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('online_registration_heading', $settings['online_registration_heading'] ?? null, ['class' => 'form-control', 'placeholder' => __('heading'), ' required']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('online_registration_description', $settings['online_registration_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('description'), 'required', ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('image') }} <span class="text-danger">*</span><span class="text-info text-small">(595px*496px)</span></label>
                                        <input type="file" name="online_registration_image" class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info" disabled=""
                                                placeholder="{{ __('image') }}" required />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                        </div>
                                        {{-- @dd($settings) --}}
                                        @if ($settings['online_registration_image'] ?? null)
                                            <img src="{{ $settings['online_registration_image'] ?? null }}" class="img-fluid w-25" alt="">
                                        @endif
                                    </div>

                                    <div class="form-group col-sm-6 col-md-4">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label><br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="online_registration_status" {{ isset($settings['online_registration_status']) && $settings['online_registration_status'] == 1 ? 'checked' : '' }} type="radio" value="1">{{ __('enable') }} <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label"> <input name="online_registration_status" {{ isset($settings['online_registration_status']) && $settings['online_registration_status'] == 0 ? 'checked' : '' }} type="radio" value="0">{{ __('disable') }} <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- online_registration_status --}}
                            {{-- Footer --}}
                            <div class="page-section mt-3 mb-3">
                                <h4 class="card-title">
                                    {{ __('footer') }}
                                </h4>
                                <hr>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <div class="row">
                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('short_description') }} </label>
                                                {!! Form::textarea('short_description', $settings['short_description'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('short_description') ]) !!}
                                            </div>

                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('footer_logo') }} </label>
                                                <input type="file" name="footer_logo" class="file-upload-default" />
                                                <div class="input-group col-xs-12">
                                                    <input type="text" class="form-control file-upload-info" disabled=""
                                                        placeholder="{{ __('image') }}" required />
                                                    <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-theme"
                                                            type="button">{{ __('upload') }}</button>
                                                    </span>
                                                </div>
                                                @if ($settings['footer_logo'] ?? null)
                                                    <img src="{{ $settings['footer_logo'] ?? null }}" class="img-fluid w-25" alt="">
                                                @endif
                                            </div>

                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('footer_text') }} </label>
                                                {!! Form::text('footer_text', $settings['footer_text'] ?? null, [ 'class' => 'form-control', 'placeholder' => __('footer_text') ]) !!}
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <div class="row">
                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('facebook') }}</label>
                                                {!! Form::text('facebook', $settings['facebook'] ?? null, ['class' => 'form-control', 'placeholder' => __('facebook')]) !!}
                                            </div>
                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('instagram') }}</label>
                                                {!! Form::text('instagram', $settings['instagram'] ?? null, ['class' => 'form-control', 'placeholder' => __('instagram')]) !!}
                                            </div>
                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('linkedin') }}</label>
                                                {!! Form::text('linkedin', $settings['linkedin'] ?? null, ['class' => 'form-control', 'placeholder' => __('linkedin')]) !!}
                                            </div>
                                            <div class="form-group col-sm-12 col-md-12">
                                                <label>{{ __('twitter') }}</label>
                                                {!! Form::text('twitter', $settings['twitter'] ?? null, ['class' => 'form-control', 'placeholder' => __('twitter')]) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- <input class="btn btn-theme mt-3" id="create-btn" type="submit" value={{ __('submit') }}> --}}
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
