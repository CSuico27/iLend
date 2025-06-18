<div class="w-full h-auto overflow-hidden">
    <div class="w-full lg:h-full flex flex-col items-center justify-center px-4 mt-10 lg:mt-5 pb-10">
        <form wire:submit.prevent="register" class="w-full max-w-md">
            <div class="w-full max-w-xs mx-auto">
                <img src="{{ asset('images/ilend-logo.png') }}" alt="logo" class="w-auto h-auto mx-auto">
            </div>
            <h1 class="text-center font-semibold text-xl lg:text-2xl">Register for an Account</h1>
            <p class="text-center text-gray-400 text-xs mb-10">Please complete all information to create your account</p>
            <div class="flex flex-col gap-5 w-full max-w-md">
                <x-input
                    right-icon="user"
                    label="Fullname"
                    placeholder="Enter Full Name"
                    wire:model="full_name"
                />
                <x-input
                    label="Email"
                    placeholder="Enter Email"
                    suffix="@mail.com"
                    wire:model="email"
                />
                <x-password label="Password" placeholder="Enter Password" wire:model="password" />
                <x-password label="Confirm Password" placeholder="Confirm Password" wire:model="confirmPassword" />
            </div>
            <div class="w-full flex flex-col justify-center mt-10 mt-6 px-4">
                <button 
                    type="submit" 
                    class="w-full max-w-md text-center font-semibold bg-[#fe0002] text-[#ffc71c] px-8 py-3 rounded-full text-xl hover:cursor-pointer"
                    wire:loading.attr="disabled"
                    wire:target="register"
                >
                    <span wire:loading.remove wire:target="register">Sign up</span>
                    <span wire:loading wire:target="register" class="flex items-center justify-center gap-2">
                        <div class="animate-spin inline-block size-4 border-3 border-current border-t-transparent text-blue-600 rounded-full dark:text-blue-500" role="status" aria-label="loading">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span>
                            Signing up...
                        </span>
                        
                    </span>
                </button>
                <div class="flex justify-center items-center text-sm mt-5">
                    <div class="flex gap-1">
                        <p>Already have an account?</p>
                        <a href="{{ route('login') }}" class="font-bold text-[#fe0002]">Sign In</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
