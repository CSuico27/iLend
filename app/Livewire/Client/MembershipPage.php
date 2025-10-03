<?php

namespace App\Livewire\Client;

use App\Models\PHCities;
use App\Models\PHProvinces;
use App\Models\PHRegions;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('Membeship Application')]
class MembershipPage extends Component
{
    use WithFilePond;

    public $name, $email, $phone, $birthdate, $gender;
    public $biodata, $barangay_clearance, $valid_id, $tin_number;

    public int $currentStep;
    public bool $isFinishedStepOne;
    public bool $isFinishedStepTwo;

    public $region;
    public $province;
    public $municipality;
    public $barangay;

    public $regionCode;
    public $provinceCode;
    public $municipalityCode;

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
        if($this->currentStep < 2 && $this->name && $this->email && $this->phone && $this->birthdate && $this->gender && $this->region && $this->province && $this->municipality && $this->barangay){
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
        'region' => 'required|max:255',
        'province' => 'required|max:255',
        'municipality' => 'required|max:255',
        'barangay' => 'required|max:255',
        'biodata' => 'required|file|image|max:2048',
        'barangay_clearance' => 'required|file|max:2048',
        'valid_id' => 'required|file|max:2048',
        'tin_number' => ['required','digits:14'],
    ];

    protected $messages = [
        'tin_number.required' => 'Your TIN number is required.',
        'tin_number.digits'   => 'Your TIN number must be exactly 14 digits.',
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
        $profile->region = $this->region;
        $profile->province = $this->province;
        $profile->municipality = $this->municipality;
        $profile->barangay = $this->barangay;
        $profile->tin_number = $this->tin_number;
        $profile->is_applied_for_membership = true;
        $profile->status = 'Pending';

        $profile->save();

        return redirect()->route('user.home')->with('membership_success', 'Thank you for submitting your application. Your application is currently under review.');
    }

    public function updatedRegion($value)
    {
        $this->getRegionCode();
    }
    public function getRegionCode(){
        if($this->region){
            $this->regionCode = PHRegions::where('region_description', $this->region)->value('region_code');
        }
    }

    public function updatedProvince($value)
    {
        $this->getProvinceCode();
    }
    public function getProvinceCode(){
        if($this->province){
            $this->provinceCode = PHProvinces::where('province_description', $this->province)->value('province_code');
        }
    }

    public function updatedMunicipality($value)
    {
        $this->getMunicipalityCode();
    }
    public function getMunicipalityCode(){
        if($this->municipality){
            $this->municipalityCode = PHCities::where('city_municipality_description', $this->municipality)->value('city_municipality_code');
        }
    }

    public function render()
    {
        return view('livewire.client.membership-page');
    }
}
