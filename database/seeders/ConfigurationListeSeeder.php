<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfigurationListe;

class ConfigurationListeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Établissements
        $etablissements = [
            ['valeur' => 'unikin', 'libelle' => 'Université de Kinshasa (UNIKIN)'],
            ['valeur' => 'ulk', 'libelle' => 'Université Libre de Kinshasa (ULK)'],
            ['valeur' => 'upc', 'libelle' => 'Université Protestante du Congo (UPC)'],
            ['valeur' => 'isc', 'libelle' => 'Institut Supérieur de Commerce (ISC)'],
            ['valeur' => 'ista', 'libelle' => 'Institut Supérieur de Techniques Appliquées (ISTA)'],
            ['valeur' => 'esg', 'libelle' => 'École Supérieure de Gestion (ESG)'],
            ['valeur' => 'isp', 'libelle' => 'Institut Supérieur Pédagogique (ISP)'],
            ['valeur' => 'upn', 'libelle' => 'Université Pédagogique Nationale (UPN)'],
            ['valeur' => 'ifasic', 'libelle' => 'Institut Facultaire des Sciences de l\'Information et de la Communication (IFASIC)'],
            ['valeur' => 'esii', 'libelle' => 'École Supérieure des Ingénieurs Industriels (ESII)'],
            ['valeur' => 'autres', 'libelle' => 'Autres'],
        ];

        foreach ($etablissements as $index => $etablissement) {
            ConfigurationListe::create([
                'type_liste' => ConfigurationListe::TYPE_ETABLISSEMENT,
                'valeur' => $etablissement['valeur'],
                'libelle' => $etablissement['libelle'],
                'ordre' => $index + 1,
                'actif' => true,
            ]);
        }

        // Niveaux d'étude
        $niveaux = [
            ['valeur' => 'ecole_secondaire', 'libelle' => 'École Secondaire'],
            ['valeur' => 'bac_1', 'libelle' => 'Première année (Bac+1)'],
            ['valeur' => 'bac_2', 'libelle' => 'Deuxième année (Bac+2)'],
            ['valeur' => 'bac_3', 'libelle' => 'Licence/Graduat (Bac+3)'],
            ['valeur' => 'bac_4', 'libelle' => 'Maîtrise (Bac+4)'],
            ['valeur' => 'bac_5', 'libelle' => 'Master (Bac+5)'],
            ['valeur' => 'doctorat', 'libelle' => 'Doctorat/PhD'],
        ];

        foreach ($niveaux as $index => $niveau) {
            ConfigurationListe::create([
                'type_liste' => ConfigurationListe::TYPE_NIVEAU_ETUDE,
                'valeur' => $niveau['valeur'],
                'libelle' => $niveau['libelle'],
                'ordre' => $index + 1,
                'actif' => true,
            ]);
        }

        // Directions
        $directions = [
            ['valeur' => 'direction_generale', 'libelle' => 'Direction Générale'],
            ['valeur' => 'direction_financiere', 'libelle' => 'Direction Financière et Comptable'],
            ['valeur' => 'direction_rh', 'libelle' => 'Direction des Ressources Humaines'],
            ['valeur' => 'direction_marketing', 'libelle' => 'Direction Marketing et Communication'],
            ['valeur' => 'direction_commerciale', 'libelle' => 'Direction Commerciale'],
            ['valeur' => 'direction_production', 'libelle' => 'Direction de Production'],
            ['valeur' => 'direction_technique', 'libelle' => 'Direction Technique'],
            ['valeur' => 'direction_qualite', 'libelle' => 'Direction Qualité'],
            ['valeur' => 'direction_logistique', 'libelle' => 'Direction Logistique'],
            ['valeur' => 'direction_informatique', 'libelle' => 'Direction Informatique'],
            ['valeur' => 'direction_juridique', 'libelle' => 'Direction Juridique'],
            ['valeur' => 'direction_audit', 'libelle' => 'Direction Audit Interne'],
        ];

        foreach ($directions as $index => $direction) {
            ConfigurationListe::create([
                'type_liste' => ConfigurationListe::TYPE_DIRECTION,
                'valeur' => $direction['valeur'],
                'libelle' => $direction['libelle'],
                'ordre' => $index + 1,
                'actif' => true,
            ]);
        }

        // Postes
        $postes = [
            ['valeur' => 'assistant_commercial', 'libelle' => 'Stagiaire Assistant(e) Commercial(e)'],
            ['valeur' => 'assistant_marketing', 'libelle' => 'Stagiaire Assistant(e) Marketing'],
            ['valeur' => 'assistant_communication', 'libelle' => 'Stagiaire Assistant(e) Communication'],
            ['valeur' => 'assistant_comptable', 'libelle' => 'Stagiaire Assistant(e) Comptable'],
            ['valeur' => 'assistant_financier', 'libelle' => 'Stagiaire Assistant(e) Financier(ère)'],
            ['valeur' => 'assistant_rh', 'libelle' => 'Stagiaire Assistant(e) RH'],
            ['valeur' => 'assistant_production', 'libelle' => 'Stagiaire Assistant(e) Production'],
            ['valeur' => 'assistant_qualite', 'libelle' => 'Stagiaire Assistant(e) Qualité'],
            ['valeur' => 'assistant_logistique', 'libelle' => 'Stagiaire Assistant(e) Logistique'],
            ['valeur' => 'assistant_technique', 'libelle' => 'Stagiaire Assistant(e) Technique'],
            ['valeur' => 'assistant_informatique', 'libelle' => 'Stagiaire Assistant(e) Informatique'],
            ['valeur' => 'assistant_juridique', 'libelle' => 'Stagiaire Assistant(e) Juridique'],
            ['valeur' => 'assistant_audit', 'libelle' => 'Stagiaire Assistant(e) Audit'],
            ['valeur' => 'developpeur', 'libelle' => 'Stagiaire Développeur(euse)'],
            ['valeur' => 'analyste_donnees', 'libelle' => 'Stagiaire Analyste de Données'],
            ['valeur' => 'chef_projet_junior', 'libelle' => 'Stagiaire Chef de Projet Junior'],
            ['valeur' => 'assistant_direction', 'libelle' => 'Stagiaire Assistant(e) Direction'],
            ['valeur' => 'autre_poste', 'libelle' => 'Autre poste (à préciser)'],
        ];

        foreach ($postes as $index => $poste) {
            ConfigurationListe::create([
                'type_liste' => ConfigurationListe::TYPE_POSTE,
                'valeur' => $poste['valeur'],
                'libelle' => $poste['libelle'],
                'ordre' => $index + 1,
                'actif' => true,
            ]);
        }
    }
} 