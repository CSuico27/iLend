<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

#[Title('Home')]
class HomePage extends Component
{
    use WireUiActions;
    
    public function mount()
    {
        if (session()->has('membership_error')) {
            $this->notification()->error(
                'Error!',
                session()->get('membership_error')
            );
        }

        if (session()->has('membership_success')) {
            $this->notification()->success(
                'Congrats!',
                session()->get('membership_success')
            );
        }

        if (session()->has('portal_error')) {
            $this->notification()->info(
                'Under Review',
                session()->get('portal_error')
            );
        }
    }

    public function applicationUnderReview(){
        
        $this->notification()->info(
            $title = 'Under Review',
            $description = 'Your membership application is currently under review. Please wait for approval.'
        );
           
    }
    
    public function render()
    {
        return view('livewire.home-page');
    }
}
