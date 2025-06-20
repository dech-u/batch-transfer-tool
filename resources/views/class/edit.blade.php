@extends('layouts.master')

@section('title')
    {{ __('Class')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Class')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3 edit-class-subject-validate-form" data-success-function="formSuccessFunction" data-pre-submit-function="classValidation" id="edit-form" action="{{ route('class.update',[$id]) }}" novalidate="novalidate">
                            <div class="modal-body">
                                <div class="form-group">
                                    <span>{{ __('medium') }} : </span>&nbsp;{{$class->medium->name}}
                                </div>

                                <div class="form-group">
                                    <label for="edit_name">{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control" value="{{$class->name}}"/>
                                </div>
                                <div class="form-group">
                                    <label for="shift_id">{{ __('Shift') }} <span class="text-info"> ({{__("Optional")}})</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control form-control select2-dropdown select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                        <option value="">--- {{__('Select Shift')}} ---</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{$shift->id}}" {{$shift->id==$class->shift_id?"selected":""}}>{{$shift->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="stream_id">{{ __('Stream') }} <span class="text-info"> ({{__("Optional")}})</span></label>
                                    <select name="stream_id" id="stream_id" class="form-control form-control select2-dropdown select2-hidden-accessible" tabindex="-1" aria-hidden="true">
                                        <option value="">--- {{__("No Stream")}} ---</option>
                                        @foreach ($streams as $stream)
                                            <option value="{{$stream->id}}" {{$stream->id==$class->stream_id?"selected":""}}>{{$stream->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('section') }} <span class="text-danger">*</span></label>
                                    @foreach ($sections as $section)
                                        <div class="form-check">
                                            <label class="form-check-label d-inline">
                                                <input type="checkbox" class="form-check-input edit" name="section_id[]" id="edit_section_id" value="{{ $section->id }}" {{$class->sections->contains($section->id) ? "checked disabled" : ""}}>{{ $section->name }}
                                            </label>
                                            @if($class_section->contains($section->id))
                                                <a href="{{route('class-section.destroy',[$class->sections->find($section->id)->pivot->id])}}" class="text-danger delete-class-section ml-2" style="cursor: pointer" title="{{__("Delete")}}"><span class="fa fa-times-circle"></span></a>
                                                <a href="{{route('class-section.edit',[$class->sections->find($section->id)->pivot->id])}}" class="text-primary ml-2" style="cursor: pointer" title="{{__("Assign Class Teacher & Subject Teacher")}}"><span class="fa fa-pencil-square-o"></span></a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <hr>
                                <div class="form-group mt-4">
                                    <div class="form-check">
                                        @if (count($semesters))
                                            <label class="form-check-label d-inline">
                                                <input type="checkbox" class="form-check-input include_semesters" name="include_semesters" value="{{$class->include_semesters}}" {{$class->include_semesters ? "checked" : ""}}>{{ __('Include Semesters') }}
                                            </label>    
                                        @endif
                                        
                                        <br>
                                        <small class="text-danger">* {{__("By Changing this Semester setting, your existing data related to this class will be Auto Deleted")}}</small>
                                        <ol class="text-danger">
                                            <li>{{__("Class Subject")}}</li>
                                            <li>{{__("timetable")}}</li>
                                            <li>{{__("Lesson & Topic")}}</li>
                                            <li>{{__("Exam & Marks")}}</li>
                                            <li>{{__("announcement")}}</li>
                                        </ol>
                                    </div>
                                </div>
                                <hr>

                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                                <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        function formSuccessFunction() {
            setTimeout(() => {
                window.location.href = "{{route('class.index')}}"
            }, 3000);
        }
    </script>
@endsection
