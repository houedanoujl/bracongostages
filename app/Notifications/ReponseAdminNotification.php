<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReponseAdminNotification extends Notification implements ShouldQueue
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
        $extrait = \Illuminate\Support\Str::limit(strip_tags($this->message->contenu), 100);

        $contenu = "
            <p>Bonjour <strong>{$this->candidature->prenom}</strong>,</p>
            <p>L'administration BRACONGO a répondu à votre message concernant votre candidature <strong>{$this->candidature->code_suivi}</strong>.</p>
            <div style=\"background: #f3f4f6; border-left: 4px solid #f97316; padding: 15px; margin: 20px 0; border-radius: 4px;\">
                <p style=\"margin: 0; font-style: italic; color: #4b5563;\">\"{$extrait}\"</p>
            </div>
            <p>
                <a href=\"" . config('app.url') . "/espace-candidat/messagerie\" style=\"display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #f97316 100%); color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;\">
                    Consulter la réponse
                </a>
            </p>
            <p style=\"color: #6b7280; font-size: 14px;\">Si vous ne pouvez pas cliquer sur le bouton, copiez ce lien dans votre navigateur :<br>" . config('app.url') . "/espace-candidat/messagerie</p>
        ";

        return (new MailMessage)
            ->subject("Réponse à votre message - Candidature {$this->candidature->code_suivi}")
            ->view('emails.layout', [
                'contenu' => $contenu,
            ]);
    }
}
