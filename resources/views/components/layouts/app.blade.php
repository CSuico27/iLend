<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <wireui:scripts />
        {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
    </head>
    <body>
        <x-notifications />
        @livewire('partials.nav-bar')
        {{ $slot }}
        @livewire('partials.footer')
        @livewireScripts
    </body>
</html>
