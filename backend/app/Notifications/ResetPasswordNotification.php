<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(
        public string $token,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontend = rtrim((string) config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
        $locale = in_array($notifiable->locale ?? null, ['th', 'en'], true) ? $notifiable->locale : 'th';
        $email = urlencode((string) $notifiable->email);
        $url = "{$frontend}/{$locale}/reset-password?token={$this->token}&email={$email}";

        return (new MailMessage)
            ->subject('Reset your Education Interntions password')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('This link expires in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
