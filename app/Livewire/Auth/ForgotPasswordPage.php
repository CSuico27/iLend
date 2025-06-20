<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

#[Title('Forgot Password')]
class ForgotPasswordPage extends Component
{
    use WireUiActions;
    
    public $email;

    public function forgot(){
        $this->validate([
            'email' => 'required|email|exists:users,email|max:255'
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if($status === Password::RESET_LINK_SENT){
            $this->notification()->success(
                $title = 'Success!',
                $description = 'Password reset link has been sent to your email!'
            );
            $this->email = '';
        }
    }
    
    public function render()
    {
        return view('livewire.auth.forgot-password-page');
    }
}
