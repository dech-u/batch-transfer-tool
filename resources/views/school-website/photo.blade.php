@extends('layouts.school.master')
@section('title')
    Photos
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
                <span class="title"> Gallery </span>
                <span>
                    <a href="{{ url('/') }}" class="home">Home</a>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="home">Gallery</span>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="page">Photos</span>
                </span>
            </div>
        </div>
    </div>




    <section class="photosGallery commonMT commonWaveSect">
        <div class="container">
            <div id="Center">
                <ul id="waterfall"></ul>
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
        if ($(window).scrollTop() >= ($(document).height() - $(window).height() - 500) - dist && !loading && count < maxCount)
        {
            loading = true;
            @foreach($galleries as $row)
                var height = random(200, 400);
                $("#waterfall").append("<li><a href='{{ url('school/photos',$row->id) }}'><div class='m-2 upperBigImg1' style='height:" + height + "px'><img style='height:" + height + "px;width: 100%;' src='{{ $row->thumbnail }}'><div class='detailArr'> <img src='{{ asset('assets/school/images/bx-plus-circle.png') }}' alt=''> <span>{{ $row->title }}</span></div></div></a></li>");

                count++;
            @endforeach
            loading = false;
        }
    }, 60);
</script>
@endsection
