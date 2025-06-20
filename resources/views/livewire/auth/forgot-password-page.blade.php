<div class="max-w-md mx-auto mt-10 p-6 bg-white shadow rounded">
    <h1 class="text-2xl md:text-4xl font-medium text-center">Forgot Password</h1>
    <p class="text-center mt-2">No worries, We'll send you instruction for reset.</p>
    <form wire:submit.prevent="forgot" class="w-full flex flex-col justify-center items-center mt-2">
        <div class="w-full flex flex-col justify-center items-center mb-10 mt-5">
            <div class="space-y-6 w-full">
                <div class="relative">
                    <x-input
                        wire:model="email"
                        label="Email"
                        placeholder="Enter your email"
                        suffix="@mail.com"
                    />
                </div>
            </div>
        </div>
        <button 
            type="submit" 
            wire:loading.attr="disabled"
            wire:target="forgot"
            class="w-full py-3 px-4 inline-flex rounded-full justify-center gap-x-2 text-sm font-semibold border border-transparent bg-[#fe0002] text-white hover:bg-[#ffc71c] hover:cursor-pointer disabled:opacity-50 disabled:pointer-events-none"
        >
            <span wire:loading.remove wire:target="forgot">Reset Password</span>
            <span wire:loading wire:target="forgot">Sending Reset Password Link...</span>
        </button>
        <a href="{{ route('login') }}" class="w-full rounded-full py-3 px-4 mt-5 inline-flex justify-center items-center gap-x-2 text-sm font-medium border border-gray-200 bg-white text-gray-800 shadow-sm hover:cursor-pointer hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">
            Back
        </a>
    </form>
</div>
