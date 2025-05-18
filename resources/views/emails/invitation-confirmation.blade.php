@component('mail::message')
# Your RSVP for {{ $event->event_name }}

<p>Dear {{ $guest->first_name }},</p>

<p>Thank you for responding to our invitation. Your RSVP status is: <strong>{{ ucfirst($invitation->rsvp_status) }}</strong></p>

@if($invitation->rsvp_status == 'accepted')
<p>We're excited that you'll be joining us for {{ $event->event_name }}!</p>

<p>Your ticket is attached below. Please present this QR code at the event entrance:</p>

@component('mail::button', ['url' => $ticketUrl])
View Your Ticket
@endcomponent

<p><strong>Event Details:</strong></p>
<ul>
    <li>Date: {{ $event->start_datetime->format('F j, Y') }}</li>
    <li>Time: {{ $event->start_datetime->format('g:i A') }}</li>
    <li>Location: {{ $event->location }}</li>
</ul>
@endif

<p>If you need to update your RSVP, please contact the event organizer.</p>

<p>Best regards,<br>
{{ $event->user->name }}</p>
@endcomponent