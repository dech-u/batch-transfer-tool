@extends('layouts.master')

@section('title')
    {{ __('web_settings') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') }} {{ __('web_settings') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="create-form-without-reset" id="formdata" action="{{ route('web-settings.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf

                            {{-- Colour settings --}}
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('colour_settings') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    {{-- Primary color --}}
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="theme_primary_color">{{ __('theme_primary_color') }} <span class="text-danger">*</span></label>
                                        <input name="theme_primary_color" id="theme_primary_color"
                                            value="{{ $settings['theme_primary_color'] ?? '' }}" type="text" required
                                            placeholder="{{ __('theme_primary_color') }}" class="theme_primary_color color-picker" />
                                        <small>
                                            <a href="javascript:null" onclick="restore_default_color(1)">{{__('restore_default')}}</a>
                                        </small>
                                    </div>
                                    {{-- Secondary color --}}
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="theme_secondary_color">{{ __('theme_secondary_color') }} <span class="text-danger">*</span></label>
                                        <input name="theme_secondary_color" id="theme_secondary_color"
                                            value="{{ $settings['theme_secondary_color'] ?? '' }}" type="text" required
                                            placeholder="{{ __('theme_secondary_color') }}" class="theme_secondary_color color-picker" />
                                        <small>
                                            <a href="javascript:null" onclick="restore_default_color(2)">{{__('restore_default')}}</a>
                                        </small>
                                    </div>
                                    {{-- Secondary color 1 --}}
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="theme_secondary_color_1">{{ __('theme_secondary_color_1') }} <span class="text-danger">*</span></label>
                                        <input name="theme_secondary_color_1" id="theme_secondary_color_1"
                                            value="{{ $settings['theme_secondary_color_1'] ?? '' }}" type="text" required
                                            placeholder="{{ __('theme_secondary_color_1') }}" class="theme_secondary_color_1 color-picker" />
                                        <small>
                                            <a href="javascript:null" onclick="restore_default_color(3)">{{__('restore_default')}}</a>
                                        </small>
                                    </div>
                                    {{-- Primary background color --}}
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="theme_primary_background_color">{{ __('theme_primary_background_color') }} <span class="text-danger">*</span></label>
                                        <input name="theme_primary_background_color" id="theme_primary_background_color"
                                            value="{{ $settings['theme_primary_background_color'] ?? '' }}" type="text" required
                                            placeholder="{{ __('theme_primary_background_color') }}" class="theme_primary_background_color color-picker" />
                                        <small>
                                            <a href="javascript:null" onclick="restore_default_color(4)">{{__('restore_default')}}</a>
                                        </small>
                                    </div>
                                    {{-- Text secondary color --}}
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="theme_text_secondary_color">{{ __('theme_text_secondary_color') }} <span class="text-danger">*</span></label>
                                        <input name="theme_text_secondary_color" id="theme_text_secondary_color"
                                            value="{{ $settings['theme_text_secondary_color'] ?? '' }}" type="text" required
                                            placeholder="{{ __('theme_text_secondary_color') }}" class="theme_text_secondary_color color-picker" />
                                        <small>
                                            <a href="javascript:null" onclick="restore_default_color(5)">{{__('restore_default')}}</a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            {{-- End Colour settings --}}

                            {{-- General settings --}}
                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('general_settings') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="image">{{ __('hero_image') }} </label>
                                        <input type="file" name="home_image" class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info"
                                                id="home_image" disabled=""
                                                placeholder="{{ __('home_image') }}" />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                            <div class="col-md-12 mt-2">
                                                <img height="50px" src='{{ $settings['home_image'] ?? asset('assets/landing_page_images/heroImg.png') }}'
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="hero_title_1">{{ __('hero_title_1') }}</label>
                                        <input name="hero_title_1" id="hero_title_1" value="{{ $settings['hero_title_1'] ?? '' }}" type="text" placeholder="{{ __('hero_title_1') }}" class="form-control" maxlength="200" />
                                    </div>

                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="hero_title_2">{{ __('hero_title_2') }}</label>
                                        <input name="hero_title_2" id="hero_title_2" value="{{ $settings['hero_title_2'] ?? '' }}" type="text" placeholder="{{ __('hero_title_2') }}" class="form-control" maxlength="50"/>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="image">{{ __('hero_image_2') }} </label>
                                        <input type="file" name="hero_title_2_image" class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info"
                                                id="hero_title_2_image" disabled=""
                                                placeholder="{{ __('hero_title_2_image') }}" />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                            <div class="col-md-12 mt-2">
                                                <img height="50px" src='{{ $settings['hero_title_2_image'] ?? asset('assets/landing_page_images/user.png') }}'
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">

                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <div class="d-flex">
                                            <div class="form-check w-fit-content ml-3">
                                                <label class="form-check-label ml-4">
                                                    @if (isset($settings['display_school_logos']))
                                                        <input type="checkbox" class="form-check-input" name="display_school_logos" value="1" {{ $settings['display_school_logos'] ? 'checked' : '' }}>{{ __('display_school_logos') }}
                                                    @else
                                                        <input type="checkbox" class="form-check-input" name="display_school_logos" value="1" checked>{{ __('display_school_logos') }}
                                                    @endif
                                                    
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <div class="d-flex">
                                            <div class="form-check w-fit-content ml-3">
                                                <label class="form-check-label ml-4">
                                                    @if (isset($settings['display_counters']))
                                                        <input type="checkbox" class="form-check-input" name="display_counters" value="1" {{ $settings['display_counters'] ? 'checked' : '' }}>{{ __('display_counters') }}
                                                    @else
                                                        <input type="checkbox" class="form-check-input" name="display_counters" value="1" checked>{{ __('display_counters') }}
                                                    @endif
                                                    
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('about_us') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="title">{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('about_us_title', $settings['about_us_title'] ?? null, ['required','class' => 'form-control','placeholder' => __('title')]) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="heading">{{ __('heading') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('about_us_heading', $settings['about_us_heading'] ?? null, ['required','class' => 'form-control', 'placeholder' => __('heading')]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="description">{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('about_us_description', $settings['about_us_description'] ?? null, ['required','class' => 'form-control','rows' => 5, 'placeholder' => __('description')]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="points">{{ __('points') }} <span class="text-danger">*</span> <span class="text-small text-info">({{ __('please_use_commas_or_press_enter_to_add_multiple_points') }})</label>
                                        {!! Form::text('about_us_points', $settings['about_us_points'] ?? null, ['required','class' => 'form-control', 'id' => 'tags', 'placeholder' => __('about_us_points')]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <label for="image">{{ __('image') }} </label>
                                        <input type="file" name="about_us_image" class="file-upload-default" />
                                        <div class="input-group col-xs-12">
                                            <input type="text" class="form-control file-upload-info"
                                                id="about_us_image" disabled=""
                                                placeholder="{{ __('image') }}" />
                                            <span class="input-group-append">
                                                <button class="file-upload-browse btn btn-theme"
                                                    type="button">{{ __('upload') }}</button>
                                            </span>
                                            <div class="col-md-12 mt-2">
                                                <img height="50px" src='{{ $settings['about_us_image'] ?? asset('assets/landing_page_images/whyBestImg.png') }}'
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('custom_package_section') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            @if (isset($settings['custom_package_status']) && $settings['custom_package_status'] == 1)
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" name="custom_package_status" id="enable" checked value="1">{{__("enable")}} </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" name="custom_package_status" id="disable" value="0">{{__("disable")}}
                                                    </label>
                                                </div>
                                            @else
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" name="custom_package_status" id="enable" value="1">{{__("enable")}} </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" name="custom_package_status" checked id="disable" value="0">{{__("disable")}}
                                                    </label>
                                                </div>
                                            @endif
                                            
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="description">{{ __('description') }} </label>
                                        {!! Form::textarea('custom_package_description', $settings['custom_package_description'] ?? null, ['class' => 'form-control','rows' => 5, 'placeholder' => __('description')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('download_our_app_section') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="image">{{ __('image') }} </label>
                                    <input type="file" name="download_our_app_image" class="file-upload-default" />
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info"
                                            id="download_our_app_image" disabled=""
                                            placeholder="{{ __('image') }}" />
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme"
                                                type="button">{{ __('upload') }}</button>
                                        </span>
                                        <div class="col-md-12 mt-2">
                                            <img height="50px" src='{{ $settings['download_our_app_image'] ?? asset('assets/landing_page_images/ourApp.png') }}'
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="description">{{ __('description') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('download_our_app_description', $settings['download_our_app_description'] ?? null, ['required','class' => 'form-control','rows' => 5, 'placeholder' => __('description')]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('social_media_links') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="facebook">{{ __('facebook') }}</label>
                                        <input name="facebook" id="facebook" value="{{ $settings['facebook'] ?? '' }}" type="text" placeholder="{{ __('facebook') }}" class="form-control" />
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="instagram">{{ __('instagram') }}</label>
                                        <input name="instagram" id="instagram" value="{{ $settings['instagram'] ?? '' }}" type="text" placeholder="{{ __('instagram') }}" class="form-control" />
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="linkedin">{{ __('linkedin') }} </label>
                                        <input name="linkedin" id="linkedin" value="{{ $settings['linkedin'] ?? '' }}" type="text" placeholder="{{ __('linkedin') }}" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="border border-secondary rounded-lg my-4 mx-1">
                                <div class="col-md-12 mt-3">
                                    <h4>{{ __('Footer Settings') }}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <hr class="mt-0">
                                </div>
                                <div class="row my-4 mx-1">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('short_description') }}</label>
                                        <textarea name="short_description" class="form-control" id="short_description" required placeholder="{{__('short_description')}}">{{$settings['short_description'] ?? ''}}</textarea>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <label for="">{{ __('footer_text') }}</label>
                                        <textarea id="tinymce_message" name="footer_text" id="footer_text" required placeholder="{{__('footer_text')}}">{{$settings['footer_text'] ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function restore_default_color(value)
        {
            if (value == 1) {
                $('#theme_primary_color').val('#56CC99');
                $('.theme_primary_color').asColorPicker('val', '#56CC99');
            }

            if (value == 2) {
                $('#theme_secondary_color').val('#215679');
                $('.theme_secondary_color').asColorPicker('val', '#215679');
            }
            if (value == 3) {
                $('#theme_secondary_color_1').val('#38A3A5');
                $('.theme_secondary_color_1').asColorPicker('val', '#38A3A5');
            }
            if (value == 4) {
                $('#theme_primary_background_color').val('#F2F5F7');
                $('.theme_primary_background_color').asColorPicker('val', '#F2F5F7');
            }
            if (value == 5) {
                $('#theme_text_secondary_color').val('#5C788C');
                $('.theme_text_secondary_color').asColorPicker('val', '#5C788C');
            }
            
        }
    </script>
@endsection