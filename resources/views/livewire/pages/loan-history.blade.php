<div class="space-y-6 relative">
    @if ($loans->where('status', 'Approved')->isNotEmpty())
        @foreach ($loans->where('status', 'Approved') as $index => $loan)
            <div class="relative flex items-start space-x-4 pb-8"> 
                
                <div class="relative z-10 w-7 h-7 flex justify-center items-center">
                    <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                    <div class="absolute left-5 top-6 w-px bg-gray-200 dark:bg-neutral-700" 
                        style="height: calc(100% + 32px + 24px);"> 
                    </div> 
                </div>

                <div class="flex-1">
                    <div class="text-xs uppercase text-gray-500 mb-1">
                        {{ $loan->created_at->format('F j, Y') }}
                    </div>

                    <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                        {{ ucfirst($loan->loan_type) }} Loan
                        {{-- <span class="ml-2 text-xs px-2 py-0.5 rounded text-gray-700 bg-gray-100 dark:text-gray-200 dark:bg-gray-700">
                            {{ $loan->status }}
                        </span> --}}
                    </h3>

                    <p class="text-sm text-gray-60:text-gray-300 mt-1">
                        <strong>Amount:</strong> ₱{{ number_format($loan->loan_amount, 2) }}<br>
                        <strong>Terms:</strong> {{ $loan->loan_term }} months<br>
                        <strong>Total:</strong> ₱{{ number_format($loan->total_payment ?? 0, 2) }}
                    </p>
                </div>
            </div>
        @endforeach
    @else
        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
            <p>No loan history found.</p>
        </div>
    @endif
</div>