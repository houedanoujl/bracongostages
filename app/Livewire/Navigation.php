<?php

namespace App\Livewire;

use Livewire\Component;

class Navigation extends Component
{
    public $currentPage = 'home';
    public $mobileMenuOpen = false;

    public function mount()
    {
        $this->currentPage = request()->route()->getName() ?? 'home';
    }

    public function toggleMobileMenu()
    {
        $this->mobileMenuOpen = !$this->mobileMenuOpen;
    }

    public function closeMobileMenu()
    {
        $this->mobileMenuOpen = false;
    }

    public function render()
    {
        return view('livewire.navigation');
    }
}