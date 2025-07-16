<?php

namespace App\Console\Commands;

use App\Models\Candidature;
use App\Notifications\FinStageNotification;
use App\Enums\StatutCandidature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnvoyerNotificationsFinStage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stages:notifier-fin-stage {--jours=1 : Nombre de jours après la fin du stage pour envoyer la notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer les notifications de fin de stage aux stagiaires';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jours = $this->option('jours');
        $dateLimite = Carbon::now()->subDays($jours);

        $this->info("Recherche des stages terminés il y a {$jours} jour(s)...");

        // Récupérer les candidatures validées dont le stage est terminé
        $candidatures = Candidature::where('statut', StatutCandidature::VALIDE)
            ->whereNotNull('date_fin_stage')
            ->where('date_fin_stage', '<=', $dateLimite)
            ->whereDoesntHave('evaluation') // Pas encore évalué
            ->get();

        if ($candidatures->isEmpty()) {
            $this->info('Aucune candidature trouvée pour l\'envoi de notification de fin de stage.');
            return 0;
        }

        $this->info("Trouvé {$candidatures->count()} candidature(s) à notifier.");

        $envoyees = 0;
        $erreurs = 0;

        foreach ($candidatures as $candidature) {
            try {
                // Créer un objet notifiable temporaire avec l'email du candidat
                $notifiable = new class($candidature->email) {
                    public function __construct(public string $email) {}
                    public function routeNotificationForMail() {
                        return $this->email;
                    }
                };

                // Envoyer la notification
                $notifiable->notify(new FinStageNotification($candidature));

                $this->line("✓ Notification envoyée à {$candidature->nom_complet} ({$candidature->email})");
                
                Log::info('Notification de fin de stage envoyée', [
                    'candidature_id' => $candidature->id,
                    'code_suivi' => $candidature->code_suivi,
                    'email' => $candidature->email,
                    'date_fin_stage' => $candidature->date_fin_stage->format('Y-m-d'),
                ]);

                $envoyees++;

            } catch (\Exception $e) {
                $this->error("✗ Erreur lors de l'envoi à {$candidature->email}: " . $e->getMessage());
                
                Log::error('Erreur lors de l\'envoi de notification de fin de stage', [
                    'candidature_id' => $candidature->id,
                    'email' => $candidature->email,
                    'error' => $e->getMessage(),
                ]);

                $erreurs++;
            }
        }

        $this->newLine();
        $this->info("Résumé :");
        $this->info("- Notifications envoyées : {$envoyees}");
        $this->info("- Erreurs : {$erreurs}");
        $this->info("- Total traité : " . ($envoyees + $erreurs));

        return 0;
    }
} 