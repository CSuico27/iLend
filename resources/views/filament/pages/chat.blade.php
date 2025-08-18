<x-filament-panels::page>
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   <div class="h-screen">
        {{-- <livewire:wirechat/> --}}
        @livewire('wirechat')
   </div>
</x-filament-panels::page>
