@extends('installer::install')

@section('step')
    <p class="pb-3 text-gray-800">Checking the server requirements</p>
    <div class="flex flex-wrap border border-gray-200 text-gray-800 rounded-md mb-4 divide-y divider-gray-200">
        @foreach(config('installer.server') as $check)
            <div class="w-full px-4 py-2 text-gray-800">
                {{ $check['name'] }}
                @if(in_array('version', $check, true))
                    <small>($check['version'])</small>
                @endif
                <div class="float-right">
                    @if($check['check']())
                        <x-installer::success-check-icon />
                    @else
                        <x-installer::error-check-icon />
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-end">
        @if($result)
            <x-installer::link :href="route('LaravelWizardInstaller::install.folders')">
                Next step
                <svg class="fill-current w-5 h-5 ml-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </x-installer::link>
        @else
            <x-installer::link :href="route('LaravelWizardInstaller::install.server')" color="red">
                Reload
                <svg class="fill-current w-5 h-5 ml-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd"
                    />
                </svg>
            </x-installer::link>
        @endif
    </div>
@endsection
