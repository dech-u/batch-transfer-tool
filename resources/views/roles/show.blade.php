@extends('layouts.master')

@section('title')
    {{__('show_role')}}
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{__('show_role')}}
            </h3>
            <a class="btn btn-sm btn-theme" href="{{ route('roles.index') }}">{{__('back')}}</a>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>{{__('name')}}:</strong>
                                    {{ $role->name }}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="row">
                                    @if(!empty($rolePermissions))
                                        @foreach($rolePermissions as $v)
                                            <div class="col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                                <label class="label label-success">{{ $v->name }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
