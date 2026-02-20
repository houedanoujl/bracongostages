<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-mail', function () {
    $this->info('Envoi d\'un email de test via Mailtrap...');

    try {
        $email = (new MailtrapEmail())
            ->from(new Address(config('mail.from.address', 'hello@bigfive.dev'), config('mail.from.name', 'BRACONGO Stages')))
            ->to(new Address('jhouedanou@gmail.com', 'Jean Luc Houédanou'))
            ->subject('Test email - BRACONGO Stages')
            ->category('Integration Test')
            ->text('Ceci est un email de test envoyé depuis la plateforme BRACONGO Stages via Mailtrap.')
        ;

        $response = MailtrapClient::initSendingEmails(
            apiKey: config('mail.mailers.mailtrap.api_key', env('MAILTRAP_API_KEY'))
        )->send($email);

        $result = ResponseHelper::toArray($response);
        $this->info('✅ Email envoyé avec succès!');
        $this->table(['Clé', 'Valeur'], collect($result)->map(fn ($v, $k) => [$k, is_array($v) ? json_encode($v) : $v])->toArray());
    } catch (\Exception $e) {
        $this->error('❌ Erreur: ' . $e->getMessage());
    }
})->purpose('Envoyer un email de test via Mailtrap');