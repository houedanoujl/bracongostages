<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailGeneriqueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $attachments = [];

    public function __construct(
        public string $sujet,
        public string $contenuHtml
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Ajouter un fichier en pièce jointe
     */
    public function attachFile(string $filePath, ?string $name = null): self
    {
        $this->attachments[] = [
            'path' => $filePath,
            'name' => $name,
        ];
        return $this;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->sujet)
            ->view('emails.layout', [
                'contenu' => $this->contenuHtml,
            ]);

        foreach ($this->attachments as $attachment) {
            if (file_exists($attachment['path'])) {
                $mail->attach($attachment['path'], array_filter([
                    'as' => $attachment['name'],
                ]));
            }
        }

        return $mail;
    }
}
