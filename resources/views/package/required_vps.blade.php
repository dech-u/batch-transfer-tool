<div class="row">
    <div class="col-sm-12 col-md-12">
        <span>
            <a href="#" data-toggle="modal" data-target="#enable-features">
                {{ __('note_if_you_have_a_VPS_server_you_can_access_these_features_by_clicking_here_to_enable_them') }}
            </a>
        </span>
    </div>
</div>

<div class="modal fade" id="enable-features" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> {{ __('enable_features') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-close"></i></span>
                </button>
            </div>
            <form id="formdata" class="create-form create-form-without-reset reload-window" action="{{ url('features/enable') }}"
                novalidate="novalidate">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @foreach ($vps_features as $feature)
                            <div class="form-group col-sm-12 col-md-3">
                                <input type="hidden" name="feature_id[{{ $feature->id }}]" value="0">
                                <input id="{{ __($feature->name) }}_{{ $feature->id }}" @if($feature->status == 1) checked @endif class="feature-checkbox"
                                type="checkbox" name="feature_id[{{ $feature->id }}]" value="1" />
                                <label class="feature-list text-center"
                                    for="{{ __($feature->name) }}_{{ $feature->id }}">{{ __($feature->name) }}</label>
                            </div>
                        @endforeach
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('Cancel') }}</button>
                    <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                </div>
            </form>
        </div>
    </div>
</div>