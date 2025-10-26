@component('mail::message')
# Hello, {{ $user->name }}

@if ($status === 'Approved')
Congratulations! Your membership request has been **approved**.

You can now access all available services using your account. We are excited to have you onboard!

{{-- @component('mail::button', ['url' => 'https://ilend.online/login']) 
Login Now
@endcomponent --}}
Click the link to proceed:
[https://ilend.online/]({{ url('https://ilend.online/') }})
@elseif ($status === 'Rejected')
We're sorry, your membership request has been **rejected**.
After careful review, we are unable to approve your application at this time.
You are welcome to reapply again with any updated information.

@if ($reason)
**Reason:** {{ $reason }}
@endif
@endif

Thanks,  
{{ config('app.name') }}
@endcomponent
