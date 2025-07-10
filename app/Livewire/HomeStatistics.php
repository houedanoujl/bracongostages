<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidature;
use App\Models\Configuration;
use App\Models\Temoignage;
use Illuminate\Support\Facades\Cache;

class HomeStatistics extends Component
{
    public $statistics;
    public $topEtablissements;
    public $temoignages;

    public function mount()
    {
        $this->loadStatistics();
        $this->loadTopEtablissements();
        $this->loadTemoignages();
    }

    private function loadStatistics()
    {
        // Récupérer les statistiques configurables
        $configurationsStats = Configuration::getStatistiques();
        
        // Calculer les statistiques dynamiques basées sur les données réelles
        $totalCandidatures = Candidature::count();
        $stagesValides = Candidature::whereIn('statut', ['validé', 'approuvé'])->count();
        $enCoursTraitement = Candidature::whereIn('statut', ['en_attente', 'en_cours'])->count();

        // Combiner les configurations avec les données dynamiques
        $this->statistics = [
            [
                'label' => 'Total des candidatures',
                'value' => $totalCandidatures,
                'icon' => 'users',
                'color' => 'blue'
            ],
            [
                'label' => 'Stages validés',
                'value' => $stagesValides,
                'icon' => 'check-circle',
                'color' => 'green'
            ],
            [
                'label' => 'En cours de traitement',
                'value' => $enCoursTraitement,
                'icon' => 'clock',
                'color' => 'yellow'
            ],
            [
                'label' => 'Stagiaires par an',
                'value' => $configurationsStats['stagiaires_par_an'] ?? 150,
                'icon' => 'academic-cap',
                'color' => 'purple'
            ],
            [
                'label' => 'Directions métiers',
                'value' => $configurationsStats['directions_metiers'] ?? 8,
                'icon' => 'building-office',
                'color' => 'indigo'
            ],
            [
                'label' => '% Taux de satisfaction',
                'value' => $configurationsStats['taux_satisfaction'] ?? 98,
                'icon' => 'heart',
                'color' => 'red'
            ],
            [
                'label' => 'Années d\'expérience',
                'value' => $configurationsStats['annees_experience'] ?? 25,
                'icon' => 'star',
                'color' => 'orange'
            ],
            [
                'label' => 'Établissements partenaires',
                'value' => $configurationsStats['etablissements_partenaires'] ?? 15,
                'icon' => 'building-library',
                'color' => 'cyan'
            ]
        ];
    }

    private function loadTopEtablissements()
    {
        // Utiliser cache pour éviter de recalculer constamment
        $this->topEtablissements = Cache::remember('top_etablissements', 3600, function () {
            return Candidature::select('etablissement')
                ->selectRaw('COUNT(*) as count')
                ->whereNotNull('etablissement')
                ->where('etablissement', '!=', '')
                ->groupBy('etablissement')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nom' => $item->etablissement,
                        'count' => $item->count
                    ];
                })
                ->toArray();
        });
    }

    private function loadTemoignages()
    {
        // Charger les témoignages mis en avant pour la homepage
        $this->temoignages = Cache::remember('temoignages_homepage', 1800, function () {
            return Temoignage::pourHomepage(3)->map(function ($temoignage) {
                return [
                    'nom_complet' => $temoignage->nom_complet,
                    'poste_occupe' => $temoignage->poste_occupe,
                    'entreprise' => $temoignage->entreprise,
                    'etablissement_origine' => $temoignage->etablissement_origine,
                    'citation_courte' => $temoignage->citation_courte,
                    'note_experience' => $temoignage->note_experience,
                    'etoiles' => $temoignage->etoiles,
                    'photo_url' => $temoignage->photo_url,
                    'direction_stage' => $temoignage->direction_stage,
                    'duree_stage_formattee' => $temoignage->duree_stage_formattee,
                ];
            })->toArray();
        });
    }

    public function refreshStatistics()
    {
        // Vider le cache et recharger les données
        Cache::forget('top_etablissements');
        Cache::forget('temoignages_homepage');
        Configuration::clearCache();
        
        $this->loadStatistics();
        $this->loadTopEtablissements();
        $this->loadTemoignages();
        
        $this->dispatch('statistics-updated');
    }

    public function render()
    {
        return view('livewire.home-statistics');
    }
} 