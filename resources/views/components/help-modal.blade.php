@props([
    'id' => 'helpModal',
    'role' => null,
    'module' => null,
    'title' => null,
    'steps' => []
])

@php
    $guideService = app(App\Services\UserGuideService::class);
    $moduleData = null;
    $moduleSteps = [];
    
    if ($role && $module) {
        $roleData = $guideService->getModuleGuide($role);
        if ($roleData && isset($roleData[$module])) {
            $moduleSteps = $roleData[$module];
            $title = $title ?? str_replace('_', ' ', $module) ?? __('Help Guide');
            $steps = !empty($steps) ? $steps : $moduleSteps;
        }
    }
@endphp

<div class="modal fade help-modal" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header help-modal-header">
                <div>
                    <h5 class="modal-title" id="{{ $id }}Label">
                        <i class="mdi mdi-help-circle-outline me-2"></i>
                        {{ __($title) }}
                    </h5>
                </div>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="steps-guide">
                    @if(!empty($steps))
                        @foreach($steps as $step)
                            <div class="step d-flex align-items-start {{ !$loop->last ? 'mb-4' : '' }}">
                                <div class="step-icon-wrapper mr-3">
                                    <i class="{{ $step['icon'] }} step-icon mdi-24px"></i>
                                    <span class="step-number badge rounded-circle d-flex align-items-center justify-content-center">
                                        {{ $step['step'] }}
                                    </span>
                                </div>
                                <div class="step-content">
                                    <h6 class="step-title mb-1">{{ $step['title'] }}</h6>
                                    <p class="step-description mb-0">{{ $step['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            {{ __('No guide steps available for this module.') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">
                    <i class="mdi mdi-close-circle me-1"></i>
                    {{ __('close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>

</style>