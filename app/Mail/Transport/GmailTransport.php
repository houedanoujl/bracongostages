<?php

namespace App\Mail\Transport;

use Google\Client as GoogleClient;
use Google\Service\Gmail;
use Google\Service\Gmail\Message as GmailMessage;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Log;

class GmailTransport extends AbstractTransport
{
    protected GoogleClient $client;
    protected string $impersonateEmail;

    public function __construct(string $credentialsPath, string $impersonateEmail)
    {
        parent::__construct();

        $this->impersonateEmail = $impersonateEmail;
        $this->client = new GoogleClient();
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setScopes([Gmail::GMAIL_SEND]);
        $this->client->setSubject($impersonateEmail);
    }

    protected function doSend(SentMessage $message): void
    {
        try {
            $email = MessageConverter::toEmail($message->getOriginalMessage());
            $rawMessage = $message->getOriginalMessage()->toString();

            $gmailMessage = new GmailMessage();
            $gmailMessage->setRaw(rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '='));

            $service = new Gmail($this->client);
            $service->users_messages->send('me', $gmailMessage);

            Log::info('Email envoyé via Gmail API', [
                'to' => $email->getTo()[0]?->getAddress() ?? 'unknown',
                'subject' => $email->getSubject(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur envoi email Gmail API: ' . $e->getMessage());
            throw $e;
        }
    }

    public function __toString(): string
    {
        return 'gmail';
    }
}
