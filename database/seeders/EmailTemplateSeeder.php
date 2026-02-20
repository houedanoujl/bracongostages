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
                'contenu' => "Madame / Monsieur {nom},

Dans le cadre du processus de sélection des stagiaires au sein de Bracongo, nous avons le plaisir de vous informer que votre candidature a été retenue pour la phase de test.

Vous êtes invité(e) à vous présenter selon les modalités suivantes :

Date : {date_test}
Heure : {heure_test}
Lieu : Bracongo - Avenue des Brasseries, numéro 7666, Quartier Kingabwa, Commune de Limete, dans la province de Kinshasa, en République Démocratique du Congo.

Nous vous prions de vous munir d'une pièce d'identité et de vous présenter 15 minutes avant l'heure indiquée.

Nous vous remercions pour l'intérêt porté à notre organisation.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'date_test', 'heure_test', 'code_suivi'],
            ],
            [
                'slug' => 'resultat_admis',
                'nom' => 'Résultat : Admis',
                'sujet' => 'Résultat du Test - Félicitations - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},

À l'issue du processus de sélection, nous avons le plaisir de vous informer que votre candidature a été retenue.

Votre stage au sein de Bracongo est donc validé.

Notre équipe prendra contact avec vous pour finaliser les modalités administratives.

Félicitations et bienvenue parmi nous.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'code_suivi'],
            ],
            [
                'slug' => 'resultat_non_admis',
                'nom' => 'Résultat : Non admis',
                'sujet' => 'Résultat du Test - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},

Pour donner suite au test de sélection organisé le {date_test}, nous vous remercions pour votre participation.

Après évaluation, nous regrettons de vous informer que vous n'avez pas atteint la moyenne requise pour cette session.

Nous vous encourageons à poursuivre vos efforts et à postuler à de prochaines opportunités.

Nous vous souhaitons plein succès dans la suite de votre parcours académique et professionnel.",
                'placeholders_disponibles' => ['nom', 'prenom', 'email', 'date_test', 'code_suivi'],
            ],
            [
                'slug' => 'confirmation_dates',
                'nom' => 'Confirmation des dates de stage',
                'sujet' => 'Confirmation des Dates de Stage - BRACONGO Stages',
                'contenu' => "Madame / Monsieur {nom},

Nous vous confirmons que votre stage au sein de Bracongo se déroulera selon les modalités suivantes :

Date de début : {date_debut}
Date de fin : {date_fin}
Direction / Service d'affectation : {direction_service}

Nous vous prions de vous présenter le premier jour à {heure_presentation} auprès de la Direction des Ressources Humaines pour les formalités d'accueil.

Nous vous souhaitons pleine réussite dans cette expérience professionnelle.",
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
