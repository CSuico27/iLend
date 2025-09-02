<?php

namespace App\Livewire\Client;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Terms and Condition')]
class TermsAndCondition extends Component
{
    public function render()
    {
        return view('livewire.client.terms-and-condition');
    }
}
