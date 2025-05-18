@component('mail::message')
# {{ $template ? $template->template_subject : "You're invited to {$event->event_name}" }}

@if($template)
{!! $template->template_body !!}
@else
<p>Dear {{ $guest->first_name }},</p>

<p>You are cordially invited to attend <strong>{{ $event->event_name }}</strong>.</p>

<p><strong>Event Details:</strong></p>
<ul>
    <li>Date: {{ $event->start_datetime->format('F j, Y') }}</li>
    <li>Time: {{ $event->start_datetime->format('g:i A') }}</li>
    <li>Location: {{ $event->location }}</li>
</ul>
@endif

@component('mail::button', ['url' => $rsvpUrl])
RSVP Now
@endcomponent

<p>Please respond by clicking the button above.</p>

<p>We look forward to seeing you there!</p>

<p>Best regards,<br>
{{ $event->user->name }}</p>
@endcomponent