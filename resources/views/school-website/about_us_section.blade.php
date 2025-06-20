@if (isset($schoolSettings['about_us_status']) && $schoolSettings['about_us_status'] == 1)
    <section class="aboutUs commonWaveSect">

        <div class="container">
            <div class="row aboutWrapper">
                <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="aboutImgWrapper">
                        <img src="{{ $schoolSettings['about_us_image'] ?? asset('assets/school/images/about us image.png') }}"
                            alt="" />
                    </div>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-6">
                    <div class="aboutContentWrapper">
                        <span class="commonTag"> {{ $schoolSettings['about_us_section'] ?? 'About Us' }} </span>
                        <span class="commonTitle">
                            {{ $schoolSettings['about_us_title'] ?? 'Personalized Learning for Every Student' }}
                        </span>
                        <span class="commonDesc">
                            {{ $schoolSettings['about_us_description'] ?? 'Personalized Learning for Every Student' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- aboutUs ends here  -->
@endif
