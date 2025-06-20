@extends('layouts.master')

@section('title')
    {{ __('gallery') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('gallery') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title float-left">
                            {{ __('edit') . ' ' . __('gallery') }}
                        </h4>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 text-right">
                                <a href="{{ route('gallery.index') }}" class="btn btn-theme btn-sm">{{ __('back') }}</a>
                            </div>
                        </div>
                        <hr>
                            {!! Form::model($gallery, [
                                'route' => ['gallery.update', $gallery->id],
                                'method' => 'post',
                                'class' => 'edit-form',
                                'novalidate' => 'novalidate',
                                'enctype' => 'multipart/form-data',
                                'data-success-function' => 'formSuccessFunction'
                            ]) !!}
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-6 col-md-6">
                                    <label>{{ __('description') }}</label>
                                    {!! Form::textarea('description', null, [
                                        'rows' => '2',
                                        'placeholder' => __('description'),
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-6 col-md-6">
                                    <label>{{ __('thumbnail') }} </label>
                                    <input type="file" name="thumbnail" id="thumbnail"
                                        class="file-upload-default" accept="image/*"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled=""
                                            placeholder="{{ __('thumbnail') }}" aria-label="" />
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme"
                                                type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                    <img src="{{ $gallery->thumbnail }}" class="img-lg" alt="">
                                </div>
                                <div class="form-group col-sm-6 col-md-6">
                                    <label>{{ __('images') }} <span class="text-small text-info">({{ __('upload_multiple_images') }})</span></label>
                                    <input type="file" multiple name="images[]" id="uploadInput"
                                        class="file-upload-default" accept="image/*" />
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled=""
                                            placeholder="{{ __('images') }}" required aria-label="" />
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-theme"
                                                type="button">{{ __('upload') }}</button>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label for="">{{ __('youtube_links') }} <span class="text-small text-info">({{__('please_use_commas_or_press_enter_to_add_multiple_links')}})</span></label>
                                    <input name="youtube_links" id="tags" class="form-control" value="" />
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label for="session_year_id">{{ __('session_year') }}</label>
                                    {!! Form::select('session_year_id', $sessionYears, null, ['class' => 'form-control']) !!}
                                </div>

                            </div>
                            {{-- <input class="btn btn-theme" type="submit" value={{ __('submit') }}> --}}
                            <input class="btn btn-theme float-right ml-3" id="create-btn" type="submit" value={{ __('submit') }}>
                            <input class="btn btn-secondary float-right" type="reset" value={{ __('reset') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ __('view') }} {{ __('gallery') }}
                        </h3>

                        <div class="row">
                            <div class="col-sm-12 col-md-12 mb-3">
                                <hr>
                                <h4 class="card-title">{{ __('photo_gallery') }}</h4>
                            </div>
                            <div id="lightgallery" class="row lightGallery">
                                @foreach ($gallery->file as $file)
                                    @if ($file->type == 1)
                                        <div class="col-sm-12 col-md-2 mt-2">
                                            <button class="mt-1 btn btn-sm btn-danger ml-2 remove-gallery-image" data-id="{{ $file->id }}">X</button>
                                            <a href="{{ $file->file_url }}" data-toggle="lightbox" class="image-tile">
                                                <img src="{{ $file->file_url }}" alt="image small" class="zoom-img">
                                            </a>
                                            <hr>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div id="lightgallery" class="row lightGallery">
                            <div class="col-sm-12 col-md-12 mb-3">
                                <hr>
                                <h4 class="card-title">{{ __('video_gallery') }}</h4>
                            </div>

                            @foreach ($gallery->file as $file)
                                @if ($file->type == 2)
                                    <div class="col-sm-12 col-md-2 mb-4">
                                        <button class="mb-1 btn btn-sm btn-danger ml-2 remove-gallery-image" data-id="{{ $file->id }}">X</button>
                                        <a href="{{ $file->file_url }}" data-toggle="lightbox" class="image-tile">
                                            <img src="{{ $file->youtube_url_action->img ?? '' }}" class="zoom-img" alt="image small">
                                        </a>
                                        <hr>
                                    </div>    
                                @endif
                            @endforeach
                        </div>

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
                location.reload();
            }, 2000);
        }
    
        const uploadInput = document.getElementById('uploadInput');

        // Event listener to handle file selection
        uploadInput.addEventListener('change', function() {
            // Update file counter with the number of selected files
            $(this).parent().find('.form-control').val(this.files.length + (this.files.length === 1 ? ' file selected' : ' files selected'));
        });
    </script>
@endsection
