<?php

namespace App\Livewire\Client;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

class MembershipPage extends Component
{
    use WithFilePond;

    public $name, $email, $phone, $birthdate, $gender, $address;
    public $biodata, $barangay_clearance, $valid_id, $tin_number;

    public int $currentStep;
    public bool $isFinishedStepOne;
    public bool $isFinishedStepTwo;

    public function mount(){
        $this->initialData();
        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->email = Auth::user()->email;

            $userProfile = Auth::user()->info;

            if ($userProfile && $userProfile->is_applied_for_membership) {
                return redirect()->route('user.home')->with('membership_error', 'You already submitted your membership application.');
            }
        }
    }

    public function initialData(){
        $this->currentStep = 1;
        $this->isFinishedStepOne = false;
        $this->isFinishedStepTwo = false;
    }

    public function nextStep(){
        if($this->currentStep < 2 && $this->name && $this->email && $this->phone && $this->birthdate && $this->gender && $this->address){
            $this->currentStep = $this->currentStep + 1;
            $this->isFinishedStepOne = true;
        }
    }

    public function backStep(){
        if($this->currentStep > 1 ){
            $this->currentStep = $this->currentStep - 1;
            $this->isFinishedStepOne = false;
        }
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string',
        'birthdate' => 'required|date',
        'gender' => 'required|string',
        'address' => 'required|string|max:255',
        'biodata' => 'required|file|image|max:2048',
        'barangay_clearance' => 'required|file|max:2048',
        'valid_id' => 'required|file|max:2048',
        'tin_number' => 'required|string',
    ];

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        $profile = UserProfile::firstOrNew(['user_id' => $user->id]);

        if ($this->biodata) {
            $profile->biodata = $this->biodata->store('user-biodata', 'public');
        }
    
        if ($this->barangay_clearance) {
            $profile->brgy_clearance = $this->barangay_clearance->store('brgy-clearance', 'public');
        }
    
        if ($this->valid_id) {
            $profile->valid_id = $this->valid_id->store('valid-ids', 'public');
        }

        $formattedPhone = ltrim($this->phone, '0');
        if (!str_starts_with($formattedPhone, '+63')) {
            $formattedPhone = '+63' . $formattedPhone;
        }

        $profile->phone = $formattedPhone;
        $profile->birthdate = $this->birthdate;
        $profile->gender = $this->gender;
        $profile->address = $this->address;
        $profile->tin_number = $this->tin_number;
        $profile->is_applied_for_membership = true;
        $profile->status = 'Pending';

        $profile->save();

        return redirect()->route('user.home')->with('membership_success', 'Thank you for submitting your application. Your application is currently under review.');
    }

    public function render()
    {
        return view('livewire.client.membership-page');
    }
}
