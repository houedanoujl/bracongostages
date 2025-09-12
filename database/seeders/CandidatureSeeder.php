<?php

namespace Database\Seeders;

use App\Models\Candidature;
use App\Models\Document;
use App\Models\Evaluation;
use App\Enums\StatutCandidature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CandidatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le dossier pour les documents de demo
        Storage::makeDirectory('documents/demo');

        $candidatures = [
            [
                'nom' => 'Nkomo',
                'prenom' => 'Éric',
                'telephone' => '+243 81 567 8901',
                'email' => 'eric.nkomo@student.unikin.cd',
                'etablissement' => 'Université de Kinshasa (UNIKIN)',
                'niveau_etude' => 'Bac+4',
                'faculte' => 'Faculté des Sciences Économiques et de Gestion',
                'objectif_stage' => 'Acquérir une expérience pratique en gestion des ressources humaines dans l\'industrie brassicole. Je souhaite apprendre les processus de recrutement, formation et gestion des performances spécifiques au secteur BRACONGO.',
                'directions_souhaitees' => ['Direction des Ressources Humaines', 'Direction Générale', 'Direction de Production'],
                'projets_souhaites' => 'Participation à l\'optimisation des processus RH, développement d\'un système d\'évaluation des performances pour les équipes de production, et création d\'un programme d\'intégration pour nouveaux employés.',
                'competences_souhaitees' => 'Gestion RH, Recrutement dans l\'industrie, Formation du personnel, Évaluation des performances, Management d\'équipes de production',
                'periode_debut_souhaitee' => now()->addDays(15),
                'periode_fin_souhaitee' => now()->addDays(105),
                'statut' => StatutCandidature::VALIDE,
                'date_debut_stage' => now()->addDays(15),
                'date_fin_stage' => now()->addDays(105),
            ],
            [
                'nom' => 'Mbala',
                'prenom' => 'Prisca',
                'telephone' => '+243 82 678 9012',
                'email' => 'prisca.mbala@ulk.cd',
                'etablissement' => 'Université Libre de Kinshasa (ULK)',
                'niveau_etude' => 'Bac+3',
                'faculte' => 'Faculté des Sciences de l\'Information et Communication',
                'objectif_stage' => 'Développer mes compétences en marketing digital et communication d\'entreprise dans le secteur des boissons. Comprendre les stratégies de marque BRACONGO et participer aux campagnes publicitaires locales.',
                'directions_souhaitees' => ['Direction Marketing et Communication', 'Direction Commerciale', 'Direction Générale'],
                'projets_souhaites' => 'Création de contenus pour les réseaux sociaux de BRACONGO, analyse des performances des campagnes marketing, participation au lancement de nouveaux produits, et développement d\'une stratégie de communication pour le marché de Kinshasa.',
                'competences_souhaitees' => 'Marketing digital, Communication de marque, Réseaux sociaux, Publicité, Analyse de marché dans les boissons',
                'periode_debut_souhaitee' => now()->addDays(30),
                'periode_fin_souhaitee' => now()->addDays(120),
                'statut' => StatutCandidature::ATTENTE_AFFECTATION,
            ],
            [
                'nom' => 'Kabongo',
                'prenom' => 'Didier',
                'telephone' => '+243 83 789 0123',
                'email' => null,
                'etablissement' => 'Institut Supérieur de Techniques Appliquées (ISTA)',
                'niveau_etude' => 'Bac+5',
                'faculte' => 'Département Génie Informatique',
                'objectif_stage' => 'Mettre en pratique mes connaissances en développement logiciel et contribuer à la modernisation du système informatique de BRACONGO. Travailler sur l\'automatisation des processus de production et la gestion des stocks.',
                'directions_souhaitees' => ['Direction Informatique', 'Direction Technique', 'Direction de Production'],
                'projets_souhaites' => 'Développement d\'une application web pour le suivi de la production en temps réel, optimisation du système de gestion des stocks, création d\'un tableau de bord pour le contrôle qualité des bières.',
                'competences_souhaitees' => 'Développement web, Base de données, Systèmes de gestion, Automatisation industrielle, Sécurité informatique',
                'periode_debut_souhaitee' => now()->addDays(45),
                'periode_fin_souhaitee' => now()->addDays(135),
                'statut' => StatutCandidature::ATTENTE_TEST,
            ],
            [
                'nom' => 'Tshiala',
                'prenom' => 'Ornella',
                'telephone' => '+243 84 890 1234',
                'email' => 'ornella.tshiala@isc.cd',
                'etablissement' => 'Institut Supérieur de Commerce (ISC)',
                'niveau_etude' => 'Bac+3',
                'faculte' => 'Faculté de Commerce et Finance',
                'objectif_stage' => 'Approfondir mes connaissances en comptabilité et gestion financière d\'entreprise dans le secteur brassicole. Comprendre la gestion des coûts de production et l\'analyse de rentabilité des produits BRACONGO.',
                'directions_souhaitees' => ['Direction Financière et Comptable', 'Direction Audit Interne', 'Direction Générale'],
                'projets_souhaites' => 'Analyse financière des coûts de production de la bière, participation aux audits internes des processus financiers, développement d\'un système de suivi budgétaire pour les départements.',
                'competences_souhaitees' => 'Comptabilité analytique, Analyse financière, Audit interne, Contrôle de gestion, Gestion budgétaire dans l\'industrie',
                'periode_debut_souhaitee' => now()->addDays(20),
                'periode_fin_souhaitee' => now()->addDays(110),
                'statut' => StatutCandidature::ANALYSE_DOSSIER,
            ],
            [
                'nom' => 'Mputu',
                'prenom' => 'Serge',
                'telephone' => '+243 85 901 2345',
                'email' => 'serge.mputu@esii.cd',
                'etablissement' => 'École Supérieure des Ingénieurs Industriels (ESII)',
                'niveau_etude' => 'Bac+4',
                'faculte' => 'Département Génie Industriel',
                'objectif_stage' => 'Comprendre les processus de production industrielle dans l\'industrie brassicole. Participer à l\'optimisation des lignes de production de bière et au contrôle qualité des produits BRACONGO.',
                'directions_souhaitees' => ['Direction de Production', 'Direction Qualité', 'Direction Technique'],
                'projets_souhaites' => 'Optimisation des lignes de production de bière Primus et Turbo King, amélioration du contrôle qualité, réduction des pertes de production, mise en place d\'indicateurs de performance industrielle.',
                'competences_souhaitees' => 'Génie industriel, Optimisation des processus, Contrôle qualité brassicole, Maintenance préventive, Lean manufacturing',
                'periode_debut_souhaitee' => now()->addDays(25),
                'periode_fin_souhaitee' => now()->addDays(115),
                'statut' => StatutCandidature::NON_TRAITE,
            ],
            [
                'nom' => 'Lubaki',
                'prenom' => 'Chantal',
                'telephone' => '+243 86 012 3456',
                'email' => 'chantal.lubaki@upc.cd',
                'etablissement' => 'Université Protestante du Congo (UPC)',
                'niveau_etude' => 'Bac+2',
                'faculte' => 'Faculté de Droit',
                'objectif_stage' => 'Découvrir les aspects juridiques de la gestion d\'entreprise brassicole. Comprendre les contrats commerciaux, les réglementations du secteur et les questions de propriété intellectuelle.',
                'directions_souhaitees' => ['Direction Juridique', 'Direction Générale', 'Direction Commerciale'],
                'projets_souhaites' => 'Étude des contrats de distribution de BRACONGO, analyse des réglementations sur les boissons alcoolisées en RDC, assistance dans la gestion des litiges commerciaux.',
                'competences_souhaitees' => 'Droit des affaires, Droit commercial, Réglementation des boissons, Propriété intellectuelle, Rédaction juridique',
                'periode_debut_souhaitee' => now()->addDays(40),
                'periode_fin_souhaitee' => now()->addDays(130),
                'statut' => StatutCandidature::REJETE,
                'motif_rejet' => 'Niveau d\'étude insuffisant pour les missions juridiques complexes proposées. Nous encourageons la candidate à repostuler après l\'obtention de sa licence en droit.',
            ],
            [
                'nom' => 'Nzeza',
                'prenom' => 'Jonathan',
                'telephone' => '+243 87 123 4567',
                'email' => 'jonathan.nzeza@student.unikin.cd',
                'etablissement' => 'Université de Kinshasa (UNIKIN)',
                'niveau_etude' => 'Bac+5',
                'faculte' => 'Faculté de Médecine - Santé Publique',
                'objectif_stage' => 'Appliquer mes connaissances en santé publique et sécurité alimentaire dans l\'industrie brassicole. Participer aux programmes de santé et sécurité au travail chez BRACONGO.',
                'directions_souhaitees' => ['Direction Qualité', 'Direction des Ressources Humaines', 'Direction de Production'],
                'projets_souhaites' => 'Évaluation des risques sanitaires dans la production, amélioration des protocoles de sécurité alimentaire, formation du personnel aux bonnes pratiques d\'hygiène, développement d\'un programme de santé au travail.',
                'competences_souhaitees' => 'Santé publique, Sécurité alimentaire, Hygiène industrielle, HACCP, Prévention des risques professionnels',
                'periode_debut_souhaitee' => now()->addDays(35),
                'periode_fin_souhaitee' => now()->addDays(125),
                'statut' => StatutCandidature::ATTENTE_RESULTATS,
            ],
            [
                'nom' => 'Mukendi',
                'prenom' => 'Béatrice',
                'telephone' => '+243 88 234 5678',
                'email' => 'beatrice.mukendi@esii.cd',
                'etablissement' => 'École Supérieure des Ingénieurs Industriels (ESII)',
                'niveau_etude' => 'Bac+3',
                'faculte' => 'Département Génie Chimique',
                'objectif_stage' => 'Comprendre les processus chimiques et biochimiques de la production de bière. Participer au développement de nouveaux produits et à l\'amélioration des recettes existantes.',
                'directions_souhaitees' => ['Direction Technique', 'Direction de Production', 'Direction Qualité'],
                'projets_souhaites' => 'Analyse des processus de fermentation, optimisation des recettes de bière, participation au développement d\'une nouvelle variété de bière locale, contrôle qualité chimique.',
                'competences_souhaitees' => 'Génie chimique, Biochimie, Procédés de fermentation, Analyse chimique, Développement produit',
                'periode_debut_souhaitee' => now()->addDays(50),
                'periode_fin_souhaitee' => now()->addDays(140),
                'statut' => StatutCandidature::ATTENTE_AFFECTATION,
            ],
            [
                'nom' => 'Kasongo',
                'prenom' => 'Patrick',
                'telephone' => '+243 89 345 6789',
                'email' => 'patrick.kasongo@ista.cd',
                'etablissement' => 'Institut Supérieur de Techniques Appliquées (ISTA)',
                'niveau_etude' => 'Bac+4',
                'faculte' => 'Département Électromécanique',
                'objectif_stage' => 'Appliquer mes connaissances en maintenance industrielle et automatisation dans l\'industrie brassicole. Participer à la maintenance préventive des équipements de production.',
                'directions_souhaitees' => ['Direction Technique', 'Direction de Production', 'Direction Logistique'],
                'projets_souhaites' => 'Mise en place d\'un programme de maintenance préventive, automatisation de certains processus de production, optimisation de la consommation énergétique des équipements.',
                'competences_souhaitees' => 'Maintenance industrielle, Automatisation, Électromécanique, Gestion énergétique, Dépannage d\'équipements',
                'periode_debut_souhaitee' => now()->addDays(28),
                'periode_fin_souhaitee' => now()->addDays(118),
                'statut' => StatutCandidature::ANALYSE_DOSSIER,
            ],
            [
                'nom' => 'Mwanza',
                'prenom' => 'Grâce',
                'telephone' => '+243 90 456 7890',
                'email' => 'grace.mwanza@ulk.cd',
                'etablissement' => 'Université Libre de Kinshasa (ULK)',
                'niveau_etude' => 'Bac+4',
                'faculte' => 'Faculté de Psychologie et Sciences de l\'Éducation',
                'objectif_stage' => 'Appliquer la psychologie du travail dans l\'amélioration du bien-être des employés BRACONGO. Développer des programmes de formation et de motivation pour les équipes.',
                'directions_souhaitees' => ['Direction des Ressources Humaines', 'Direction Générale', 'Direction de Production'],
                'projets_souhaites' => 'Évaluation du climat organisationnel, développement de programmes de formation comportementale, création d\'ateliers de gestion du stress pour les équipes de production.',
                'competences_souhaitees' => 'Psychologie du travail, Formation comportementale, Évaluation organisationnelle, Gestion du stress, Team building',
                'periode_debut_souhaitee' => now()->addDays(42),
                'periode_fin_souhaitee' => now()->addDays(132),
                'statut' => StatutCandidature::VALIDE,
                'date_debut_stage' => now()->addDays(42),
                'date_fin_stage' => now()->addDays(132),
            ],
        ];

        foreach ($candidatures as $candidatureData) {
            $candidature = Candidature::create($candidatureData);

            // Créer des documents factices pour chaque candidature
            $this->createDemoDocuments($candidature);

            // Créer une évaluation pour les candidatures validées
            if ($candidature->statut === StatutCandidature::VALIDE) {
                $this->createDemoEvaluation($candidature);
            }
        }

        $this->command->info('10 candidatures de démonstration BRACONGO créées avec succès.');
    }

    /**
     * Créer des documents de démonstration
     */
    private function createDemoDocuments(Candidature $candidature): void
    {
        $documents = [
            [
                'type_document' => 'cv',
                'nom_original' => "CV_{$candidature->prenom}_{$candidature->nom}_BRACONGO.pdf",
                'chemin_fichier' => "documents/demo/cv_{$candidature->id}.pdf",
                'taille_fichier' => rand(200000, 500000),
                'mime_type' => 'application/pdf',
            ],
            [
                'type_document' => 'lettre_motivation',
                'nom_original' => "Lettre_Motivation_{$candidature->prenom}_{$candidature->nom}_Stage_BRACONGO.pdf",
                'chemin_fichier' => "documents/demo/lettre_{$candidature->id}.pdf",
                'taille_fichier' => rand(100000, 300000),
                'mime_type' => 'application/pdf',
            ],
            [
                'type_document' => 'piece_identite',
                'nom_original' => "Carte_Identite_{$candidature->prenom}_{$candidature->nom}.jpg",
                'chemin_fichier' => "documents/demo/id_{$candidature->id}.jpg",
                'taille_fichier' => rand(150000, 400000),
                'mime_type' => 'image/jpeg',
            ],
        ];

        // Ajouter parfois des documents supplémentaires
        if (rand(1, 3) === 1) {
            $documents[] = [
                'type_document' => 'diplome',
                'nom_original' => "Diplome_{$candidature->prenom}_{$candidature->nom}.pdf",
                'chemin_fichier' => "documents/demo/diplome_{$candidature->id}.pdf",
                'taille_fichier' => rand(200000, 600000),
                'mime_type' => 'application/pdf',
            ];
        }

        if (rand(1, 4) === 1) {
            $documents[] = [
                'type_document' => 'lettre_recommandation',
                'nom_original' => "Lettre_Recommandation_{$candidature->prenom}_{$candidature->nom}.pdf",
                'chemin_fichier' => "documents/demo/recommandation_{$candidature->id}.pdf",
                'taille_fichier' => rand(100000, 250000),
                'mime_type' => 'application/pdf',
            ];
        }

        foreach ($documents as $documentData) {
            $documentData['candidature_id'] = $candidature->id;
            
            // Créer un fichier fictif avec contenu réaliste
            $contenu = $this->genererContenuDocument($documentData['type_document'], $candidature);
            Storage::put($documentData['chemin_fichier'], $contenu);
            
            Document::create($documentData);
        }
    }

    /**
     * Générer un contenu réaliste pour les documents
     */
    private function genererContenuDocument(string $type, Candidature $candidature): string
    {
        $nom_complet = "{$candidature->prenom} {$candidature->nom}";
        
        return match($type) {
            'cv' => "CURRICULUM VITAE - {$nom_complet}\n\nCandidat(e) pour un stage chez BRACONGO\nÉtablissement: {$candidature->etablissement}\nNiveau: {$candidature->niveau_etude}\nFaculté: {$candidature->faculte}\n\nCe document est généré automatiquement pour la démonstration de la plateforme BRACONGO Stages.",
            
            'lettre_motivation' => "LETTRE DE MOTIVATION\n\nObjet: Candidature pour un stage chez BRACONGO\n\nMadame, Monsieur,\n\nJe soussigné(e) {$nom_complet}, étudiant(e) en {$candidature->niveau_etude} à {$candidature->etablissement}, souhaite effectuer un stage au sein de votre prestigieuse entreprise BRACONGO.\n\n{$candidature->objectif_stage}\n\nCordialement,\n{$nom_complet}",
            
            'piece_identite' => "DOCUMENT D'IDENTITÉ - {$nom_complet}\nTéléphone: {$candidature->telephone}\nEmail: {$candidature->email}\n\nDocument généré pour la démonstration BRACONGO Stages.",
            
            'diplome' => "DIPLÔME/ATTESTATION\n\n{$nom_complet}\n{$candidature->etablissement}\n{$candidature->faculte}\nNiveau: {$candidature->niveau_etude}\n\nDocument de démonstration BRACONGO Stages.",
            
            'lettre_recommandation' => "LETTRE DE RECOMMANDATION\n\nJe recommande {$nom_complet} pour un stage chez BRACONGO.\nÉtudiant(e) sérieux(se) et motivé(e).\n\nProfesseur référent\n{$candidature->etablissement}",
            
            default => "Document de démonstration BRACONGO Stages - Type: {$type}\nCandidat: {$nom_complet}"
        };
    }

    /**
     * Créer une évaluation de démonstration
     */
    private function createDemoEvaluation(Candidature $candidature): void
    {
        $commentaires = [
            "Excellente expérience de stage chez BRACONGO. L'équipe de la {$candidature->directions_souhaitees[0]} était très accueillante et les missions dans l'industrie brassicole étaient variées et enrichissantes. J'ai beaucoup appris sur les processus de production de la bière.",
            "Stage très formateur dans l'univers BRACONGO. Les responsables m'ont bien encadré et j'ai pu participer à des projets concrets dans la production de bières. L'ambiance de travail était excellente et j'ai développé mes compétences techniques.",
            "Très satisfait de mon passage chez BRACONGO. L'entreprise offre un environnement d'apprentissage exceptionnel dans le secteur brassicole. Les équipes sont professionnelles et bienveillantes. Je recommande vivement BRACONGO pour les stages.",
        ];
        
        $suggestions = [
            "Peut-être prévoir plus de formations techniques au début du stage sur les spécificités de la production brassicole.",
            "Il serait intéressant d'organiser des visites dans toutes les directions de BRACONGO pour avoir une vision globale.",
            "Proposer des sessions de formation sur l'histoire et les valeurs de BRACONGO serait un plus.",
            "Organiser des rencontres avec des anciens stagiaires devenus employés BRACONGO pourrait être motivant.",
        ];

        $recommandations = ['oui', 'peut_etre', 'non'];

        \App\Models\Evaluation::create([
            'candidature_id' => $candidature->id,
            'satisfaction_generale' => rand(3, 5),
            'recommandation' => $recommandations[array_rand($recommandations)],
            'suggestions_amelioration' => $suggestions[array_rand($suggestions)],
            'commentaire_libre' => $commentaires[array_rand($commentaires)],
        ]);
    }
} 