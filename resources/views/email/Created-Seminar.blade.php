@component('mail::message')

<p>Hello {{ $userName }},</p>

<p>We're pleased to inform you that a new seminar has been scheduled and you are assigned to attend. Here are the details:</p>

<ul>
    <li><strong>Title:</strong> {{ $seminar->title }}</li>
    <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($seminar->seminar_date)->format('F j, Y') }}</li>
    <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($seminar->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($seminar->end_time)->format('h:i A') }}</li>
</ul>

@if (!empty($emailBody))
<p>{!! $emailBody !!}</p>
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent