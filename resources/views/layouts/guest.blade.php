<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-cookie-consent="{{ request()->cookie('bstest_cookie_consent') === '1' ? '1' : '0' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div
            x-data="{ legalPrivacy: false, legalCookies: false }"
            @open-legal-privacy.window="legalPrivacy = true; legalCookies = false"
            @open-legal-cookies.window="legalCookies = true; legalPrivacy = false"
            @keydown.escape.window="if (legalPrivacy || legalCookies) { legalPrivacy = false; legalCookies = false; }"
            class="flex min-h-screen flex-col bg-gray-100"
        >
            <div class="flex flex-1 flex-col items-center justify-center pt-6 sm:pt-0">
                <div>
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>

            @include('layouts.legal-footer')

            <x-cookie-consent />

            @include('layouts.legal-modals')
        </div>
    </body>
</html>
