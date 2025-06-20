<a
    href="{{ $attributes->get('href') }}"
    class="bg-{{ $attributes->get('color', 'blue') }}-500 hover:bg-{{ $attributes->get('color', 'blue') }}-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
>
    {{ $slot }}
</a>
