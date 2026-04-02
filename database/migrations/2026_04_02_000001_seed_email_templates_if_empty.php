<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration qui insère les templates email si la table est vide.
 * Cela garantit que les templates sont disponibles après chaque déploiement,
 * sans dépendre d'un db:seed manuel.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_templates')) {
            return;
        }

        $now = now();

        $templates = [
            [
                'slug' => 'analyse_dossier',
                'nom' => 'Analyse du dossier',
                'sujet' => "Votre dossier est en cours d'analyse - BRACONGO Stages",
                'contenu' => "Madame / Monsieur {nom},\n\nNous accusons réception de votre dossier de candidature pour le programme de stages BRACONGO.\n\nVotre dossier est actuellement en cours d'analyse par notre Direction des Ressources Humaines.\n\nVotre code de suivi : {code_suivi}\n\nNous vous tiendrons informé(e) des prochaines étapes dans les meilleurs délais.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'email']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'dossier_incomplet',
                'nom' => 'Dossier incomplet',
                'sujet' => 'Dossier incomplet - Action requise - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nAprès examen de votre dossier de candidature, nous constatons que celui-ci est incomplet.\n\nNous vous prions de bien vouloir compléter les pièces manquantes dans les plus brefs délais afin de poursuivre le traitement de votre candidature.\n\nVotre code de suivi : {code_suivi}\n\nVeuillez vous connecter à votre espace candidat pour mettre à jour votre dossier.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'email']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'convocation_test',
                'nom' => 'Convocation au test',
                'sujet' => 'Convocation au Test - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nDans le cadre du processus de sélection des stagiaires au sein de Bracongo, nous avons le plaisir de vous informer que votre candidature a été retenue pour la phase de test.\n\nVous êtes invité(e) à vous présenter selon les modalités suivantes :\n\nDate : {date_test}\nHeure : {heure_test}\nLieu : Bracongo - Avenue des Brasseries, numéro 7666, Quartier Kingabwa, Commune de Limete, dans la province de Kinshasa, en République Démocratique du Congo.\n\nNous vous prions de vous munir d'une pièce d'identité et de vous présenter 15 minutes avant l'heure indiquée.\n\nNous vous remercions pour l'intérêt porté à notre organisation.",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'email', 'date_test', 'heure_test', 'code_suivi']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'resultat_test',
                'nom' => 'Résultat du test',
                'sujet' => 'Résultat de votre test - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nNous vous informons que vous avez passé le test dans le cadre du processus de sélection des stagiaires BRACONGO.\n\nVotre dossier va maintenant être soumis à la phase de décision.\n\nVous serez informé(e) de la suite dans les meilleurs délais.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'email']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'resultat_admis',
                'nom' => 'Résultat : Admis / Accepté',
                'sujet' => 'Résultat - Candidature retenue - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nÀ l'issue du processus de sélection, nous avons le plaisir de vous informer que votre candidature a été retenue.\n\nVotre stage au sein de Bracongo est donc validé.\n\nNotre équipe prendra contact avec vous pour finaliser les modalités administratives.\n\nFélicitations et bienvenue parmi nous.",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'email', 'code_suivi']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'resultat_non_admis',
                'nom' => 'Résultat : Non admis / Rejeté',
                'sujet' => 'Résultat du processus de sélection - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nPour donner suite au test de sélection organisé le {date_test}, nous vous remercions pour votre participation.\n\nAprès évaluation, nous regrettons de vous informer que vous n'avez pas atteint la moyenne requise pour cette session.\n\nNous vous encourageons à poursuivre vos efforts et à postuler à de prochaines opportunités.\n\nNous vous souhaitons plein succès dans la suite de votre parcours académique et professionnel.",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'email', 'date_test', 'code_suivi']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'confirmation_dates',
                'nom' => 'Confirmation des dates de stage (Validé)',
                'sujet' => 'Confirmation des Dates de Stage - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nNous vous confirmons que votre stage au sein de Bracongo se déroulera selon les modalités suivantes :\n\nDate de début : {date_debut}\nDate de fin : {date_fin}\nDirection / Service d'affectation : {direction_service}\nTuteur de stage : {tuteur_nom}\n\nNous vous prions de vous présenter le premier jour à {heure_presentation} auprès de la Direction des Ressources Humaines pour les formalités d'accueil.\n\nNous vous souhaitons pleine réussite dans cette expérience professionnelle.",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'email', 'date_debut', 'date_fin', 'direction_service', 'heure_presentation', 'tuteur_nom', 'code_suivi']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'reponse_lettre_recommandation',
                'nom' => 'Réponse lettre de recommandation',
                'sujet' => 'Lettre de recommandation - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nDans le cadre de la préparation de votre stage au sein de BRACONGO, nous vous informons que la lettre de recommandation a été traitée.\n\nVotre affectation :\nDirection / Service : {direction_service}\nTuteur de stage : {tuteur_nom}\nDate de début prévue : {date_debut}\nDate de fin prévue : {date_fin}\n\nLes prochaines étapes vous seront communiquées prochainement.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'direction_service', 'tuteur_nom', 'date_debut', 'date_fin']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'induction_rh',
                'nom' => 'Induction RH',
                'sujet' => 'Induction RH - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nNous vous informons que votre session d'induction RH est programmée.\n\nCette session vous permettra de découvrir l'organisation de BRACONGO, ses valeurs, ses politiques internes et les règles de sécurité.\n\nDirection / Service d'affectation : {direction_service}\nTuteur de stage : {tuteur_nom}\n\nVeuillez vous munir d'une pièce d'identité.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'direction_service', 'tuteur_nom']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'debut_stage',
                'nom' => 'Début du stage',
                'sujet' => 'Votre stage débute - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nNous avons le plaisir de vous informer que votre stage au sein de BRACONGO débute officiellement.\n\nVoici les informations relatives à votre stage :\nDirection / Service : {direction_service}\nTuteur de stage : {tuteur_nom}\nDate de début : {date_debut}\nDate de fin prévue : {date_fin}\n\nNous vous souhaitons pleine réussite dans cette expérience professionnelle.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'direction_service', 'tuteur_nom', 'date_debut', 'date_fin']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'envoi_evaluation',
                'nom' => 'Évaluation de stage',
                'sujet' => 'Évaluation de votre stage - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nVotre évaluation de stage a été finalisée.\n\nTuteur de stage : {tuteur_nom}\nNote d'évaluation : {note_evaluation}/20\nAppréciation du tuteur : {appreciation_tuteur}\n\nNous vous remercions pour votre engagement tout au long de votre stage.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'tuteur_nom', 'note_evaluation', 'appreciation_tuteur']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'envoi_attestation',
                'nom' => 'Envoi attestation',
                'sujet' => 'Votre attestation de stage - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nNous avons le plaisir de vous informer que votre attestation de stage a été générée.\n\nVous pouvez la récupérer auprès de la Direction des Ressources Humaines de BRACONGO ou la télécharger depuis votre espace candidat.\n\nNous vous remercions pour votre contribution et vous souhaitons plein succès dans la suite de votre parcours.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'stage_termine',
                'nom' => 'Stage terminé',
                'sujet' => 'Fin de votre stage - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nVotre stage au sein de BRACONGO est officiellement terminé.\n\nNous tenons à vous remercier pour votre implication et votre sérieux tout au long de cette expérience.\n\nN'hésitez pas à partager votre retour d'expérience depuis votre espace candidat.\n\nNous vous souhaitons une excellente continuation dans votre parcours académique et professionnel.\n\nCordialement,\nLa Direction des Ressources Humaines\nBRACONGO",
                'placeholders_disponibles' => json_encode(['nom', 'prenom', 'code_suivi', 'note_evaluation', 'appreciation_tuteur']),
                'actif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('email_templates')->updateOrInsert(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    public function down(): void
    {
        // Ne pas supprimer les templates au rollback pour éviter de perdre les personnalisations
    }
};
