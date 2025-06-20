<!DOCTYPE html>
@php
    $lang = Session::get('language');
@endphp
@if ($lang)
    @if ($lang->is_rtl)
        <html lang="en" dir="rtl">
            <link href="{{ asset('assets/home_page/css/style-rtl.css') }}" rel="stylesheet">
    @else
        <html lang="en">
    @endif
@else
    <html lang="en">
@endif

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta property="og:title" content="{{ $systemSettings['system_name'] ?? 'eSchool - Saas' }}">
    <meta property="og:image" content="{{ $systemSettings['horizontal_logo'] ?? asset('assets/home_page/images/logo.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:description" content="{{ $systemSettings['tag_line'] ?? 'eSchool - Saas' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')
        {{-- {{ config('app.name') }} --}}
        {{ $systemSettings['system_name'] ?? 'eSchool - Saas' }}
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.home_page.include')
    @yield('css')
</head>

<body class="sidebar-fixed">
    <div class="container-scroller">

        @yield('content')

        @include('layouts.home_page.footer')
    </div>
    @include('layouts.home_page.footer_js')
    @yield('js')
    @yield('script')
</body>

</html>
