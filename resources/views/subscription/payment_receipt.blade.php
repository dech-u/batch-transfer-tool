@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>Payment Receipt</h3>
        </div>
        <div class="card-body">
            <p><strong>Transaction Reference:</strong> {{ $transaction->order_id }}</p>
            <p><strong>Amount:</strong> {{ $transaction->amount }} {{ $transaction->currency ?? '' }}</p>
            <p><strong>Status:</strong> 
                @if($transaction->payment_status === 'succeed')
                    <span class="badge bg-success">Success</span>
                @elseif($transaction->payment_status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                @else
                    <span class="badge bg-danger">Failed</span>
                @endif
            </p>
            <p><strong>Date:</strong> {{ $transaction->created_at }}</p>
            <p><strong>Payment Gateway:</strong> {{ $transaction->payment_gateway }}</p>
            <p><strong>User:</strong> {{ $transaction->user->name ?? 'N/A' }}</p>
            <hr>
            <a href="#" onclick="window.print()" class="btn btn-primary">Download/Print Receipt</a>
            <a href="{{ route('subscription.index') }}" class="btn btn-secondary">Back to Subscriptions</a>
        </div>
    </div>
</div>
@endsection 