<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Temoignage;

class TemoignagesSection extends Component
{
    public function render()
    {
        $temoignages = Temoignage::actif()
            ->ordonne()
            ->limit(3)
            ->get();

        return view('livewire.temoignages-section', [
            'temoignages' => $temoignages
        ]);
    }
}
