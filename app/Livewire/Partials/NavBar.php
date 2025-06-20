<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use WireUi\Traits\WireUiActions;

class NavBar extends Component
{
    use WireUiActions;

    public function applicationUnderReview(){
        
        $this->notification()->info(
            $title = 'Under Review',
            $description = 'Your membership application is currently under review. Please wait for approval.'
        );
           
    }
    
    public function render()
    {
        return view('livewire.partials.nav-bar');
    }
}
