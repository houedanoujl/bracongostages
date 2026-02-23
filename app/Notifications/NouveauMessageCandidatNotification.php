<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouveauMessageCandidatNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
        public Candidature $candidature
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $candidatNom = $this->candidature->prenom . ' ' . $this->candidature->nom;
        $extrait = \Illuminate\Support\Str::limit(strip_tags($this->message->contenu), 100);

        $contenu = "
            <p>Bonjour,</p>
            <p>Vous avez reçu un nouveau message de <strong>{$candidatNom}</strong> concernant la candidature <strong>{$this->candidature->code_suivi}</strong>.</p>
            <div style=\"background: #f3f4f6; border-left: 4px solid #f97316; padding: 15px; margin: 20px 0; border-radius: 4px;\">
                <p style=\"margin: 0; font-style: italic; color: #4b5563;\">\"{$extrait}\"</p>
            </div>
            <p>
                <a href=\"" . config('app.url') . "/admin/messagerie\" style=\"display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #f97316 100%); color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;\">
                    Voir le message
                </a>
            </p>
        ";

        return (new MailMessage)
            ->subject("Nouveau message de {$candidatNom} - {$this->candidature->code_suivi}")
            ->view('emails.layout', [
                'contenu' => $contenu,
            ]);
    }
}
