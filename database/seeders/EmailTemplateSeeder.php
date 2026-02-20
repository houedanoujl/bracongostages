<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'convocation_test',
                'nom' => 'Convocation au test',
                'sujet' => 'Convocation au Test - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nDans le cadre du processus de sélection des stagiaires au sein de Bracongo, nous avons le plaisir de vous informer que votre candidature a été retenue pour la phase de test.\n\nVous êtes invité(e) à vous présenter selon les modalités suivantes :\n\nDate : {date_test}\nHeure : {heure_test}\nLieu : Bracongo - Avenue des Brasseries, numéro 7666, Quartier Kingabwa, Commune de Limete, dans la province de Kinshasa, en République Démocratique du Congo.\n\nNous vous prions de vous munir d'une pièce d'identité et de vous présenter 15 minutes avant l'heure indiquée.\n\nNous vous remercions pour l'intérêt porté à notre organisation.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'date_test', 'heure_test', 'code_suivi'],
            ],
            [
                'slug' => 'resultat_admis',
                'nom' => 'Résultat : Admis / Accepté',
                'sujet' => 'Résultat - Candidature retenue - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nÀ l'issue du processus de sélection, nous avons le plaisir de vous informer que votre candidature a été retenue.\n\nVotre stage au sein de Bracongo est donc validé.\n\nNotre équipe prendra contact avec vous pour finaliser les modalités administratives.\n\nFélicitations et bienvenue parmi nous.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'code_suivi'],
            ],
            [
                'slug' => 'resultat_non_admis',
                'nom' => 'Résultat : Non admis / Rejeté',
                'sujet' => 'Résultat du processus de sélection - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nPour donner suite au test de sélection organisé le {date_test}, nous vous remercions pour votre participation.\n\nAprès évaluation, nous regrettons de vous informer que vous n'avez pas atteint la moyenne requise pour cette session.\n\nNous vous encourageons à poursuivre vos efforts et à postuler à de prochaines opportunités.\n\nNous vous souhaitons plein succès dans la suite de votre parcours académique et professionnel.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'date_test', 'code_suivi'],
            ],
            [
                'slug' => 'confirmation_dates',
                'nom' => 'Confirmation des dates de stage (Validé)',
                'sujet' => 'Confirmation des Dates de Stage - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},\n\nNous vous confirmons que votre stage au sein de Bracongo se déroulera selon les modalités suivantes :\n\nDate de début : {date_debut}\nDate de fin : {date_fin}\nDirection / Service d'affectation : {direction_service}\n\nNous vous prions de vous présenter le premier jour à {heure_presentation} auprès de la Direction des Ressources Humaines pour les formalités d'accueil.\n\nNous vous souhaitons pleine réussite dans cette expérience professionnelle.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'date_debut', 'date_fin', 'direction_service', 'heure_presentation', 'code_suivi'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
