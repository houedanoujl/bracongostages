<?php

namespace App\Models;

use App\Enums\StatutCandidature;
use App\Models\ConfigurationListe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Candidature extends Model
{
    use HasFactory;

    /**
     * Stockage temporaire pour le changement de statut (hors Eloquent pour ne pas être inclus dans le SQL)
     */
    protected static array $pendingStatusChanges = [];

    /**
     * Stockage temporaire des extras pour les placeholders email (ex: heure_test, heure_presentation)
     */
    protected static array $pendingEmailExtras = [];

    /**
     * Définir des extras pour le prochain email de changement de statut
     */
    public function setEmailExtras(array $extras): self
    {
        static::$pendingEmailExtras[$this->id] = $extras;
        return $this;
    }

    protected $fillable = [
        'nom',
        'prenom', 
        'telephone',
        'email',
        'etablissement',
        'etablissement_autre',
        'niveau_etude',
        'faculte',
        'objectif_stage',
        'poste_souhaite',
        'opportunite_id',
        'directions_souhaitees',
        'projets_souhaites',
        'competences_souhaitees',
        'periode_debut_souhaitee',
        'periode_fin_souhaitee',
        'statut',
        'motif_rejet',
        'date_debut_stage',
        'date_fin_stage',
        'code_suivi',
        // Nouveaux champs workflow
        'service_affecte',
        'tuteur_id',
        'programme_stage',
        'date_test',
        'heure_test',
        'lieu_test',
        'note_test',
        'resultat_test',
        'commentaire_test',
        'date_induction',
        'induction_completee',
        'date_accueil_service',
        'date_debut_stage_reel',
        'date_fin_stage_reel',
        'note_evaluation',
        'commentaire_evaluation',
        'competences_acquises_evaluation',
        'chemin_evaluation',
        'appreciation_tuteur',
        'decision_drh',
        'date_evaluation',
        'attestation_generee',
        'chemin_attestation',
        'date_attestation',
        'montant_transport',
        'remboursement_effectue',
        'date_remboursement',
        'reference_paiement',
        'chemin_justificatif_remboursement',
        'reponse_lettre_envoyee',
        'date_reponse_recommandation',
        'date_reponse_lettre',
        'chemin_reponse_lettre',
        'historique_statuts',
        'notes_internes',
        'emails_envoyes_par_etape',
    ];

    protected $casts = [
        'directions_souhaitees' => 'array',
        'periode_debut_souhaitee' => 'date',
        'periode_fin_souhaitee' => 'date',
        'date_debut_stage' => 'date',
        'date_fin_stage' => 'date',
        'date_test' => 'date',
        'date_induction' => 'date',
        'date_accueil_service' => 'date',
        'date_reponse_recommandation' => 'date',
        'date_debut_stage_reel' => 'date',
        'date_fin_stage_reel' => 'date',
        'date_evaluation' => 'date',
        'date_attestation' => 'date',
        'date_remboursement' => 'date',
        'date_reponse_lettre' => 'date',
        'statut' => StatutCandidature::class,
        'induction_completee' => 'boolean',
        'attestation_generee' => 'boolean',
        'remboursement_effectue' => 'boolean',
        'reponse_lettre_envoyee' => 'boolean',
        'historique_statuts' => 'array',
        'emails_envoyes_par_etape' => 'array',
        'note_test' => 'decimal:2',
        'note_evaluation' => 'decimal:2',
        'montant_transport' => 'decimal:2',
    ];

    /**
     * Note maximale autorisée pour les évaluations et tests
     */
    const NOTE_MAX = 20;

    /**
     * Marquer l'email d'une étape du wizard comme envoyé.
     */
    public function marquerEmailEnvoye(string $stepName): void
    {
        $emails = $this->emails_envoyes_par_etape ?? [];
        $emails[$stepName] = now()->toIso8601String();
        $this->emails_envoyes_par_etape = $emails;
        $this->saveQuietly();
    }

    /**
     * Vérifier si l'email d'une étape a été envoyé.
     */
    public function emailEtapeEnvoye(string $stepName): bool
    {
        $emails = $this->emails_envoyes_par_etape ?? [];
        return !empty($emails[$stepName]);
    }

    /**
     * Obtenir la date d'envoi de l'email d'une étape.
     */
    public function dateEmailEtape(string $stepName): ?string
    {
        $emails = $this->emails_envoyes_par_etape ?? [];
        return $emails[$stepName] ?? null;
    }

    /**
     * Mapping : nom d'étape → slugs d'emails requis.
     * Strings simples = requis (logique ET — tous doivent être envoyés).
     * Sous-tableaux = alternatives (logique OU — un seul suffit).
     */
    public static function getRequiredEmailsForStep(string $stepName): array
    {
        return match ($stepName) {
            'Documents' => [['documents_complet', 'documents_incomplet']],
            'Gestion' => [['gestion_complet', 'gestion_incomplet']],
            'Convocation test' => ['convocation_test'],
            'Résultats test' => [['resultat_admis', 'resultat_non_admis']],
            'Affectation' => ['affectation_confirmation', 'affectation_debut'],
            'Induction & Réponse' => ['induction_rh', 'induction_reponse'],
            'Évaluation' => ['evaluation'],
            'Attestation' => ['attestation'],
            'Remboursement' => ['remboursement'],
            default => [],
        };
    }

    /**
     * Libellés lisibles pour chaque slug d'email.
     */
    public static function getEmailSlugLabels(): array
    {
        return [
            'documents_complet' => 'Dossier complet',
            'documents_incomplet' => 'Dossier incomplet',
            'gestion_complet' => 'Dossier complet',
            'gestion_incomplet' => 'Dossier incomplet',
            'convocation_test' => 'Convocation au test',
            'resultat_admis' => 'Résultat : Admis',
            'resultat_non_admis' => 'Résultat : Non admis',
            'affectation_confirmation' => 'Confirmation des dates',
            'affectation_debut' => 'Début du stage',
            'induction_rh' => 'Induction RH',
            'induction_reponse' => 'Réponse lettre',
            'evaluation' => 'Évaluation',
            'attestation' => 'Attestation',
            'remboursement' => 'Stage terminé',
        ];
    }

    /**
     * Vérifier si TOUS les emails requis d'une étape ont été envoyés.
     * Supporte la rétrocompatibilité avec l'ancien format (clé = nom d'étape).
     */
    public function tousEmailsEtapeEnvoyes(string $stepName): bool
    {
        $required = self::getRequiredEmailsForStep($stepName);
        if (empty($required)) return true;

        $emails = $this->emails_envoyes_par_etape ?? [];

        // Rétrocompatibilité : si l'ancienne clé (nom d'étape) existe, considérer comme fait
        if (!empty($emails[$stepName])) return true;

        foreach ($required as $req) {
            if (is_array($req)) {
                // Logique OU : au moins un doit être envoyé
                $anySent = false;
                foreach ($req as $slug) {
                    if (!empty($emails[$slug])) { $anySent = true; break; }
                }
                if (!$anySent) return false;
            } else {
                // Logique ET : doit être envoyé
                if (empty($emails[$req])) return false;
            }
        }
        return true;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($candidature) {
            if (empty($candidature->code_suivi)) {
                $candidature->code_suivi = 'BRC-' . strtoupper(Str::random(8));
            }
            if (empty($candidature->statut)) {
                $candidature->statut = StatutCandidature::DOSSIER_RECU;
            }
            // Plafonner les notes à la valeur maximale
            if ($candidature->note_test !== null && $candidature->note_test > self::NOTE_MAX) {
                $candidature->note_test = self::NOTE_MAX;
            }
            if ($candidature->note_evaluation !== null && $candidature->note_evaluation > self::NOTE_MAX) {
                $candidature->note_evaluation = self::NOTE_MAX;
            }
        });

        // Envoi automatique d'email lors du changement de statut
        static::updating(function ($candidature) {
            // Plafonner les notes à la valeur maximale
            if ($candidature->note_test !== null && $candidature->note_test > self::NOTE_MAX) {
                $candidature->note_test = self::NOTE_MAX;
            }
            if ($candidature->note_evaluation !== null && $candidature->note_evaluation > self::NOTE_MAX) {
                $candidature->note_evaluation = self::NOTE_MAX;
            }

            if ($candidature->isDirty('statut')) {
                $ancienStatut = $candidature->getOriginal('statut');
                $nouveauStatut = $candidature->statut;

                // Convertir en enum si nécessaire
                if (is_string($ancienStatut)) {
                    $ancienStatut = StatutCandidature::tryFrom($ancienStatut);
                }
                if (is_string($nouveauStatut)) {
                    $nouveauStatut = StatutCandidature::tryFrom($nouveauStatut);
                }

                // Si le formulaire tente de rétrograder le statut (valeur obsolète après auto-avancement),
                // annuler silencieusement le changement de statut et garder la valeur actuelle en DB
                if ($ancienStatut && $nouveauStatut && $nouveauStatut->getEtape() < $ancienStatut->getEtape()) {
                    $candidature->statut = $ancienStatut;
                    \Illuminate\Support\Facades\Log::info("Rétrogradation de statut bloquée : {$nouveauStatut->value} → maintenu à {$ancienStatut->value}");
                    return;
                }

                // Valider que la transition est autorisée
                if ($ancienStatut && $nouveauStatut && !$ancienStatut->canTransitionTo($nouveauStatut)) {
                    throw ValidationException::withMessages([
                        'statut' => "Transition non autorisée : {$ancienStatut->getLabel()} vers {$nouveauStatut->getLabel()}.",
                    ]);
                }

                // Sauvegarder dans une propriété statique (PAS dans les attributs du modèle)
                static::$pendingStatusChanges[$candidature->id] = [
                    'ancien' => $ancienStatut,
                    'nouveau' => $nouveauStatut,
                    'extras' => static::$pendingEmailExtras[$candidature->id] ?? [],
                ];
            }
        });

        static::updated(function ($candidature) {
            if (isset(static::$pendingStatusChanges[$candidature->id])) {
                $change = static::$pendingStatusChanges[$candidature->id];
                unset(static::$pendingStatusChanges[$candidature->id]);

                $ancienStatut = $change['ancien'];
                $nouveauStatut = $change['nouveau'];
                $extras = $change['extras'] ?? [];
                
                // Nettoyer les extras temporaires
                unset(static::$pendingEmailExtras[$candidature->id]);
                
                try {
                    // Envoyer la notification CandidatureStatusChanged avec les extras
                    $notification = new \App\Notifications\CandidatureStatusChanged($candidature, $ancienStatut, $nouveauStatut, $extras);
                    $candidat = Candidat::where('email', $candidature->email)->first();
                    if ($candidat) {
                        $candidat->notify($notification);
                        \Illuminate\Support\Facades\Log::info("Email template envoyé à {$candidature->email}: {$ancienStatut->value} → {$nouveauStatut->value}");
                    } else {
                        // Pas de compte candidat, envoyer directement à l'adresse email
                        \Illuminate\Support\Facades\Notification::route('mail', $candidature->email)
                            ->notify($notification);
                        \Illuminate\Support\Facades\Log::info("Email template envoyé (sans compte) à {$candidature->email}: {$ancienStatut->value} → {$nouveauStatut->value}");
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Erreur envoi email changement statut: " . $e->getMessage());
                }
            }
        });
    }

    /**
     * Relation avec l'opportunité de stage
     */
    public function opportunite(): BelongsTo
    {
        return $this->belongsTo(Opportunite::class);
    }

    /**
     * Relation avec les documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relation avec les messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Relation avec l'évaluation
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }

    /**
     * Relation avec le tuteur de stage
     */
    public function tuteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tuteur_id');
    }

    /**
     * Changer le statut avec historique
     *
     * @throws \InvalidArgumentException si la transition n'est pas autorisée
     */
    public function changerStatut(StatutCandidature $nouveauStatut, ?string $commentaire = null): bool
    {
        $ancienStatut = $this->statut;
        
        // Vérifier si la transition est autorisée (dépendance entre étapes)
        if (!$ancienStatut->canTransitionTo($nouveauStatut)) {
            $nextLabels = collect($ancienStatut->getNextStatuts())
                ->map(fn ($s) => $s->getLabel())
                ->implode(', ');
            $message = "Transition interdite : impossible de passer de \"{$ancienStatut->getLabel()}\" (étape {$ancienStatut->getEtape()}) à \"{$nouveauStatut->getLabel()}\" (étape {$nouveauStatut->getEtape()}).";
            if (!empty($nextLabels)) {
                $message .= " Prochaine(s) étape(s) autorisée(s) : {$nextLabels}.";
            } else {
                $message .= " Ce statut est terminal, aucune transition n'est possible.";
            }
            \Illuminate\Support\Facades\Log::warning('Tentative de saut d\'étape bloquée', [
                'candidature_id' => $this->id,
                'de' => $ancienStatut->value,
                'vers' => $nouveauStatut->value,
                'utilisateur' => auth()->user()?->name ?? 'Système',
            ]);
            throw new \InvalidArgumentException($message);
        }
        
        // Ajouter à l'historique
        $historique = $this->historique_statuts ?? [];
        $historique[] = [
            'de' => $ancienStatut->value,
            'vers' => $nouveauStatut->value,
            'date' => now()->toIso8601String(),
            'utilisateur' => auth()->user()?->name ?? 'Système',
            'commentaire' => $commentaire,
        ];
        
        $this->historique_statuts = $historique;
        $this->statut = $nouveauStatut;
        $this->save();
        
        return true;
    }

    /**
     * Obtenir l'étape actuelle du workflow
     */
    public function getEtapeWorkflow(): int
    {
        return $this->statut->getEtape();
    }

    /**
     * Obtenir les prochaines actions possibles
     */
    public function getActionsDisponibles(): array
    {
        return $this->statut->getNextStatuts();
    }

    /**
     * Vérifier si le stage est en cours
     */
    public function estEnCours(): bool
    {
        return $this->statut === StatutCandidature::STAGE_EN_COURS;
    }

    /**
     * Vérifier si le stage est terminé
     */
    public function estTermine(): bool
    {
        return in_array($this->statut, [
            StatutCandidature::TERMINE,
            StatutCandidature::REJETE,
        ]);
    }

    /**
     * Mutator pour plafonner note_test à NOTE_MAX
     */
    public function setNoteTestAttribute($value): void
    {
        $this->attributes['note_test'] = $value !== null ? min((float) $value, self::NOTE_MAX) : null;
    }

    /**
     * Mutator pour plafonner note_evaluation à NOTE_MAX
     */
    public function setNoteEvaluationAttribute($value): void
    {
        $this->attributes['note_evaluation'] = $value !== null ? min((float) $value, self::NOTE_MAX) : null;
    }

    /**
     * Accessor pour le nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Accessor pour la durée du stage souhaitée
     */
    public function getDureeSouhaiteeAttribute(): ?int
    {
        if ($this->periode_debut_souhaitee && $this->periode_fin_souhaitee) {
            return $this->periode_debut_souhaitee->diffInDays($this->periode_fin_souhaitee);
        }
        return null;
    }

    /**
     * Accessor pour vérifier si la candidature est terminée
     */
    public function getEstTermineeAttribute(): bool
    {
        return $this->statut->isTerminal();
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeParStatut($query, StatutCandidature $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour filtrer par établissement
     */
    public function scopeParEtablissement($query, string $etablissement)
    {
        return $query->where('etablissement', $etablissement);
    }

    /**
     * Scope pour filtrer par niveau d'étude
     */
    public function scopeParNiveau($query, string $niveau)
    {
        return $query->where('niveau_etude', $niveau);
    }

    /**
     * Scope pour les candidatures récentes (dernière semaine)
     */
    public function scopeRecentes($query)
    {
        return $query->where('created_at', '>=', now()->subWeek());
    }

    /**
     * Valider la candidature avec dates de stage (décision positive)
     */
    public function valider(\Carbon\Carbon $dateDebut, \Carbon\Carbon $dateFin): bool
    {
        $this->statut = StatutCandidature::DECISION_POSITIVE;
        $this->date_debut_stage = $dateDebut;
        $this->date_fin_stage = $dateFin;
        $this->motif_rejet = null;

        // L'email est envoyé automatiquement via le listener boot() updated
        return $this->save();
    }

    /**
     * Rejeter la candidature
     */
    public function rejeter(string $motif): bool
    {
        $this->statut = StatutCandidature::REJETE;
        $this->motif_rejet = $motif;
        $this->date_debut_stage = null;
        $this->date_fin_stage = null;

        // L'email est envoyé automatiquement via le listener boot() updated
        return $this->save();
    }

    /**
     * Obtenir les directions disponibles
     */
    public static function getDirectionsDisponibles(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_DIRECTION);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'direction_generale' => 'Direction Générale',
            'direction_financiere' => 'Direction Financière et Comptable',
            'direction_rh' => 'Direction des Ressources Humaines',
            'direction_marketing' => 'Direction Marketing et Communication',
            'direction_commerciale' => 'Direction Commerciale',
            'direction_production' => 'Direction de Production',
            'direction_technique' => 'Direction Technique',
            'direction_qualite' => 'Direction Qualité',
            'direction_logistique' => 'Direction Logistique',
            'direction_informatique' => 'Direction Informatique',
            'direction_juridique' => 'Direction Juridique',
            'direction_audit' => 'Direction Audit Interne',
        ];
    }

    /**
     * Obtenir les établissements référencés
     */
    public static function getEtablissements(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_ETABLISSEMENT);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'unikin' => 'Université de Kinshasa (UNIKIN)',
            'ulk' => 'Université Libre de Kinshasa (ULK)',
            'upc' => 'Université Protestante du Congo (UPC)',
            'isc' => 'Institut Supérieur de Commerce (ISC)',
            'ista' => 'Institut Supérieur de Techniques Appliquées (ISTA)',
            'esg' => 'École Supérieure de Gestion (ESG)',
            'isp' => 'Institut Supérieur Pédagogique (ISP)',
            'upn' => 'Université Pédagogique Nationale (UPN)',
            'ifasic' => 'Institut Facultaire des Sciences de l\'Information et de la Communication (IFASIC)',
            'esii' => 'École Supérieure des Ingénieurs Industriels (ESII)',
            'autres' => 'Autres',
        ];
    }

    /**
     * Obtenir les niveaux d'étude
     */
    public static function getNiveauxEtude(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_NIVEAU_ETUDE);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'ecole_secondaire' => 'École Secondaire',
            'bac_1' => 'Première année (Bac+1)',
            'bac_2' => 'Deuxième année (Bac+2)',
            'bac_3' => 'Licence/Graduat (Bac+3)',
            'bac_4' => 'Maîtrise (Bac+4)',
            'bac_5' => 'Master (Bac+5)',
            'doctorat' => 'Doctorat/PhD',
        ];
    }

    /**
     * Déduire automatiquement le poste souhaité depuis la première direction choisie
     */
    public static function deduirePosteDepuisDirection(?string $direction): ?string
    {
        if (empty($direction)) {
            return null;
        }

        $mapping = [
            'direction_generale'    => 'assistant_direction',
            'direction_financiere'  => 'assistant_financier',
            'direction_rh'          => 'assistant_rh',
            'direction_marketing'   => 'assistant_marketing',
            'direction_commerciale' => 'assistant_commercial',
            'direction_production'  => 'assistant_production',
            'direction_technique'   => 'assistant_technique',
            'direction_qualite'     => 'assistant_qualite',
            'direction_logistique'  => 'assistant_logistique',
            'direction_informatique'=> 'assistant_informatique',
            'direction_juridique'   => 'assistant_juridique',
            'direction_audit'       => 'assistant_audit',
        ];

        return $mapping[$direction] ?? null;
    }

    /**
     * Déduire le poste depuis un tableau de directions (prend la première)
     */
    public static function deduirePosteDepuisDirections(array $directions): ?string
    {
        if (empty($directions)) {
            return null;
        }

        return static::deduirePosteDepuisDirection($directions[0]);
    }

    /**
     * Obtenir les postes disponibles pour les stages
     */
    public static function getPostesDisponibles(): array
    {
        // Utiliser les configurations si disponibles, sinon fallback
        $configurationsDisponibles = ConfigurationListe::getOptions(ConfigurationListe::TYPE_POSTE);
        
        if (!empty($configurationsDisponibles)) {
            return $configurationsDisponibles;
        }

        // Fallback vers les valeurs en dur
        return [
            'assistant_commercial' => 'Stagiaire Assistant(e) Commercial(e)',
            'assistant_marketing' => 'Stagiaire Assistant(e) Marketing',
            'assistant_communication' => 'Stagiaire Assistant(e) Communication',
            'assistant_comptable' => 'Stagiaire Assistant(e) Comptable',
            'assistant_financier' => 'Stagiaire Assistant(e) Financier(ère)',
            'assistant_rh' => 'Stagiaire Assistant(e) RH',
            'assistant_production' => 'Stagiaire Assistant(e) Production',
            'assistant_qualite' => 'Stagiaire Assistant(e) Qualité',
            'assistant_logistique' => 'Stagiaire Assistant(e) Logistique',
            'assistant_technique' => 'Stagiaire Assistant(e) Technique',
            'assistant_informatique' => 'Stagiaire Assistant(e) Informatique',
            'assistant_juridique' => 'Stagiaire Assistant(e) Juridique',
            'assistant_audit' => 'Stagiaire Assistant(e) Audit',
            'developpeur' => 'Stagiaire Développeur(euse)',
            'analyste_donnees' => 'Stagiaire Analyste de Données',
            'chef_projet_junior' => 'Stagiaire Chef de Projet Junior',
            'assistant_direction' => 'Stagiaire Assistant(e) Direction',
            'autre_poste' => 'Autre poste (à préciser)',
        ];
    }
} 