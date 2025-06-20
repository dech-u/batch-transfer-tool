@extends('layouts.school.master')
@section('title')
    About Us
@endsection
@section('content')
    <div class="breadcrumb">
        <div class="container">
            <div class="contentWrapper">
                <span class="title"> About Us </span>
                <span>
                    <a href="{{ url('/') }}" class="home">Home</a>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="page">About Us</span>
                </span>
            </div>
        </div>
    </div>
    <div class="commonMT">
        @include('school-website.about_us_section')
    </div>

    @if (isset($schoolSettings['our_mission_status']) && $schoolSettings['our_mission_status'] == 1)
        <section class="whoWeAre commonMT">
            <div class="container">
                <div class="row whoWeAreContentWrapper">
                    <div class="col-lg-6 contentDiv">
                        <div class="flex_column_center">
                            <span class="commonTag"> {{ $schoolSettings['our_mission_section'] ?? 'Our Mission' }} </span>
                            <span class="commonTitle">
                                {{ $schoolSettings['our_mission_title'] ?? 'Discover Our Mission for eSchool' }}
                            </span>

                            <span class="commonDesc">
                                {{ $schoolSettings['our_mission_description'] ?? '' }}
                            </span>
                            <div class="listWrapper row">
                                @foreach ($schoolSettings['our_mission_points'] ?? [] as $item)
                                    <div class="list col-lg-6">
                                        <img src="{{ asset('assets/school/images/rightIcon.png') }}" alt="">
                                        <span>{{ $item }}</span>
                                    </div>    
                                @endforeach
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-6 whoweAreImgDiv">
                        <div class="">
                            <img src="{{ $schoolSettings['our_mission_image'] ?? asset('assets/school/images/ourMission.png') }}"
                                alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- whoWeAre ends here  -->
    @endif

    <section class="commonWaveSect ourTeacherAndGallery">
        @include('school-website.our_teacher_section')
        @include('school-website.gallery_section')
    </section>
@endsection
