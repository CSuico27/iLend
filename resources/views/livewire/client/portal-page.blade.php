<div class="w-full h-auto flex justify-center items-center overflow-hidden">
    <div class="flex flex-col justify-center items-center w-full max-w-[95%] lg:max-w-[80%] h-auto">
        <h1 class="mt-5 font-semibold text-xl">Welcome to iLend</h1>
        <div class="w-full h-auto mx-auto">
            <div class="border-b border-gray-200 dark:border-neutral-700">
                <nav class="flex justify-center gap-x-4" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
                    <button wire:click="setActiveTab('dashboard')" type="button" class="{{ $activeTab === 'dashboard' ? 'hover:cursor-pointer border-[#ff3134] text-[#ff3134] font-semibold' : 'hover:cursor-pointer border-transparent text-gray-500' }} py-4 px-1 inline-flex items-center gap-x-2 border-b-2 text-sm" id="tabs-with-icons-item-1" aria-selected="true" data-hs-tab="#tabs-with-icons-1" aria-controls="tabs-with-icons-1" role="tab">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </button>
                    <button wire:click="setActiveTab('loans')" type="button" class="{{ $activeTab === 'loans' ? 'hover:cursor-pointer border-[#ff3134] text-[#ff3134] font-semibold' : 'hover:cursor-pointer border-transparent text-gray-500' }} py-4 px-1 inline-flex items-center gap-x-2 border-b-2 text-sm" id="tabs-with-icons-item-2" aria-selected="false" data-hs-tab="#tabs-with-icons-2" aria-controls="tabs-with-icons-2" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                        </svg>
                          
                    Loans
                    </button>
                    <button wire:click="setActiveTab('dues')" type="button" class="{{ $activeTab === 'dues' ? 'hover:cursor-pointer border-[#ff3134] text-[#ff3134] font-semibold' : 'hover:cursor-pointer border-transparent text-gray-500' }} py-4 px-1 inline-flex items-center gap-x-2 border-b-2 text-sm" id="tabs-with-icons-item-3" aria-selected="false" data-hs-tab="#tabs-with-icons-3" aria-controls="tabs-with-icons-3" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>                          
                        Dues
                    </button>
                    <button wire:click="activateCreditScoreTab" type="button" class="{{ $activeTab === 'cs' ? 'hover:cursor-pointer border-[#ff3134] text-[#ff3134] font-semibold' : 'hover:cursor-pointer border-transparent text-gray-500' }} py-4 px-1 inline-flex items-center gap-x-2 border-b-2 text-sm" id="tabs-with-icons-item-4" aria-selected="false" data-hs-tab="#tabs-with-icons-4" aria-controls="tabs-with-icons-4" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>                          
                        Credit Score
                    </button>
                </nav>
            </div>
            
            <div class="mt-3">
                @if ($activeTab === 'dashboard')
                    <div class="w-full h-auto mt-5 space-y-4">
                        <div class="flex justify-end">
                            {{-- @if ($this->canApply) --}}
                               <x-button
                                    label="Apply for Loan"
                                    class="w-full lg:w-auto bg-red-600 hover:bg-red-400 focus:bg-red-400 focus:ring-red-400"
                                    wire:click="openLoanApplicationModal"
                                >
                                </x-button>

                                <x-modal-card title="Loan Application" wire:model="showLoanApplicationModal">
                                    <form id="loanApplicationForm" wire:submit.prevent="submitLoanApplication" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <x-input label="Pangalan ng Umutang" placeholder="Your full name" wire:model="user_name" readonly />
                                        </div>
                                        <x-select label="Uri ng Loan" wire:model.live="loan_type" placeholder="Uri ng Loan">
                                            <x-select.option label="Regular Loan" value="regular" />
                                            <x-select.option label="Emergency Loan" value="emergency" />
                                            <x-select.option label="Car Loan" value="car" />
                                        </x-select> 
                                        <x-currency
                                            label="Halagang Hiniram"
                                            placeholder="Halagang Hiniram"
                                            wire:model.live="loan_amount" 
                                            prefix="₱"
                                        />
                                        {{-- <x-input label="Interest Rate" placeholder="Interest Rate" wire:model.live="interest_rate" />  --}}
                                        <x-number 
                                            label="Interest Rate" 
                                            placeholder="0" 
                                            wire:model.live="interest_rate"
                                        />
                                        <x-select label="Tagal ng Buwan" wire:model.live="loan_term" placeholder="Tagal ng Buwan">
                                            <x-select.option label="3 Months" value="3" />
                                            <x-select.option label="6 Months" value="6" />
                                            <x-select.option label="9 Months" value="9" />
                                            <x-select.option label="12 Months" value="12" />
                                            <x-select.option label="24 Months" value="24" />
                                        </x-select> 
                                        {{-- <x-select
                                            label="Payment Frequency"
                                            placeholder="Select payment frequency"
                                            wire:model.live="payment_frequency"
                                            :options="[
                                                'daily' => 'daily',
                                                'weekly' => 'weekly',
                                                'biweekly' => 'biweekly',
                                                'monthly'=> 'monthly'
                                            ]"
                                        /> --}}
                                        <x-radio id="daily" label="Araw-araw" wire:model.live="payment_frequency" value="daily" />
                                        <x-radio id="weekly" label="Lingguhan" wire:model.live="payment_frequency" value="weekly" />
                                        <x-radio id="biweekly" label="Ikalawang Linggo" wire:model.live="payment_frequency" value="biweekly" />
                                        <x-radio id="monthly" label="Buwanan" wire:model.live="payment_frequency" value="monthly" />
                                        <x-input label="Date ng Unang Bayad" placeholder="First Payment Date" :value="$start_date ? \Carbon\Carbon::parse($start_date)->format('F d, Y') : ''" readonly/>
                                        <x-input label="Date ng Huling Bayad" placeholder="Last Payment Date" :value="$end_date ? \Carbon\Carbon::parse($end_date)->format('F d, Y') : ''" readonly />
                                        <x-input label="Kabuuang Interest" placeholder="Total Interest" :value="number_format($interest_amount, 2)" readonly />
                                        <x-input label="Kabuuang Babayaran" placeholder="Total Loan Payable" :value="number_format($total_payment, 2)" readonly />
                                        <div class="sm:col-span-2 w-full">
                                            <x-input label="Halagang Babayaran Kada Hulugan" placeholder="Payment Per Term" :value="number_format($payment_per_term, 2)" readonly/>
                                        </div>
                                    </form>
                                    <x-slot name="footer" class="flex justify-between gap-x-4">
                                        {{-- <x-button flat negative label="Delete" x-on:click="close" />  --}}

                                        <div class="flex gap-x-4 ml-auto">
                                            <x-button flat label="Cancel" x-on:click="close" />

                                            <x-button
                                                primary
                                                label="Submit"
                                                type="submit" 
                                                form="loanApplicationForm" 
                                                wire:target="submitLoanApplication"
                                                wire:loading.attr="disabled"
                                            >
                                                <span wire:loading.remove wire:target="submitLoanApplication">Save</span>
                                                <span wire:loading wire:target="submitLoanApplication">Saving...</span>
                                            </x-button>
                                        </div>
                                    </x-slot>
                                </x-modal-card>
                            {{-- @endif --}}
                        </div>
                       
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                            <x-card title="Active Loan" rounded="3xl">
                                @if ($hasActiveLoan)
                                    <div>
                                        @if ($activeLoanDetails)
                                            <p>Loan Type: <span class="text-sm capitalize italic bg-green-600 text-white px-3 rounded-full">{{ $activeLoanDetails->loan_type }}</span></p>
                                        @endif
                                        @if ($activeLoanDetails)
                                            <p class="mt-2">Loan Term: <span class="text-sm capitalize italic">{{ $activeLoanDetails->loan_term }}</span></p>
                                        @endif

                                        @if ($activeLoanDetails)
                                            <p class="mt-2">Interest Rate: <span class="text-sm capitalize italic">{{ $activeLoanDetails->interest_rate }}%</span></p>
                                        @endif

                                        @if ($activeLoanDetails)
                                            <p class="mt-2">Payment Per Term: <span class="text-sm capitalize italic">₱{{ number_format($activeLoanDetails->payment_per_term, 2) }}</span></p>
                                        @endif
                                        <hr class="border-red-500">
                                        <p class="mt-5">Total Loan Amount: <span class="text-xl font-bold text-blue-600 my-5">₱{{ number_format($activeLoanAmount, 2) }}</span> </p>
                                    </div>
                                @else
                                    <p class="text-gray-400 italic">No active loan found</p>
                                @endif
                            </x-card>
                            <x-card title="Total Payment Paid" rounded="3xl">
                                <div class="text-xl font-bold text-green-600">
                                    ₱{{ number_format($totalPaid, 2) }}
                                </div>
                            </x-card>
                            <x-card title="Total Remaining Balance" rounded="3xl">
                                <div class="text-xl font-bold text-red-600">
                                    ₱{{ number_format($remainingBalance, 2) }}
                                </div>
                            </x-card>
                        </div>
                        
                    </div>
                @elseif ($activeTab === 'loans')
                    <div class="w-full h-auto mt-5 overflow-x-scroll lg:overflow-visible">
                        <div class="flex flex-col">
                            @if ($userLoans->where('status', 'Approved')->where('is_finished', 0)->isNotEmpty())
                                <div class="-m-1.5">
                                    <div class="p-1.5 min-w-full inline-block align-middle">
                                        <div class="">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                                <thead>
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan ID</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan Type</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan Amount</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Interest Rate</th>
                                                    <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan Term</th>
                                                    <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                                    @foreach ($userLoans->where('status', 'Approved')->where('is_finished', 0) as $loan)
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loan->id }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full">{{ $loan->loan_type }} Loan</span></td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">₱{{ number_format($loan->loan_amount, 2) }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $loan->interest_rate }}%</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $loan->loan_term }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                                <x-dropdown position="bottom">
                                                                    <x-dropdown.item label="Full Details" x-on:click="$openModal('laonDetailsModal')" wire:click="loadLoanDetails({{ $loan->id }})" />
                                                                    {{-- <x-dropdown.item label="Ledgers" /> --}}
                                                                </x-dropdown>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-10 text-gray-500 italic dark:text-neutral-400">
                                    You have no active loan records yet.
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col">
                        <hr class="border-yellow-500">
                        <h1 class="font-semibold text-xl mt-5 mx-auto">Loan History</h1>
                        @if ($userLoans->where('status', 'Approved')->where('is_finished', 1)->isNotEmpty())
                            <div class="mt-4 flex flex-col justify-center items-center">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan ID</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan Type</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan Amount</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Interest Rate</th>
                                            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Loan Term</th>
                                            <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        @foreach ($userLoans->where('status', 'Approved')->where('is_finished', 1) as $loan)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loan->id }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full">{{ $loan->loan_type }} Loan</span></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">₱{{ number_format($loan->loan_amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $loan->interest_rate }}%</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $loan->loan_term }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                                    <x-dropdown position="bottom">
                                                        <x-dropdown.item label="Full Details" x-on:click="$openModal('laonDetailsModal')" wire:click="loadLoanDetails({{ $loan->id }})" />
                                                        {{-- <x-dropdown.item label="Ledgers" /> --}}
                                                    </x-dropdown>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                            </table>
                        </div>
                        @else
                            <div class="mt-4 text-center text-gray-500 italic dark:text-neutral-400">
                                No loan history available.
                            </div>
                        @endif
                    </div>
                    </div>
                @elseif ($activeTab === 'dues')
                {{-- <div id="tabs-with-icons-3" class="hidden" role="tabpanel" aria-labelledby="tabs-with-icons-item-3"> --}}
                    <p class="text-gray-500 dark:text-neutral-400">
                        This is the <em class="font-semibold text-gray-800 dark:text-neutral-200">third</em> item's tab body.
                    </p>
                {{-- </div> --}}
                @elseif ($activeTab === 'cs')
                    <div class="w-full h-auto mt-5 flex flex-col-reverse pb-10 lg:pb-0 lg:flex-row justify-center gap-5">
                        <div class="w-full lg:max-w-lg">
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Behavior
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Effect on Score
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center gap-3">
                                                    <span>On-time payment</span> 
                                                    <div class="hs-tooltip inline-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hs-tooltip-toggle w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                        </svg>                                                      
                                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700" role="tooltip">
                                                            Paid on or before due date
                                                        </span>
                                                    </div>
                                                </div>
                                            </th>
                                            <td class="px-6 py-4">
                                                +2 <span class="italic text-[10px] text-blue-700">points</span>
                                            </td>
                                        </tr>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center gap-3">
                                                    <span>Late payment</span> 
                                                    <div class="hs-tooltip inline-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hs-tooltip-toggle w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                        </svg>                                                      
                                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700" role="tooltip">
                                                            Paid but after due date
                                                        </span>
                                                    </div>
                                                </div>
                                            </th>
                                            <td class="px-6 py-4">
                                                +1 <span class="italic text-[10px] text-blue-700">points</span>
                                            </td>
                                        </tr>
                                        <tr class="bg-white dark:bg-gray-800">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center gap-3">
                                                    <span>Fully finished loan</span> 
                                                    <div class="hs-tooltip inline-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hs-tooltip-toggle w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                        </svg>                                                      
                                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700" role="tooltip">
                                                            Finished the loan
                                                        </span>
                                                    </div>
                                                </div>
                                            </th>
                                            <td class="px-6 py-4">
                                                +5 <span class="italic text-[10px] text-blue-700">points</span>
                                            </td>
                                        </tr>

                                        <tr class="bg-white dark:bg-gray-800">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center gap-3">
                                                    <span>Missed or unpaid due</span> 
                                                    <div class="hs-tooltip inline-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hs-tooltip-toggle w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                        </svg>                                                      
                                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700" role="tooltip">
                                                            Ledger is Due
                                                        </span>
                                                    </div>
                                                </div>
                                            </th>
                                            <td class="px-6 py-4">
                                                −3 <span class="italic text-[10px] text-red-700">points</span>
                                            </td>
                                        </tr>
                                        <tr class="bg-white dark:bg-gray-800">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <div class="flex items-center gap-3">
                                                    <span>Overdue loan still unfinished</span> 
                                                    <div class="hs-tooltip inline-block">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 hs-tooltip-toggle w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                        </svg>                                                      
                                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700" role="tooltip">
                                                            loan not finished & past end date
                                                        </span>
                                                    </div>
                                                </div>
                                            </th>
                                            <td class="px-6 py-4">
                                                −5 <span class="italic text-[10px] text-red-700">points</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- <p class="text-gray-500 dark:text-neutral-400 text-center">
                               {{ $creditRemarks }}
                            </p> --}}
                        </div>
                        <div class="w-full lg:max-w-xs relative">
                            <canvas id="creditScoreChart" class="z-10"></canvas>
                            <p class="absolute inset-0 flex items-center justify-center text-3xl font-semibold pointer-events-none">
                                {{ $creditScore ?? 0 }}%
                            </p>
                            <p class="text-center">
                                <span class="text-xs">Last updated:</span> <span class="italic text-blue-800">{{ $creditLastUpdated }}</span>
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-modal name="laonDetailsModal" persistent>
        <x-card title="Loan Details" style="z-index: 100">
            <div class="w-full lg:max-w-3xl lg:min-w-3xl">
                @if ($selectedLoan)
                    <div class="w-full h-auto">
                        <div class="flex justify-between py-2">
                            <h2 class="text-xl font-bold">Loan #{{ $selectedLoan->id }} Details</h2>
                            @if ($selectedLoan->is_finished)
                                <x-badge lg icon="check-badge" positive label="Fully Paid" />
                            @else
                                <x-badge lg icon="arrow-path-rounded-square" negative label="Ongoing/Current" />
                            @endif
                        </div>
                        <hr class="border-red-500 mb-4">
                        <div class="flex flex-col">
                            <div class="lg:-m-1.5 overflow-x-auto">
                              <div class="p-1.5 lg:min-w-full inline-block align-middle">
                                <div class="border border-gray-200 overflow-hidden dark:border-neutral-700">
                                  <table class="lg:min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Type</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full">{{ $selectedLoan->loan_type }}</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Amount</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full"> ₱{{ number_format($selectedLoan->loan_amount, 2) }}</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Interest Rate</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full"> {{ $selectedLoan->interest_rate }}%</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Term</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full"> {{ $selectedLoan->loan_term }}</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Payment Frequency</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full"> {{ $selectedLoan->payment_frequency }}</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Payment Per Term</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full"> ₱{{ number_format($selectedLoan->payment_per_term, 2) }}</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"> <span class="font-semibold text-md">Total Interest Amount</span> </td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200"><span class="capitalize px-4 text-sm italic bg-green-600 text-white rounded-full"> ₱{{ number_format($selectedLoan->interest_amount, 2) }}</span></td>
                                      </tr>
                                      <tr>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"></td>
                                        <td class="w-1/2 px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">Total: <span class="capitalize px-4 text-lg"> ₱{{ number_format($selectedLoan->total_payment, 2) }}</span></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                        </div>                     
                        <h3 class="mt-4 font-semibold mb-2">Ledgers</h3>
                        <hr class="border-red-500 mb-4">
                        <div class="flex flex-col max-w-[320px] mx-auto lg:mx-0 lg:max-w-full">
                            <div class="lg:-m-1.5 overflow-x-auto">
                                <div class="lg:p-1.5 min-w-full inline-block align-middle">
                                    <div class="overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                            <thead>
                                                <tr class="bg-gray-300">
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Due Date</th>
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Is Due</th>
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Status</th>
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Amount Paid</th>
                                                    <th scope="col" class="pl-6 py-3 text-end text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Payment Method</th>
                                                    <th scope="col" class="pl-6 py-3 text-end text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Downloadables</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                                @foreach ($selectedLoan->ledgers as $ledger)
                                                    <tr>
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ \Carbon\Carbon::parse($ledger->due_date)->format('F j, Y') }}
                                                        </td>
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200 
                                                            {{ $ledger->is_due ? 'bg-red-200 dark:bg-red-600 text-red-900 dark:text-red-100 font-bold' : '' }}">
                                                            {{ $ledger->is_due ? 'Yes' : 'No' }}
                                                        </td>
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $ledger->status }}
                                                        </td>
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                            ₱{{ number_format(optional($ledger->payment)->amount ?? 0, 2) }}
                                                        </td>
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                            {{ $ledger->payment?->payment_method ?? 'Unpaid' }}
                                                        </td>
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-end text-sm font-medium">
                                                            @if ($ledger->payment && $ledger->payment->receipt)
                                                                <a href="{{ asset('storage/' . $ledger->payment->receipt) }}"
                                                                   download
                                                                   class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-green-600 hover:text-green-800 focus:outline-hidden focus:text-green-800 dark:text-green-400 dark:hover:text-green-300 dark:focus:text-green-300">
                                                                    Download Receipt
                                                                </a>
                                                            @else
                                                                <span class="text-xs text-gray-400 italic">No receipt</span>
                                                            @endif
                                                        </td>                                                        
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                @else
                    <div class="w-full h-auto flex justify-center items-center py-10">
                        <div class="animate-spin inline-block size-8 border-3 border-current border-t-transparent text-blue-600 rounded-full dark:text-blue-500" role="status" aria-label="loading">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                @endif
            </div>
            <x-slot name="footer" class="flex justify-end gap-x-4">
                <x-button flat label="Close" x-on:click="close" wire:click="clearSelectedLoan" />
            </x-slot>
        </x-card>
    </x-modal>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const score = @json($creditScore ?? 0);
        const remaining = 100 - score;

        let tierColor = '#f44336';
        if (score >= 81) {
            tierColor = '#4caf50';
        } else if (score >= 61) {
            tierColor = '#2196f3';
        } else if (score >= 41) {
            tierColor = '#ff9800';
        }

        const ctx = document.getElementById('creditScoreChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Credit Score', 'Remaining'],
                datasets: [{
                    data: [score, remaining],
                    backgroundColor: [tierColor, '#e0e0e0'],
                    borderWidth: 1
                }]
            },
            options: {
            cutout: '70%',
            plugins: {
                    title: {
                        display: true,
                        text: 'Your Credit Score',
                        font: {
                            size: 18
                        }
                    },
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script>
        window.addEventListener('reload', event => {
            window.location.reload();
        })
    </script>

</div>
 