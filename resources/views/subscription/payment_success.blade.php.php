@extends('layouts.master')

@section('title', 'Payment Success')

@section('content')
<div class="container mt-5">
    <div class="alert alert-success text-center">
        <h2>Payment Successful!</h2>
        <p>Your payment was received and your subscription is now active.</p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
    </div>
</div>
@endsection