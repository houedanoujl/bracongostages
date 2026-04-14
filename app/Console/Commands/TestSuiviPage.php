<?php

namespace App\Console\Commands;

use App\Models\Candidature;
use App\Enums\StatutCandidature;
use App\Models\User;
use Illuminate\Console\Command;

class TestSuiviPage extends Command
{
    protected $signature = 'test:suivi';
    protected $description = 'Teste la page de suivi et diagnostique les problèmes Filament';

    public function handle()
    {
        $this->info('DIAGNOSTIC COMPLET - BRACONGO Stages');
        $this->info('=====================================');

        // Test 1: Page de suivi
        $this->info('');
        $this->info('TEST 1: Page de suivi');
        $this->info('------------------------');

        // Créer une candidature de test
        $candidature = Candidature::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'nom' => 'Test',
                'prenom' => 'Utilisateur',
                'telephone' => '+243 81 000 0000',
                'etablissement' => 'unikin',
                'niveau_etude' => 'bac_3',
                'faculte' => 'Informatique',
                'objectif_stage' => 'Test de la fonctionnalité de suivi',
                'poste_souhaite' => 'assistant_informatique',
                'directions_souhaitees' => ['direction_informatique'],
                'periode_debut_souhaitee' => now()->addMonth(),
                'periode_fin_souhaitee' => now()->addMonths(3),
                'statut' => StatutCandidature::NON_TRAITE,
            ]
        );

        $this->info("[OK] Candidature test: {$candidature->code_suivi}");
        $this->info("🔗 URL directe: http://localhost:8000/suivi/{$candidature->code_suivi}");
        $this->info("🔗 URL recherche: http://localhost:8000/suivi");

        // Test 2: Permissions Filament
        $this->info('');
        $this->info('TEST 2: Permissions Filament');
        $this->info('-------------------------------');

        $user = User::first();
        if ($user) {
            $this->info("[OK] Utilisateur admin trouvé: {$user->name}");
            $this->info("Email: {$user->email}");
            $this->info("🔓 Actif: " . ($user->is_active ? 'Oui' : 'Non'));
            
            try {
                $canAccess = $user->canAccessPanel(app('filament')->getPanel('admin'));
                $this->info("Accès Filament: " . ($canAccess ? 'Autorisé' : 'Refusé'));
            } catch (\Exception $e) {
                $this->error("[ERREUR] Erreur test Filament: " . $e->getMessage());
            }
        } else {
            $this->error("[ERREUR] Aucun utilisateur trouvé");
        }

        // Test 3: Ressources Filament
        $this->info('');
        $this->info('📦 TEST 3: Ressources Filament');
        $this->info('-----------------------------');

        $resources = [
            'CandidatureResource' => \App\Filament\Resources\CandidatureResource::class,
            'ConfigurationResource' => \App\Filament\Resources\ConfigurationResource::class,
            'ConfigurationListeResource' => \App\Filament\Resources\ConfigurationListeResource::class,
            'OpportuniteResource' => \App\Filament\Resources\OpportuniteResource::class,
            'EvaluationResource' => \App\Filament\Resources\EvaluationResource::class,
        ];

        foreach ($resources as $name => $class) {
            if (class_exists($class)) {
                try {
                    $shouldRegister = $class::shouldRegisterNavigation() ?? true;
                    $this->info("[OK] {$name}: " . ($shouldRegister ? 'Visible' : 'Masquée'));
                } catch (\Exception $e) {
                    $this->info("[ATTENTION] {$name}: Erreur - " . $e->getMessage());
                }
            } else {
                $this->error("[ERREUR] {$name}: Classe non trouvée");
            }
        }

        // Test 4: Routes Filament
        $this->info('');
        $this->info('🛣️  TEST 4: Routes admin');
        $this->info('---------------------');
        $this->info("🔗 Admin login: http://localhost:8000/admin");
        $this->info("🔗 Admin dashboard: http://localhost:8000/admin");

        // Instructions
        $this->info('');
        $this->info('INSTRUCTIONS DE TEST');
        $this->info('======================');
        $this->info('1. Testez la page de suivi avec le code: ' . $candidature->code_suivi);
        $this->info('2. Connectez-vous à l\'admin avec: admin@bracongo.com');
        $this->info('3. Vérifiez que tous les menus s\'affichent dans la barre latérale');
        $this->info('');
        $this->info('🐛 EN CAS DE PROBLÈME:');
        $this->info('- Si la page de suivi est vide: Vérifiez la console du navigateur');
        $this->info('- Si les menus Filament manquent: Essayez de vous déconnecter/reconnecter');
        $this->info('- Vérifiez que JavaScript est activé dans votre navigateur');

        return 0;
    }
}
