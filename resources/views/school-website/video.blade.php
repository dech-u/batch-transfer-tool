@extends('layouts.school.master')
@section('title')
    Videos
@endsection
@section('content')
<style>
    ul {
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

<section class="videosGallery commonWaveSect commonMT">
    <div class="container">
        <div class="row videosGalleryContainer">
    
            <div id="Center">
                <ul id="waterfall">
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript">
        
    $(document).ready(function ()
    {
        $('#waterfall').NewWaterfall({
            width: 360,
            delay: 100,
        });
    });

    function random(min, max)
    {
        return min + Math.floor(Math.random() * (max - min + 1))
    }
    var loading = false;
    var dist = 600;
    var num = 1;
    var count = 0; // Current count of loaded items
    var maxCount = {{ count($galleries) }};
    setInterval(function ()
    {
        if ($(window).scrollTop() >= $(document).height() - $(window).height() - dist && !loading && count < maxCount)
        {
            loading = true;
            @foreach($galleries as $row)
                var height = random(200, 400);
                $("#waterfall").append("<li><div class='video1 videos' style='height:" + height + "px'> <a href='{{ url('school/videos',$row->id) }}'> <div class='detailArr'> <img src='{{ asset('assets/school/images/videoPlayIcon.png') }}' alt=''> <span>{{ $row->title }}</span> </div> <img style='height:" + height + "px;width: 100%;' src='{{ $row->thumbnail }}' alt=''> </a> </div></li>");

                count++;
            @endforeach
            loading = false;
        }
    }, 60);
</script>
@endsection