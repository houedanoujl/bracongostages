<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Opportunite;

class OpportunitiesGrid extends Component
{
    public $selectedCategory = 'all';
    public $viewMode = 'grid'; // grid ou list

    public $categories = [
        'all' => 'Tous les domaines',
        'technique' => 'Technique',
        'commercial' => 'Commercial',
        'administratif' => 'Administratif',
        'production' => 'Production',
        'finance' => 'Finance',
        'rh' => 'Ressources Humaines',
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
        $query = Opportunite::publiee()->ordonne();
        
        if ($this->selectedCategory !== 'all') {
            $query->where('categorie', $this->selectedCategory);
        }
        
        return $query->get();
    }

    public function render()
    {
        return view('livewire.opportunities-grid', [
            'filteredOpportunities' => $this->getFilteredOpportunities()
        ]);
    }
}