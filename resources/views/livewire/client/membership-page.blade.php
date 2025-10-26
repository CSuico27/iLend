<div class="w-full h-auto">
    <div class="w-[90%] lg:w-full max-w-xl mx-auto mt-10 mb-10">
        <form wire:submit.prevent="save">
            <!-- Stepper -->
            <div>
                <!-- Stepper Nav -->
                <ul class="relative flex flex-row gap-x-2 mb-4">
                    <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group">
                        <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                        <span class="size-7 flex justify-center items-center shrink-0 font-medium rounded-full {{ $currentStep == 1 || $isFinishedStepOne == true ? 'bg-blue-600 text-white' : 'text-gray-800'}}">
                            <span class="{{ $isFinishedStepOne == true ? 'hidden' : ''}}">1</span>
                            <svg class="flex-shrink-0 size-3 {{ $isFinishedStepOne == true ? 'block' : 'hidden'}}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="ms-2 text-xs lg:text-sm font-medium text-gray-800">
                            Personal Details
                        </span>
                        </span>
                        <div class="w-full h-px flex-1 {{ $isFinishedStepOne == true ? 'bg-blue-600' : 'bg-gray-200'}}"></div>
                    </li>
                
                    <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group">
                        <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                        <span class="size-7 flex justify-center items-center shrink-0 font-medium rounded-full {{ $currentStep == 2 || $isFinishedStepOne == true ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800'}}">
                            <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">2</span>
                            <svg class="hidden shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                        <span class="ms-2 text-xs lg:text-sm font-medium text-gray-800">
                            Valid ID / Proof of Identity
                        </span>
                        </span>
                        <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600 dark:bg-neutral-700 dark:hs-stepper-success:bg-blue-600 dark:hs-stepper-completed:bg-teal-600"></div>
                    </li>
            
                <!-- End Item -->
                </ul>
                
                @if ($currentStep == 1)
                    <div class="h-auto">
                        <div class="h-auto px-4 py-10 bg-gray-50 flex justify-center items-center border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                            <div class="w-full h-auto px-0 lg:px-10 flex flex-col gap-3">
                                <x-input
                                    wire:model.blur="name"
                                    icon="user"
                                    label="Name"
                                    placeholder="Enter your Full Name"
                                    disabled
                                />
                                <x-input
                                    wire:model.blur="email"
                                    icon="at-symbol"
                                    label="Email"
                                    placeholder="Enter your email"
                                    disabled
                                />
                                <x-phone
                                    id="multiple-mask"
                                    wire:model.blur="phone"
                                    label="Phone"
                                    placeholder="9XXXXXXXXX"
                                    :mask="['##########']"
                                >
                                    <x-slot name="prefix">
                                        <span class="text-gray-500 font-medium text-sm pl-1">+63</span>
                                    </x-slot>
                                </x-phone>
                                
                                <x-select
                                    label="Marital Status"
                                    placeholder="Select marital status"
                                    :options="['Single', 'Married', 'Divorced', 'Widowed']"
                                    wire:model="marital_status"
                                />

                                <x-datetime-picker
                                    wire:model.live="birthdate"
                                    label="Birthdate"
                                    placeholder="Birthdate"
                                    display-format="MM/DD/YYYY"
                                    parse-format="MM/DD/YYYY"
                                    without-time
                                />
                                <div class="grid grid-cols-3 gap-2 mt-4">
                                    <div class="flex justify-start items-center">
                                        <label for="gender" class="block mb-1 text-sm font-medium text-gray-900 font-secondary">Gender: </label>
                                    </div>
                                    <div class="flex flex-col justify-center items-center col-span-2">
                                        <ul class="w-full m-0 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex">
                                            <li class="list-none w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                                <div class="flex items-center ps-3">
                                                    <input id="horizontal-list-radio-male" type="radio" wire:model.live="gender" value="Male" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500  focus:ring-2 ">
                                                    <label for="horizontal-list-radio-male" class="font-secondary w-full py-3 ms-2 text-sm font-medium text-gray-900">Male</label>
                                                </div>
                                            </li>
                                            <li class="list-none w-full border-b border-gray-200 sm:border-b-0 sm:border-r">
                                                <div class="flex items-center ps-3">
                                                    <input id="horizontal-list-radio-female" type="radio" value="Female" wire:model.live="gender" name="gender" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2">
                                                    <label for="horizontal-list-radio-female" class="font-secondary w-full py-3 ms-2 text-sm font-medium text-gray-900">Female</label>
                                                </div>
                                            </li>
                                        </ul>
                                        @error('gender')
                                            <p class="font-secondary italic text-red-500 text-xs px-3 py-0.5 rounded-md bg-gray-300 mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="w-full flex flex-col md:flex-row gap-5 mt-5 mb-4">
                                    <x-select
                                        label="Select Region"
                                        wire:model.live="region"
                                        placeholder="Ex: REGION IV-A"
                                        :async-data="route('api.regions.index')"
                                        :template="[
                                            'region_description'   => 'user-option',
                                        ]"
                                        option-label="region_description"
                                        option-value="region_description"
                                        {{-- option-description="region_description" --}}
                                        
                                    />
                                    @if (!$region)
                                        <x-select
                                            label="Select City/Province"
                                            wire:model.live="province"
                                            placeholder="Ex: CITY OF MANILA"
                                            {{-- :async-data="route('location.region', ['regionCode' => $regionCode])" --}}
                                            :template="[
                                                'province_description'   => 'user-option',
                                            ]"
                                            option-label="province_description"
                                            option-value="province_description"
                                            {{-- option-description="province_description" --}}
                                            disabled
                                        />
                                    @else
                                        <x-select
                                            label="Select City/Province"
                                            wire:model.live="province"
                                            placeholder="Ex: CITY OF MANILA"
                                            :async-data="route('location.province', ['regionCode' => $regionCode])"
                                            :template="[
                                                'province_description'   => 'user-option',
                                            ]"
                                            option-label="province_description"
                                            option-value="province_description"
                                            {{-- option-description="province_description" --}}
                                        />
                                    @endif
                                </div>
                                <div class="w-full flex flex-col md:flex-row gap-5 mt-5 mb-4">
                                    @if (!$province)
                                    
                                        <x-select
                                            label="Select Municipality"
                                            wire:model.live="municipality"
                                            placeholder="Ex: ATIMONAN"
                                            {{-- :async-data="route('location.province', ['provinceCode' => $provinceCode])" --}}
                                            :template="[
                                                'city_municipality_description'   => 'user-option',
                                            ]"
                                            option-label="city_municipality_description"
                                            option-value="city_municipality_description"
                                            {{-- option-description="city_municipality_description" --}}
                                            disabled
                                        />
                                    @else
                                        <x-select
                                            label="Select Municipality"
                                            wire:model.live="municipality"
                                            placeholder="Ex: ATIMONAN"
                                            :async-data="route('location.municipality', ['provinceCode' => $provinceCode])"
                                            :template="[
                                                'city_municipality_description'   => 'user-option',
                                            ]"
                                            option-label="city_municipality_description"
                                            option-value="city_municipality_description"
                                            {{-- option-description="city_municipality_description" --}}
                                        />
                                    @endif
                                    
                                    @if (!$region || !$province || !$municipality)
                                        <x-select
                                            label="Select Barangay"
                                            wire:model.live="barangay"
                                            placeholder="Ex: Poblacion II"
                                            {{-- :async-data="route('api.barangays.index')" --}}
                                            :template="[
                                                'barangay_description'   => 'user-option',
                                            ]"
                                            option-label="barangay_description"
                                            option-value="barangay_description"
                                            {{-- option-description="barangay_description" --}}
                                            disabled
                                        />
                                    @else
                                        <x-select
                                            label="Select Barangay"
                                            wire:model.live="barangay"
                                            placeholder="Ex: Poblacion II"
                                            :async-data="route('location.barangay', ['municipalityCode' => $municipalityCode])"
                                            :template="[
                                                'barangay_description'   => 'user-option',
                                            ]"
                                            option-label="barangay_description"
                                            option-value="barangay_description"
                                            {{-- option-description="barangay_description" --}}
                                        />
                                    @endif
                                </div>
                                {{-- <x-input
                                    wire:model.blur="address"
                                    icon="map-pin"
                                    label="Address"
                                    placeholder="Enter your address"
                                /> --}}
                            </div>
                        </div>
                    </div>
                @elseif($currentStep == 2)
                    <div class="h-auto">
                        <div class="h-auto px-4 py-10 bg-gray-50 flex justify-center items-center border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                            <div class="w-full h-auto px-0 lg:px-10 flex flex-col gap-3">
                                <div>
                                    <p class="font-semibold">Your Biodata</p>
                                    <x-filepond::upload 
                                        wire:model="biodata" 
                                        :accepted-file-types="['image/jpeg', 'image/png', 'application/pdf']"
                                        label="Upload your Biodata"
                                    />
                                </div>

                                <div>
                                    <p class="font-semibold">Barangay Clearance</p>
                                    <x-filepond::upload 
                                        wire:model="barangay_clearance" 
                                        :accepted-file-types="['image/jpeg', 'image/png', 'application/pdf']"
                                        label="Upload your Barangay Clearance"
                                    />
                                </div>

                                <div>
                                    <p class="font-semibold">Valid Government ID</p>
                                    <x-filepond::upload 
                                        wire:model="valid_id" 
                                        :accepted-file-types="['image/jpeg', 'image/png', 'application/pdf']"
                                        label="Upload your Valid ID"
                                    />
                                </div>
                                <div>
                                    <x-maskable 
                                        wire:key="tin"
                                        wire:model.blur="tin_number"
                                        label="TIN Number" 
                                        mask="###-###-###-###" 
                                        placeholder="TIN Number" 
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="flex">
                    {{-- @if ($currentStep > 1)
                        <button wire:click="backStep" type="button" class="justify-start mt-4 py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                        {{  $currentStep == 1 ? 'disabled="disabled"' : '' }}
                        >
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                            Back
                        </button>
                    @endif --}}

                    @if ($currentStep < 2)
                        <button wire:click="nextStep" type="button" class="ml-auto mt-4 py-2 px-3 inline-flex items-center gap-x-1 text-base font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                        {{ !$name || !$email || !$phone || !$birthdate || !$gender || !$marital_status || !$region || !$province || !$municipality || !$barangay ? 'disabled=disabled' : '' }}
                        >
                            Next
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </button>
                    @else
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="ml-auto mt-4 py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                        >
                            <span wire:loading.remove wire:target="save">Submit</span>
                            <span wire:loading wire:target="save">Submitting...</span>
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </button>
                    @endif
                    
                </div>
                
            </div>
        </form>
    </div>
</div>
