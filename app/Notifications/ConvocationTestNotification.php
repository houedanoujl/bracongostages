<?php

namespace App\Notifications;

use App\Models\Candidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConvocationTestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Candidature $candidature,
        public string $heure_test = '09:00'
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
            ->subject('📝 Convocation au Test - BRACONGO Stages')
            ->view('emails.convocation-test', [
                'nom' => $this->candidature->nom,
                'prenom' => $this->candidature->prenom,
                'date_test' => $this->candidature->date_test,
                'heure_test' => $this->heure_test,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'code_suivi' => $this->candidature->code_suivi,
            'date_test' => $this->candidature->date_test,
            'type' => 'convocation_test',
        ];
    }
}
