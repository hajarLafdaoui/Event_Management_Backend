<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FinalInvitationNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $invitation;

    /**
     * Create a new notification instance.
     */
    public function __construct($event, $invitation)
    {
        $this->event = $event;
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Final Invitation for ' . $this->event->event_name)
            ->markdown('emails.final_invitation', [
                'event' => $this->event,
                'invitation' => $this->invitation,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
