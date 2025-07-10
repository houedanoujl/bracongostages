<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            // Statistiques pour la homepage
            [
                'cle' => 'stagiaires_par_an',
                'valeur' => '150',
                'type' => 'integer',
                'libelle' => 'Stagiaires par an',
                'description' => 'Nombre moyen de stagiaires accueillis chaque année',
                'groupe' => Configuration::GROUPE_STATISTIQUES,
                'type_champ' => Configuration::CHAMP_NUMBER,
                'ordre_affichage' => 1,
            ],
            [
                'cle' => 'directions_metiers',
                'valeur' => '8',
                'type' => 'integer',
                'libelle' => 'Directions métiers',
                'description' => 'Nombre de directions métiers disponibles pour les stages',
                'groupe' => Configuration::GROUPE_STATISTIQUES,
                'type_champ' => Configuration::CHAMP_NUMBER,
                'ordre_affichage' => 2,
            ],
            [
                'cle' => 'taux_satisfaction',
                'valeur' => '98',
                'type' => 'integer',
                'libelle' => '% Taux de satisfaction',
                'description' => 'Pourcentage de satisfaction des stagiaires (sur 100)',
                'groupe' => Configuration::GROUPE_STATISTIQUES,
                'type_champ' => Configuration::CHAMP_NUMBER,
                'ordre_affichage' => 3,
            ],
            [
                'cle' => 'annees_experience',
                'valeur' => '25',
                'type' => 'integer',
                'libelle' => 'Années d\'expérience',
                'description' => 'Nombre d\'années d\'expérience de BRACONGO dans l\'accueil de stagiaires',
                'groupe' => Configuration::GROUPE_STATISTIQUES,
                'type_champ' => Configuration::CHAMP_NUMBER,
                'ordre_affichage' => 4,
            ],
            [
                'cle' => 'etablissements_partenaires',
                'valeur' => '15',
                'type' => 'integer',
                'libelle' => 'Établissements partenaires',
                'description' => 'Nombre d\'établissements d\'enseignement supérieur partenaires',
                'groupe' => Configuration::GROUPE_STATISTIQUES,
                'type_champ' => Configuration::CHAMP_NUMBER,
                'ordre_affichage' => 5,
            ],
            [
                'cle' => 'taux_embauche',
                'valeur' => '35',
                'type' => 'integer',
                'libelle' => '% Taux d\'embauche',
                'description' => 'Pourcentage de stagiaires embauchés après leur stage',
                'groupe' => Configuration::GROUPE_STATISTIQUES,
                'type_champ' => Configuration::CHAMP_NUMBER,
                'ordre_affichage' => 6,
            ],

            // Informations de contact
            [
                'cle' => 'email_contact',
                'valeur' => 'stages@bracongo.cd',
                'type' => 'string',
                'libelle' => 'Email de contact stages',
                'description' => 'Adresse email pour les demandes de stage',
                'groupe' => Configuration::GROUPE_CONTACT,
                'type_champ' => Configuration::CHAMP_TEXT,
                'ordre_affichage' => 1,
            ],
            [
                'cle' => 'telephone_contact',
                'valeur' => '+243 81 555 0123',
                'type' => 'string',
                'libelle' => 'Téléphone de contact',
                'description' => 'Numéro de téléphone pour les renseignements',
                'groupe' => Configuration::GROUPE_CONTACT,
                'type_champ' => Configuration::CHAMP_TEXT,
                'ordre_affichage' => 2,
            ],
            [
                'cle' => 'adresse_entreprise',
                'valeur' => 'Avenue du Commerce, Kinshasa, République Démocratique du Congo',
                'type' => 'text',
                'libelle' => 'Adresse de l\'entreprise',
                'description' => 'Adresse complète du siège social',
                'groupe' => Configuration::GROUPE_CONTACT,
                'type_champ' => Configuration::CHAMP_TEXTAREA,
                'ordre_affichage' => 3,
            ],

            // Configuration SEO
            [
                'cle' => 'meta_description',
                'valeur' => 'Rejoignez BRACONGO pour un stage enrichissant dans l\'industrie des boissons. Découvrez nos opportunités de stage dans nos différentes directions métiers.',
                'type' => 'text',
                'libelle' => 'Meta description',
                'description' => 'Description SEO pour les moteurs de recherche',
                'groupe' => Configuration::GROUPE_SEO,
                'type_champ' => Configuration::CHAMP_TEXTAREA,
                'ordre_affichage' => 1,
            ],
            [
                'cle' => 'meta_keywords',
                'valeur' => 'stage, BRACONGO, formation, emploi, RDC, Congo, industrie, boissons, opportunité',
                'type' => 'string',
                'libelle' => 'Mots-clés SEO',
                'description' => 'Mots-clés pour le référencement (séparés par des virgules)',
                'groupe' => Configuration::GROUPE_SEO,
                'type_champ' => Configuration::CHAMP_TEXT,
                'ordre_affichage' => 2,
            ],

            // Configuration générale
            [
                'cle' => 'nom_entreprise',
                'valeur' => 'BRACONGO',
                'type' => 'string',
                'libelle' => 'Nom de l\'entreprise',
                'description' => 'Nom officiel de l\'entreprise',
                'groupe' => Configuration::GROUPE_GENERAL,
                'type_champ' => Configuration::CHAMP_TEXT,
                'ordre_affichage' => 1,
                'modifiable' => false,
            ],
            [
                'cle' => 'maintenance_mode',
                'valeur' => 'false',
                'type' => 'boolean',
                'libelle' => 'Mode maintenance',
                'description' => 'Activer le mode maintenance du site',
                'groupe' => Configuration::GROUPE_GENERAL,
                'type_champ' => Configuration::CHAMP_BOOLEAN,
                'ordre_affichage' => 2,
            ],
            [
                'cle' => 'candidatures_ouvertes',
                'valeur' => 'true',
                'type' => 'boolean',
                'libelle' => 'Candidatures ouvertes',
                'description' => 'Permettre les nouvelles candidatures',
                'groupe' => Configuration::GROUPE_GENERAL,
                'type_champ' => Configuration::CHAMP_BOOLEAN,
                'ordre_affichage' => 3,
            ],
        ];

        foreach ($configurations as $configData) {
            Configuration::updateOrCreate(
                ['cle' => $configData['cle']],
                $configData
            );
        }
    }
}
