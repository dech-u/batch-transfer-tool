@extends('layouts.school.master')
@section('title')
    Admission
@endsection
@section('content')
    <div class="main">
        <div class="breadcrumb">
            <div class="container">
                <div class="contentWrapper">
                    <span class="title">
                        Admission Form
                    </span>
                    <span>
                        <a href="/" class="home">Home</a>
                        <span><i class="fa-solid fa-caret-right"></i></span>
                        <span class="page">Admission Form</span>
                    </span>
                </div>
            </div>
        </div>

        <section class="admissionPage ">
            <div class="commonMT commonWaveSect">
                <div class="container">
                    {{-- <form class="pt-3 student-registration-form" id="create-form" data-success-function="formSuccessFunction" enctype="multipart/form-data" action="{{ route('online-admission.store') }}" method="POST" novalidate="novalidate"> --}}
                        <form class="pt-3 student-registration-form online-admission-form" enctype="multipart/form-data" action="{{ route('online-admission.store') }}" method="POST" novalidate="novalidate">
                        @csrf
                        <div class="row formContainer">
                                <div class="col-12 mainDiv">
                                    <div class="formHeading">
                                        <span> Student Information </span>
                                    </div>
                                    <div class="row formWapper">
                                        <div class="col-lg-8">
                                            <div class="row formDiv">

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="firstName">First Name <span>*</span></label>
                                                    <input type="text" placeholder="First Name" name="first_name" id="first_name" required>
                                                </div>

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="lastName">Last Name <span>*</span></label>
                                                    <input type="text" placeholder="Last Name" name="last_name" id="last_name" required>
                                                </div>

                                                <!-- ====================================================================================== -->

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="dob">Date Of Birth <span>*</span></label>
                                                    <input type="date" placeholder="DOB" class="invalid" name="dob" id="dob" required> 
                                                </div>

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="number">Mobile Number</label>
                                                    <input type="text" placeholder="Mobile Number" oninput="this.value=this.value.replace(/[^0-9]/g,'');" name="mobile" id="mobile" maxlength="16">
                                                </div>

                                                <!-- ====================================================================================== -->

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="currentAddress">Current Address <span>*</span></label>
                                                    <textarea placeholder="Current Address" name="current_address" id="current_address" required></textarea>
                                                </div>

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="permanentAddress">Permanent Address <span>*</span></label>
                                                    <textarea placeholder="Permanent Address" name="permanent_address" id="permanent_address" required></textarea>
                                                </div>

                                                <!-- ====================================================================================== -->

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="=classMedium">Class and Medium <span>*</span></label>
                                                    <div>
                                                        <select name="class_id" id="class_id">
                                                            <option value="">Choose Class Medium</option>
                                                            @foreach ($classes as $class)
                                                                <option value="{{ $class->id }}">{{ $class->name.' '. $class->medium->name.' '.($class->stream->name ?? '')}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                        
                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="gender">Gender<span>*</span></label>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <input type="radio" name="gender" checked id="male" value="male" required>
                                                            <span>Male</span>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <input type="radio" name="gender" id="female" value="female" required>
                                                            <span>Female</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="fileInputWrapper">
                                                <div>
                                                    <img src="{{ asset('assets/school/images/Image Preview.png') }}" alt="imgPreview" class="upperImgPreview default-image">
                                                </div>
                                                <div class="file-upload upperFileUpload">
                                                    <div class="file-select">
                                                        <button type="button">Browse...</button>
                                                        <span>No File Selected.</span>
                                                        <input type="file" name="image" id="fileUpload" accept="image/*" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(count($extraFields))
                                <div class="row other-details mx-2 pe-4">

                                    {{-- Loop the FormData --}}
                                    @foreach ($extraFields as $key => $data)
                                            {{-- Edit Extra Details ID --}}
                                            {{ Form::hidden('extra_fields['.$key.'][id]', '', ['id' => $data->type.'_'.$key.'_id']) }}

                                            {{-- Form Field ID --}}
                                            {{ Form::hidden('extra_fields['.$key.'][form_field_id]', $data->id, ['id' => $data->type.'_'.$key.'_id']) }}

                                            <div class='form-group col-md-12 col-lg-6 col-xl-4 col-sm-12'>

                                                {{-- Add lable to all the elements excluding checkbox --}}
                                                @if($data->type != 'radio' && $data->type != 'checkbox')
                                                    <label>{{$data->name}} @if($data->is_required)
                                                            <span class="text-danger">*</span>
                                                        @endif</label>
                                                @endif

                                                {{-- Text Field --}}
                                                @if($data->type == 'text')
                                                    {{ Form::text('extra_fields['.$key.'][data]', '', ['class' => 'form-control text-fields', 'id' => $data->type.'_'.$key, 'placeholder' => $data->name, ($data->is_required == 1 ? 'required' : '')]) }}
                                                    {{-- Number Field --}}
                                                @elseif($data->type == 'number')
                                                    {{ Form::number('extra_fields['.$key.'][data]', '', ['min' => 0, 'class' => 'form-control number-fields', 'id' => $data->type.'_'.$key, 'placeholder' => $data->name, ($data->is_required == 1 ? 'required' : '')]) }}

                                                    {{-- Dropdown Field --}}
                                                @elseif($data->type == 'dropdown')
                                                    {{ Form::select('extra_fields['.$key.'][data]',$data->default_values,null,
                                                        ['id' => $data->type.'_'.$key,'class' => 'form-control select-fields',
                                                            ($data->is_required == 1 ? 'required' : ''),
                                                            'placeholder' => 'Select '.$data->name
                                                        ]
                                                    )}}

                                                        {{-- Radio Field --}}
                                                    @elseif($data->type == 'radio')
                                                        <label class="d-block">{{$data->name}} @if($data->is_required)
                                                                <span class="text-danger">*</span>
                                                            @endif</label>
                                                        <div class="row col-md-12 col-lg-12 col-xl-6 col-sm-12">
                                                            @if(count($data->default_values))
                                                                @foreach ($data->default_values as $keyRadio => $value)
                                                                    <div class="form-check mr-2">
                                                                        <label class="form-check-label">
                                                                            {{ Form::radio('extra_fields['.$key.'][data]', $value, null, ['id' => $data->type.'_'.$keyRadio, 'class' => 'radio-fields',($data->is_required == 1 ? 'required' : '')]) }}
                                                                            {{$value}}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                        {{-- Checkbox Field --}}
                                                    @elseif($data->type == 'checkbox')
                                                        <label class="d-block">{{$data->name}} @if($data->is_required)
                                                                <span class="text-danger">*</span>
                                                            @endif</label>
                                                        @if(count($data->default_values))
                                                            <div class="row col-lg-12 col-xl-6 col-md-12 col-sm-12 ms-1" style="gap:1rem;">
                                                                @foreach ($data->default_values as $chkKey => $value)
                                                                    <div class="mr-2 form-check">
                                                                        <label class="form-check-label">
                                                                            {{ Form::checkbox('extra_fields['.$key.'][data][]', $value, null, ['id' => $data->type.'_'.$chkKey, 'class' => 'form-check-input chkclass checkbox-fields',($data->is_required == 1 ? 'required' : '')]) }} {{ $value }}

                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        {{-- Textarea Field --}}
                                                    @elseif($data->type == 'textarea')
                                                        {{ Form::textarea('extra_fields['.$key.'][data]', '', ['placeholder' => $data->name, 'id' => $data->type.'_'.$key, 'class' => 'form-control textarea-fields', ($data->is_required ? 'required' : '') , 'rows' => 3]) }}

                                                        {{-- File Upload Field --}}
                                                    @elseif($data->type == 'file')
                                                        <div class="input-group col-xs-12">
                                                            {{ Form::file('extra_fields['.$key.'][data]', ['class' => 'file-upload-default', 'hidden', 'id' => $data->type.'_'.$key, ($data->is_required ? 'required' : '')]) }}
                                                            {{ Form::text('', '', ['class' => 'form-control file-upload-info', 'disabled' => '', 'placeholder' => __('image')]) }}
                                                            <span class="input-group-append">
                                                                <button class="file-upload-browse btn btn-theme" style="background-color: #D8E0E6;" type="button">{{ __('upload') }}</button>
                                                            </span>
                                                        </div>
                                                        <div id="file_div_{{$key}}" class="mt-2 d-none file-div">
                                                            <a href="" id="file_link_{{$key}}" target="_blank">{{$data->name}}</a>
                                                        </div>

                                                    @endif
                                                </div>
                                    @endforeach
                                </div>
                            @endif
                                <div class="col-12 mainDiv">
                                    <div class="formHeading">
                                        <span> Parents Information </span>
                                    </div>
                                    <div class="row formWapper">
                                        <div class="col-lg-8">
                                            <div class="row formDiv">

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="firstName">First Name <span>*</span></label>
                                                    <input type="text" placeholder="First Name" name="guardian_first_name" id="guardian_first_name" required>
                                                </div>

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="lastName">Last Name <span>*</span></label>
                                                    <input type="text" placeholder="Last Name" name="guardian_last_name" id="guardian_last_name" required>
                                                </div>

                                                <!-- ====================================================================================== -->

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="number">Mobile Number <span>*</span></label>
                                                    <input type="text" placeholder="Mobile Number" name="guardian_mobile" id="guardian_mobile" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="16" required>
                                                </div>

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="email">Email<span>*</span></label>
                                                    <input type="email" placeholder="Email" name="guardian_email" id="guardian_email" required>
                                                </div>

                                                <!-- ====================================================================================== -->

                                                <div class="col-lg-6 inputWrapper">
                                                    <label for="gender">Gender<span>*</span></label>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <input type="radio" name="guardian_gender" checked id="guardian_male" value="male" required>
                                                            <span>Male</span>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <input type="radio" name="guardian_gender" id="guardian_female" value="female" required>
                                                            <span>Female</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="fileInputWrapper">
                                                <div>
                                                    <img src="{{ asset('assets/school/images/Image Preview.png') }}" alt="imgPreview" class="lowerImgPreview default-image" style="height: 331px">
                                                </div>
                                                <div class="file-upload lowerFileUpload">
                                                    <div class="file-select">
                                                        <button type="button">Browse...</button>
                                                        <span>No File Selected.</span>
                                                        <input type="file" name="guardian_image" id="fileUpload" accept="image/*" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if ($schoolSettings['SCHOOL_RECAPTCHA_SITE_KEY'] ?? '')
                                            <div class="col-lg-12">
                                                <div class="g-recaptcha mt-4" data-sitekey={{ $schoolSettings['SCHOOL_RECAPTCHA_SITE_KEY'] }}></div>
                                            </div>    
                                        @endif

                                    </div>
                                </div>

                                <div class="col-12 formBtnsWrapper">
                                    <button class="commonBtn">Reset</button>
                                    {{-- <button class="commonBtn">Submit</button> --}}
                                    <input type="submit" class="commonBtn" value="Submit">
                                </div>
                        
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')    
    <script async src="https://www.google.com/recaptcha/api.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('/assets/js/custom/common.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/custom.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/validate.js') }}"></script>
    <script src="{{ asset('/assets/js/custom/function.js') }}"></script>
    <script src="{{ asset('/assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
    
    {{--  --}}
    <script src="{{ asset('assets/home_page/js/owl.carousel.min.js') }}"></script>
    {{--  --}}


    <!-- bootstrap  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <!-- fontawesome icons   -->
    <script src="https://kit.fontawesome.com/1d2a297b20.js" crossorigin="anonymous"></script>



    <script type='text/javascript'>
        const today = new Date().toISOString().split('T')[0];

        // Set the max attribute to today's date to prevent future dates
        window.onlod = document.getElementById('dob').setAttribute('max', today);
    </script>
@endsection
