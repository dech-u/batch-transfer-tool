<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Installation - {{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ config('installer.icon') }}">
    <link href="{{ asset('vendor/wizard-installer/styles.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen h-full w-full bg-cover bg-no-repeat bg-center flex" style="background-image: url('{{ config('installer.background') }}');">
<div class="p-12 h-full m-auto">
    <div class="mx-auto w-full max-w-5xl w-[64rem]">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-8 border-b border-gray-200 sm:px-6">
                <div class="flex justify-center items-center">
                    <img alt="App logo" class="h-12" src="{{ url(config('installer.icon')) }}">
                    <h2 class="pl-6 uppercase font-medium text-2xl text-gray-800">{{ config('app.name', 'Laravel') }} Installation</h2>
                </div>
            </div>
            <div class="px-4 py-5 sm:px-6 w-full">
                @yield('step')
            </div>
        </div>
    </div>
</div>
</body>
</html>
