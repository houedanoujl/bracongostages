<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Corriger les valeurs de statut pour correspondre à l'enum PHP
     */
    public function up(): void
    {
        // D'abord convertir en varchar pour permettre les modifications
        DB::statement("ALTER TABLE candidatures MODIFY COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'dossier_recu'");
        
        // Corriger les valeurs
        $corrections = [
            'analyse_drh' => 'analyse_dossier',
            'test_programme' => 'attente_test',
            'decision_positive' => 'accepte',
            'reponse_recommandation' => 'reponse_lettre_envoyee',
            'induction_rh' => 'induction_terminee',
            'evaluation_fin' => 'evaluation_terminee',
            'remboursement_effectue' => 'termine',
        ];
        
        foreach ($corrections as $incorrect => $correct) {
            DB::table('candidatures')
                ->where('statut', $incorrect)
                ->update(['statut' => $correct]);
        }
        
        // Reconvertir en enum avec toutes les valeurs supportées par l'enum PHP
        DB::statement("ALTER TABLE candidatures MODIFY COLUMN statut ENUM(
            'dossier_recu',
            'analyse_dossier',
            'dossier_incomplet',
            'attente_test',
            'test_planifie',
            'test_passe',
            'attente_decision',
            'accepte',
            'rejete',
            'planification',
            'affecte',
            'reponse_lettre_envoyee',
            'induction_planifiee',
            'induction_terminee',
            'accueil_service',
            'stage_en_cours',
            'en_evaluation',
            'evaluation_terminee',
            'attestation_generee',
            'remboursement_en_cours',
            'termine',
            'non_traite',
            'attente_resultats',
            'attente_affectation',
            'valide'
        ) NOT NULL DEFAULT 'dossier_recu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de retour en arrière nécessaire
    }
};
