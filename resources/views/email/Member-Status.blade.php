@component('mail::message')
# Hello, {{ $user->name }}

@if ($status === 'Approved')
Congratulations! Your membership request has been **approved**.

You can now access all available services using your account. We are excited to have you onboard!
@elseif ($status === 'Rejected')
We're sorry, your membership request has been **rejected**.

@if ($reason)
**Reason:** {{ $reason }}
@endif
@endif

Thanks,  
{{ config('app.name') }}
@endcomponent
