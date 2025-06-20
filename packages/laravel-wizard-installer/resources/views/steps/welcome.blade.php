@extends('installer::install')

@section('step')
    <p class="pb-2 text-gray-800">
        Welcome to the installation wizard.
    </p>
    <p class="pb-3 text-gray-800">
        Before getting started, we need some information on the database.
        You will need to know the following items before proceeding.
    </p>
    <div class="px-3 pb-3 text-gray-800">
        <ol class="list-decimal list-inside">
            <li>Database host</li>
            <li>Database port</li>
            <li>Database name</li>
            <li>Database username</li>
            <li>Database password</li>
        </ol>
    </div>
    <p class="pb-3 text-gray-800">
        Most likely these items were supplied to you by your Web Host.
        If you don’t have this information, then you will need to contact them before you can continue.
    </p>
    <p class="pb-3 text-gray-800">
        Installer will insert this information inside a configuration file so your site can communicate with your database.
    </p>
    @if(config('installer.support_url'))
        <p class="pb-4 text-gray-800">
            Need more help?
            <a class="text-blue-500 hover:underline" href="{{ config('installer.support_url') }}" target="_blank">Contact support</a>.
        </p>
    @endif
    <div class="flex justify-end">
        <x-installer::link :href="route('LaravelWizardInstaller::install.server')">
            Next step
            <svg class="fill-current w-5 h-5 ml-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
        </x-installer::link>
    </div>
@endsection
