<?php

namespace App\Livewire;

use App\Models\Candidature;
use App\Models\Message;
use Livewire\Component;

class Messagerie extends Component
{
    public $candidatureId;
    public $candidature;
    public $messages = [];
    public $newMessage = '';
    public $selectedCandidatureId = null;

    public function mount($candidatureId = null)
    {
        $candidat = auth('candidat')->user();

        if ($candidatureId) {
            // Vérifier que la candidature appartient au candidat
            $this->candidature = Candidature::where('id', $candidatureId)
                ->where('email', $candidat->email)
                ->first();

            if ($this->candidature) {
                $this->candidatureId = $candidatureId;
                $this->loadMessages();
            }
        }
    }

    public function selectCandidature($id)
    {
        $candidat = auth('candidat')->user();
        $this->candidature = Candidature::where('id', $id)
            ->where('email', $candidat->email)
            ->first();

        if ($this->candidature) {
            $this->candidatureId = $id;
            $this->selectedCandidatureId = $id;
            $this->loadMessages();
        }
    }

    public function loadMessages()
    {
        if (!$this->candidatureId) {
            return;
        }

        $this->messages = Message::forCandidature($this->candidatureId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        // Marquer les messages admin comme lus
        Message::forCandidature($this->candidatureId)
            ->where('sender_type', 'admin')
            ->unread()
            ->update(['lu_at' => now()]);
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:2000',
        ]);

        if (!$this->candidatureId) {
            return;
        }

        $candidat = auth('candidat')->user();

        Message::create([
            'candidature_id' => $this->candidatureId,
            'sender_type' => 'candidat',
            'sender_id' => $candidat->id,
            'contenu' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function getCandidaturesProperty()
    {
        $candidat = auth('candidat')->user();

        return Candidature::where('email', $candidat->email)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadCountProperty()
    {
        $candidat = auth('candidat')->user();
        $candidatureIds = Candidature::where('email', $candidat->email)->pluck('id');

        return Message::whereIn('candidature_id', $candidatureIds)
            ->where('sender_type', 'admin')
            ->unread()
            ->count();
    }

    public function render()
    {
        return view('livewire.messagerie', [
            'candidatures' => $this->candidatures,
            'unreadCount' => $this->unreadCount,
        ])->layout('layouts.simple');
    }
}
