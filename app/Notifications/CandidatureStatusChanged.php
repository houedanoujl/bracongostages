<?php

namespace App\Notifications;

use App\Models\Candidature;
use App\Models\EmailTemplate;
use App\Enums\StatutCandidature;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CandidatureStatusChanged extends Notification
{
    use Queueable;

    public array $extras;

    public function __construct(
        public Candidature $candidature,
        public StatutCandidature $ancienStatut,
        public StatutCandidature $nouveauStatut,
        array $extras = []
    ) {
        $this->extras = $extras;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Mapping des statuts vers les slugs de templates email
     */
    private function getTemplateSlug(): ?string
    {
        return match ($this->nouveauStatut) {
            StatutCandidature::ANALYSE_DOSSIER => 'analyse_dossier',
            StatutCandidature::DOSSIER_INCOMPLET => 'dossier_incomplet',
            StatutCandidature::ATTENTE_TEST, StatutCandidature::TEST_PLANIFIE => 'convocation_test',
            StatutCandidature::TEST_PASSE => 'resultat_test',
            StatutCandidature::ACCEPTE, StatutCandidature::VALIDE => 'resultat_admis',
            StatutCandidature::REJETE => 'resultat_non_admis',
            StatutCandidature::AFFECTE, StatutCandidature::PLANIFICATION => 'confirmation_dates',
            StatutCandidature::REPONSE_LETTRE_ENVOYEE => 'reponse_lettre_recommandation',
            StatutCandidature::INDUCTION_PLANIFIEE, StatutCandidature::INDUCTION_TERMINEE => 'induction_rh',
            StatutCandidature::STAGE_EN_COURS => 'debut_stage',
            StatutCandidature::EVALUATION_TERMINEE => 'envoi_evaluation',
            StatutCandidature::ATTESTATION_GENEREE => 'envoi_attestation',
            StatutCandidature::TERMINE => 'stage_termine',
            default => null,
        };
    }

    public function toMail($notifiable): MailMessage
    {
        $candidature = $this->candidature;
        $nouveauStatut = $this->nouveauStatut;
        
        // Essayer d'utiliser le template email configuré
        $templateSlug = $this->getTemplateSlug();
        if ($templateSlug) {
            try {
                $template = EmailTemplate::getTemplate($templateSlug);
                $rendered = $template->remplacerPlaceholders($candidature, $this->extras);

                Log::info("Email template '{$templateSlug}' utilisé pour candidature {$candidature->code_suivi} (statut: {$nouveauStatut->value})");

                return (new MailMessage)
                    ->subject($rendered['sujet'])
                    ->greeting("Bonjour {$candidature->prenom},")
                    ->line(new \Illuminate\Support\HtmlString($rendered['contenu']))
                    ->line("Code de suivi : {$candidature->code_suivi}")
                    ->action('Suivre ma candidature', url("/suivi/{$candidature->code_suivi}"))
                    ->salutation("Cordialement,\nL'équipe BRACONGO Stages");
            } catch (\Exception $e) {
                Log::warning("Template email '{$templateSlug}' non trouvé pour candidature {$candidature->code_suivi}, utilisation du fallback. Erreur: " . $e->getMessage());
                Log::warning("Vérifiez que le seeder EmailTemplateSeeder a été exécuté (php artisan db:seed --class=EmailTemplateSeeder)");
            }
        } else {
            Log::info("Aucun template configuré pour le statut '{$nouveauStatut->value}' (candidature {$candidature->code_suivi}), utilisation du fallback.");
        }

        // Fallback : contenu par défaut si pas de template trouvé
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

            case StatutCandidature::DOSSIER_INCOMPLET:
                $message->line("Votre dossier est incomplet. Veuillez compléter les informations manquantes.");
                break;

            case StatutCandidature::ATTENTE_TEST:
            case StatutCandidature::TEST_PLANIFIE:
                $message->line("Votre candidature a été retenue pour la phase de test.");
                if ($candidature->date_test) {
                    $message->line("Date du test : " . $candidature->date_test->format('d/m/Y'));
                }
                if ($candidature->lieu_test) {
                    $message->line("Lieu : " . $candidature->lieu_test);
                }
                break;

            case StatutCandidature::TEST_PASSE:
            case StatutCandidature::ATTENTE_RESULTATS:
                $message->line("Vos tests ont été reçus et sont en cours d'évaluation.")
                    ->line("Nous vous communiquerons les résultats dans les plus brefs délais.");
                break;

            case StatutCandidature::ACCEPTE:
            case StatutCandidature::VALIDE:
                $message->line("Félicitations ! Votre candidature a été acceptée !");
                if ($candidature->date_debut_stage) {
                    $message->line("Début : " . $candidature->date_debut_stage->format('d/m/Y'));
                }
                if ($candidature->date_fin_stage) {
                    $message->line("Fin : " . $candidature->date_fin_stage->format('d/m/Y'));
                }
                break;

            case StatutCandidature::REJETE:
                $message->line("Nous regrettons de vous informer que votre candidature n'a pas pu être retenue.")
                    ->line("Motif : " . ($candidature->motif_rejet ?? 'Non spécifié'))
                    ->line("Nous vous encourageons à postuler à d'autres opportunités futures.");
                break;

            case StatutCandidature::AFFECTE:
                $message->line("Vous avez été affecté(e) à un service.");
                if ($candidature->service_affecte) {
                    $directions = Candidature::getDirectionsDisponibles();
                    $message->line("Service : " . ($directions[$candidature->service_affecte] ?? $candidature->service_affecte));
                }
                break;

            case StatutCandidature::INDUCTION_TERMINEE:
                $message->line("Votre session d'induction RH est terminée. Bienvenue chez BRACONGO !");
                break;

            case StatutCandidature::STAGE_EN_COURS:
                $message->line("Votre stage a officiellement commencé. Nous vous souhaitons une excellente expérience !");
                break;

            case StatutCandidature::EVALUATION_TERMINEE:
                $message->line("Votre évaluation de fin de stage a été complétée.");
                if ($candidature->note_evaluation) {
                    $message->line("Note obtenue : " . $candidature->note_evaluation . "/20");
                }
                break;

            case StatutCandidature::ATTESTATION_GENEREE:
                $message->line("Votre attestation de stage a été générée. Vous pouvez la récupérer.");
                break;

            case StatutCandidature::TERMINE:
                $message->line("Votre stage est officiellement terminé. Merci pour votre contribution chez BRACONGO !")
                    ->line("N'hésitez pas à partager votre expérience via notre formulaire d'évaluation.");
                break;

            default:
                $message->line("Nouveau statut : " . $nouveauStatut->getLabel());
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