<?php

namespace App\Notifications;

use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CandidatureStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Candidature $candidature,
        public StatutCandidature $ancienStatut,
        public StatutCandidature $nouveauStatut
    ) {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Mise à jour de votre candidature de stage BRACONGO')
            ->greeting('Bonjour ' . $this->candidature->prenom . ' ' . $this->candidature->nom . ',')
            ->line('Nous vous informons que le statut de votre candidature de stage a été mis à jour.')
            ->line('**Code de suivi :** ' . $this->candidature->code_suivi)
            ->line('**Nouveau statut :** ' . $this->nouveauStatut->getLabel());

        switch ($this->nouveauStatut) {
            case StatutCandidature::ANALYSE_DOSSIER:
                $message->line('Votre dossier est actuellement en cours d\'analyse par nos équipes. Nous vous tiendrons informé de la suite du processus.');
                break;

            case StatutCandidature::ATTENTE_TEST:
                $message->line('Félicitations ! Votre dossier a été retenu pour la suite du processus. Vous serez prochainement convoqué(e) pour un test technique.');
                break;

            case StatutCandidature::ATTENTE_RESULTATS:
                $message->line('Vous avez passé le test technique. Nous analysons actuellement vos résultats et vous informerons très prochainement de la suite.');
                break;

            case StatutCandidature::ATTENTE_AFFECTATION:
                $message->line('Excellent ! Vos résultats sont satisfaisants. Nous procédons actuellement à votre affectation dans la direction la plus appropriée.');
                break;

            case StatutCandidature::VALIDE:
                $message->line('🎉 **Félicitations !** Votre candidature a été validée.')
                    ->line('Votre stage se déroulera du **' . $this->candidature->date_debut_stage?->format('d/m/Y') . '** au **' . $this->candidature->date_fin_stage?->format('d/m/Y') . '**.')
                    ->line('Vous recevrez prochainement toutes les informations pratiques concernant votre intégration.')
                    ->action('Voir ma candidature', route('candidature.suivi', ['code' => $this->candidature->code_suivi]));
                break;

            case StatutCandidature::REJETE:
                $message->line('Malheureusement, nous ne pouvons pas donner suite favorable à votre candidature.')
                    ->line('**Motif :** ' . ($this->candidature->motif_rejet ?? 'Non spécifié'))
                    ->line('Nous vous encourageons à postuler à nouveau pour de futures opportunités de stage.');
                break;
        }

        if ($this->nouveauStatut !== StatutCandidature::VALIDE) {
            $message->action('Suivre ma candidature', route('candidature.suivi', ['code' => $this->candidature->code_suivi]));
        }

        $message->line('Merci de votre intérêt pour BRACONGO.')
            ->salutation('L\'équipe Ressources Humaines BRACONGO');

        return $message;
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'code_suivi' => $this->candidature->code_suivi,
            'ancien_statut' => $this->ancienStatut->value,
            'nouveau_statut' => $this->nouveauStatut->value,
            'candidat_nom' => $this->candidature->nom_complet,
        ];
    }
} 