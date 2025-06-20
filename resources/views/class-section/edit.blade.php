@extends('layouts.master')

@section('title')
    {{ __('Class Section')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Class Section & Teachers')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-theme" href="{{ route('class-section.index') }}">{{ __('back') }}</a>
                        </div>
                        <form class="pt-3 edit-class-subject-validate-form" data-success-function="formSuccessFunction" id="edit-form" action="{{ route('class-section.update',[$id]) }}" novalidate="novalidate">
                            <div class="modal-body">

                                <div class="text-center">
                                    <h3>{{$classSection->full_name}}</h3>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12 col-12">
                                        <h4 class="mb-3">{{ __('Class Teacher') }}</h4>
                                        <div class="form-group">
                                            {{--                                            <select name="class_teacher_id" id="class_teacher_id" class="form-control select2" required="required" multiple>--}}
                                            <select multiple name="class_teacher_id[]" id="class_teacher_id" data-class-section="{{$classSection->id}}" class="form-control select2-dropdown select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true" data-placeholder="{{__("Search Teacher Name")}}">
                                                @foreach ($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}" {{ $classSection->class_teachers->contains('teacher_id',$teacher->id) ? "selected data-exists=true" : "data-exists=false" }} >{{$teacher->full_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h4 class="mb-3">{{ __('Subject Teacher') }}</h4>

                                @if($classSection->class->include_semesters == 1)
                                    @foreach ($semesters as $key => $data)
                                        <div class="subject-teachers-with-semesters-{{ $key }} bg-light p-3 mt-4 mb-4">
                                            <h4 class="card-title col-12 text-center mb-4 pb-4">
                                                <u>{{ $data->name }}</u>
                                            </h4>
                                            <div class="row m-3">
                                                <div class="col-sm-12 col-md 6 text-center">
                                                    <label>{{ __('subjects') }}</label>
                                                </div>
                                                <div class="col-sm-12 col-md 6 text-center">
                                                    <label>{{ __('teachers') }}</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @foreach ($classSubjects as $key => $classSubject)
                                                    @if($classSubject->semester_id == $data->id)
                                                        <input type="hidden" name="subject_teachers[{{ $key }}][semester_id]" value="{{ $data->id }}">
                                                        <input type="hidden" name="subject_teachers[{{ $key }}][class_subject_id]" value="{{ $classSubject->id }}">
                                                        <input type="hidden" name="subject_teachers[{{ $key }}][subject_id]" value="{{ $classSubject->subject_id }}">
                                                        <div class="form-group col-sm-12 col-md-6 ">
                                                            <span class="form-control" readonly>{{ $classSubject->subject->name_with_type }}</span>
                                                        </div>
                                                        <div class="form-group col-sm-12 col-md-6 ">
                                                            <select multiple name="subject_teachers[{{ $key }}][teacher_user_id][]]" data-class-section="{{$classSection->id}}" class="form-control select2-dropdown select2-hidden-accessible subject_teacher_id" style="width:100%;" tabindex="-1" aria-hidden="true" data-placeholder="{{__("Search Teacher Name")}}">
                                                                @foreach ($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}" {{ $classSubject->subjectTeachers->contains('teacher_id',$teacher->id) ? "selected" : "" }} {{ $classSubject->subjectTeachers->contains('teacher_id',$teacher->id) ? "data-exists = true" : "data-exists = false" }} data-subjectId= {{ $classSubject->subject_id }}>{{$teacher->full_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                <div class="subject-teachers-without-semesters bg-light p-3 mt-4">
                                    <div class="row m-3">
                                        <div class="col-sm-12 col-md 6 text-center">
                                            <label>{{ __('subjects') }}</label>
                                        </div>
                                        <div class="col-sm-12 col-md 6 text-center">
                                            <label>{{ __('teachers') }}</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @foreach ($classSubjects as $key => $classSubject)
                                            <input type="hidden" name="subject_teachers[{{ $key }}][class_subject_id]" value="{{ $classSubject->id }}">
                                            <input type="hidden" name="subject_teachers[{{ $key }}][subject_id]" value="{{ $classSubject->subject_id }}">
                                            <div class="form-group col-sm-12 col-md-6 ">
                                                <span class="form-control" readonly>{{ $classSubject->subject->name.' - '.$classSubject->subject->type }}</span>
                                            </div>
                                            <div class="form-group col-sm-12 col-md-6 ">
                                                <select multiple name="subject_teachers[{{ $key }}][teacher_user_id][]]" data-class-section="{{$classSection->id}}" class="form-control select2-dropdown select2-hidden-accessible subject_teacher_id" style="width:100%;" tabindex="-1" aria-hidden="true" data-placeholder="{{__("Search Teacher Name")}}">
                                                    @foreach ($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" {{ $classSubject->subjectTeachers->contains('teacher_id',$teacher->id) ? "selected" : "" }} {{ $classSubject->subjectTeachers->contains('teacher_id',$teacher->id) ? "data-exists = true" : "data-exists = false" }} data-subjectId= {{ $classSubject->subject_id }}>{{$teacher->full_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>

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


        function formSuccessFunction(response) {
            setTimeout(() => {
                window.location.href = "{{route('class-section.index')}}"
            }, 1000);
        }
    </script>
@endsection
