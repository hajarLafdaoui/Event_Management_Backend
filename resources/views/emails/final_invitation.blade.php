{{-- resources/views/emails/final_invitation.blade.php --}}
@component('mail::message')
# Final Invitation: {{ $event->event_name }}

Dear {{ $invitation->guest->first_name }},

Thank you for accepting the invitation! Here are the event details:

- **Date:** {{ $event->start_datetime->format('F j, Y, g:i a') }}
- **Location:** {{ $event->location }}

We look forward to seeing you!

Thanks,<br>
{{ config('app.name') }}
@endcomponent