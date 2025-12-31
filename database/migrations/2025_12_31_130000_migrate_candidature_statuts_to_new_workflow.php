<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mapping des anciens statuts vers les nouveaux
     */
    private array $statutMapping = [
        'non_traite' => 'dossier_recu',        // Réception initiale
        'analyse_dossier' => 'analyse_drh',     // Analyse par DRH
        'attente_test' => 'test_programme',     // Test programmé
        'attente_resultats' => 'test_passe',    // Test passé, en attente résultats
        'attente_affectation' => 'decision_positive', // Décision positive, prêt pour affectation
        'valide' => 'affecte',                  // Validé = affecté
        'rejete' => 'rejete',                   // Reste inchangé
        'termine' => 'termine',                 // Reste inchangé (si existe)
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convertir la colonne enum en varchar pour permettre tous les changements
        DB::statement("ALTER TABLE candidatures MODIFY COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'dossier_recu'");
        
        // 2. Mettre à jour les valeurs
        foreach ($this->statutMapping as $ancien => $nouveau) {
            DB::table('candidatures')
                ->where('statut', $ancien)
                ->update(['statut' => $nouveau]);
        }
        
        // 3. Reconvertir en enum avec les nouvelles valeurs
        DB::statement("ALTER TABLE candidatures MODIFY COLUMN statut ENUM(
            'dossier_recu',
            'analyse_drh',
            'test_programme',
            'test_passe',
            'decision_positive',
            'affecte',
            'reponse_recommandation',
            'induction_rh',
            'accueil_service',
            'stage_en_cours',
            'evaluation_fin',
            'attestation_generee',
            'remboursement_effectue',
            'termine',
            'rejete'
        ) NOT NULL DEFAULT 'dossier_recu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mapping inverse
        $inverseMapping = [
            'dossier_recu' => 'non_traite',
            'analyse_drh' => 'analyse_dossier',
            'test_programme' => 'attente_test',
            'test_passe' => 'attente_resultats',
            'decision_positive' => 'attente_affectation',
            'affecte' => 'valide',
            'reponse_recommandation' => 'valide',
            'induction_rh' => 'valide',
            'accueil_service' => 'valide',
            'stage_en_cours' => 'valide',
            'evaluation_fin' => 'valide',
            'attestation_generee' => 'valide',
            'remboursement_effectue' => 'valide',
            'rejete' => 'rejete',
            'termine' => 'termine',
        ];

        // 1. Convertir en varchar
        DB::statement("ALTER TABLE candidatures MODIFY COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'non_traite'");

        // 2. Mettre à jour les valeurs
        foreach ($inverseMapping as $nouveau => $ancien) {
            DB::table('candidatures')
                ->where('statut', $nouveau)
                ->update(['statut' => $ancien]);
        }
        
        // 3. Reconvertir en ancien enum
        DB::statement("ALTER TABLE candidatures MODIFY COLUMN statut ENUM(
            'non_traite',
            'analyse_dossier',
            'attente_test',
            'attente_resultats',
            'attente_affectation',
            'valide',
            'rejete',
            'termine'
        ) NOT NULL DEFAULT 'non_traite'");
    }
};
