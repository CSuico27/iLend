<?php

namespace App\Livewire\Components;

use App\Models\Notifications;
use Livewire\Component;

class NotificationBell extends Component
{
    public $count = 0;

    protected $listeners = ['notificationsRead' => 'resetCount'];

    public function mount()
    {
        $this->count = Notifications::where('is_read', false)->count();
    }

    public function resetCount()
    {
        $this->count = 0;
    }

    public function render()
    {
        $this->count = Notifications::where('is_read', false)->count();
        
        return view('livewire.components.notification-bell');
    }
}
