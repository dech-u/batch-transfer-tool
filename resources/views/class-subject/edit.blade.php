@extends('layouts.master')

@section('title')
    {{ __('Class Subject')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Class Subject')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <b>{{__("Class")}}</b> : {{$class->full_name}}
                            </div>
                            <div class="form-group">
                                <b>{{__("Semester Included")}}</b> : {{$class->include_semesters ? trans("Yes") : trans("No")}}
                            </div>
                        </div>
                        <form class="pt-3 edit-class-subject-validate-form" data-success-function="formSuccessFunction" data-pre-submit-function="classValidation" id="edit-form" action="{{ route('class.subject.update',[$id]) }}" novalidate="novalidate">
                            <div class="modal-body">
                                <div class="form-group">
                                    <h4 title="{{ __('Core Subjects are the Compulsory Subject') }}." class="mb-3">{{ __('Core Subjects') }}<span class="fa fa-info-circle pl-2"></span></h4>
                                    <div class="core-subject-repeater">
                                        <div data-repeater-list="core_subject">
                                            <div class="row" data-repeater-item>
                                                @if($class->include_semesters)
                                                    <div class="col-5 semester-div">
                                                        <div class="form-group">
                                                            <label for="semester_id" class="d-none"></label>
                                                            <select name="semester_id" id="semester_id" class="form-control semesters" required>
                                                                <option value="" hidden="">-- {{__("Select Semester")}} --</option>
                                                                @foreach ($semesters as $semester)
                                                                    <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <input type="hidden" name="class_subject_id" class="class_subject_id"/>
                                                        <label for="core_subject_id" class="d-none"></label>
                                                        <select name="id" id="core_subject_id" class="form-control subject" required="required">
                                                            <option value="">{{ __('Select Subject') }}</option>
                                                            @foreach ($subjects as $subject)
                                                                <option value="{{ $subject->id }}">{{ $subject->name }} - {{ __($subject->type)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-1 pl-0">
                                                    <button data-repeater-delete type="button" class="btn btn-inverse-danger btn-icon" title="{{__('Remove Core Subject')}}">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-group pl-0 mt-4">
                                                <button type="button" class="col-md-3 btn btn-inverse-success" data-repeater-create>{{ __('Core Subjects') }} <i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <h4 class="mb-4" title="{{ __('Elective Subjects are the subjects where student have the choice to select the subject from the given subjects') }}.">
                                        {{ __('elective_subject') }} <span class="fa fa-info-circle pl-2"></span>
                                    </h4>

                                    <div class="row">
                                        <div class="elective-subject-group-repeater col-12 col-sm-12 col-md-12">
                                            <div data-repeater-list="elective_subject_group">
                                                <div data-repeater-item class="elective-subject-group">
                                                    <input type="hidden" name="id" class="class_subject_group_id"/>
                                                    <div class="align-items-center d-flex mb-2">
                                                        <h5 class="mb-0 group-no">{{ __('Group') }}</h5>
                                                        <button data-repeater-delete type="button" class="btn p-0 ml-1" title="Delete Subject Group">
                                                            <span class="fa fa-2x fa-times-circle text-danger"></span>
                                                        </button>
                                                    </div>
                                                    <div class="col-3 semester-div p-0">
                                                        <div class="form-group">
                                                            <label for="semester_id" class="d-none"></label>
                                                            @if($class->include_semesters)
                                                                <select name="semester_id" id="semester_id" class="form-control semesters" required>
                                                                    <option value="" hidden="">-- {{__("Select Semester")}} --</option>
                                                                    @foreach($semesters as $semester)
                                                                        <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="form-group elective-subject-repeater">
                                                        <div data-repeater-list="subject" class="mr-3 row">
                                                            <div data-repeater-item class="elective-subject col-md-4 col-sm-12 col-12">
                                                                <div class="row">
                                                                    <div class="col-md-10 col-sm-10 col-10 mb-5">
                                                                        <input type="hidden" name="class_subject_id" class="class_subject_id"/>
                                                                        <label for="elective_id" class="d-none"></label>
                                                                        <select name="id" id="elective_id" class="form-control subject" required="required">
                                                                            <option value="">{{ __('Select Subject') }}</option>
                                                                            @foreach ($subjects as $subject)
                                                                                <option value="{{ $subject->id }}">{{ $subject->name }} - {{ __($subject->type)}}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        <button data-repeater-delete type="button" class='btn p-0 remove-elective-subject' title="Delete Subject ">
                                                                            <span class='fa fa-times-circle text-danger'></span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-md-2 col-sm-2 col-2 mt-3 or-div">
                                                                        <span class='mt-3'>{{ __('or') }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-sm-12 col-12 pl-0 mt-3">
                                                            <button data-repeater-create type="button" class="btn btn-inverse-success btn-icon add-new-elective-subject" title="Add New Elective Subject"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-3">
                                                            <label for="total_selectable_subjects">{{ __('total_selectable_subjects') }}<span class="text-danger">*</span></label>
                                                            <input name="total_selectable_subjects" type="text" id="total_selectable_subjects" placeholder="{{ __('total_selectable_subjects') }}" class="form-control total_selectable_subjects" min="1" max="0" data-convert="number" required/>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-12 pl-0">
                                                <div class="form-group mt-4 col-md-4 col-sm-12 col-12 pl-0">
                                                    <button data-repeater-create type="button" class="col-md-12 btn btn-inverse-success">{{ __('elective_subject') }} <i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        $(document).ready(function () {
            @if($class->core_subjects)
            coreSubject.setList([
                    @foreach($class->core_subjects as $key=>$coreSubject)
                {
                    id: "{{$coreSubject->id}}",
                    class_subject_id: "{{$coreSubject->class_subject_id}}",
                    semester_id: "{{$coreSubject->pivot->semester_id}}"
                },
                @endforeach
            ]);
            @endif

            @if($class->elective_subject_groups)
            electiveSubjectGroupRepeater.setList([
                    @foreach($class->elective_subject_groups as $key=>$group)
                {
                    subject: [
                            @foreach($group->subjects as $subjects)
                        {
                            id: "{{$subjects->id}}",
                            class_subject_id: "{{$subjects->class_subject_id}}",

                        },
                        @endforeach
                    ],
                    id: "{{$group->id}}",
                    total_selectable_subjects: "{{$group->total_selectable_subjects}}",
                    semester_id: "{{$group->semester_id}}"
                },
                @endforeach
            ])
            @endif

            /* This line will auto add Group id to subjects */
            $('.semesters').trigger('change');
        });

        function formSuccessFunction() {
            window.location.href = "{{route('class.subject.index')}}"
        }
    </script>
@endsection
