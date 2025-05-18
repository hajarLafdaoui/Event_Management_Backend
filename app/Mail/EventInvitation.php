<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\GuestList;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $guest;
    public $invitation;
    public $template;

    public function __construct(Event $event, GuestList $guest, Invitation $invitation, $template = null)
    {
        $this->event = $event;
        $this->guest = $guest;
        $this->invitation = $invitation;
        $this->template = $template;
    }

    public function build()
    {
        $subject = $this->template ? $this->template->template_subject : "You're invited to {$this->event->event_name}";
        
        return $this->subject($subject)
                    ->markdown('emails.event-invitation')
                    ->with([
                        'event' => $this->event,
                        'guest' => $this->guest,
                        'invitation' => $this->invitation,
                        'template' => $this->template,
                        'rsvpUrl' => url('/rsvp/' . $this->invitation->token)
                    ]);
    }
}