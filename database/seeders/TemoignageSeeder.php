<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Temoignage;

class TemoignageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $temoignages = [
            [
                'nom' => 'Mukendi',
                'prenom' => 'Grace',
                'poste_occupe' => 'Ingénieure Qualité',
                'entreprise' => 'BRACONGO',
                'etablissement_origine' => 'Université de Kinshasa (UNIKIN)',
                'temoignage' => '<p>Mon stage de 6 mois chez BRACONGO a été une expérience transformatrice. J\'ai eu l\'opportunité de travailler directement avec l\'équipe qualité sur l\'amélioration des processus de production. Ce qui m\'a le plus marqué, c\'est la culture d\'excellence et l\'attention portée aux détails dans chaque étape de la production.</p><p>L\'encadrement était exceptionnel, avec des mentors qui ont pris le temps de m\'expliquer non seulement les aspects techniques, mais aussi la vision stratégique de l\'entreprise. J\'ai pu participer à des projets concrets qui ont eu un impact direct sur la qualité du produit final.</p>',
                'citation_courte' => 'BRACONGO m\'a donné les clés pour exceller dans mon domaine. Une expérience qui a façonné ma carrière professionnelle.',
                'date_stage_debut' => '2023-02-01',
                'date_stage_fin' => '2023-08-01',
                'duree_stage' => '6 mois',
                'direction_stage' => 'Production et Qualité',
                'actif' => true,
                'mis_en_avant' => true,
                'ordre_affichage' => 1,
                'note_experience' => 5,
                'competences_acquises' => [
                    'Gestion qualité',
                    'Process industriels',
                    'Analyse des données',
                    'Travail en équipe',
                    'Résolution de problèmes'
                ],
            ],
            [
                'nom' => 'Kabongo',
                'prenom' => 'David',
                'poste_occupe' => 'Chef de Projet Marketing',
                'entreprise' => 'PUBLICIS CONSEIL',
                'etablissement_origine' => 'Université Protestante du Congo (UPC)',
                'temoignage' => '<p>Mon passage chez BRACONGO en tant que stagiaire dans l\'équipe Marketing & Communication a été le tremplin de ma carrière. J\'ai découvert l\'univers passionnant du marketing dans l\'industrie des boissons, avec ses défis uniques et ses opportunités créatives.</p><p>Ce qui distingue BRACONGO, c\'est leur approche innovante et leur volonté de former la nouvelle génération. J\'ai participé au lancement d\'une campagne publicitaire majeure et j\'ai pu voir l\'impact direct de notre travail sur le marché congolais.</p>',
                'citation_courte' => 'BRACONGO m\'a permis de découvrir ma passion pour le marketing et m\'a donné toutes les armes pour réussir.',
                'date_stage_debut' => '2022-09-01',
                'date_stage_fin' => '2023-03-01',
                'duree_stage' => '6 mois',
                'direction_stage' => 'Marketing et Communication',
                'actif' => true,
                'mis_en_avant' => true,
                'ordre_affichage' => 2,
                'note_experience' => 5,
                'competences_acquises' => [
                    'Marketing digital',
                    'Communication',
                    'Gestion de projet',
                    'Créativité',
                    'Analyse marché'
                ],
            ],
            [
                'nom' => 'Nsimba',
                'prenom' => 'Patricia',
                'poste_occupe' => 'Responsable Ressources Humaines',
                'entreprise' => 'BRACONGO',
                'etablissement_origine' => 'Institut Supérieur de Commerce (ISC)',
                'temoignage' => '<p>En tant qu\'étudiante en Ressources Humaines, mon stage chez BRACONGO a été une immersion complète dans les enjeux RH d\'une grande entreprise. J\'ai eu la chance de participer aux processus de recrutement, à l\'élaboration de programmes de formation et à la gestion des relations sociales.</p><p>L\'équipe RH m\'a fait confiance et m\'a confié des responsabilités importantes. Cette expérience m\'a permis de comprendre l\'importance du facteur humain dans le succès d\'une entreprise et m\'a confortée dans mon choix de carrière.</p>',
                'citation_courte' => 'Chez BRACONGO, j\'ai appris que les ressources humaines sont au cœur de la performance d\'une entreprise.',
                'date_stage_debut' => '2023-06-01',
                'date_stage_fin' => '2023-12-01',
                'duree_stage' => '6 mois',
                'direction_stage' => 'Ressources Humaines',
                'actif' => true,
                'mis_en_avant' => true,
                'ordre_affichage' => 3,
                'note_experience' => 5,
                'competences_acquises' => [
                    'Gestion RH',
                    'Recrutement',
                    'Formation',
                    'Communication interpersonnelle',
                    'Législation du travail'
                ],
            ],
            [
                'nom' => 'Mbala',
                'prenom' => 'Jean-Claude',
                'poste_occupe' => 'Analyste Financier',
                'entreprise' => 'GROUPE FORREST INTERNATIONAL',
                'etablissement_origine' => 'Université de Kinshasa (UNIKIN)',
                'temoignage' => '<p>Mon stage de 4 mois au sein de la Direction Financière de BRACONGO a été une expérience enrichissante qui m\'a permis d\'appliquer mes connaissances théoriques dans un environnement professionnel exigeant. J\'ai travaillé sur l\'analyse financière, le contrôle de gestion et la préparation des reportings.</p><p>Ce qui m\'a particulièrement impressionné, c\'est la rigueur et la transparence dans la gestion financière. Les équipes m\'ont transmis leur expertise avec bienveillance et professionnalisme.</p>',
                'citation_courte' => 'BRACONGO m\'a donné une vision claire et rigoureuse de la gestion financière d\'entreprise.',
                'date_stage_debut' => '2023-01-15',
                'date_stage_fin' => '2023-05-15',
                'duree_stage' => '4 mois',
                'direction_stage' => 'Direction Financière',
                'actif' => true,
                'mis_en_avant' => false,
                'ordre_affichage' => 4,
                'note_experience' => 4,
                'competences_acquises' => [
                    'Analyse financière',
                    'Contrôle de gestion',
                    'Comptabilité',
                    'Excel avancé',
                    'Reporting'
                ],
            ],
            [
                'nom' => 'Tshilomba',
                'prenom' => 'Anny',
                'poste_occupe' => 'Responsable Logistique',
                'entreprise' => 'DHL EXPRESS',
                'etablissement_origine' => 'Institut Facultaire des Sciences de l\'Information et de la Communication (IFASIC)',
                'temoignage' => '<p>Mon stage dans l\'équipe logistique de BRACONGO m\'a ouvert les yeux sur la complexité et l\'importance de la chaîne d\'approvisionnement dans l\'industrie des boissons. J\'ai participé à l\'optimisation des flux, à la gestion des stocks et à la coordination avec les fournisseurs.</p><p>Cette expérience m\'a permis de développer ma capacité d\'adaptation et mon sens de l\'organisation dans un environnement dynamique où chaque détail compte.</p>',
                'citation_courte' => 'BRACONGO m\'a appris l\'art de la logistique et l\'importance de chaque maillon de la chaîne.',
                'date_stage_debut' => '2022-10-01',
                'date_stage_fin' => '2023-02-01',
                'duree_stage' => '4 mois',
                'direction_stage' => 'Logistique et Supply Chain',
                'actif' => true,
                'mis_en_avant' => false,
                'ordre_affichage' => 5,
                'note_experience' => 4,
                'competences_acquises' => [
                    'Gestion logistique',
                    'Supply chain',
                    'Négociation fournisseurs',
                    'Planification',
                    'Systèmes informatiques'
                ],
            ],
        ];

        foreach ($temoignages as $temoignageData) {
            Temoignage::create($temoignageData);
        }
    }
}
