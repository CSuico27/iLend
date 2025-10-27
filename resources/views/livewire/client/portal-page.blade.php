<div class="w-full h-auto flex overflow-hidden">
    <div x-data="{ sidebarOpen: false }" class="relative">
        <div class="fixed top-[5.5rem] z-50 md:hidden transition-all duration-300 transform" 
            :class="sidebarOpen ? 'translate-x-[13rem]' : 'translate-x-0'">
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="group relative inline-flex items-center p-2 text-sm font-medium text-white transition-all duration-300 transform bg-[#ff3134] h-12 rounded-r-lg focus:outline-none">
                
                <span x-show="!sidebarOpen" x-transition.duration.300ms>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span x-show="sidebarOpen" x-transition.duration.300ms>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                    </svg>
                </span>
                
                <span class="absolute left-full top-1/2 -translate-y-1/2 z-50 ml-2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-medium text-white opacity-0 transition-opacity group-hover:opacity-100">
                    <span x-text="sidebarOpen ? 'Close Sidebar' : 'Open Sidebar'"></span>
                </span>
            </button>
        </div>
        
        <div class="flex flex-col justify-start z-70 w-54 md:w-64 h-screen border-t border-r border-gray-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-900 
                    fixed top-0 md:z-40 md:relative md:ml-0 md:transform-none md:transition-none" 
            :class="{ 'transition-transform duration-300 ease-in-out transform translate-x-0': sidebarOpen, 'transition-transform duration-300 ease-in-out transform -translate-x-full': !sidebarOpen, 'md:translate-x-0': true }">
            
            <div class="w-full h-auto">
                <nav class="flex flex-col gap-y-4" aria-label="Tabs" role="tablist" aria-orientation="vertical">
                    <a href="/" class=" block md:hidden">
                        <img src="{{ asset('images/ilend-logo.png') }}" class="h-14" alt="iLend Logo">
                    </a>
                    <button wire:click="setActiveTab('dashboard')" @click="sidebarOpen = false" type="button" class="{{ $activeTab === 'dashboard' ? 'border-[#ff3134] text-[#ff3134] font-semibold bg-gray-100 dark:bg-neutral-800 rounded-lg' : 'border-transparent text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 hover:rounded-lg cursor-pointer' }} py-3 px-4 inline-flex items-center gap-x-2 border-l-2 text-sm text-start" id="tabs-with-icons-item-1" aria-selected="true" data-hs-tab="#tabs-with-icons-1" aria-controls="tabs-with-icons-1" role="tab">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Dashboard
                    </button>
                    <button wire:click="setActiveTab('loans')" @click="sidebarOpen = false" type="button" class="{{ $activeTab === 'loans' ? 'border-[#ff3134] text-[#ff3134] font-semibold bg-gray-100 dark:bg-neutral-800 rounded-lg' : 'border-transparent text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 hover:rounded-lg cursor-pointer' }} py-3 px-4 inline-flex items-center gap-x-2 border-l-2 text-sm text-start" id="tabs-with-icons-item-2" aria-selected="false" data-hs-tab="#tabs-with-icons-2" aria-controls="tabs-with-icons-2" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                        </svg>
                        Loans
                    </button>
                    <button wire:click="activateCreditScoreTab" @click="sidebarOpen = false" type="button" class="{{ $activeTab === 'cs' ? 'border-[#ff3134] text-[#ff3134] font-semibold bg-gray-100 dark:bg-neutral-800 rounded-lg' : 'border-transparent text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 hover:rounded-lg cursor-pointer' }} py-3 px-4 inline-flex items-center gap-x-2 border-l-2 text-sm text-start" id="tabs-with-icons-item-4" aria-selected="false" data-hs-tab="#tabs-with-icons-4" aria-controls="tabs-with-icons-4" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                        Credit Score
                    </button>
                    <button wire:click="setActiveTab('profile')" @click="sidebarOpen = false" type="button" class="{{ $activeTab === 'profile' ? 'border-[#ff3134] text-[#ff3134] font-semibold bg-gray-100 dark:bg-neutral-800 rounded-lg' : 'border-transparent text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 hover:rounded-lg cursor-pointer' }} py-3 px-4 inline-flex items-center gap-x-2 border-l-2 text-sm text-start" id="tabs-with-icons-item-3" aria-selected="false" data-hs-tab="#tabs-with-icons-3" aria-controls="tabs-with-icons-3" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Profile
                    </button>
                    <button wire:click="setActiveTab('chats')" @click="sidebarOpen = false" type="button" class="{{ $activeTab === 'chats' ? 'border-[#ff3134] text-[#ff3134] font-semibold bg-gray-100 dark:bg-neutral-800 rounded-lg' : 'border-transparent text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 hover:rounded-lg cursor-pointer' }} py-3 px-4 inline-flex items-center gap-x-2 border-l-2 text-sm text-start" id="tabs-with-icons-item-3" aria-selected="false" data-hs-tab="#tabs-with-icons-3" aria-controls="tabs-with-icons-3" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                        Chats
                    </button>
                </nav>
            </div>
        </div>
    </div>
    <div class="flex-grow w-full h-auto p-4 mx-auto">
        <h1 class="font-semibold text-xl mb-10 flex justify-center items-center">Welcome to iLEND</h1>
        <div class="mt-3">
            @if ($activeTab === 'dashboard')
                <div class="w-full h-auto pb-5 space-y-4">
                    <div class="flex justify-end w-full">
                        <x-modal-card title="Loan Application" wire:model="showLoanApplicationModal" width="3xl">
                            <form id="loanApplicationForm" wire:submit.prevent="submitLoanApplication" class="grid grid-cols-1 sm:grid-cols-2 gap-4 space-y-2">
                                <div class="sm:col-span-2 space-y-3 text-sm">
                                    <x-input label="Borrower's Name / Pangalan ng Umutang" placeholder="Your full name" wire:model="user_name" readonly />
                                    <x-select label="Loan Type / Uri ng Loan" wire:model.live="loan_type" placeholder="Enter loan type">
                                        <x-select.option label="Regular Loan" value="regular" />
                                        <x-select.option label="Emergency Loan" value="emergency" />
                                        <x-select.option label="Car Loan" value="car" />
                                    </x-select>
                                    <x-currency
                                        label="Loan Amount / Halagang Hiniram"
                                        placeholder="Enter loan amount"
                                        wire:model.live="loan_amount"
                                        prefix="₱"
                                    />
                                    <x-input
                                        label="Interest Rate"
                                        placeholder="Interest Rate"
                                        wire:model.live="interest_rate"
                                        prefix="%"
                                        readonly
                                    />
                                    <x-select label="Loan Term / Tagal ng Buwan" wire:model.live="loan_term" placeholder="Enter loan term">
                                        <x-select.option label="3 Months / Tatlong Buwan" value="3" />
                                        <x-select.option label="6 Months / Anim na Buwan" value="6" />
                                        <x-select.option label="9 Months / Siyam na Buwan" value="9" />
                                        <x-select.option label="12 Months / Labindalawang Buwan" value="12" />
                                        <x-select.option label="24 Months / Dalawampu't apat na Buwan" value="24" />
                                    </x-select>
                                </div>
                                
                                <x-radio id="daily" label="Daily / Araw-araw" wire:model.live="payment_frequency" value="daily" />
                                <x-radio id="weekly" label="Weekly / Linguhan" wire:model.live="payment_frequency" value="weekly" />
                                <x-radio id="biweekly" label="Biweekly / Ikalawang Linggo" wire:model.live="payment_frequency" value="biweekly" />
                                <x-radio id="monthly" label="Monthly / Buwanan" wire:model.live="payment_frequency" value="monthly" />
                                <x-input label="First Payment Date / Date ng Unang Bayad" placeholder="First payment date" :value="$start_date ? \Carbon\Carbon::parse($start_date)->format('F d, Y') : ''" readonly/>
                                <x-input label="Last Payment Date / Date ng Huling Bayad" placeholder="Last payment date" :value="$end_date ? \Carbon\Carbon::parse($end_date)->format('F d, Y') : ''" readonly />
                                {{-- <x-input label="Total Interest / Kabuuang Interest" placeholder="Total Interest" :value="number_format($interest_amount, 2)" readonly /> --}}
                                <div class="sm:col-span-2 w-full space-y-3">
                                    <x-input label="Total Loan Payable / Kabuuang Babayaran" placeholder="Total Loan Payable" :value="number_format($total_payment, 2)" readonly />
                                    <x-input label="Payment Per Term / Halagang Babayaran kada Hulugan" placeholder="Payment Per Term" :value="number_format($payment_per_term, 2)" readonly/>
                                </div>
                            </form>
                            <x-slot name="footer" class="flex justify-between gap-x-4">
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
                   <div class="flex flex-col items-end mt-4 space-y-2">
                        <x-button
                            label="Apply for Loan"
                            class="bg-red-600 hover:bg-red-400 focus:bg-red-400 focus:ring-red-400"
                            wire:click="openLoanApplicationModal"
                        />

                        @if (! $this->canApply && $this->loanApplicationError)
                            <span class="text-sm text-red-500 italic">
                                @switch($this->loanApplicationError)
                                    @case('not_one_year')
                                    
                                        @break

                                    @case('pending_application')
                                        
                                        @break

                                    @case('ongoing_loan')
                                    
                                        @break

                                    @default
                                        You cannot apply for a loan at this time.
                                @endswitch
                            </span>
                        @endif
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
                                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium flex justify-center items-center">
                                                            <x-dropdown position="bottom">
                                                                <x-dropdown.item label="Full Details" x-on:click="$openModal('laonDetailsModal')" wire:click="loadLoanDetails({{ $loan->id }})" />
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
            @elseif ($activeTab === 'profile')
                <div class="w-full mt-5 flex flex-col items-center">
                    <div class="w-full max-w-[650px] flex flex-col items-center bg-gray-50 rounded-xl shadow-md p-6 relative">

                        <div class="flex flex-row mr-auto gap-4">
                            <div class="w-28 h-28">
                                @if ($userInfo->info->status === 'Approved' && $userInfo->avatar)
                                    <img src="{{ asset('storage/' . $userInfo->avatar) }}"
                                        alt="{{ $userInfo->name }}"
                                        class="w-full h-full object-cover rounded-full border-4 border-white shadow-md">
                                @else
                                    <img src="{{ asset('storage/' . $userInfo->avatar) }}"
                                        alt="{{ $userInfo->name }}"
                                        class="w-full h-full object-cover rounded-full border-4 border-white shadow-md">
                                @endif
                            </div>

                            <div class="mt-4 text-start">
                                <h1 class="text-xl font-semibold text-gray-800">{{ $userInfo->name }}</h1>
                                <p class="text-sm text-blue-500">
                                    <span class="text-xs italic text-gray-500">Member since:</span>
                                    {{ \Carbon\Carbon::parse($userInfo->info->approved_at)->format('F j, Y') }}
                                </p>
                                <x-button 
                                    icon="pencil-square" 
                                    label="Edit" 
                                    primary 
                                    class="w-16 h-6 mt-2 bg-red-600 hover:bg-red-400 focus:bg-red-400 focus:ring-red-400"
                                    wire:click="openProfileEditModal"
                                />
                            </div>
                        </div>

                        <x-modal-card title="Edit Profile" wire:model="showProfileEditModal">
                            <form id="profileEditForm" wire:submit.prevent="updateProfile" class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Change Profile Picture
                                    </label>
                                    <x-filepond::upload wire:model="avatar" />
                                </div>

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

                                <x-select label="Marital Status" wire:model.live="marital_status" placeholder="Enter your marital status">
                                        <x-select.option label="Single" value="Single" />
                                        <x-select.option label="Married" value="Married" />
                                        <x-select.option label="Divorced" value="Divorced" />
                                        <x-select.option label="Widowed" value="Widowed" />
                                </x-select>

                                {{-- <x-input 
                                    label="Address" 
                                    placeholder="Address" 
                                    wire:model.defer="address" 
                                /> --}}
                                
                                <div class="w-full flex flex-col md:flex-row gap-2">
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
                                <div class="w-full flex flex-col md:flex-row gap-2">
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
                            </form>

                            <x-slot name="footer" class="flex justify-between gap-x-4">
                                <div class="flex gap-x-4 ml-auto">
                                    <x-button flat label="Cancel" x-on:click="close" />
                                    <x-button
                                        primary
                                        label="Save"
                                        type="submit"
                                        form="profileEditForm"
                                        wire:target="updateProfile"
                                        wire:loading.attr="disabled"
                                    >
                                        <span wire:loading.remove wire:target="updateProfile">Save</span>
                                        <span wire:loading wire:target="updateProfile">Saving...</span>
                                    </x-button>
                                </div>
                            </x-slot>
                        </x-modal-card>

                        <div class="mt-6 w-full">
                            <hr class="border-blue-500">
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2">
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-500">Member ID: 
                                        <span class="font-semibold text-lg text-gray-800">{{ $userInfo->info->member_id }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        TIN No.: 
                                        <span class="font-semibold text-lg text-gray-800">
                                            {{ preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{5})$/', '$1-$2-$3-$4', $userInfo->info->tin_number) }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-500">Email: 
                                        <span class="font-semibold text-lg text-gray-800 break-words">{{ $userInfo->email }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">Marital Status: 
                                        <span class="font-semibold text-lg text-gray-800 break-words">{{ $userInfo->info->marital_status }}</span>
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-500">Phone: 
                                        <span class="font-semibold text-lg text-gray-800"> {{ ltrim($userInfo->info->phone, '0') }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500">Birthday: 
                                        <span class="font-semibold text-lg text-gray-800">
                                            {{ \Carbon\Carbon::parse($userInfo->info->birthdate)->format('F j, Y') }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-500">Address: 
                                        <span class="font-semibold text-lg text-gray-800">{{ $userInfo->info->region . ", " . $userInfo->info->province . ", " . $userInfo->info->municipality . ", " . $userInfo->info->barangay}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif ($activeTab === 'chats')
                    <div class="h-[calc(100vh_-_10.0rem)]">
                        @livewire('wirechat')
                    </div>
            @endif
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
                         <h3 class="mt-4 font-semibold mb-2 flex justify-between items-center">
                            <span>Ledgers</span>
                            <div class="flex flex-row space-x-2">
                                <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800">
                                    Total Payments: 
                                    <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs font-medium bg-red-500 text-white">{{$remainingLedgers}}</span>
                                </button>
                                 @if ($selectedLoan->ledgers->first()?->ledger_path)
                                    <a href="{{ asset('storage/' . $selectedLoan->ledgers->first()->ledger_path) }}"
                                        download
                                        class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent p-1 bg-red-500 hover:bg-red-600 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        Export as PDF
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">No ledger PDF available</span>
                                @endif
                                
                            </div>
                           
                        </h3>
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
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Payment Status</th>
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Downloadables</th>
                                                    <th scope="col" class="pl-6 py-3 text-start text-[10px] font-bold text-gray-500 uppercase dark:text-neutral-500">Actions</th>
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
                                                        <td class="px-2 lg:px-6 py-2 whitespace-nowrap text-center text-sm font-medium mx-auto">
                                                            @if ($ledger->status === 'Pending' && optional($ledger->payment)->status === 'Pending') 
                                                                <x-badge sm icon="clock" warning label="Pending" />
                                                            @elseif ($ledger->status === 'Paid' && optional($ledger->payment)->status === 'Approved') 
                                                                <x-badge sm icon="check-circle" positive label="Paid" />
                                                            @else 
                                                                <x-button label="Pay" wire:click="openPaymentModal({{ $ledger->id }})" positive />

                                                                <x-modal-card title="Payment" wire:model="showPaymentModal">
                                                                    <form 
                                                                        id="paymentForm"
                                                                        wire:submit.prevent="save({{ $ledger->id }})"
                                                                    >
                                                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" x-data="{ payment_method: @entangle('payment_method'), selected_gcash_qr: @entangle('selected_gcash_qr') }">
                                                                            <x-select 
                                                                                label="Payment Method" 
                                                                                wire:model="payment_method" 
                                                                                placeholder="Select Payment Method"
                                                                                :error="$errors->first('payment_method')"
                                                                            >
                                                                                <x-select.option label="GCash" value="GCash" />
                                                                                <x-select.option label="Bank Transfer" value="Bank Transfer" />
                                                                            </x-select>

                                                                            <x-input 
                                                                                label="Amount" 
                                                                                wire:model="amount" 
                                                                                readonly 
                                                                                :error="$errors->first('amount')"
                                                                            />

                                                                            <div x-show="payment_method === 'GCash'" class="sm:col-span-2">
                                                                                <x-select 
                                                                                    label="Select GCash QR"
                                                                                    wire:model="selected_gcash_qr"
                                                                                    placeholder="Choose QR Code"
                                                                                >
                                                                                    @foreach ($gcashQrs as $qr)
                                                                                        <x-select.option 
                                                                                            value="{{ $qr->id }}" 
                                                                                            label="QR #{{ $qr->id }}" 
                                                                                        />
                                                                                    @endforeach
                                                                                </x-select>
                                                                            </div>

                                                                            <template x-if="payment_method === 'GCash' && selected_gcash_qr">
                                                                                <div class="sm:col-span-2 flex justify-center">
                                                                                    <img 
                                                                                        :src="'{{ asset('storage') }}/' + 
                                                                                            ({{ Js::from($gcashQrs->pluck('qr_path','id')) }}[selected_gcash_qr] ?? '')" 
                                                                                        class="max-w-[200px] h-auto rounded-lg shadow-md" 
                                                                                    />
                                                                                </div>
                                                                            </template>

                                                                            <div class="sm:col-span-2">
                                                                                <p class="text-start font-semibold">Upload Proof of Billing</p>
                                                                                <x-filepond::upload wire:model="proof_of_billing" />
                                                                                @error('proof_of_billing') 
                                                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <x-slot name="footer">
                                                                            <div class="flex justify-between gap-x-4 w-full">
                                                                                <div class="flex gap-x-4 ml-auto">
                                                                                    <x-button flat label="Cancel" x-on:click="close" />
                                                                                    <x-button
                                                                                        primary
                                                                                        label="Submit"
                                                                                        type="submit" 
                                                                                        form="paymentForm" 
                                                                                        wire:target="save{{ $ledger->id }}"
                                                                                        wire:loading.attr="disabled"
                                                                                    >
                                                                                        <span wire:loading.remove wire:target="save{{ $ledger->id }}">Save</span>
                                                                                        <span wire:loading wire:target="save{{ $ledger->id }}">Saving...</span>
                                                                                    </x-button>
                                                                                </div>
                                                                            </div>
                                                                        </x-slot>
                                                                    </form>
                                                                </x-modal-card>
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