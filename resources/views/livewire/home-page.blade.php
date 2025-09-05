<div class="w-full h-svh overflow-hidden">
    <div class="w-full max-w-5xl h-full -mt-12 md:mt-0 mx-auto py-10 px-5 flex flex-col justify-center items-center">
        <div class="w-100 h-auto">
            <img src="{{ asset('images/ilend-logo.png') }}" alt="logo" class="w-auto h-auto">
        </div>
        <p class="text-center text-red-800 font-semibold">
            Quick and Easy Membership and Loan Services
        </p>
        <p class="text-center text-red-800 mt-10 max-w-4xl">
            Join and enjoy fast, reliable membership and loan processing. Our cooperative offers a hassle-free application process, ensuring members get access to financial support when they need it most.
        </p>
        @if(Auth::check() && Auth::user()->info?->is_applied_for_membership == 1 && Auth::user()->info?->status === 'Pending')
            <button 
                wire:click="applicationUnderReview"
                class="font-semibold bg-[#fe0002] text-[#ffc71c] px-20 py-3 rounded-full text-xl mt-8 md:mt-20 cursor-pointer">
                GET STARTED
            </button>
        @else
            <a href="{{ Auth::check() && Auth::user()->info?->is_applied_for_membership == 1 
                && Auth::user()->info?->status === 'Approved'
                ? route('client.portal') 
                : route('client.membership') }}" 
                class="font-semibold bg-[#fe0002] text-[#ffc71c] px-20 py-3 rounded-full text-xl mt-8 md:mt-20">
                GET STARTED
            </a>
        @endif
    </div>
</div>
