<div class="flex justify-center items-center w-full h-screen">
    <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
        <h1 class="text-center mb-2 font-semibold">OTP Verification</h1>
        <p class="text-center mb-8 text-xs">Enter the OTP sent to your email.</p>
        <div class="flex gap-x-3 mb-5" data-hs-pin-input="">
            @foreach(range(0, 5) as $index)
                <input 
                    type="text" 
                    maxlength="1"
                    class="block w-9.5 text-center bg-transparent border-t-transparent focus:outline-none border-b-2 border-x-transparent border-b-gray-200 sm:text-sm focus:border-t-transparent focus:border-x-transparent focus:border-b-blue-500 focus:ring-0 disabled:opacity-50 disabled:pointer-events-none dark:border-b-neutral-700 dark:text-neutral-400  dark:placeholder-neutral-500 dark:focus:ring-neutral-600 dark:focus:border-b-neutral-600" 
                    placeholder="⚬" 
                    data-hs-pin-input-item=""
                    wire:model.lazy="otp.{{ $index }}"
                >
            @endforeach
        </div>
        @if (session('error'))
            <div>
                <p class="text-red-500 text-xs text-center mb-5">
                    {{ session('error') }}
                </p>
            </div>
        @endif
        <a href="#" wire:click="verifyOtp" class="py-3 px-4 inline-flex rounded-md justify-center gap-x-2 text-sm font-semibold border border-transparent bg-[#2b2b31] text-white hover:bg-slate-950 disabled:opacity-50 disabled:pointer-events-none">
            Sign up
        </a>
    </div>
</div>
