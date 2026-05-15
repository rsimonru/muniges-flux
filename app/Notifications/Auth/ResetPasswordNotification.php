<?php

namespace App\Notifications\Auth;

use Devlab\LaravelMailer\CustomMail\CustomMailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification // implements ShouldQueue
{
    // use Queueable;

    public $token;
    public $user;
    public $townhall_id;
    public $townhall_data;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $user, $townhall_id, $townhall_data)
    {
        $this->token = $token;
        $this->user = $user;
        $this->townhall_id = $townhall_id;
        $this->townhall_data = $townhall_data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [CustomMailChannel::class];
    }

    public function toCustomMail(object $notifiable)
    {
        $to = get_class($notifiable) == AnonymousNotifiable::class ?  $notifiable->routes['mail'] : $notifiable->email;
        $url = url(route('reset.password', ['token' => $this->token, 'email' => $to], false));

        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(__('passwords.reset_password_subject'))
            ->view('mails.auth.reset-password', [
                'user' => $this->user,
                'url' => $url,
                'townhall_id' => $this->townhall_id,
                'townhall_data' => $this->townhall_data,
            ]);
    }
}
