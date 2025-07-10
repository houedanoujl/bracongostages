<?php

namespace App\Livewire;

use App\Models\Candidature;
use Livewire\Component;

class HeroSearch extends Component
{
    public $searchTerm = '';
    public $selectedDomain = '';
    public $showSuggestions = false;
    
    public $domains = [
        'production' => 'Production & Qualité',
        'marketing' => 'Marketing & Commercial',
        'technique' => 'Technique & Maintenance',
        'rh' => 'Ressources Humaines',
        'finance' => 'Finance & Comptabilité',
        'it' => 'IT & Digital',
    ];

    public function updatedSearchTerm()
    {
        $this->showSuggestions = !empty($this->searchTerm);
    }

    public function search()
    {
        $params = [];
        
        if (!empty($this->searchTerm)) {
            $params['search'] = $this->searchTerm;
        }
        
        if (!empty($this->selectedDomain)) {
            $params['domain'] = $this->selectedDomain;
        }
        
        $url = '/candidature';
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return redirect($url);
    }

    public function selectDomain($domain)
    {
        $this->selectedDomain = $domain;
        $this->search();
    }

    public function hideSuggestions()
    {
        $this->showSuggestions = false;
    }

    public function render()
    {
        return view('livewire.hero-search');
    }
}