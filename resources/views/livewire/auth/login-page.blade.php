<div class="w-full h-screen overflow-hidden">
    <div class="w-full lg:h-full flex flex-col items-center justify-center px-4 mt-18 lg:mt-0">
        <form wire:submit.prevent="login">
            <div class="w-full max-w-md">
                <img src="{{ asset('images/ilend-logo.png') }}" alt="logo" class="w-auto h-auto mb-6 mx-auto">
            </div>
            <div class="flex flex-col gap-5 w-full max-w-md">
                <x-input
                    label="Email"
                    placeholder="Enter Email"
                    suffix="@mail.com"
                    wire:model="email"
                />
                <x-password label="Password" placeholder="Enter Password" wire:model="password" />
            </div>
            <div class="flex justify-end md:justify-end">
                <a href="{{ route('password.request') }}" class="mt-2">Forgot Password?</a>
            </div>
            <div class="w-full flex flex-col justify-center lg:mt-10 mt-6 md:static fixed bottom-10 left-0 px-4">
                <button 
                    type="submit" 
                    class="w-full max-w-md text-center font-semibold bg-[#fe0002] text-[#ffc71c] px-8 py-3 rounded-full text-xl hover:cursor-pointer"
                    wire:loading.attr="disabled"
                    wire:target="login"
                >
                    <span wire:loading.remove wire:target="login">Log in</span>
                    <span wire:loading wire:target="login" class="flex items-center justify-center gap-2">
                        <div class="animate-spin inline-block size-4 border-3 border-current border-t-transparent text-blue-600 rounded-full dark:text-blue-500" role="status" aria-label="loading">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span>
                            Logging in...
                        </span>
                        
                    </span>
                </button>
                <div class="flex justify-center items-center text-sm mt-5">
                    <div class="flex gap-1">
                        <p>Don't have an account?</p>
                        <a href="{{ route('register') }}" class="font-bold text-[#fe0002]">Sign Up</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
