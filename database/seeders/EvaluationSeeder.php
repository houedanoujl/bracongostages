<?php

namespace Database\Seeders;

use App\Models\Candidature;
use App\Models\Evaluation;
use App\Enums\StatutCandidature;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les candidatures validées qui ont une date de fin de stage passée
        $candidatures = Candidature::where('statut', StatutCandidature::VALIDE)
            ->whereNotNull('date_fin_stage')
            ->where('date_fin_stage', '<=', now())
            ->whereDoesntHave('evaluation')
            ->take(10)
            ->get();

        if ($candidatures->isEmpty()) {
            $this->command->info('Aucune candidature éligible pour créer des évaluations de test.');
            return;
        }

        $evaluations = [
            [
                'satisfaction_generale' => 5,
                'recommandation' => 'oui',
                'accueil_integration' => 'excellent',
                'encadrement_suivi' => 'excellent',
                'conditions_travail' => 'excellent',
                'ambiance_travail' => 'excellent',
                'competences_developpees' => 'J\'ai développé mes compétences en gestion de projet, travail en équipe, et communication. J\'ai également appris à utiliser de nouveaux outils professionnels.',
                'reponse_attentes' => 'Ce stage a largement dépassé mes attentes. J\'ai pu travailler sur des projets concrets et être encadré par des professionnels expérimentés.',
                'aspects_enrichissants' => 'Les rencontres avec différents départements, la participation à des réunions importantes, et la responsabilité qui m\'a été confiée ont été très enrichissants.',
                'suggestions_amelioration' => 'Peut-être organiser plus de sessions de networking entre stagiaires et employés.',
                'contact_futur' => 'oui',
                'commentaire_libre' => 'Excellente expérience, je recommande vivement !',
            ],
            [
                'satisfaction_generale' => 4,
                'recommandation' => 'oui',
                'accueil_integration' => 'bon',
                'encadrement_suivi' => 'excellent',
                'conditions_travail' => 'bon',
                'ambiance_travail' => 'excellent',
                'competences_developpees' => 'Amélioration de mes compétences techniques et découverte de nouveaux processus métier.',
                'reponse_attentes' => 'Le stage a répondu à mes attentes principales. J\'ai pu mettre en pratique mes connaissances théoriques.',
                'aspects_enrichissants' => 'L\'ambiance de travail et l\'encadrement ont été les points forts de cette expérience.',
                'suggestions_amelioration' => 'Plus de formation initiale sur les outils utilisés.',
                'contact_futur' => 'oui',
                'commentaire_libre' => 'Très bonne expérience professionnelle.',
            ],
            [
                'satisfaction_generale' => 4,
                'recommandation' => 'peut_etre',
                'accueil_integration' => 'bon',
                'encadrement_suivi' => 'bon',
                'conditions_travail' => 'bon',
                'ambiance_travail' => 'bon',
                'competences_developpees' => 'Développement de compétences en analyse de données et présentation.',
                'reponse_attentes' => 'Le stage a globalement répondu à mes attentes, avec quelques améliorations possibles.',
                'aspects_enrichissants' => 'La diversité des missions et l\'autonomie accordée.',
                'suggestions_amelioration' => 'Plus de feedback régulier sur le travail effectué.',
                'contact_futur' => 'oui',
                'commentaire_libre' => 'Expérience positive dans l\'ensemble.',
            ],
            [
                'satisfaction_generale' => 3,
                'recommandation' => 'peut_etre',
                'accueil_integration' => 'moyen',
                'encadrement_suivi' => 'moyen',
                'conditions_travail' => 'bon',
                'ambiance_travail' => 'bon',
                'competences_developpees' => 'J\'ai appris les bases du travail en entreprise et quelques outils spécifiques.',
                'reponse_attentes' => 'Le stage a partiellement répondu à mes attentes. J\'aurais souhaité plus de responsabilités.',
                'aspects_enrichissants' => 'La découverte de l\'environnement professionnel.',
                'suggestions_amelioration' => 'Améliorer l\'accueil et l\'intégration des nouveaux stagiaires.',
                'contact_futur' => 'non',
                'commentaire_libre' => 'Expérience correcte mais pourrait être améliorée.',
            ],
            [
                'satisfaction_generale' => 2,
                'recommandation' => 'non',
                'accueil_integration' => 'insuffisant',
                'encadrement_suivi' => 'insuffisant',
                'conditions_travail' => 'moyen',
                'ambiance_travail' => 'moyen',
                'competences_developpees' => 'Peu d\'opportunités de développement de compétences.',
                'reponse_attentes' => 'Le stage n\'a pas répondu à mes attentes. Manque d\'encadrement et de missions intéressantes.',
                'aspects_enrichissants' => 'La découverte de l\'entreprise malgré tout.',
                'suggestions_amelioration' => 'Améliorer significativement l\'encadrement et l\'accueil des stagiaires.',
                'contact_futur' => 'non',
                'commentaire_libre' => 'Expérience décevante, beaucoup d\'améliorations nécessaires.',
            ],
        ];

        foreach ($candidatures as $index => $candidature) {
            $evaluationData = $evaluations[$index % count($evaluations)];
            
            Evaluation::create([
                'candidature_id' => $candidature->id,
                ...$evaluationData,
            ]);

            $this->command->info("Évaluation créée pour {$candidature->nom_complet}");
        }

        $this->command->info("{$candidatures->count()} évaluations de test créées avec succès.");
    }
} 