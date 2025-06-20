@extends('layouts.school.master')
@section('title')
    Videos
@endsection
@section('content')
<style>
    ol, ul {
        padding-left: unset !important;
    }
</style>
    <div class="breadcrumb">
        <div class="container">
            <div class="contentWrapper">
                <span class="title">
                    Gallery
                </span>
                <span>
                    <a href="{{ url('/') }}" class="home">Home</a>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="home">Gallery</span>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="page">Videos</span>
                </span>
            </div>
        </div>
    </div>


    <section class="videosGallery commonMT commonWaveSect">
        <div class="container">
            <div id="Center">
                <ul id="waterfall"></ul>
            </div>
        </div>
    </section>

    <div id="lightbox" class="lightbox">
        <div class="lightbox-size">
            <span class="close"><i class="fa fa-close"></i></span>
            <img class="lightbox-content" id="lightbox-img">
            <iframe class="lightbox-content responsive-iframe" width="560" height="315" id="lightbox-video" allowfullscreen></iframe>
            <div class="caption" id="caption"></div>
        </div>
        
    </div>

    
@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#waterfall').NewWaterfall({
            width: 360,
            delay: 100,
        });
    });

    function random(min, max) {
        return min + Math.floor(Math.random() * (max - min + 1))
    }
    var loading = false;
    var dist = 500;
    var num = 1;
    var count = 0; // Current count of loaded items
    var maxCount = {{ count($videos->file) }};
    setInterval(function() {
        if ($(window).scrollTop() >= $(document).height() - $(window).height() - dist && !loading && count <
            maxCount) {
            loading = true;
            @foreach ($videos->file as $row)
                var height = random(200, 400);
                $("#waterfall").append("<li><div class='m-2 video1 videos' style='height:" + height +
                    "px'><img class='thumbnail video-thumbnail' data-video='{{ $row->youtube_url_action->embed_url }}' style='height:" + height +
                    "px;width: 100%;' src='{{ $row->youtube_url_action->img }}' alt=''><div class='detailArr'> <img src='{{ asset('assets/school/images/videoPlayIcon.png') }}' alt=''></div></div></li>"
                );

                count++;
            @endforeach
            loading = false;
        }
    }, 60);

</script>
@endsection
