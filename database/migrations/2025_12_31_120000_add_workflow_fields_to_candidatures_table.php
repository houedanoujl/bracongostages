<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            // Champs pour l'affectation
            $table->string('service_affecte')->nullable()->after('directions_souhaitees');
            $table->unsignedBigInteger('tuteur_id')->nullable()->after('service_affecte');
            $table->text('programme_stage')->nullable()->after('tuteur_id');
            
            // Champs pour les tests
            $table->date('date_test')->nullable()->after('programme_stage');
            $table->string('lieu_test')->nullable()->after('date_test');
            $table->decimal('note_test', 5, 2)->nullable()->after('lieu_test');
            $table->text('commentaire_test')->nullable()->after('note_test');
            
            // Champs pour l'induction
            $table->date('date_induction')->nullable()->after('commentaire_test');
            $table->boolean('induction_completee')->default(false)->after('date_induction');
            
            // Champs pour le stage réel
            $table->date('date_debut_stage_reel')->nullable()->after('induction_completee');
            $table->date('date_fin_stage_reel')->nullable()->after('date_debut_stage_reel');
            
            // Champs pour l'évaluation
            $table->decimal('note_evaluation', 5, 2)->nullable()->after('date_fin_stage_reel');
            $table->text('commentaire_evaluation')->nullable()->after('note_evaluation');
            $table->text('competences_acquises_evaluation')->nullable()->after('commentaire_evaluation');
            $table->string('appreciation_tuteur')->nullable()->after('competences_acquises_evaluation');
            $table->date('date_evaluation')->nullable()->after('appreciation_tuteur');
            
            // Champs pour l'attestation
            $table->boolean('attestation_generee')->default(false)->after('date_evaluation');
            $table->string('chemin_attestation')->nullable()->after('attestation_generee');
            $table->date('date_attestation')->nullable()->after('chemin_attestation');
            
            // Champs pour le remboursement transport
            $table->decimal('montant_transport', 10, 2)->nullable()->after('date_attestation');
            $table->boolean('remboursement_effectue')->default(false)->after('montant_transport');
            $table->date('date_remboursement')->nullable()->after('remboursement_effectue');
            $table->string('reference_paiement')->nullable()->after('date_remboursement');
            
            // Champs pour la réponse à la lettre de recommandation
            $table->boolean('reponse_lettre_envoyee')->default(false)->after('reference_paiement');
            $table->date('date_reponse_lettre')->nullable()->after('reponse_lettre_envoyee');
            $table->string('chemin_reponse_lettre')->nullable()->after('date_reponse_lettre');
            
            // Historique des changements de statut
            $table->json('historique_statuts')->nullable()->after('chemin_reponse_lettre');
            
            // Notes internes
            $table->text('notes_internes')->nullable()->after('historique_statuts');
            
            // Clé étrangère pour le tuteur
            $table->foreign('tuteur_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropForeign(['tuteur_id']);
            
            $table->dropColumn([
                'service_affecte',
                'tuteur_id',
                'programme_stage',
                'date_test',
                'lieu_test',
                'note_test',
                'commentaire_test',
                'date_induction',
                'induction_completee',
                'date_debut_stage_reel',
                'date_fin_stage_reel',
                'note_evaluation',
                'commentaire_evaluation',
                'competences_acquises_evaluation',
                'appreciation_tuteur',
                'date_evaluation',
                'attestation_generee',
                'chemin_attestation',
                'date_attestation',
                'montant_transport',
                'remboursement_effectue',
                'date_remboursement',
                'reference_paiement',
                'reponse_lettre_envoyee',
                'date_reponse_lettre',
                'chemin_reponse_lettre',
                'historique_statuts',
                'notes_internes',
            ]);
        });
    }
};
