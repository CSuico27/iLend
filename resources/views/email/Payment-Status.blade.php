@component('mail::message')
# Dear {{ $payment->user->name }},

@if ($status === 'Approved')
Your submitted payment has been approved.

**Payment Amount:** ₱{{ number_format($payment->amount, 2) }} <br>
**Payment Method:** {{ ucfirst($payment->payment_method) }} <br>

@elseif ($status === 'Rejected')
We're sorry, your payment has been **rejected**. You may resubmit your proof of payment or contact us for further assistance.
@endif

Thank you,  
{{ config('app.name') }}
@endcomponent
