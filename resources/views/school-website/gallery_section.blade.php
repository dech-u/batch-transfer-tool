@if (isset($schoolSettings['gallery_status']) && $schoolSettings['gallery_status'] == 1 && count($galleries)) 
    <section class="ourGalleryPhotos commonMT commonWaveSect">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="flex_column_center">
                        <span class="commonTag"> {{ $schoolSettings['gallery_section'] ?? 'Our Photo Gallery' }} </span>
                        <span class="commonTitle">

                            {{ $schoolSettings['gallery_title'] ?? 'Tiny Scholars Showcase' }}
                        </span>
                        <span class="commonDesc">
                            {{ $schoolSettings['gallery_description'] ?? '' }}
                        </span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row galleryImgsContainer">
                        <div class="col-md-6 col-lg-6 leftImgs">
                            @foreach ($galleries->take(1) as $row)
                                <div class="bigImg">
                                    <img src="{{ $row->thumbnail }}" alt="">
                                    <a href="{{ url('school/photos',$row->id) }}">
                                        <div class="detailsCard">
                                            <img src="{{ asset('assets/school/images/bx-plus-circle.png') }}"
                                                alt="">
                                            <span>{{ $row->title }}</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                            <div class="smallImgs">
                                @foreach ($galleries->skip(1)->take(2) as $row)
                                    <div class="leftSmallImg1">
                                        <img src="{{ $row->thumbnail }}" alt="">
                                        <a href="{{ url('school/photos',$row->id) }}">
                                            <div class="detailsCard">
                                                <img src="{{ asset('assets/school/images/bx-plus-circle.png') }}"
                                                    alt="">
                                                <span>{{ $row->title }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-6 rightImgs">
                            <div class="upperImgs">
                                @foreach ($galleries->skip(3)->take(2) as $row)
                                    <div class="upperImg1">
                                        <img src="{{ $row->thumbnail }}" alt="">
                                        <a href="{{ url('school/photos',$row->id) }}">
                                            <div class="detailsCard">
                                                <img src="{{ asset('assets/school/images/bx-plus-circle.png') }}" alt="">
                                                <span>{{ $row->title }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <div class="lowerImgs">
                                @foreach ($galleries->skip(5)->take(2) as $row)
                                    <div class="upperImg2">
                                        <img src="{{ $row->thumbnail }}" alt="">
                                        <a href="{{ url('school/photos',$row->id) }}">
                                            <div class="detailsCard">
                                                <img src="{{ asset('assets/school/images/bx-plus-circle.png') }}" alt="">
                                                <span>{{ $row->title }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ourGalleryPhotos ends here  -->
@endif

