<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailGeneriqueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $sujet,
        public string $contenuHtml
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->sujet)
            ->view('emails.layout', [
                'contenu' => nl2br(e($this->contenuHtml)),
            ]);
    }
}
