<?php

namespace App\Jobs;

use App\Models\Candidature;
use App\Notifications\CandidatureStatusChanged;
use App\Enums\StatutCandidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCandidatureNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(
        public Candidature $candidature,
        public StatutCandidature $ancienStatut,
        public StatutCandidature $nouveauStatut
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            // Créer un objet notifiable temporaire avec l'email du candidat
            $notifiable = new class($this->candidature->email) {
                public function __construct(public string $email) {}
                public function routeNotificationForMail() {
                    return $this->email;
                }
            };

            // Envoyer la notification
            $notification = new CandidatureStatusChanged(
                $this->candidature,
                $this->ancienStatut,
                $this->nouveauStatut
            );

            $notifiable->notify($notification);

            Log::info('Notification de candidature envoyée', [
                'candidature_id' => $this->candidature->id,
                'code_suivi' => $this->candidature->code_suivi,
                'email' => $this->candidature->email,
                'ancien_statut' => $this->ancienStatut->value,
                'nouveau_statut' => $this->nouveauStatut->value,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification de candidature', [
                'candidature_id' => $this->candidature->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job SendCandidatureNotification échoué définitivement', [
            'candidature_id' => $this->candidature->id,
            'error' => $exception->getMessage(),
        ]);
    }
} 