@extends('layouts.home_page.master')
@section('title')
    {{ __($type ?? '') }} || 
@endsection
@section('content')
<style>
    :root {
    --primary-color: {{ $settings['theme_primary_color'] ?? '#56cc99' }};
    --secondary-color: {{ $settings['theme_secondary_color'] ?? '#215679' }};
    --secondary-color1: {{ $settings['theme_secondary_color_1'] ?? '#38a3a5' }};
    --primary-background-color: {{ $settings['theme_primary_background_color'] ?? '#f2f5f7' }};
    --text--secondary-color: {{ $settings['theme_text_secondary_color'] ?? '#5c788c' }};
    
}
</style>
<script src="{{ asset('assets/home_page/js/jquery-1-12-4.min.js') }}"></script>
<header class="navbar">
    <div class="container">
        <div class="navbarWrapper">
            <div class="navLogoWrapper">
                <div class="navLogo">
                    <a href="{{ url('/') }}">
                        <img src="{{ $settings['horizontal_logo'] ?? asset('assets/landing_page_images/Logo1.svg') }}" class="logo" alt="">
                    </a>

                </div>
            </div>
            <div class="menuListWrapper">
                <ul class="listItems">
                    <li>
                        <a href="{{ url('/') }}">{{ __('home') }}</a>
                    </li>
                    <li>
                        <a href="{{ url('/#features') }}">{{ __('features') }}</a>
                    </li>
                    <li>
                        <a href="{{ url('/#about-us') }}">{{ __('about_us') }}</a>
                    </li>
                    <li>
                        <a href="{{ url('/#pricing') }}">{{ __('pricing') }}</a>
                    </li>
                    @if (count($faqs))
                        <li>
                            <a href="{{ url('/#faq') }}">{{ __('faqs') }}</a>
                        </li>    
                    @endif
                    <li>
                        <a href="{{ url('/#contact-us') }}">{{ __('contact') }}</a>
                    </li>
                    @if (count($guidances))
                        <li>
                            <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                    id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('guidance') }}
                                </a>                                
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    @foreach ($guidances as $key => $guidance)
                                        <li><a class="dropdown-item" href="{{ $guidance->link }}">{{ $guidance->name }}</a></li>
                                        @if (count($guidances) > ($key + 1))
                                            <hr>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                    <li>
                        <div class="dropdown">
                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('language') }}
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                @foreach ($languages as $key => $language)
                                    <li><a class="dropdown-item" href="{{ url('set-language') . '/' . $language->code }}">{{ $language->name }}</a></li>
                                    @if (count($languages) > ($key + 1))
                                        <hr>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>

                </ul>
                <div class="hamburg">
                    <span data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight"
                        aria-controls="offcanvasRight"><i class="fa-solid fa-bars"></i></span>
                </div>
            </div>

            <div class="loginBtnsWrapper">
                <button class="commonBtn redirect-login">{{ __('login') }}</button>
                <button class="commonBtn" data-bs-toggle="modal" data-bs-target="#staticBackdrop">{{ __('start_trial') }}</button>
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
            aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <div class="navLogoWrapper">
                    <div class="navLogo">
                        <img src="{{ $settings['horizontal_logo'] ?? asset('assets/landing_page_images/Logo1.svg') }}" alt="">
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="listItems">
                    <li>
                        <a href="{{ url('/') }}">{{ __('home') }}</a>
                    </li>
                    <li>
                        <a href="{{ url('/#features') }}">{{ __('features') }}</a>
                    </li>
                    <li>
                        <a href="{{ url('/#about-us') }}">{{ __('about_us') }}</a>
                    </li>
                    <li>
                        <a href="{{ url('/#pricing') }}">{{ __('pricing') }}</a>
                    </li>
                    @if (count($faqs))
                        <li>
                            <a href="{{ url('/#faq') }}">{{ __('faqs') }}</a>
                        </li>    
                    @endif
                    <li>
                        <a href="{{ url('/#contact-us') }}">{{ __('contact') }}</a>
                    </li>
                    @if (count($guidances))
                        <li>
                            <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                    id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('guidance') }}
                                </a>                                
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    @foreach ($guidances as $key => $guidance)
                                        <li><a class="dropdown-item" href="{{ $guidance->link }}">{{ $guidance->name }}</a></li>
                                        @if (count($guidances) > ($key + 1))
                                            <hr>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                    <li>
                        <div class="dropdown">
                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('language') }}
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                @foreach ($languages as $key => $language)
                                    <li><a class="dropdown-item" href="{{ url('set-language') . '/' . $language->code }}">{{ $language->name }}</a></li>
                                    @if (count($languages) > ($key + 1))
                                        <hr>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </li>

                </ul>

                {{-- <div class="loginBtnsWrapper"> --}}
                    <button class="commonBtn redirect-login">{{ __('login') }}</button>
                    <button class="commonBtn" data-bs-toggle="modal" data-bs-dismiss="offcanvas" data-bs-target="#staticBackdrop">{{ __('start_trial') }}</button>
                {{-- </div> --}}
            </div>
        </div>
    </div>
</header>

<!-- navbar ends here  -->


<section class="features commonMT container" id="features">
    <div class="row">
        <div class="col-12">
            <div class="sectionTitle">
                <span>{{ __($type ?? '') }}</span>

            </div>
        </div>
        <div class="col-12">
            <div class="row cardWrapper">
                @if ($type == 'privacy-policy')
                    {!! htmlspecialchars_decode($settings['privacy_policy'] ?? '') !!}
                @endif
                @if ($type == 'terms-conditions')
                    {!! htmlspecialchars_decode($settings['terms_condition'] ?? '') !!}
                @endif
                @if ($type == 'refund-cancellation')
                    {!! htmlspecialchars_decode($settings['refund_cancellation'] ?? '') !!}
                @endif
            </div>
        </div>
    </div>
</section>



@endsection