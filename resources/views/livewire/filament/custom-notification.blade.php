<div 
    x-data="{ open: false }"
    x-on:toggle-custom-sidebar.window="
        open = !open;
        if (open) {
            $nextTick(() => {
                $wire.markAsRead().then(() => {
                    Livewire.dispatch('notificationsRead');
                });
            });
        }
    "
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    x-cloak
    style="position: fixed; top: 0; right: 0; z-index: 9999; width: 30%; height: 100vh; background-color: #fff; box-shadow: -2px 0 8px rgba(0,0,0,0.1); border-left: 1px solid #ddd;"
>
    <div style="padding: 1rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd;">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Notifications</h2>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <button 
                wire:click="clearAll" 
                style="font-size: 0.75rem; color: #dc2626; text-decoration: underline; background: none; border: none; cursor: pointer;"
            >
                Clear All
            </button>
            <button @click="open = false" style="background: none; border: none; cursor: pointer;">
                <x-heroicon-o-x-mark style="width: 20px; height: 20px;" />
            </button>
        </div>
    </div>
    <div style="padding: 1rem; overflow-y: auto; height: calc(100% - 64px); display: flex; flex-direction: column; gap: 0.75rem;">
        @forelse ($notifications as $notification)
            <div style="background-color: #f3f4f6; padding: 0.75rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); font-size: 0.875rem;">
                {{ $notification->message }}
                <span style="display: block; font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">
                    {{ $notification->created_at->format('M d, Y h:i A') }} - 
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </div>
        @empty
            <p style="font-size: 0.875rem; color: #6b7280;">No notifications</p>
        @endforelse
    </div>
</div>
