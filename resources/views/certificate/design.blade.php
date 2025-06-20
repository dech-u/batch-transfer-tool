@extends('layouts.master')

@section('title')
    {{ __('certificate') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_certificate') . ' ' . __('template') }}
            </h3>
        </div>
        <form class="pt-3" id="edit-form" action="{{ route('certificate-template.design.store',$certificateTemplate->id) }}" method="POST" novalidate="novalidate" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="custom-card-body">
                        <h4 class="card-title">
                            {{ __('design') }} {{ __('certificate') . ' ' . __('template') }}
                        </h4>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <div class="d-flex">
                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('school_name', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="school_name">{{ __('school_name') }}
                                        </label>
                                    </div>

                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('school_logo', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="school_logo">{{ __('school_logo') }}
                                        </label>
                                    </div>

                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('signature', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="signature">{{ __('signature') }}
                                        </label>
                                    </div>

                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('issue_date', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="issue_date">{{ __('issue_date') }}
                                        </label>
                                    </div>

                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('user_image', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="user_image">{{ __('user_image') }}
                                        </label>
                                    </div>

                                    {{--  --}}

                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('school_address', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="school_address">{{ __('school_address') }}
                                        </label>
                                    </div>
                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('school_mobile', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="school_mobile">{{ __('school_mobile') }}
                                        </label>
                                    </div>
                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('school_email', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="school_email">{{ __('school_email') }}
                                        </label>
                                    </div>

                                    <div class="form-check w-fit-content ml-3">
                                        <label class="form-check-label ml-4">
                                            <input type="checkbox" class="form-check-input" {{ in_array('title', $certificateTemplate->fields) ? 'checked' : '' }} name="school_data[]" value="title">{{ __('title') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-12 col-md-12">
                                <span class="text-danger">{{ __('note_required_medium_or_large_screen_only') }}</span>
                            </div>

                            <div class="form-group col-sm-12 col-md-12">
                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" id="reset-btn" type="reset" value={{ __('reset') }}>
                            </div>


                            {{-- school logo --}}
                            <input type="hidden" name="style[school_logo]" value="{{ $style['school_logo'] ?? '' }}" id="school_logo">
                            {{-- User image --}}
                            <input type="hidden" name="style[user_image]" value="{{ $style['user_image'] ?? '' }}" id="user_image">
                            {{-- Title --}}
                            <input type="hidden" name="style[title]" value="{{ $style['title'] ?? '' }}" id="title">
                            {{-- Description --}}
                            <input type="hidden" name="style[description]" value="{{ $style['description'] ?? '' }}" id="description">
                            {{-- Issue date --}}
                            <input type="hidden" name="style[issue_date]" value="{{ $style['issue_date'] ?? '' }}" id="issue_date">
                            {{-- Signature --}}
                            <input type="hidden" name="style[signature]" value="{{ $style['signature'] ?? '' }}" id="signature">
                            {{-- School name --}}
                            <input type="hidden" name="style[school_name]" value="{{ $style['school_name'] ?? '' }}" id="school_name">
                            {{-- School address --}}
                            <input type="hidden" name="style[school_address]" value="{{ $style['school_address'] ?? '' }}" id="school_address">
                            {{-- School mobile --}}
                            <input type="hidden" name="style[school_mobile]" value="{{ $style['school_mobile'] ?? '' }}" id="school_mobile">
                            {{-- School email --}}
                            <input type="hidden" name="style[school_email]" value="{{ $style['school_email'] ?? '' }}" id="school_email">
                        </div>
                    </div>
                </div>
            </div>

            <div style="position: relative">

                <div class="design" id="draggableElements">
                    @if ($certificateTemplate->background_image)
                        {{-- Background image --}}
                        <img src="{{ $certificateTemplate->background_image }}" class="background-image" alt="">    
                    @else
                        <div class="frame-border">

                        </div>
                    @endif
                    
                    {{-- School logo --}}
                    <img id="item_school_logo" class="draggableItem height-100" {!! $style['school_logo'] ?? '' !!} src="{{ $settings['vertical_logo'] }}" alt="school_logo">
                    {{-- User image --}}
                    <img id="item_user_image" class="draggableItem" {!! $style['user_image'] ?? '' !!} src="{{ url('assets/dummy_logo.jpg') }}" height="{{ $certificateTemplate->image_size }}" width="{{ $certificateTemplate->image_size }}" alt="user_image">

                    {{-- Title --}}
                    <div class="draggableItem p-2 draggableText text-center title" {!! $style['title'] ?? '' !!} id="item_title">
                        <b>{{ $certificateTemplate->name }}</b>
                    </div>

                    {{-- Description --}}
                    <div class="draggableItem p-2 w-75 certificate-description draggableText" {!! $style['description'] ?? '' !!} id="item_description">
                        {!! $certificateTemplate->description !!}
                    </div>

                    {{-- Signature --}}
                    <img id="item_signature" class="draggableItem height-100" {!! $style['signature'] ?? '' !!} src="{{ $settings['signature'] ?? '' }}" alt="signature">

                    {{-- Issue date --}}
                    <div class="draggableItem p-1 draggableText text-center" {!! $style['issue_date'] ?? '' !!} id="item_issue_date">
                        <b>Issue Date</b>
                    </div>

                    {{-- School Name --}}
                    <div class="draggableItem p-2 draggableText text-center h2" {!! $style['school_name'] ?? '' !!} id="item_school_name">
                        <b>{{ $settings['school_name'] }}</b>
                    </div>

                    {{-- School address --}}
                    <div class="draggableItem p-1 draggableText text-center" {!! $style['school_address'] ?? '' !!} id="item_school_address">
                        {{ $settings['school_address'] }}
                    </div>

                    {{-- School mobile --}}
                    <div class="draggableItem p-1 draggableText text-center" {!! $style['school_mobile'] ?? '' !!} id="item_school_mobile">
                        {{ $settings['school_phone'] }}
                    </div>

                    {{-- School email --}}
                    <div class="draggableItem p-1 draggableText text-center" {!! $style['school_email'] ?? '' !!} id="item_school_email">
                        {{ $settings['school_email'] }}
                    </div>
                </div>

            </div>
        </div>
    </form>
    </div>
@endsection

@section('script')
    <script>
        window.onload = setTimeout(() => {
            $('.page_layout').trigger('change');
            $('#certificate_type_id').trigger('change');
            $('.form-check-input').trigger('change');
        }, 500);

        $('#reset-btn').click(function (e) { 
            e.preventDefault();
            // School name
            $('#item_school_name').css('top', '60px');
            $('#item_school_name').css('left', '480px');
            // User image
            $('#item_user_image').css('top', '140px');
            $('#item_user_image').css('left', '145px');
            // Title
            $('#item_title').css('top', '290px');
            $('#item_title').css('left', '145px');
            // Description
            $('#item_description').css('top', '355px');
            $('#item_description').css('left', '145px');
            // Issue date
            $('#item_issue_date').css('top', '610px');
            $('#item_issue_date').css('left', '360px');
            // School logo
            $('#item_school_logo').css('top', '600px');
            $('#item_school_logo').css('left', '500px');
            // Signature
            $('#item_signature').css('top', '560px');
            $('#item_signature').css('left', '655px');

            // School address
            $('#item_school_address').css('top', '85px');
            $('#item_school_address').css('left', '125px');

            // School mobile
            $('#item_school_mobile').css('top', '130px');
            $('#item_school_mobile').css('left', '125px');

            // School email
            $('#item_school_email').css('top', '175px');
            $('#item_school_email').css('left', '125px');
            
        });


        let isDragging = false;
        let currentImage = null;
        let offsetX, offsetY;

        const container = document.getElementById('draggableElements');
        const draggableImages = document.querySelectorAll('.draggableItem');

        draggableImages.forEach(element => {
            element.addEventListener('mousedown', (e) => {
                isDragging = true;
                currentImage = element;
                offsetX = e.clientX - element.getBoundingClientRect().left;
                offsetY = e.clientY - element.getBoundingClientRect().top;
            });
        });

        document.addEventListener('mousemove', (e) => {
        if (isDragging && currentImage) {
            const containerRect = container.getBoundingClientRect();
            const imgRect = currentImage.getBoundingClientRect();

            let newLeft = e.clientX - containerRect.left - offsetX;
            let newTop = e.clientY - containerRect.top - offsetY;

            // Ensure the image stays within the bounds of the container
            newLeft = Math.max(0, Math.min(newLeft, containerRect.width - imgRect.width));
            newTop = Math.max(0, Math.min(newTop, containerRect.height - imgRect.height));

            currentImage.style.left = newLeft + 'px';
            currentImage.style.top = newTop + 'px';
        }
        });

        document.addEventListener('mouseup', () => {
            if (isDragging && currentImage) {
                isDragging = false;
                // console.log(`Current Position of ${currentImage.alt} - Left: ${currentImage.style.left}, Top: ${currentImage.style.top}`);
                let style = '';
                let type = currentImage.id;
                style = 'style="position:absolute; left: '+currentImage.style.left+';top: '+currentImage.style.top+'"';
                type = type.replace('item_', "");
                $('#'+type).val(style);
                currentImage = null;

            }
        });

        $('.form-check-input').change(function (e) { 
            e.preventDefault();
            let field = '#item_'+$(this).val();
            let status = $(this).is(':checked');
            if (status) {
                $(field).show(500);
            } else {
                $(field).hide(500);
            }
        });


    </script>
@endsection
@section('css')
    <style>
        .design {
            height : {{ $layout['height'] }};
            width : {{ $layout['width'] }};
            user-select: none; /* Standard property */
            -webkit-user-select: none; /* Safari */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none;

        }
        .frame-border {
            height : {{ $layout['height'] }};
            width : {{ $layout['width'] }};
            border: 1px solid black;
        }
        .background-image {
            height : {{ $layout['height'] }};
            width : {{ $layout['width'] }};
        }
        .draggableItem {
            position: absolute;
            cursor: pointer;
        }
        .height-100 {
            height: 100px;
        }
        .title {
            font-weight: bold;
            font-size: 38px;
        }

    </style>

    @if ($certificateTemplate->user_image_shape == 'Round')
        <style>
            #item_user_image {
                border-radius: 50%;
            }
        </style>
    @else
        <style>
            #item_user_image {
                border-radius: 6%;
            }
        </style>
    @endif

@endsection
