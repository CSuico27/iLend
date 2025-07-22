<x-filament-panels::page>
    {{-- @livewireScripts
    @livewireStyles --}}
    <wireui:scripts /> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
     
    @foreach($widgets as $widget)
        @livewire($widget)
    @endforeach
    
    <livewire:appointments-calendar
        before-calendar-view="calendar-event"
        event-view="event-view"
        :drag-and-drop-enabled="false"
        :event-click-enabled="true"
        {{-- after-calendar-view="calendar-event" --}}
    />
     
   
    <script>
    function onLivewireCalendarEventDragStart(event, eventId) {
        event.dataTransfer.setData('text/plain', eventId);
    }

    function onLivewireCalendarEventDragEnter(event) {
        event.preventDefault();
        event.target.classList.add('drag-over');
    }

    function onLivewireCalendarEventDragOver(event) {
        event.preventDefault();
    }

    function onLivewireCalendarEventDragLeave(event) {
        event.target.classList.remove('drag-over');
    }

    function onLivewireCalendarEventDrop(event, day) {
        event.preventDefault();
        event.target.classList.remove('drag-over');
        
        const eventId = event.dataTransfer.getData('text/plain');
        
        // Emit a Livewire event to handle the drop (Livewire v3 syntax)
        Livewire.dispatch('onEventDropped', { eventId: eventId, day: day });
    }
    </script>

</x-filament-panels::page>
