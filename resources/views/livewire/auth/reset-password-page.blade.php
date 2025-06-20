<div class="max-w-lg mx-auto mt-10 p-6 bg-white shadow rounded">
    <h1 class="text-2xl md:text-4xl font-medium text-center">Choose new password</h1>
    <p class="text-center">Almost done. Enter your new password and you're all set.</p>
    <form wire:submit.prevent="resetPass" class="w-full flex flex-col justify-center items-center mt-4">
        <div class="w-full flex flex-col justify-center items-center mb-2">
            <div class="space-y-2 w-[90%]">
                <div class="relative">
                    <x-input type="password" icon="key" label="New password" placeholder="Enter your new password" class="py-3" wire:model="password"/>
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-2 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                    </div>
                </div>
                <div class="relative">
                    <x-input type="password" icon="key" label="Confirm new password" placeholder="Confirm your new password" class="py-3" wire:model="password_confirmation"/>
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-2 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                    </div>
                </div>
            </div>
        </div>
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            wire:target="resetPass"
            class="w-[60%] md:w-[70%] mb-2 py-3 px-4 mt-5 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-full border border-transparent bg-[#fe0002] text-white hover:bg-[#ffc71c] hover:cursor-pointer disabled:opacity-50 disabled:pointer-events-none"
        >
            <span wire:loading.remove wire:target="forgot">Reset Password</span>
            <span wire:loading wire:target="forgot">Resetting...</span>
        </button>
        <a href="{{ route('login') }}" class="w-[60%] md:w-[70%] py-3 px-4 mt-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-full border border-gray-200 bg-white text-gray-800 shadow-sm hover:cursor-pointer hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">
            Back
        </a>
    </form>
</div>
