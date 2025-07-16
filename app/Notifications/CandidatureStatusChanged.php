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
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $candidature = $this->candidature;
        $nouveauStatut = $this->nouveauStatut;
        
        $message = (new MailMessage)
            ->subject("Mise à jour de votre candidature - {$nouveauStatut->getLabel()}")
            ->greeting("Bonjour {$candidature->prenom},")
            ->line("Nous vous informons que le statut de votre candidature a été mis à jour.");

        // Contenu spécifique selon le statut
        switch ($nouveauStatut) {
            case StatutCandidature::ANALYSE_DOSSIER:
                $message->line("Votre dossier est actuellement en cours d'analyse par nos équipes.")
                    ->line("Nous vous tiendrons informé des prochaines étapes.");
                break;

            case StatutCandidature::ATTENTE_TEST:
                $message->line("Votre candidature a été retenue pour la phase de test.")
                    ->line("Vous recevrez prochainement les détails concernant les tests à effectuer.");
                break;

            case StatutCandidature::ATTENTE_RESULTATS:
                $message->line("Vos tests ont été reçus et sont en cours d'évaluation.")
                    ->line("Nous vous communiquerons les résultats dans les plus brefs délais.");
                break;

            case StatutCandidature::ATTENTE_AFFECTATION:
                $message->line("Félicitations ! Votre candidature a été validée.")
                    ->line("Nous recherchons actuellement le poste le plus adapté à votre profil.");
                break;

            case StatutCandidature::VALIDE:
                $message->line("🎉 Félicitations ! Votre candidature a été acceptée !")
                    ->line("Détails de votre stage :")
                    ->line("• Début : " . $candidature->date_debut_stage->format('d/m/Y'))
                    ->line("• Fin : " . $candidature->date_fin_stage->format('d/m/Y'))
                    ->line("• Poste : " . $candidature->poste_souhaite)
                    ->action('Voir les détails', url("/suivi/{$candidature->code_suivi}"));
                break;

            case StatutCandidature::REJETE:
                $message->line("Nous regrettons de vous informer que votre candidature n'a pas pu être retenue.")
                    ->line("Motif : " . ($candidature->motif_rejet ?? 'Non spécifié'))
                    ->line("Nous vous encourageons à postuler à d'autres opportunités futures.");
                break;
        }

        return $message
            ->line("Code de suivi : {$candidature->code_suivi}")
            ->action('Suivre ma candidature', url("/suivi/{$candidature->code_suivi}"))
            ->line("Merci de votre intérêt pour BRACONGO Stages !")
            ->salutation("Cordialement,\nL'équipe BRACONGO Stages");
    }

    public function toArray($notifiable): array
    {
        return [
            'candidature_id' => $this->candidature->id,
            'ancien_statut' => $this->ancienStatut->value,
            'nouveau_statut' => $this->nouveauStatut->value,
            'code_suivi' => $this->candidature->code_suivi,
        ];
    }
} 