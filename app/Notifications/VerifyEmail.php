<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
    
        if (!$verificationUrl) {
            \Log::error('Failed to generate verification URL for user: ' . $notifiable->id);
            throw new \Exception('Failed to generate verification URL');
        }
    
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl);
    }
    
    // app/Notifications/VerifyEmail.php
    protected function verificationUrl($notifiable)
    {
        return config('app.frontend_url') . '/email/verify/' . 
               $notifiable->getKey() . '/' . 
               sha1($notifiable->getEmailForVerification());
    }
}