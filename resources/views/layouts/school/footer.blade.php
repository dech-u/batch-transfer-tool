<footer class="">
    <div class="container">
        <div class="row">

            <div class="col-12 infoContainer">
                <div class="row">
                    <div class="col-md-6 col-lg-4 infoDivWrapper">
                        <div class="iconDiv">
                            <span class="iconWrapper"><i class="fa-solid fa-location-dot"></i></span>
                        </div>
                        <div class="textDiv">
                            <span>{{ __('school_address') }}</span>
                            <span>{{ $schoolSettings['school_address'] ?? '' }}</span>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 infoDivWrapper">
                        <div class="iconDiv">
                            <span class="iconWrapper"><i class="fa-solid fa-envelope"></i></span>
                        </div>
                        <div class="textDiv">
                            <span>{{ __('mail_us') }}</span>
                            <span><a class="footer-contact" href="mailto:{{ $schoolSettings['school_email'] ?? '' }}">{{ $schoolSettings['school_email'] ?? '' }}</a></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 infoDivWrapper">
                        <div class="iconDiv">
                            <span class="iconWrapper"><i class="fa-solid fa-phone-volume"></i></i></span>
                        </div>
                        <div class="textDiv">
                            <span>{{ __('call_us') }}</span>
                            <span><a class="footer-contact" href="tel:+{{ $schoolSettings['school_phone'] ?? '' }}">{{ $schoolSettings['school_phone'] ?? '' }}</a></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="companyInfoWrapper">
                    <div>
                        <a href="{{ url('/') }}">
                            <img src="" class="footer-logo companyLogo" alt="" />
                        </a>
                    </div>
                    <div>
                        <span class="commonDesc">
                            {{ $schoolSettings['short_description'] ?? '' }}
                        </span>
                    </div>

                    <div class="socialIcons">
                        @if ($schoolSettings['facebook'] ?? '')
                            <span>
                                <a href="{{ $schoolSettings['facebook'] }}" target="_blank">
                                    <i class="fa-brands fa-facebook"></i>
                                </a>
                            </span>    
                        @endif

                        @if ($schoolSettings['instagram'] ?? '')
                            <span>
                                <a href="{{ $schoolSettings['instagram'] }}" target="_blank">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </span>    
                        @endif

                        @if ($schoolSettings['linkedin'] ?? '')
                            <span>
                                <a href="{{ $schoolSettings['linkedin'] }}" target="_blank">
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                            </span>    
                        @endif

                        @if ($schoolSettings['twitter'] ?? '')
                            <span>
                                <a href="{{ $schoolSettings['twitter'] }}" target="_blank">
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                            </span>    
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="linksWrapper usefulLinksDiv">
                    <span class="title">{{ __('useful_links') }}</span>
                    <span><a href="{{ url('/') }}">{{ __('home') }}</a></span>
                    <span><a href="{{ url('school/about-us') }}">{{ __('about_us') }}</a></span>
                    <span><a href="{{ url('school/photos') }}">{{ __('photos') }}</a></span>
                    <span><a href="{{ url('school/videos') }}">{{ __('videos') }}</a></span>
                    <span><a href="{{ url('school/contact-us') }}">{{ __('contact_us') }}</a></span>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-2">
                <div class="linksWrapper">
                    <span class="title">{{ __('quick_links') }}</span>
                    <span>
                        <a href="{{ url('login') }}"> {{ __('admin_login') }}</a>
                    </span>
                    <span>
                        <a href="{{ url('school/terms-conditions') }}">{{ __('terms_condition') }}</a>
                    </span>
                    <span>
                        <a href="{{ url('school/privacy-policy') }}"> {{ __('privacy_policy') }}</a>
                    </span>

                    <span>
                        <a href="{{ url('school/refund-cancellation-policy') }}"> {{ __('refund_cancellation') }}</a>
                    </span>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-4">
                <div class="linksWrapper">
                    <span class="title">{{ __('download_eschool_apps') }}</span>

                    <div class="appContainer">
                        <a class="appWrapper" href="{{ $systemSettings['app_link'] ?? '' }}">
                            <img src="{{ asset('assets/school/images/PlayStore.png') }}" alt="">
                            <span class="appNameWrapper">
                                <span>{{ __('student_parent') }}</span>
                                <span>{{ __('android_app') }}</span>
                            </span>
                        </a>

                        <a class="appWrapper" href="{{ $systemSettings['ios_app_link'] ?? '' }}">
                            <img src="{{ asset('assets/school/images/AppStore.png') }}" alt="">
                            <span class="appNameWrapper">
                                <span>{{ __('student_parent') }}</span>
                                <span>{{ __('ios_app') }}</span>
                            </span>
                        </a>
                    </div>

                    <div class="appContainer mt-4">
                        <a class="appWrapper" href="{{ $systemSettings['teacher_app_link'] ?? '' }}">
                            <img src="{{ asset('assets/school/images/PlayStore.png') }}" alt="">
                            <span class="appNameWrapper">
                                <span>{{ 'staff_teacher' }}</span>
                                <span>{{ __('android_app') }}</span>
                            </span>
                        </a>
                        <a class="appWrapper" href="{{ $systemSettings['teacher_ios_app_link'] ?? '' }}">
                            <img src="{{ asset('assets/school/images/AppStore.png') }}" alt="">
                            <span class="appNameWrapper">
                                <span>{{ 'staff_teacher' }}</span>
                                <span>{{ __('ios_app') }}</span>
                            </span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="copyRightText">
        <span class="text-center">
            {!! isset($schoolSettings['footer_text']) ? $schoolSettings['footer_text'] : $systemSettings['footer_text'] !!}
        </span>
    </div>
</footer>
