@extends('layouts.school.master')
@section('title')
    Contact  Us
@endsection
@section('content')
    <div class="breadcrumb">
        <div class="container">
            <div class="contentWrapper">
                <span class="title"> Contact Us </span>
                <span>
                    <a href="{{ url('/') }}" class="home">Home</a>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="page">Contact Us</span>
                </span>
            </div>
        </div>
    </div>
    

    <section class="contactUs commonMT commonWaveSect">
        <div class="container">
            <div class="row">

                <div class="col-lg-6">

                    <div class="headlines">
                        <span>Get In Touch</span>
                        <span>Have Any Query?</span>
                    </div>

                    <div class="formWrapper">
                        <form action="{{ url('school/contact-us') }}" class="create-form-with-captcha" method="post">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="school_email" value="{{ $schoolSettings['school_email'] ?? '' }}">
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="d-flex flex-column gap-1">
                                        <label for="First Name">Name</label>
                                        <input type="text" name="name" required placeholder="Enter First Name"></input>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="d-flex flex-column gap-1">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" required placeholder="Enter Your Email"></input>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex flex-column gap-1">
                                        <label for="Message">Subject</label>
                                        <input name="subject" id="subject" required placeholder="Subject"></input>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex flex-column gap-1">
                                        <label for="Message">Message</label>
                                        <textarea name="message" id="message" required cols="30" rows="5"
                                            placeholder="Enter Message"></textarea>
                                    </div>
                                </div>

                                @if ($schoolSettings['SCHOOL_RECAPTCHA_SITE_KEY'] ?? '')
                                    <div class="col-12">
                                        <div class="g-recaptcha mt-4" data-sitekey={{ $schoolSettings['SCHOOL_RECAPTCHA_SITE_KEY'] }}></div>
                                    </div>    
                                @endif

                                <div class="col-4">
                                    <button type="submit" class="commonBtn">
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="col-12 infoContainer">
                        <div class="col-12">
                            <div class="mapWrapper commonMT">
                                <div>
                                    {!! $schoolSettings['google_map_link'] ?? '' !!}
                                    {{-- <iframe src="{{ $schoolSettings['google_map_link'] ?? '' }}" width="100%" height="100%" style="border:0;" allowfullscreen="true" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('script')
    <script async src="https://www.google.com/recaptcha/api.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('/assets/js/custom/common.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/custom.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/validate.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/function.js') }}"></script>
    <script src="{{ asset('/assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.js') }}"></script>

    <script src="{{ asset('assets/home_page/js/owl.carousel.min.js') }}"></script>
@endsection