<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Certificate</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/certificate.css') }}" />

</head>

<body>
    <div class="certificate">
        {{-- Loop --}}
        @foreach ($users as $user)
        <div class="sheet">
            <div class="template">

                {{-- Background image --}}
                @if ($certificateTemplate->background_image)
                    <img src="{{ $certificateTemplate->background_image }}" class="background-image" alt="">
                @else
                    <div class="frame-border"></div>
                @endif
                

                {{-- School address --}}
                @if (in_array('school_address', $certificateTemplate->fields))
                    <div {!! $style['school_address'] !!}>
                        {{ $settings['school_address'] }}
                    </div>
                @endif

                {{-- School mobile --}}
                @if (in_array('school_mobile', $certificateTemplate->fields))
                    <div {!! $style['school_mobile'] !!}>
                        {{ $settings['school_phone'] }}
                    </div>
                @endif

                {{-- School email --}}
                @if (in_array('school_email', $certificateTemplate->fields))
                    <div {!! $style['school_email'] !!}>
                        {{ $settings['school_email'] }}
                    </div>
                @endif

                {{-- User image --}}
                @if (in_array('user_image', $certificateTemplate->fields))
                    <img src="{{ $user['image'] }}" {!! $style['user_image'] !!} alt="" class="user_image">
                @endif
                {{-- School logo --}}
                @if (in_array('school_logo', $certificateTemplate->fields))
                    <img src="{{ $settings['vertical_logo'] }}" {!! $style['school_logo'] !!} alt="" class="school_logo">
                @endif

                {{-- School name --}}
                @if (in_array('school_name', $certificateTemplate->fields))
                <div {!! $style['school_name'] !!} class="school-name">
                    <b>{{ $settings['school_name'] }}</b>
                </div>
                @endif

                {{-- Signature --}}
                @if (in_array('signature', $certificateTemplate->fields))
                    <img src="{{ $settings['signature'] ?? '' }}" {!! $style['signature'] !!} alt="" class="school_logo">
                @endif

                {{-- Isuue date --}}
                @if (in_array('issue_date', $certificateTemplate->fields))
                    <div {!! $style['issue_date'] !!} class="issue-date">
                        {{ date($settings['date_format'],strtotime(date('Y-m-d'))) }}
                    </div>
                @endif

                {{-- Title --}}
                @if (in_array('title', $certificateTemplate->fields))
                    <div class="title" {!! $style['title'] !!}>
                        {{ $certificateTemplate->name }}
                    </div>
                @endif

                {{-- Description --}}
                <div class="description" {!! $style['description'] !!}>
                    {!! $user['description'] !!}
                </div>
            </div>
        </div>
        @endforeach
        
    </div>
</body>
<style>
    .template {
        width: {{ $layout['width'] }};
        height: {{ $layout['height'] }};
        position: relative;
    }
    .frame-border {
        width: {{ $layout['width'] }};
        height: {{ $layout['height'] }};
        border: 1px solid black;
    }

    .sheet {
        width: {{ $layout['width'] }};
        height: {{ $layout['height'] }};
    }
    .school_logo {
        height: 100px !important;
    }
    .background-image {
        width: {{ $layout['width'] }};
        height: {{ $layout['height'] }};
    }
    .user_image {
        height: {{ $certificateTemplate->image_size }}px;
        width: {{ $certificateTemplate->image_size }}px;
    }
    
</style>
@if ($certificateTemplate->user_image_shape == 'Round')
        <style>
            .user_image {
                border-radius: 50%;
            }
        </style>
    @else
        <style>
            .user_image {
                border-radius: 6%;
            }
        </style>
    @endif

</html>
