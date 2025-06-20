@extends('layouts.master')

@section('title')
    {{ __('manage').' '.__('role') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('role') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('roles.index') }}">{{ __('back') }}</a>
                        </div>
                        {!! Form::model($role, ['method' => 'PATCH', 'class' => 'edit-form-without-reset', 'route' => ['roles.update', $role->id]]) !!}
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <label><strong> {{ __('name') }}:</strong></label>
                                    {!! Form::text('name', null, ['placeholder' => __('name'), 'class' => 'form-control',$role->name=="Teacher"?"readonly":""]) !!}
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        {{ Form::checkbox('selectall', 1, false, ['class' => 'name form-check-input', 'id' => 'selectall']) }}Select all
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="row">
                                    @foreach ($permission as $value)
                                        <div class="form-group col-lg-3 col-sm-12 col-xs-12 col-md-3">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    {{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions), ['class' => 'name form-check-input selectedId']) }}
                                                    {{ $value->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);
            $('#selectall').prop("checked", check);

            $('#selectall').click(function () {
                $('.selectedId').prop('checked', this.checked);
            });
    
            $('.selectedId').change(function () {
                var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);
                $('#selectall').prop("checked", check);
    
                if ($('.selectedId').filter(":checked").length === 0) {
                    $('#selectall').prop("checked", false);
                }
            });
        });
    </script>
@endsection