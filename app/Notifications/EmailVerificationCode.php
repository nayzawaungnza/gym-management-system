<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCode extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Email Verification Code - Gym Management System')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your email verification code is:')
            ->line('**' . $this->verificationCode . '**')
            ->line('This code will expire in 10 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Regards, Gym Management System Team');
    }
}