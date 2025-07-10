<?php

namespace App\Livewire;

use Livewire\Component;

class OpportunitiesGrid extends Component
{
    public $selectedCategory = 'all';
    public $viewMode = 'grid'; // grid ou list
    
    public $opportunities = [
        [
            'id' => 'production',
            'title' => 'Production & Qualité',
            'description' => 'Participez aux processus de production et de contrôle qualité. Apprenez les standards internationaux et les technologies modernes de brassage.',
            'icon' => '🏭',
            'duration' => '3-6 mois',
            'level' => 'Bac+2/3',
            'category' => 'technique',
            'skills' => ['Contrôle qualité', 'Processus industriels', 'Normes ISO'],
            'available_spots' => 5,
        ],
        [
            'id' => 'marketing',
            'title' => 'Marketing & Commercial',
            'description' => 'Développez vos compétences en marketing digital, stratégie commerciale et gestion de marque dans un environnement dynamique.',
            'icon' => '📊',
            'duration' => '3-6 mois',
            'level' => 'Bac+3/4',
            'category' => 'commercial',
            'skills' => ['Marketing digital', 'Stratégie commerciale', 'Gestion de marque'],
            'available_spots' => 3,
        ],
        [
            'id' => 'technique',
            'title' => 'Technique & Maintenance',
            'description' => 'Maîtrisez la maintenance industrielle, l\'automatisation et la gestion des équipements de pointe dans l\'industrie brassicole.',
            'icon' => '⚙️',
            'duration' => '4-6 mois',
            'level' => 'Bac+2/3',
            'category' => 'technique',
            'skills' => ['Maintenance industrielle', 'Automatisation', 'Électromécanique'],
            'available_spots' => 4,
        ],
        [
            'id' => 'rh',
            'title' => 'Ressources Humaines',
            'description' => 'Découvrez la gestion des talents, le recrutement et le développement organisationnel dans une entreprise de référence.',
            'icon' => '👥',
            'duration' => '3-4 mois',
            'level' => 'Bac+3/4',
            'category' => 'administratif',
            'skills' => ['Recrutement', 'Gestion des talents', 'Formation'],
            'available_spots' => 2,
        ],
        [
            'id' => 'finance',
            'title' => 'Finance & Comptabilité',
            'description' => 'Approfondissez vos connaissances en gestion financière, contrôle de gestion et analyse des performances dans un contexte international.',
            'icon' => '💼',
            'duration' => '3-6 mois',
            'level' => 'Bac+3/5',
            'category' => 'administratif',
            'skills' => ['Comptabilité', 'Contrôle de gestion', 'Analyse financière'],
            'available_spots' => 3,
        ],
        [
            'id' => 'it',
            'title' => 'IT & Transformation Digitale',
            'description' => 'Participez à la digitalisation des processus et au développement des solutions technologiques innovantes.',
            'icon' => '💻',
            'duration' => '4-6 mois',
            'level' => 'Bac+3/5',
            'category' => 'technique',
            'skills' => ['Développement', 'Systèmes d\'information', 'Digital'],
            'available_spots' => 3,
        ],
    ];

    public $categories = [
        'all' => 'Tous les domaines',
        'technique' => 'Technique',
        'commercial' => 'Commercial',
        'administratif' => 'Administratif',
    ];

    public function filterByCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function getFilteredOpportunities()
    {
        if ($this->selectedCategory === 'all') {
            return $this->opportunities;
        }
        
        return array_filter($this->opportunities, function($opportunity) {
            return $opportunity['category'] === $this->selectedCategory;
        });
    }

    public function render()
    {
        return view('livewire.opportunities-grid', [
            'filteredOpportunities' => $this->getFilteredOpportunities()
        ]);
    }
}