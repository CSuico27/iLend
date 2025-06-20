<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PortalPage extends Component
{
    public function mount(){
        if (Auth::check()) {
           
            $userProfile = Auth::user()->info;

            if ($userProfile && $userProfile->status == 'Pending') {
                return redirect()->route('user.home')->with('portal_error', 'Your membership application is currently under review. Please wait for approval.');
            }
        }
    }

    public function render()
    {
        return view('livewire.client.portal-page');
    }
}
