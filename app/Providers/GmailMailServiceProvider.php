<?php

namespace App\Providers;

use App\Mail\Transport\GmailTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class GmailMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->app->afterResolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('gmail', function () {
                $credentialsPath = config('mail.mailers.gmail.credentials');
                $impersonateEmail = config('mail.mailers.gmail.impersonate');

                return new GmailTransport($credentialsPath, $impersonateEmail);
            });
        });
    }
}
