<?php
/**
 * Diagnostic email - fichier statique dans public/
 * Accessible directement sans passer par le cache de routes Laravel
 * URL: https://bracongostages.bigfive.dev/debug-mail.php?secret=bracongo2026diag
 * SUPPRIMER CE FICHIER APRÈS DIAGNOSTIC
 */

// Vérification du secret
if (($_GET['secret'] ?? '') !== 'bracongo2026diag') {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

header('Content-Type: application/json; charset=utf-8');

$results = [];

// 1. Config mail
$results['mail_default'] = config('mail.default');
$results['mail_from'] = config('mail.from');
$results['mailers_defined'] = array_keys(config('mail.mailers', []));
$results['mailtrap_config'] = config('mail.mailers.mailtrap', 'NOT DEFINED');
$results['mailtrap_api_key_set'] = !empty(config('mail.mailers.mailtrap.apiKey'));
$results['services_mailtrap'] = config('services.mailtrap-sdk', 'NOT DEFINED');
$results['env_mail_mailer'] = env('MAIL_MAILER', 'NOT SET');
$results['env_mailtrap_key'] = env('MAILTRAP_API_KEY') ? 'SET (' . substr(env('MAILTRAP_API_KEY'), 0, 8) . '...)' : 'NOT SET';
$results['config_cached'] = file_exists(base_path('bootstrap/cache/config.php')) ? 'OUI - config:cache actif' : 'NON';
$results['routes_cached'] = file_exists(base_path('bootstrap/cache/routes-v7.php')) ? 'OUI - route:cache actif' : 'NON';

// 2. Provider check
$results['mailtrap_provider_class_exists'] = class_exists(\Mailtrap\Bridge\Laravel\MailtrapSdkProvider::class) ? 'OUI' : 'NON';

// 3. Contenu du fichier .env (mail seulement)
$envFile = base_path('.env');
if (file_exists($envFile)) {
    $envLines = file($envFile);
    $mailLines = array_filter($envLines, function ($l) {
        return preg_match('/^(MAIL_|MAILTRAP)/i', trim($l));
    });
    $results['env_file_mail_vars'] = array_map('trim', array_values($mailLines));
} else {
    $results['env_file'] = 'FICHIER .env NON TROUVÉ';
}

// 4. Vérifier config/mail.php
$mailConfigFile = base_path('config/mail.php');
if (file_exists($mailConfigFile)) {
    $mailContent = file_get_contents($mailConfigFile);
    $results['mail_php_has_mailtrap'] = str_contains($mailContent, 'mailtrap') ? 'OUI' : 'NON';
    $results['mail_php_has_mailtrap_sdk'] = str_contains($mailContent, 'mailtrap-sdk') ? 'OUI' : 'NON';
} else {
    $results['mail_config_file'] = 'config/mail.php NON TROUVÉ';
}

// 5. Vérifier config/app.php pour le provider
$appConfigFile = base_path('config/app.php');
if (file_exists($appConfigFile)) {
    $appContent = file_get_contents($appConfigFile);
    $results['app_php_has_mailtrap_provider'] = str_contains($appContent, 'MailtrapSdkProvider') ? 'OUI' : 'NON';
} else {
    $results['app_config_file'] = 'config/app.php NON TROUVÉ';
}

// 6. Test transport
try {
    $mailer = app('mail.manager')->mailer(config('mail.default'));
    $results['transport_class'] = get_class($mailer->getSymfonyTransport());
    $results['transport_status'] = 'OK';
} catch (\Exception $e) {
    $results['transport_error'] = $e->getMessage();
    $results['transport_exception_class'] = get_class($e);
}

// 7. Test envoi si demandé
if (isset($_GET['test']) && filter_var($_GET['test'], FILTER_VALIDATE_EMAIL)) {
    try {
        Illuminate\Support\Facades\Notification::route('mail', $_GET['test'])
            ->notify(new App\Notifications\EmailGeneriqueNotification(
                'Test diagnostic BRACONGO - ' . now()->format('d/m/Y H:i:s'),
                'Ceci est un email de test envoyé depuis la route de diagnostic sur Forge.'
            ));
        $results['send_test'] = 'SUCCESS - envoyé à ' . $_GET['test'];
    } catch (\Exception $e) {
        $results['send_test'] = 'FAILED: ' . $e->getMessage();
        $results['send_exception_class'] = get_class($e);
        $results['send_trace'] = array_slice(explode("\n", $e->getTraceAsString()), 0, 8);
    }
}

// 8. Dernières erreurs log
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = array_slice(file($logFile), -50);
    $errorLines = array_filter($lines, function ($l) {
        return str_contains($l, 'ERROR') || str_contains(strtolower($l), 'mail') || str_contains($l, 'Mailtrap') || str_contains($l, 'mailtrap');
    });
    $results['recent_log_errors'] = array_values(array_map('trim', array_slice($errorLines, -10)));
}

// 9. PHP info
$results['php_version'] = PHP_VERSION;
$results['laravel_version'] = app()->version();
$results['server_time'] = now()->toDateTimeString();

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
