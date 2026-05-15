<?php

namespace App\Notifications;

use App\Classes\Panel;
use App\Classes\PdfDocument;
use Devlab\LaravelMailer\CustomMail\CustomMailChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use NotificationChannels\Webhook\WebhookChannel;
use NotificationChannels\Webhook\WebhookMessage;
use Illuminate\Http\UploadedFile;

class SendDocument extends Notification // implements ShouldQueue
{
    // use Queueable;

    public $subject;
    public $message;
    public $recipients;
    public $elements;

    /**
     * Create a new notification instance.
     */
    public function __construct($subject, $message, $recipients, $elements = null)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->recipients = $recipients;
        $this->elements = $elements;
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
        // $content = PdfDocument::generateDocument($this->elements);
        // $attachments_ids = [];

        // if ($content['content']) {
        //     $tempPath = tempnam(sys_get_temp_dir(), 'attachment');
        //     file_put_contents($tempPath, $content['content']);
        //     $file = new UploadedFile($tempPath, $content['file_name']);
        //     $vcAttachment = Panel::sendAttachment(0, $content['file_name'], $file);
        //     if (!empty($vcAttachment)) {
        //         $attachments_ids[] = $vcAttachment['iResult'];
        //     }
        //     unlink($tempPath);
        // }
        // if (!empty($attachments_ids)) {
        //     $data['attachments'] = implode(',', $attachments_ids);
        // }

        return (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->subject)
            ->view('mails.send-document', [
                'user' => $notifiable ?? null,
                'message' => $this->message,
            ]);
    }
}
