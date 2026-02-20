<?php

namespace App\Notifications;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResultatAdmisNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
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
        return (new MailMessage)
            ->subject('🎉 Félicitations - Admis au Test - BRACONGO Stages')
            ->view('emails.resultat-admis', [
                'nom' => $this->candidature->nom,
                'prenom' => $this->candidature->prenom,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'code_suivi' => $this->candidature->code_suivi,
            'type' => 'resultat_admis',
        ];
    }
}
