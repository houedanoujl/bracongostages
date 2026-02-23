<?php

namespace App\Filament\Pages;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Message;
use App\Notifications\ReponseAdminNotification;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class Messagerie extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Messagerie';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?string $title = 'Boîte de réception';

    protected static ?string $slug = 'messagerie';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.messagerie';

    public ?int $selectedCandidatureId = null;

    public array $messages = [];

    public string $newMessage = '';

    public function mount(): void
    {
        // Auto-select first conversation with unread messages
        $first = Candidature::withCount([
            'messages as unread_count' => fn ($q) => $q->where('sender_type', 'candidat')->whereNull('lu_at'),
        ])
            ->whereHas('messages')
            ->orderByDesc('unread_count')
            ->first();

        if ($first) {
            $this->selectCandidature($first->id);
        }
    }

    public function selectCandidature(int $id): void
    {
        $this->selectedCandidatureId = $id;
        $this->newMessage = '';
        $this->loadMessages();
    }

    public function loadMessages(): void
    {
        if (! $this->selectedCandidatureId) {
            return;
        }

        $this->messages = Message::forCandidature($this->selectedCandidatureId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        // Marquer les messages du candidat comme lus
        Message::forCandidature($this->selectedCandidatureId)
            ->where('sender_type', 'candidat')
            ->unread()
            ->update(['lu_at' => now()]);

        $this->dispatch('messagesLoaded');
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:2000',
        ]);

        if (! $this->selectedCandidatureId) {
            return;
        }

        $message = Message::create([
            'candidature_id' => $this->selectedCandidatureId,
            'sender_type' => 'admin',
            'sender_id' => auth()->id(),
            'contenu' => $this->newMessage,
        ]);

        // Notifier le candidat par email
        try {
            $candidature = Candidature::find($this->selectedCandidatureId);
            if ($candidature) {
                $candidat = Candidat::where('email', $candidature->email)->first();
                if ($candidat) {
                    $candidat->notify(new ReponseAdminNotification($message, $candidature));
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification candidat réponse admin: ' . $e->getMessage());
        }

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function getConversationsProperty()
    {
        return Candidature::withCount([
            'messages as unread_count' => fn ($q) => $q->where('sender_type', 'candidat')->whereNull('lu_at'),
            'messages as total_count',
        ])
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->whereHas('messages')
            ->orderByDesc('unread_count')
            ->orderByDesc('updated_at')
            ->get();
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Message::where('sender_type', 'candidat')->whereNull('lu_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
