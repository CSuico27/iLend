<?php

namespace App\Livewire\Auth;

use App\Mail\SendOTP;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\PngEncoder;
use Livewire\Attributes\Title;
use Livewire\Component;
use Laravolt\Avatar\Facade as Avatar;

#[Title('Register')]
class RegisterPage extends Component
{
    public $full_name;
    public $email;

    public $password;
    public $confirmPassword;
    public bool $agreedToTerms = false;
    public function register()
    {
        $this->validate([
            'full_name' => 'required|max:255',
            'email' => 'email|unique:users,email|max:255',
            'password' => 'required|min:8|max:255',
            'confirmPassword' => 'required|same:password|min:8|max:255',
            'agreedToTerms' => 'accepted',
        ]);

        try {
            $otp = mt_rand(100000, 999999);  // Generate a 6-digit OTP

            $user = User::create([
                'name' => $this->full_name,
                'email' => $this->email,
                'role' => 'user',
                'password' => Hash::make($this->password),
                'otp' => $otp,
                'is_verified' => false
            ]);

            // Generate the avatar
            $avatar = Avatar::create($user->name)->getImageObject()->encode(new PngEncoder());
            $avatarPath = 'avatars/' . $user->id . '.png';
            Storage::disk('public')->put($avatarPath, (string) $avatar);

            $user->avatar = $avatarPath;
            $user->save();

            do {
                $memberId = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            } while (UserProfile::where('member_id', $memberId)->exists());

            $userProfile = UserProfile::create([
                'user_id' => $user->id,
                'member_id'  => $memberId,
            ]);

            // Send OTP email
            Mail::to($user->email)->send(new SendOTP($user->otp));

            // Redirect to OTP verification page
            return redirect()->route('account.verify', ['user_id' => $user->id]);
            // return redirect()->route('verify.otp')->with('userId', $user->id);

        } catch (\Exception $e) {
            Log::error('Error registering user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
