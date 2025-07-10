<?php

namespace App\Livewire;

use Livewire\Component;

class TestModal extends Component
{
    public $showModal = false;

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.test-modal');
    }
}