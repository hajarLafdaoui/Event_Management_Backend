<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function build()
    {
        $subject = "Your RSVP for {$this->invitation->event->event_name}";
        
        return $this->subject($subject)
                    ->markdown('emails.invitation-confirmation')
                    ->with([
                        'invitation' => $this->invitation,
                        'event' => $this->invitation->event,
                        'guest' => $this->invitation->guest,
                        'ticketUrl' => url('/ticket/' . $this->invitation->guest->qr_code)
                    ]);
    }
}