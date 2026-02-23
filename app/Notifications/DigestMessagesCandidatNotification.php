<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DigestMessagesCandidatNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Collection $messagesNonLus,
        public string $prenomCandidat
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $count = $this->messagesNonLus->count();
        $grouped = $this->messagesNonLus->groupBy('candidature_id');

        $lignes = '';
        foreach ($grouped as $candidatureId => $messages) {
            $candidature = $messages->first()->candidature;
            if (!$candidature) continue;

            $nb = $messages->count();
            $dernier = \Illuminate\Support\Str::limit(strip_tags($messages->last()->contenu), 80);

            $lignes .= "
                <tr>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #e5e7eb;\">
                        <strong>{$candidature->code_suivi}</strong>
                    </td>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: center;\">
                        <span style=\"background: #fee2e2; color: #dc2626; padding: 2px 10px; border-radius: 10px; font-weight: 600; font-size: 13px;\">{$nb}</span>
                    </td>
                    <td style=\"padding: 10px 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 13px; font-style: italic;\">
                        \"{$dernier}\"
                    </td>
                </tr>
            ";
        }

        $contenu = "
            <p>Bonjour <strong>{$this->prenomCandidat}</strong>,</p>
            <p>Vous avez des réponses non consultées de l'administration BRACONGO :</p>
            <div style=\"background: #fff7ed; border-radius: 8px; padding: 16px; margin: 20px 0;\">
                <p style=\"margin: 0; font-size: 18px; font-weight: 700; color: #dc2626;\">{$count} réponse" . ($count > 1 ? 's' : '') . " non lue" . ($count > 1 ? 's' : '') . "</p>
            </div>
            <table style=\"width: 100%; border-collapse: collapse; margin: 20px 0;\">
                <thead>
                    <tr style=\"background: #f9fafb;\">
                        <th style=\"padding: 10px 12px; text-align: left; font-size: 13px; color: #6b7280; border-bottom: 2px solid #e5e7eb;\">Candidature</th>
                        <th style=\"padding: 10px 12px; text-align: center; font-size: 13px; color: #6b7280; border-bottom: 2px solid #e5e7eb;\">Messages</th>
                        <th style=\"padding: 10px 12px; text-align: left; font-size: 13px; color: #6b7280; border-bottom: 2px solid #e5e7eb;\">Dernier message</th>
                    </tr>
                </thead>
                <tbody>
                    {$lignes}
                </tbody>
            </table>
            <p>
                <a href=\"" . config('app.url') . "/espace-candidat/messagerie\" style=\"display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #f97316 100%); color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;\">
                    Consulter mes messages
                </a>
            </p>
            <p style=\"color: #6b7280; font-size: 14px;\">Si vous ne pouvez pas cliquer sur le bouton, copiez ce lien :<br>" . config('app.url') . "/espace-candidat/messagerie</p>
        ";

        return (new MailMessage)
            ->subject("Vous avez {$count} réponse" . ($count > 1 ? 's' : '') . " non lue" . ($count > 1 ? 's' : '') . " - BRACONGO Stages")
            ->view('emails.layout', [
                'contenu' => $contenu,
            ]);
    }
}
