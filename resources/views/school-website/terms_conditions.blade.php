@extends('layouts.school.master')
@section('title')
    Terms & Conditions
@endsection
@section('content')
    <div class="breadcrumb">
        <div class="container">
            <div class="contentWrapper">
                <span class="title"> Terms & Conditions </span>
                <span>
                    <a href="{{ url('/') }}" class="home">Home</a>
                    <span><i class="fa-solid fa-caret-right"></i></span>
                    <span class="page">Terms & Conditions</span>
                </span>
            </div>
        </div>
    </div>
    
    <section class="aboutUs commonMT commonWaveSect">
        <div class="container">
            <div class="row aboutWrapper">
                <div class="title text-center">
                    <h1>Terms & Conditions</h1>
                </div>

                <div class="col-sm-12 col-md-12">
                    <div class="aboutContentWrapper">
                        <span class="commonDesc">
                            {!! htmlspecialchars_decode($schoolSettings['terms_condition'] ?? '') !!}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
