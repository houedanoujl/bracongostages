<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Opportunite;

class OpportuniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opportunites = [
            [
                'titre' => 'Production & Qualité',
                'slug' => 'production-qualite',
                'description' => 'Participez aux processus de production et de contrôle qualité. Apprenez les standards internationaux et les technologies modernes de brassage.',
                'description_longue' => 'Ce stage vous permettra de découvrir l\'ensemble des processus de production de la bière, depuis la réception des matières premières jusqu\'à l\'expédition du produit fini. Vous travaillerez aux côtés d\'experts pour comprendre les enjeux de qualité, les contrôles à chaque étape et les normes internationales (ISO, HACCP) appliquées dans l\'industrie brassicole.',
                'categorie' => 'production',
                'niveau_requis' => 'bac_2',
                'duree' => '3-6 mois',
                'competences_requises' => [
                    'Bases en chimie/biologie',
                    'Rigueur et attention aux détails',
                    'Esprit d\'équipe'
                ],
                'competences_acquises' => [
                    'Contrôle qualité industriel',
                    'Processus de brassage',
                    'Normes ISO et HACCP',
                    'Analyse sensorielle'
                ],
                'places_disponibles' => 5,
                'actif' => true,
                'icone' => '🏭',
                'ordre_affichage' => 1,
                'directions_associees' => ['direction_production', 'direction_qualite'],
            ],
            [
                'titre' => 'Marketing & Commercial',
                'slug' => 'marketing-commercial',
                'description' => 'Développez vos compétences en marketing digital, stratégie commerciale et gestion de marque dans un environnement dynamique.',
                'description_longue' => 'Intégrez notre équipe marketing pour participer au développement et à l\'exécution de campagnes marketing innovantes. Vous travaillerez sur la stratégie de marque, le marketing digital, l\'analyse des tendances du marché et la création de contenu pour nos différents canaux de communication.',
                'categorie' => 'commercial',
                'niveau_requis' => 'bac_3',
                'duree' => '3-6 mois',
                'competences_requises' => [
                    'Bases en marketing',
                    'Créativité',
                    'Maîtrise des outils digitaux',
                    'Sens de la communication'
                ],
                'competences_acquises' => [
                    'Marketing digital',
                    'Stratégie de marque',
                    'Analyse de marché',
                    'Gestion de campagnes',
                    'Content marketing'
                ],
                'places_disponibles' => 3,
                'actif' => true,
                'icone' => 'chart-bar',
                'ordre_affichage' => 2,
                'directions_associees' => ['direction_marketing', 'direction_commerciale'],
            ],
            [
                'titre' => 'Technique & Maintenance',
                'slug' => 'technique-maintenance',
                'description' => 'Maîtrisez la maintenance industrielle, l\'automatisation et la gestion des équipements de pointe dans l\'industrie brassicole.',
                'description_longue' => 'Rejoignez notre équipe technique pour apprendre la maintenance préventive et corrective des équipements industriels. Vous découvrirez les systèmes d\'automatisation, la gestion de la maintenance assistée par ordinateur (GMAO) et les technologies de pointe utilisées dans notre brasserie.',
                'categorie' => 'technique',
                'niveau_requis' => 'bac_2',
                'duree' => '4-6 mois',
                'competences_requises' => [
                    'Bases en électromécanique',
                    'Aptitudes techniques',
                    'Capacité d\'adaptation',
                    'Respect des règles de sécurité'
                ],
                'competences_acquises' => [
                    'Maintenance industrielle',
                    'Automatisation',
                    'GMAO',
                    'Sécurité industrielle',
                    'Diagnostic technique'
                ],
                'places_disponibles' => 4,
                'actif' => true,
                'icone' => '⚙️',
                'ordre_affichage' => 3,
                'directions_associees' => ['direction_technique', 'direction_production'],
            ],
            [
                'titre' => 'Ressources Humaines',
                'slug' => 'ressources-humaines',
                'description' => 'Découvrez la gestion des talents, le recrutement et le développement organisationnel dans une entreprise de référence.',
                'description_longue' => 'Participez aux activités RH stratégiques : recrutement, formation, gestion des performances, développement des compétences. Vous contribuerez à l\'amélioration des processus RH et découvrirez les enjeux de la gestion humaine dans un environnement industriel en pleine transformation.',
                'categorie' => 'administratif',
                'niveau_requis' => 'bac_3',
                'duree' => '3-4 mois',
                'competences_requises' => [
                    'Sens relationnel',
                    'Confidentialité',
                    'Organisation',
                    'Communication'
                ],
                'competences_acquises' => [
                    'Recrutement',
                    'Gestion des talents',
                    'Formation et développement',
                    'Évaluation des performances',
                    'SIRH'
                ],
                'places_disponibles' => 2,
                'actif' => true,
                'icone' => '👥',
                'ordre_affichage' => 4,
                'directions_associees' => ['direction_rh'],
            ],
            [
                'titre' => 'Finance & Comptabilité',
                'slug' => 'finance-comptabilite',
                'description' => 'Approfondissez vos connaissances en gestion financière, contrôle de gestion et analyse des performances dans un contexte international.',
                'description_longue' => 'Intégrez notre direction financière pour participer à l\'élaboration des budgets, au suivi des performances, à l\'analyse financière et aux reportings. Vous découvrirez les spécificités de la gestion financière dans l\'industrie agroalimentaire et les standards internationaux.',
                'categorie' => 'finance',
                'niveau_requis' => 'bac_3',
                'duree' => '3-6 mois',
                'competences_requises' => [
                    'Bases en comptabilité/finance',
                    'Maîtrise d\'Excel',
                    'Rigueur analytique',
                    'Esprit de synthèse'
                ],
                'competences_acquises' => [
                    'Contrôle de gestion',
                    'Analyse financière',
                    'Budgétisation',
                    'Reporting financier',
                    'ERP/SAP'
                ],
                'places_disponibles' => 3,
                'actif' => true,
                'icone' => 'briefcase',
                'ordre_affichage' => 5,
                'directions_associees' => ['direction_financiere'],
            ],
            [
                'titre' => 'IT & Transformation Digitale',
                'slug' => 'it-transformation-digitale',
                'description' => 'Participez à la digitalisation des processus et au développement des solutions technologiques innovantes.',
                'description_longue' => 'Rejoignez notre équipe IT pour contribuer aux projets de transformation digitale de l\'entreprise. Vous participerez au développement d\'applications, à l\'amélioration des systèmes d\'information et à l\'implémentation de nouvelles technologies (IoT, IA, automatisation).',
                'categorie' => 'technique',
                'niveau_requis' => 'bac_3',
                'duree' => '4-6 mois',
                'competences_requises' => [
                    'Programmation (Java, Python, PHP)',
                    'Bases de données',
                    'Réseau et sécurité',
                    'Curiosité technologique'
                ],
                'competences_acquises' => [
                    'Développement d\'applications',
                    'Systèmes d\'information',
                    'Transformation digitale',
                    'IoT industriel',
                    'Cybersécurité'
                ],
                'places_disponibles' => 3,
                'actif' => true,
                'icone' => 'computer-desktop',
                'ordre_affichage' => 6,
                'directions_associees' => ['direction_informatique'],
            ],
            [
                'titre' => 'Logistique & Supply Chain',
                'slug' => 'logistique-supply-chain',
                'description' => 'Optimisez les flux logistiques et découvrez les enjeux de la chaîne d\'approvisionnement dans l\'industrie.',
                'description_longue' => 'Participez à l\'optimisation de notre chaîne logistique, de l\'approvisionnement en matières premières à la distribution des produits finis. Vous découvrirez les outils de planification, la gestion des stocks et les enjeux de la logistique moderne.',
                'categorie' => 'administratif',
                'niveau_requis' => 'bac_2',
                'duree' => '3-5 mois',
                'competences_requises' => [
                    'Organisation',
                    'Maîtrise des outils informatiques',
                    'Sens de l\'analyse',
                    'Capacité d\'adaptation'
                ],
                'competences_acquises' => [
                    'Gestion des stocks',
                    'Planification logistique',
                    'WMS/ERP',
                    'Optimisation des flux',
                    'Négociation fournisseurs'
                ],
                'places_disponibles' => 2,
                'actif' => true,
                'icone' => '🚛',
                'ordre_affichage' => 7,
                'directions_associees' => ['direction_logistique'],
            ],
            [
                'titre' => 'Audit & Contrôle Interne',
                'slug' => 'audit-controle-interne',
                'description' => 'Participez aux missions d\'audit et renforcez les dispositifs de contrôle interne de l\'entreprise.',
                'description_longue' => 'Intégrez notre équipe d\'audit pour participer aux missions d\'évaluation des processus, d\'identification des risques et de recommandations d\'amélioration. Une excellente opportunité pour développer votre esprit d\'analyse et votre compréhension globale de l\'entreprise.',
                'categorie' => 'finance',
                'niveau_requis' => 'bac_4',
                'duree' => '3-4 mois',
                'competences_requises' => [
                    'Esprit d\'analyse',
                    'Rigueur méthodologique',
                    'Sens de l\'observation',
                    'Communication'
                ],
                'competences_acquises' => [
                    'Méthodologie d\'audit',
                    'Évaluation des risques',
                    'Contrôle interne',
                    'Reporting d\'audit',
                    'Recommandations d\'amélioration'
                ],
                'places_disponibles' => 1,
                'actif' => true,
                'icone' => 'magnifying-glass',
                'ordre_affichage' => 8,
                'directions_associees' => ['direction_audit'],
            ],
        ];

        foreach ($opportunites as $opportuniteData) {
            Opportunite::create($opportuniteData);
        }
    }
}
