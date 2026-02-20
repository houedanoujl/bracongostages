<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiagnosticEmail extends Command
{
    protected $signature = 'diagnostic:email {--test= : Adresse email pour tester l\'envoi}';
    protected $description = 'Diagnostic complet de la configuration email';

    public function handle()
    {
        $this->info('=== DIAGNOSTIC EMAIL BRACONGO ===');
        $this->newLine();

        // 1. Config mail
        $this->info('📧 Configuration Mail:');
        $this->table(['Paramètre', 'Valeur'], [
            ['MAIL_MAILER (config)', config('mail.default')],
            ['MAIL_MAILER (env)', env('MAIL_MAILER', 'NON DÉFINI')],
            ['MAIL_FROM_ADDRESS', config('mail.from.address')],
            ['MAIL_FROM_NAME', config('mail.from.name')],
            ['MAILTRAP_API_KEY (env)', env('MAILTRAP_API_KEY') ? 'SET (' . substr(env('MAILTRAP_API_KEY'), 0, 8) . '...)' : 'NON DÉFINI'],
            ['Config cachée', file_exists(base_path('bootstrap/cache/config.php')) ? 'OUI' : 'NON'],
            ['Routes cachées', file_exists(base_path('bootstrap/cache/routes-v7.php')) ? 'OUI' : 'NON'],
        ]);

        // 2. Mailers définis
        $mailers = array_keys(config('mail.mailers', []));
        $this->info('📋 Mailers définis: ' . implode(', ', $mailers));

        // 3. Config mailtrap
        $mailtrapConfig = config('mail.mailers.mailtrap');
        if ($mailtrapConfig) {
            $this->info('✅ Mailer "mailtrap" trouvé:');
            $this->table(['Clé', 'Valeur'], collect($mailtrapConfig)->map(function ($v, $k) {
                if ($k === 'apiKey' && $v) return [$k, substr($v, 0, 8) . '...'];
                return [$k, is_string($v) ? $v : json_encode($v)];
            })->values()->toArray());
        } else {
            $this->error('❌ Mailer "mailtrap" NON TROUVÉ dans config/mail.php !');
        }

        // 4. Services mailtrap-sdk
        $services = config('services.mailtrap-sdk');
        if ($services) {
            $this->info('✅ services.mailtrap-sdk trouvé:');
            $this->line(json_encode($services, JSON_PRETTY_PRINT));
        } else {
            $this->warn('⚠️ services.mailtrap-sdk non défini (peut être normal si le provider n\'est pas chargé)');
        }

        // 5. Provider check
        $providerExists = class_exists(\Mailtrap\Bridge\Laravel\MailtrapSdkProvider::class);
        $this->info('📦 Provider Mailtrap: ' . ($providerExists ? '✅ Classe trouvée' : '❌ Classe NON TROUVÉE'));

        // 6. Vérifier config/app.php
        $appContent = file_get_contents(base_path('config/app.php'));
        $hasProvider = str_contains($appContent, 'MailtrapSdkProvider');
        $this->info('📄 config/app.php contient MailtrapSdkProvider: ' . ($hasProvider ? '✅ OUI' : '❌ NON'));

        // 7. Vérifier config/mail.php
        $mailContent = file_get_contents(base_path('config/mail.php'));
        $hasMailtrapSdk = str_contains($mailContent, 'mailtrap-sdk');
        $this->info('📄 config/mail.php contient "mailtrap-sdk": ' . ($hasMailtrapSdk ? '✅ OUI' : '❌ NON'));

        // 8. Variables .env liées au mail
        $this->newLine();
        $this->info('📄 Variables .env (mail):');
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $lines = file($envFile);
            $mailLines = array_filter($lines, fn($l) => preg_match('/^(MAIL_|MAILTRAP)/i', trim($l)));
            foreach ($mailLines as $line) {
                $this->line('  ' . trim($line));
            }
        } else {
            $this->error('Fichier .env non trouvé !');
        }

        // 9. Test transport
        $this->newLine();
        $this->info('🔌 Test du transport:');
        try {
            $defaultMailer = config('mail.default');
            $mailer = app('mail.manager')->mailer($defaultMailer);
            $transport = $mailer->getSymfonyTransport();
            $this->info('✅ Transport OK: ' . get_class($transport));
        } catch (\Exception $e) {
            $this->error('❌ Transport ERREUR: ' . $e->getMessage());
            $this->error('   Exception: ' . get_class($e));
        }

        // 10. Test envoi
        if ($testEmail = $this->option('test')) {
            $this->newLine();
            $this->info("📤 Test d'envoi vers: {$testEmail}");
            try {
                \Illuminate\Support\Facades\Notification::route('mail', $testEmail)
                    ->notify(new \App\Notifications\EmailGeneriqueNotification(
                        'Test diagnostic BRACONGO - ' . now()->format('d/m/Y H:i:s'),
                        'Ceci est un email de test envoyé depuis la commande de diagnostic sur Forge.'
                    ));
                $this->info('✅ Email envoyé avec succès à ' . $testEmail);
            } catch (\Exception $e) {
                $this->error('❌ Envoi ÉCHOUÉ: ' . $e->getMessage());
                $this->error('   Exception: ' . get_class($e));
                $this->line(collect(explode("\n", $e->getTraceAsString()))->take(5)->implode("\n"));
            }
        }

        // 11. Dernières erreurs
        $this->newLine();
        $this->info('📋 Dernières erreurs mail dans les logs:');
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $lines = array_slice(file($logFile), -100);
            $errorLines = array_filter($lines, function ($l) {
                $lower = strtolower($l);
                return (str_contains($lower, 'error') || str_contains($lower, 'mail') || str_contains($lower, 'mailtrap'))
                    && !str_contains($lower, 'debug');
            });
            $lastErrors = array_slice($errorLines, -8);
            if (empty($lastErrors)) {
                $this->info('  Aucune erreur mail récente');
            } else {
                foreach ($lastErrors as $line) {
                    $this->line('  ' . substr(trim($line), 0, 200));
                }
            }
        }

        $this->newLine();
        $this->info('=== FIN DIAGNOSTIC ===');
        
        return 0;
    }
}
