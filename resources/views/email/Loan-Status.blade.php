@component('mail::message')
# Dear {{ $loan->user->name }},

@if ($status === 'Approved')
We are pleased to inform you that your loan application with LGTSMPC has been approved.
Here are the key details of your approved loan:

**Loan Amount:** ₱{{ number_format($loan->loan_amount, 2) }} <br>
**Loan Type:** {{ ucfirst($loan->loan_type) }} <br>
**Term:** {{ $loan->loan_term }} Months <br>
**Interest Rate:** {{ $loan->interest_rate }} %

To proceed, please visit our office or check your email for the attached documents requiring your signature. Once all documents are signed, your funds will be disbursed accordingly.
Should you have any questions or need further assistance, feel free to contact us at 09092432467 or reply to this email.
@elseif ($status === 'Rejected')
We're sorry, your loan application has been **rejected**.
@endif

Thank you,  
{{ config('app.name') }}
@endcomponent
