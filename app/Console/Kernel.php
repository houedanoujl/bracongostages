<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Envoyer les notifications de fin de stage quotidiennement à 9h
        $schedule->command('stages:notifier-fin-stage')
            ->dailyAt('09:00')
            ->appendOutputTo(storage_path('logs/stages-notifications.log'));

        // Nettoyer les fichiers temporaires Livewire chaque semaine
        $schedule->command('livewire:discover')
            ->weekly()
            ->sundays()
            ->at('02:00');

        // Optimiser la base de données mensuellement
        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 