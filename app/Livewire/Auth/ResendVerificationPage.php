<?php

namespace App\Livewire\Auth;

use App\Mail\SendOTP;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

#[Title('Account Verification')]
class ResendVerificationPage extends Component
{
    use WireUiActions;
    
    public $email;

    public function sendOtp()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user->is_verified) {
            $this->notification()->error(
                $title = 'Error!',
                $description = 'This account is already verified.'
            );
            return;
        }

        $user->otp = mt_rand(100000, 999999);
        $user->save();

        Mail::to($user->email)->send(new SendOTP($user->otp));

        session()->flash('success', 'OTP sent to your email.');
        return redirect()->route('account.verify', ['user_id' => $user->id]);
    }
    
    public function render()
    {
        return view('livewire.auth.resend-verification-page');
    }
}
