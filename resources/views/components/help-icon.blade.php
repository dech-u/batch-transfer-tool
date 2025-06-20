@props([
    'modalTarget' => 'helpModal',
    'tooltip' => __('Click for help'),
    'class' => '',
    'size' => 'normal' // small, normal, large
])

@php
    $sizeClass = match($size) {
        'small' => 'font-size: 16px;',
        'large' => 'font-size: 24px;',
        default => 'font-size: 20px;'
    };
@endphp

<a href="javascript:void(0)" 
   data-toggle="modal" 
   data-target="#{{ $modalTarget }}" 
   class="help-trigger {{ $class }}"
>
    <i class="mdi mdi-help-circle help-icon text-theme" 
       data-toggle="tooltip" 
       data-placement="top"
       title="{{ $tooltip }}"
       style="{{ $sizeClass }}"
    ></i>
</a> 