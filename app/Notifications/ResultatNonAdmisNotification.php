<?php

namespace App\Notifications;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResultatNonAdmisNotification extends Notification implements ShouldQueue
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
            ->subject('📋 Résultat du Test - BRACONGO Stages')
            ->view('emails.resultat-non-admis', [
                'nom' => $this->candidature->nom,
                'prenom' => $this->candidature->prenom,
                'date_test' => $this->candidature->date_test,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'code_suivi' => $this->candidature->code_suivi,
            'type' => 'resultat_non_admis',
        ];
    }
}
