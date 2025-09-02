<?php

namespace App\Livewire\Filament;

use App\Models\Notifications;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomNotification extends Component
{
    public $notifications = [];

    protected $listeners = ['markNotificationsAsRead' => 'markAsRead'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notifications::latest()->take(10)->get();
    }

    public function markAsRead()
    {
        Notifications::where('is_read', false)->update(['is_read' => true]);
        $this->loadNotifications();
    }

    public function clearAll()
    {
        DB::table('notifications')->delete();
        $this->notifications = [];
    }


    public function render()
    {
        return view('livewire.filament.custom-notification');
    }
}
