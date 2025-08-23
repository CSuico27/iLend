<x-filament-panels::page>
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   <div class="h-[calc(100vh_-_10.0rem)]">
        {{-- <livewire:wirechat/> --}}
        @livewire('wirechat')
   </div>
</x-filament-panels::page>
