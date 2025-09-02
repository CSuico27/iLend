<div 
    wire:poll.5s
    x-data 
    x-on:click="$dispatch('toggle-custom-sidebar');" 
    style="position: relative; margin-right: 0.75rem; cursor: pointer; color: #4B5563;" 
    title="Open Notifications"
    onmouseover="this.style.color='#2563EB'" 
    onmouseout="this.style.color='#4B5563'"
>
    <x-heroicon-o-bell style="width: 20px; height: 20px;" />
    @if($count > 0)
        <span 
            style="position: absolute; top: -0.5rem; right: -0.375rem; background-color: #991B1B; color: white; font-size: 8px; font-weight: bold; padding: 0.125rem 0.375rem; border-radius: 9999px;"
        >
            {{ $count }}
        </span>
    @endif
</div>
