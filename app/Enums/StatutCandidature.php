<?php

namespace App\Enums;

enum StatutCandidature: string
{
    case NON_TRAITE = 'non_traite';
    case ANALYSE_DOSSIER = 'analyse_dossier';
    case ATTENTE_TEST = 'attente_test';
    case ATTENTE_RESULTATS = 'attente_resultats';
    case ATTENTE_AFFECTATION = 'attente_affectation';
    case VALIDE = 'valide';
    case REJETE = 'rejete';

    public function getLabel(): string
    {
        return match ($this) {
            self::NON_TRAITE => 'Non traité',
            self::ANALYSE_DOSSIER => 'Analyse du dossier',
            self::ATTENTE_TEST => 'En attente de test',
            self::ATTENTE_RESULTATS => 'En attente des résultats',
            self::ATTENTE_AFFECTATION => 'En attente d\'affectation',
            self::VALIDE => 'Validé',
            self::REJETE => 'Rejeté',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NON_TRAITE => 'gray',
            self::ANALYSE_DOSSIER => 'blue',
            self::ATTENTE_TEST => 'yellow',
            self::ATTENTE_RESULTATS => 'orange',
            self::ATTENTE_AFFECTATION => 'purple',
            self::VALIDE => 'green',
            self::REJETE => 'red',
        };
    }

    public function getBadgeClass(): string
    {
        return match ($this) {
            self::NON_TRAITE => 'badge-pending',
            self::ANALYSE_DOSSIER, self::ATTENTE_TEST, self::ATTENTE_RESULTATS, self::ATTENTE_AFFECTATION => 'badge-in-progress',
            self::VALIDE => 'badge-approved',
            self::REJETE => 'badge-rejected',
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
        return in_array($this, [self::VALIDE, self::REJETE]);
    }

    public function canTransitionTo(StatutCandidature $newStatus): bool
    {
        // Logique de transitions valides
        return match ($this) {
            self::NON_TRAITE => in_array($newStatus, [self::ANALYSE_DOSSIER, self::REJETE]),
            self::ANALYSE_DOSSIER => in_array($newStatus, [self::ATTENTE_TEST, self::REJETE]),
            self::ATTENTE_TEST => in_array($newStatus, [self::ATTENTE_RESULTATS, self::REJETE]),
            self::ATTENTE_RESULTATS => in_array($newStatus, [self::ATTENTE_AFFECTATION, self::REJETE]),
            self::ATTENTE_AFFECTATION => in_array($newStatus, [self::VALIDE, self::REJETE]),
            self::VALIDE, self::REJETE => false, // États terminaux
        };
    }
} 