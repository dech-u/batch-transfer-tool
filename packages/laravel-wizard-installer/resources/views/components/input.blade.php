<input
    class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
    id="{{ $attributes->get('id') }}"
    type="{{ $attributes->get('type', 'text') }}"
    name="{{ $attributes->get('name') }}"
    value="{{ $attributes->get('value') }}"
    {{ $attributes->merge(['required' => $attributes->get('required', false) ? 'required' : null ]) }}
>
