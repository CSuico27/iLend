@component('mail::message')
# Hello {{ $user->name }},

This is a friendly reminder regarding your loan payment:

- **Loan ID:** {{ $ledger->loan_id }}  
- **Ledger ID:** {{ $ledger->id }}  
- **Due Date:** {{ \Carbon\Carbon::parse($ledger->due_date)->format('M j, Y') }}  

{{-- @php
    $dueDate = \Carbon\Carbon::parse($ledger->due_date)->startOfDay();
    $today   = \Carbon\Carbon::today();
@endphp

@if($dueDate->lt($today))
Your payment is **overdue**. Please settle this immediately to avoid additional penalties.
@elseif($dueDate->eq($today))
Your payment is **due today**. Please ensure timely payment to avoid penalties.
@endif --}}

Please ensure timely payment to avoid any penalties or issues with your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
