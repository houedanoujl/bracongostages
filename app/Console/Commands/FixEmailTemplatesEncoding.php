<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use Illuminate\Console\Command;

class FixEmailTemplatesEncoding extends Command
{
    protected $signature = 'fix:email-encoding {--reseed : Re-seeder les templates depuis le seeder PHP}';
    protected $description = 'Corrige le double encodage UTF-8 dans les templates email existants en base de données';

    public function handle(): int
    {
        if ($this->option('reseed')) {
            $this->info('Re-seeding des templates email...');
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\EmailTemplateSeeder', '--force' => true]);
            $this->info('✅ Templates email re-seedés avec succès.');
            return self::SUCCESS;
        }

        $this->info('Vérification et correction de l\'encodage des templates email...');

        $templates = EmailTemplate::all();
        $fixed = 0;

        foreach ($templates as $template) {
            $sujetOriginal = $template->sujet;
            $contenuOriginal = $template->contenu;

            $sujetFixed = $this->fixUtf8($template->sujet);
            $contenuFixed = $this->fixUtf8($template->contenu);

            if ($sujetFixed !== $sujetOriginal || $contenuFixed !== $contenuOriginal) {
                $template->update([
                    'sujet' => $sujetFixed,
                    'contenu' => $contenuFixed,
                ]);
                $this->line("  ✔ Corrigé : <info>{$template->slug}</info> ({$template->nom})");
                $fixed++;
            } else {
                $this->line("  ─ OK : <comment>{$template->slug}</comment> (pas de correction nécessaire)");
            }
        }

        if ($fixed > 0) {
            $this->info("✅ {$fixed} template(s) corrigé(s).");
        } else {
            $this->info('✅ Aucune correction nécessaire — tous les templates sont correctement encodés.');
        }

        return self::SUCCESS;
    }

    /**
     * Corrige le double encodage UTF-8 (ex: informÃ©(e) → informé(e))
     */
    private function fixUtf8(string $text): string
    {
        // Détecter les séquences doublement encodées (Ã suivi d'un octet 0x80-0xBF)
        if (preg_match('/\xC3[\x80-\xBF]/', $text)) {
            $fixed = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            if (mb_check_encoding($fixed, 'UTF-8')) {
                return $fixed;
            }
        }
        return $text;
    }
}
