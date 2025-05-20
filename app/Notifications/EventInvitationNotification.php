<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $invitation;

    public function __construct($event, $invitation)
    {
        $this->event = $event;
        $this->invitation = $invitation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $rsvpUrl = $this->rsvpUrl($this->invitation);
        $body = $this->replaceTemplatePlaceholders(
            $this->invitation->template->content,
            $this->event,
            $rsvpUrl
        );

        return (new MailMessage)
            ->markdown('emails.custom_invitation', [
                'body' => $body,
                'rsvpUrl' => $rsvpUrl,
            ]);
    }

    protected function rsvpUrl($invitation)
    {
        return route('rsvp.show', $invitation->token);
    }

    protected function replaceTemplatePlaceholders($content, $event, $rsvpUrl)
    {
        $replacements = [
            '{{event_name}}' => $event->event_name,
            '{{event_date}}' => $event->start_datetime->format('F j, Y'),
            '{{event_time}}' => $event->start_datetime->format('g:i a'),
            '{{location}}' => $event->location,
            '{{rsvp_link}}' => $rsvpUrl,
            '{{guest_name}}' => $this->invitation->guest->first_name,
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $content
        );
    }
}