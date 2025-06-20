@extends('layouts.master')

@section('title')
    {{ __('support') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('support') }}
            </h3>
        </div>
        <div class="row">
            @foreach ($support_staffs as $staff)
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row flex-wrap">
                                <img src="{{ $staff->user->image }}" class="img-lg rounded" alt="profile image">
                                <div class="mx-3">
                                    <h6>{{ $staff->user->full_name }}</h6>
                                    <p class="text-muted">{{ $staff->user->email }}</p>
                                    <p class="mt-2 text-info font-weight-bold">{{ $staff->user->mobile }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @if(empty($support_staffs) || count($support_staffs) == 0)
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row flex-wrap">
                                <img src="{{ $super_admin->image }}" class="img-lg rounded" alt="profile image">
                                <div class="mx-3">
                                    <h6>{{ $super_admin->full_name }}</h6>
                                    <p class="text-muted">{{ $super_admin->email }}</p>
                                    <p class="mt-2 text-info font-weight-bold">{{ $super_admin->mobile }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
