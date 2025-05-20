@component('mail::message')
{!! $body !!}

@component('mail::button', ['url' => $rsvpUrl])
RSVP Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent