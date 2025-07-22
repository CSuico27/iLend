{{-- <div
    draggable="true"
    ondragstart="onLivewireCalendarEventDragStart(event, '{{ $event['id'] }}')"
    wire:click="$dispatch('onEventClick', '{{ $event['id'] }}')"
    class="px-2 py-1 bg-[#ca1619] text-white text-sm font-medium rounded shadow cursor-pointer hover:bg-[#ff3134] transition"
>
    {{ $event['title'] ?? 'No Title' }}
    <span>{{$start .'-' . $end}}</span>
</div> --}}
<div class="px-2 py-1 bg-[#ca1619] text-white text-xs font-medium rounded shadow hover:bg-[#ff3134] transition">
    <div class="font-bold">{{ $event['title'] }}</div>
    <div>{{ $event['start_time'] }} - {{ $event['end_time'] }}</div>

    {{-- View Details button to open modal --}}
    <div class="mt-1">
        <button 
            wire:click="onEventClick({{ $event['id'] }})" 
            class="underline text-white hover:text-gray-200 text-[10px]"
        >
            View Details
        </button>
    </div>
</div>

