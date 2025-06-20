@extends('layouts.master')

@section('title')
    {{ __('Related Data') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Related Data For : ').strtoupper(str_replace("_"," ",$table)) }}
                        </h4>

                        <div class="row mt-2 col-12">
                            <div class="col-12 col-sm-4 col-md-3 pl-0 mb-3">
                                <div class="border border-dark p-2 rounded-lg">
                                    @foreach($currentData as $columnName=>$columnData)
                                        @if(!in_array($columnName,["created_at","updated_at","deleted_at","school_id"]))
                                        <span class="text-monospace"><b>{{ucwords(str_replace("_"," ",$columnName))."  : "}}</b>{{$columnData}}<br></span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @foreach($data as $tableName=>$tableData)
                                <div class="border border-secondary rounded-lg mb-2 p-3 col-12">
                                    <b>{{strtoupper(str_replace("_"," ",$tableName))}}</b>
                                    <div class="row mt-2 col-12">

                                        @foreach($tableData as $data)
                                            <div class="col-12 col-sm-4 col-md-3 pl-0 mb-3">
                                                <div class="border border-dark p-2 rounded-lg">
                                                    <div class="text-right">
                                                        <a href="{{route('related-data.index',[$tableName,$data->id])}}" target="_blank" class="btn btn-primary btn-sm btn-rounded" title="{{__("View Related Data")}}"><span class="fa fa-eye"></span></a>
                                                        <a href="#" class="btn btn-danger btn-sm btn-rounded delete-related-data" data-table="{{$table}}" data-id="{{$data->id}}" title="{{__("Delete")}}"><span class="fa fa-trash"></span></a>
                                                    </div>
                                                    @foreach($data as $columnName=>$columnData)
                                                        @if($columnData!=null && !in_array($columnName,["created_at","updated_at","deleted_at","school_id"]))
                                                            <span class="text-monospace"><b>{{ucwords(str_replace("_"," ",$columnName))."  : "}}</b>{{$columnData}}<br></span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
@endsection
