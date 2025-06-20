<label
    for="{{ $attributes->get('for') }}"
    class="block font-medium leading-5 text-gray-700 pb-2"
>
    {{ $slot }}
    @if($attributes->get('required', false))
        <span class="text-red-400">*</span>
    @endif
</label>
