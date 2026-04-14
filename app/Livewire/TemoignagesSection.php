<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Evaluation;

class TemoignagesSection extends Component
{
    public function render()
    {
        $retours = Evaluation::pourHomepage(6);

        return view('livewire.temoignages-section', [
            'retours' => $retours,
        ]);
    }
}
