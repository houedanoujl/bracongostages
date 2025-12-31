<?php

namespace App\Enums;

enum StatutCandidature: string
{
    // Étape 1: Réception
    case DOSSIER_RECU = 'dossier_recu';
    
    // Étape 2: Analyse DRH
    case ANALYSE_DOSSIER = 'analyse_dossier';
    case DOSSIER_INCOMPLET = 'dossier_incomplet';
    
    // Étape 3: Tests
    case ATTENTE_TEST = 'attente_test';
    case TEST_PLANIFIE = 'test_planifie';
    case TEST_PASSE = 'test_passe';
    
    // Étape 4: Décision
    case ATTENTE_DECISION = 'attente_decision';
    case ACCEPTE = 'accepte';
    case REJETE = 'rejete';
    
    // Étape 5: Planification & Affectation
    case PLANIFICATION = 'planification';
    case AFFECTE = 'affecte';
    
    // Étape 6: Réponse lettre de recommandation
    case REPONSE_LETTRE_ENVOYEE = 'reponse_lettre_envoyee';
    
    // Étape 7: Induction RH
    case INDUCTION_PLANIFIEE = 'induction_planifiee';
    case INDUCTION_TERMINEE = 'induction_terminee';
    
    // Étape 8: Accueil service
    case ACCUEIL_SERVICE = 'accueil_service';
    
    // Étape 9: Stage en cours
    case STAGE_EN_COURS = 'stage_en_cours';
    
    // Étape 10: Évaluation
    case EN_EVALUATION = 'en_evaluation';
    case EVALUATION_TERMINEE = 'evaluation_terminee';
    
    // Étape 11: Attestation
    case ATTESTATION_GENEREE = 'attestation_generee';
    
    // Étape 12: Remboursement
    case REMBOURSEMENT_EN_COURS = 'remboursement_en_cours';
    case TERMINE = 'termine';

    // Anciens statuts pour compatibilité
    case NON_TRAITE = 'non_traite';
    case ATTENTE_RESULTATS = 'attente_resultats';
    case ATTENTE_AFFECTATION = 'attente_affectation';
    case VALIDE = 'valide';

    public function getLabel(): string
    {
        return match ($this) {
            // Workflow principal
            self::DOSSIER_RECU => 'Dossier reçu',
            self::ANALYSE_DOSSIER => 'Analyse du dossier',
            self::DOSSIER_INCOMPLET => 'Dossier incomplet',
            self::ATTENTE_TEST => 'En attente de test',
            self::TEST_PLANIFIE => 'Test planifié',
            self::TEST_PASSE => 'Test passé',
            self::ATTENTE_DECISION => 'En attente de décision',
            self::ACCEPTE => 'Accepté',
            self::REJETE => 'Rejeté',
            self::PLANIFICATION => 'Planification en cours',
            self::AFFECTE => 'Affecté à un service',
            self::REPONSE_LETTRE_ENVOYEE => 'Réponse lettre envoyée',
            self::INDUCTION_PLANIFIEE => 'Induction RH planifiée',
            self::INDUCTION_TERMINEE => 'Induction RH terminée',
            self::ACCUEIL_SERVICE => 'Accueil dans le service',
            self::STAGE_EN_COURS => 'Stage en cours',
            self::EN_EVALUATION => 'En évaluation',
            self::EVALUATION_TERMINEE => 'Évaluation terminée',
            self::ATTESTATION_GENEREE => 'Attestation générée',
            self::REMBOURSEMENT_EN_COURS => 'Remboursement en cours',
            self::TERMINE => 'Stage terminé',
            
            // Compatibilité anciens statuts
            self::NON_TRAITE => 'Non traité',
            self::ATTENTE_RESULTATS => 'En attente des résultats',
            self::ATTENTE_AFFECTATION => 'En attente d\'affectation',
            self::VALIDE => 'Validé',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DOSSIER_RECU, self::NON_TRAITE => 'gray',
            self::ANALYSE_DOSSIER, self::ATTENTE_DECISION => 'info',
            self::DOSSIER_INCOMPLET => 'warning',
            self::ATTENTE_TEST, self::TEST_PLANIFIE, self::ATTENTE_RESULTATS => 'warning',
            self::TEST_PASSE => 'info',
            self::ACCEPTE, self::VALIDE => 'success',
            self::REJETE => 'danger',
            self::PLANIFICATION, self::ATTENTE_AFFECTATION => 'primary',
            self::AFFECTE => 'primary',
            self::REPONSE_LETTRE_ENVOYEE => 'info',
            self::INDUCTION_PLANIFIEE, self::INDUCTION_TERMINEE => 'info',
            self::ACCUEIL_SERVICE => 'success',
            self::STAGE_EN_COURS => 'success',
            self::EN_EVALUATION => 'warning',
            self::EVALUATION_TERMINEE => 'success',
            self::ATTESTATION_GENEREE => 'success',
            self::REMBOURSEMENT_EN_COURS => 'warning',
            self::TERMINE => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DOSSIER_RECU, self::NON_TRAITE => 'heroicon-o-inbox',
            self::ANALYSE_DOSSIER => 'heroicon-o-document-magnifying-glass',
            self::DOSSIER_INCOMPLET => 'heroicon-o-exclamation-triangle',
            self::ATTENTE_TEST, self::TEST_PLANIFIE => 'heroicon-o-clipboard-document-list',
            self::TEST_PASSE, self::ATTENTE_RESULTATS => 'heroicon-o-clipboard-document-check',
            self::ATTENTE_DECISION => 'heroicon-o-clock',
            self::ACCEPTE, self::VALIDE => 'heroicon-o-check-circle',
            self::REJETE => 'heroicon-o-x-circle',
            self::PLANIFICATION, self::ATTENTE_AFFECTATION => 'heroicon-o-calendar',
            self::AFFECTE => 'heroicon-o-building-office',
            self::REPONSE_LETTRE_ENVOYEE => 'heroicon-o-envelope',
            self::INDUCTION_PLANIFIEE, self::INDUCTION_TERMINEE => 'heroicon-o-academic-cap',
            self::ACCUEIL_SERVICE => 'heroicon-o-hand-raised',
            self::STAGE_EN_COURS => 'heroicon-o-briefcase',
            self::EN_EVALUATION => 'heroicon-o-star',
            self::EVALUATION_TERMINEE => 'heroicon-o-check-badge',
            self::ATTESTATION_GENEREE => 'heroicon-o-document-text',
            self::REMBOURSEMENT_EN_COURS => 'heroicon-o-banknotes',
            self::TERMINE => 'heroicon-o-flag',
        };
    }

    public function getEtape(): int
    {
        return match ($this) {
            self::DOSSIER_RECU, self::NON_TRAITE => 1,
            self::ANALYSE_DOSSIER, self::DOSSIER_INCOMPLET => 2,
            self::ATTENTE_TEST, self::TEST_PLANIFIE, self::TEST_PASSE, self::ATTENTE_RESULTATS => 3,
            self::ATTENTE_DECISION => 4,
            self::ACCEPTE, self::REJETE, self::VALIDE => 5,
            self::PLANIFICATION, self::ATTENTE_AFFECTATION, self::AFFECTE => 6,
            self::REPONSE_LETTRE_ENVOYEE => 7,
            self::INDUCTION_PLANIFIEE, self::INDUCTION_TERMINEE => 8,
            self::ACCUEIL_SERVICE => 9,
            self::STAGE_EN_COURS => 10,
            self::EN_EVALUATION, self::EVALUATION_TERMINEE => 11,
            self::ATTESTATION_GENEREE => 12,
            self::REMBOURSEMENT_EN_COURS, self::TERMINE => 13,
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::DOSSIER_RECU, self::NON_TRAITE => 'badge-pending',
            self::ANALYSE_DOSSIER, self::ATTENTE_TEST, self::TEST_PLANIFIE, 
            self::ATTENTE_DECISION, self::ATTENTE_RESULTATS, self::ATTENTE_AFFECTATION,
            self::PLANIFICATION, self::INDUCTION_PLANIFIEE => 'badge-in-progress',
            self::DOSSIER_INCOMPLET => 'badge-warning',
            self::ACCEPTE, self::VALIDE, self::AFFECTE, self::INDUCTION_TERMINEE, 
            self::STAGE_EN_COURS, self::EVALUATION_TERMINEE, self::TERMINE => 'badge-approved',
            self::REJETE => 'badge-rejected',
            default => 'badge-in-progress',
        };
    }

    /**
     * Obtenir les prochains statuts possibles
     */
    public function getNextStatuts(): array
    {
        return match ($this) {
            self::DOSSIER_RECU, self::NON_TRAITE => [self::ANALYSE_DOSSIER],
            self::ANALYSE_DOSSIER => [self::DOSSIER_INCOMPLET, self::ATTENTE_TEST],
            self::DOSSIER_INCOMPLET => [self::ANALYSE_DOSSIER, self::REJETE],
            self::ATTENTE_TEST => [self::TEST_PLANIFIE, self::TEST_PASSE],
            self::TEST_PLANIFIE => [self::TEST_PASSE],
            self::TEST_PASSE, self::ATTENTE_RESULTATS => [self::ATTENTE_DECISION, self::ACCEPTE, self::REJETE],
            self::ATTENTE_DECISION => [self::ACCEPTE, self::REJETE],
            self::ACCEPTE, self::VALIDE, self::ATTENTE_AFFECTATION => [self::PLANIFICATION, self::AFFECTE],
            self::PLANIFICATION => [self::AFFECTE],
            self::AFFECTE => [self::REPONSE_LETTRE_ENVOYEE],
            self::REPONSE_LETTRE_ENVOYEE => [self::INDUCTION_PLANIFIEE, self::INDUCTION_TERMINEE],
            self::INDUCTION_PLANIFIEE => [self::INDUCTION_TERMINEE],
            self::INDUCTION_TERMINEE => [self::ACCUEIL_SERVICE],
            self::ACCUEIL_SERVICE => [self::STAGE_EN_COURS],
            self::STAGE_EN_COURS => [self::EN_EVALUATION, self::EVALUATION_TERMINEE],
            self::EN_EVALUATION => [self::EVALUATION_TERMINEE],
            self::EVALUATION_TERMINEE => [self::ATTESTATION_GENEREE],
            self::ATTESTATION_GENEREE => [self::REMBOURSEMENT_EN_COURS, self::TERMINE],
            self::REMBOURSEMENT_EN_COURS => [self::TERMINE],
            self::REJETE, self::TERMINE => [],
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())->mapWithKeys(function ($case) {
            return [$case->value => $case->getLabel()];
        })->toArray();
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::TERMINE, self::REJETE]);
    }

    public function canTransitionTo(StatutCandidature $newStatus): bool
    {
        $nextStatuts = $this->getNextStatuts();
        return in_array($newStatus, $nextStatuts);
    }

    /**
     * Grouper les statuts par phase
     */
    public static function getStatutsParPhase(): array
    {
        return [
            'Réception & Analyse' => [
                self::DOSSIER_RECU,
                self::NON_TRAITE,
                self::ANALYSE_DOSSIER,
                self::DOSSIER_INCOMPLET,
            ],
            'Tests' => [
                self::ATTENTE_TEST,
                self::TEST_PLANIFIE,
                self::TEST_PASSE,
                self::ATTENTE_RESULTATS,
            ],
            'Décision' => [
                self::ATTENTE_DECISION,
                self::ACCEPTE,
                self::VALIDE,
                self::REJETE,
            ],
            'Intégration' => [
                self::PLANIFICATION,
                self::ATTENTE_AFFECTATION,
                self::AFFECTE,
                self::REPONSE_LETTRE_ENVOYEE,
                self::INDUCTION_PLANIFIEE,
                self::INDUCTION_TERMINEE,
            ],
            'Stage' => [
                self::ACCUEIL_SERVICE,
                self::STAGE_EN_COURS,
                self::EN_EVALUATION,
                self::EVALUATION_TERMINEE,
            ],
            'Clôture' => [
                self::ATTESTATION_GENEREE,
                self::REMBOURSEMENT_EN_COURS,
                self::TERMINE,
            ],
        ];
    }
} 