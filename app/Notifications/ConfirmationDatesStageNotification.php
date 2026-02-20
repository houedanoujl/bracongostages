<?php

namespace App\Notifications;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmationDatesStageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Candidature $candidature,
        public string $heure_presentation = '08:00'
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $directions = \App\Models\Candidature::getDirectionsDisponibles();
        $directionLabel = $directions[$this->candidature->service_affecte] ?? $this->candidature->service_affecte ?? 'Non défini';

        return (new MailMessage)
            ->subject('📅 Confirmation des Dates de Stage - BRACONGO')
            ->view('emails.confirmation-dates-stage', [
                'nom' => $this->candidature->nom,
                'prenom' => $this->candidature->prenom,
                'date_debut' => $this->candidature->date_debut_stage,
                'date_fin' => $this->candidature->date_fin_stage,
                'direction_service' => $directionLabel,
                'heure_presentation' => $this->heure_presentation,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'code_suivi' => $this->candidature->code_suivi,
            'date_debut' => $this->candidature->date_debut_stage,
            'date_fin' => $this->candidature->date_fin_stage,
            'type' => 'confirmation_dates_stage',
        ];
    }
}
