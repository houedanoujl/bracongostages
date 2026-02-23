<?php

namespace App\Console\Commands;

use App\Models\Candidat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\DigestMessagesAdminNotification;
use App\Notifications\DigestMessagesCandidatNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnvoyerDigestMessages extends Command
{
    protected $signature = 'messages:digest';

    protected $description = 'Envoie un résumé quotidien des messages non lus aux admins et candidats';

    public function handle(): int
    {
        $this->info('Envoi des digests de messages...');

        $this->envoyerDigestAdmin();
        $this->envoyerDigestCandidats();

        $this->info('Digests envoyés avec succès.');

        return Command::SUCCESS;
    }

    protected function envoyerDigestAdmin(): void
    {
        // Messages non lus des candidats vers l'admin
        $messagesNonLus = Message::with('candidature')
            ->where('sender_type', 'candidat')
            ->whereNull('lu_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($messagesNonLus->isEmpty()) {
            $this->info('  Admin : aucun message non lu.');
            return;
        }

        // Envoyer à tous les admins actifs
        $admins = User::where('is_active', true)->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new DigestMessagesAdminNotification($messagesNonLus));
                $this->info("  Digest admin envoyé à {$admin->email} ({$messagesNonLus->count()} messages)");
            } catch (\Exception $e) {
                Log::error("Erreur envoi digest admin à {$admin->email}: " . $e->getMessage());
                $this->error("  Erreur pour {$admin->email}: " . $e->getMessage());
            }
        }
    }

    protected function envoyerDigestCandidats(): void
    {
        // Messages non lus de l'admin vers les candidats, groupés par candidat
        $messagesNonLus = Message::with('candidature')
            ->where('sender_type', 'admin')
            ->whereNull('lu_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($messagesNonLus->isEmpty()) {
            $this->info('  Candidats : aucun message non lu.');
            return;
        }

        // Grouper par email du candidat (via candidature)
        $parCandidat = $messagesNonLus->groupBy(function ($message) {
            return $message->candidature?->email;
        })->filter(function ($messages, $email) {
            return !empty($email);
        });

        foreach ($parCandidat as $email => $messages) {
            $candidat = Candidat::where('email', $email)->first();

            if (!$candidat) {
                continue;
            }

            try {
                $candidat->notify(new DigestMessagesCandidatNotification(
                    $messages,
                    $candidat->prenom ?? 'Candidat'
                ));
                $this->info("  Digest candidat envoyé à {$email} ({$messages->count()} messages)");
            } catch (\Exception $e) {
                Log::error("Erreur envoi digest candidat à {$email}: " . $e->getMessage());
                $this->error("  Erreur pour {$email}: " . $e->getMessage());
            }
        }
    }
}
