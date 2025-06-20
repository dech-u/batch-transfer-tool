

<!-- swiper -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

<link rel="stylesheet" href="{{ asset('/assets/school/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/school/css/responsive.css') }}">

<link rel="stylesheet" href="{{ asset('/assets/school/css/custom.css') }}">

<link rel="stylesheet" href="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.css') }}">

<!-- bootstrap  -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />

{{-- <link rel="stylesheet" href="{{ asset('/assets/css/ekko-lightbox.css') }}"> --}}

{{-- <link rel="stylesheet" href="{{ asset('/assets/css/style.min.css') }}">
<script src="{{ asset('/assets/js/vendor.bundle.base.js') }}"></script> --}}

<link rel="shortcut icon" class="school-favicon" href=""/>

<style>
    :root {
    --primary-color: {{ $schoolSettings['primary_color'] ?? '#22577a' }};
    --primary-hover-color: {{ $schoolSettings['primary_hover_color'] ?? '#143449' }};
    --secondary-color1: {{ $schoolSettings['primary_color'] ?? '#22577a' }};
    --secondary-color2: {{ $schoolSettings['secondary_color'] ?? '#57cc99' }};
    
    --secondary-color3: #80ed99;
    --text--primary-color: #38a3a51f;
    
    --text--secondary-color: {{ $schoolSettings['text_secondary_color'] ?? '#2d2c2fb5' }};
    --text-white-color: #fff;
    --primary-background-color: {{ $schoolSettings['primary_background_color'] ?? '#f2f5f7' }};
}

</style>