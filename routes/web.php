<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CandidatureForm;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil moderne avec design BRACONGO
Route::get('/', function () {
    return view('home-modern');
})->name('home');

// Route alternative pour tester l'ancien design
Route::get('/classic', function () {
    return view('welcome');
})->name('home.classic');

// Routes principales de l'application
Route::get('/candidature', CandidatureForm::class)
    ->middleware('auth.candidat')
    ->name('candidature.form');
Route::get('/suivi', function () {
    return view('suivi-simple');
})->name('candidature.suivi');

Route::get('/suivi/{code}', function ($code) {
    $candidature = \App\Models\Candidature::where('code_suivi', $code)
        ->with(['documents', 'evaluation'])
        ->first();
    
    if (!$candidature) {
        return redirect('/suivi')->with('error', 'Aucune candidature trouvée avec ce code : ' . $code);
    }
    
    return view('suivi-simple', compact('candidature'));
})->name('candidature.suivi.code');

Route::post('/suivi/search', function (\Illuminate\Http\Request $request) {
    $code = strtoupper(trim($request->input('searchCode')));
    
    $candidature = \App\Models\Candidature::where('code_suivi', $code)
        ->with(['documents', 'evaluation'])
        ->first();
    
    if (!$candidature) {
        return redirect('/suivi')->with('error', 'Aucune candidature trouvée avec ce code : ' . $code);
    }
    
    return redirect('/suivi/' . $candidature->code_suivi);
})->name('candidature.suivi.search');

// Route de diagnostic email (protégée par secret) - à supprimer après debug
Route::get('/debug-mail/{secret}', function ($secret) {
    if ($secret !== 'bracongo2026diag') {
        abort(404);
    }

    $results = [];

    // 1. Config mail
    $results['mail_default'] = config('mail.default');
    $results['mail_from'] = config('mail.from');
    $results['mailers_defined'] = array_keys(config('mail.mailers', []));
    $results['mailtrap_config'] = config('mail.mailers.mailtrap', 'NOT DEFINED');
    $results['mailtrap_api_key_set'] = !empty(config('mail.mailers.mailtrap.apiKey', config('services.mailtrap-sdk.apiKey')));
    $results['services_mailtrap'] = config('services.mailtrap-sdk', 'NOT DEFINED');
    $results['env_mail_mailer'] = env('MAIL_MAILER', 'NOT SET');
    $results['env_mailtrap_key'] = env('MAILTRAP_API_KEY') ? 'SET (' . substr(env('MAILTRAP_API_KEY'), 0, 8) . '...)' : 'NOT SET';
    $results['config_cached'] = file_exists(base_path('bootstrap/cache/config.php')) ? 'YES' : 'NO';

    // 2. Provider check
    $results['mailtrap_provider_loaded'] = class_exists(\Mailtrap\Bridge\Laravel\MailtrapSdkProvider::class) ? 'YES' : 'NO';

    // 3. Test transport
    try {
        $mailer = app('mail.manager')->mailer('mailtrap');
        $results['transport_class'] = get_class($mailer->getSymfonyTransport());
        $results['transport_status'] = 'OK';
    } catch (\Exception $e) {
        $results['transport_error'] = $e->getMessage();
        $results['transport_class'] = get_class($e);
    }

    // 4. Test envoi
    if (request()->has('test')) {
        try {
            \Illuminate\Support\Facades\Notification::route('mail', request('test'))
                ->notify(new \App\Notifications\EmailGeneriqueNotification(
                    'Test diagnostic BRACONGO - ' . now()->format('H:i:s'),
                    'Ceci est un email de test envoyé depuis la route de diagnostic.'
                ));
            $results['send_test'] = 'SUCCESS - envoyé à ' . request('test');
        } catch (\Exception $e) {
            $results['send_test'] = 'FAILED: ' . $e->getMessage();
            $results['send_trace'] = collect(explode("\n", $e->getTraceAsString()))->take(5)->implode("\n");
        }
    }

    // 5. Dernières erreurs log
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $lines = array_slice(file($logFile), -30);
        $errorLines = array_filter($lines, fn($l) => str_contains($l, 'ERROR') || str_contains($l, 'mail') || str_contains($l, 'Mailtrap'));
        $results['recent_errors'] = array_values(array_slice($errorLines, -10));
    }

    return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});

// Routes pour les évaluations
Route::get('/evaluation/{candidature}', [App\Http\Controllers\EvaluationController::class, 'show'])
    ->name('candidature.evaluation');
Route::post('/evaluation/{candidature}', [App\Http\Controllers\EvaluationController::class, 'store'])
    ->name('evaluation.store');

// API pour les statistiques d'évaluation
Route::get('/api/evaluations/statistiques', [App\Http\Controllers\EvaluationController::class, 'statistiques'])
    ->name('evaluation.statistiques');

// Routes pour les pages dédiées
Route::get('/opportunites', function () {
    return view('opportunites');
})->name('opportunites');

Route::get('/opportunites/{slug}', function ($slug) {
    $opportunite = \App\Models\Opportunite::where('slug', $slug)
        ->where('actif', true)
        ->first();
    
    if (!$opportunite) {
        return redirect('/opportunites')->with('error', 'Opportunité non trouvée.');
    }
    
    return view('opportunite-detail', compact('opportunite'));
})->name('opportunite.detail');

Route::get('/contact', [App\Http\Controllers\ContactController::class, 'show'])
    ->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])
    ->name('contact.store');

// API pour tests et monitoring
Route::get('/api/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'database' => \DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'cache' => \Cache::get('test') !== null ? 'working' : 'not working',
        'queue' => 'redis',
        'version' => app()->version(),
    ]);
});

// Test simple pour vérifier que l'application fonctionne
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'BRACONGO Stages - Application fonctionnelle !',
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'extensions' => [
            'intl' => extension_loaded('intl'),
            'redis' => extension_loaded('redis'),
            'pdo_mysql' => extension_loaded('pdo_mysql')
        ],
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});

// Route de login pour les administrateurs (Filament gère l'authentification)
Route::get('/login', function () {
    return redirect('/admin');
})->name('login');

// Route pour servir les images uploadées (workaround Nginx)
Route::get('/uploads/{path}', function ($path) {
    try {
        $file = storage_path('app/public/' . $path);
        
        \Log::info('Trying to serve file: ' . $file);
        
        if (!file_exists($file)) {
            \Log::error('File not found: ' . $file);
            abort(404);
        }
        
        $mimeType = mime_content_type($file);
        \Log::info('Serving file with mime type: ' . $mimeType);
        
        return response()->file($file, ['Content-Type' => $mimeType]);
    } catch (\Exception $e) {
        \Log::error('Error serving file: ' . $e->getMessage());
        abort(500);
    }
})->where('path', '.*')->name('uploads.serve');

// Test route
Route::get('/test-uploads', function () {
    return 'Uploads route is working!';
});

// Route pour télécharger les documents depuis l'admin
Route::get('/admin/documents/{document}/download', function ($documentId) {
    try {
        \Log::info('Tentative de téléchargement document ID: ' . $documentId);
        
        $document = \App\Models\Document::findOrFail($documentId);
        \Log::info('Document trouvé: ' . $document->nom_original . ', Chemin: ' . $document->chemin_fichier);
        
        // Utiliser la méthode getCheminReel() pour trouver le bon chemin
        $cheminReel = $document->getCheminReel();
        
        if ($cheminReel) {
            \Log::info('Téléchargement du fichier: ' . $cheminReel);
            
            // Essayer d'abord avec le disque public
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cheminReel)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->download(
                    $cheminReel, 
                    $document->nom_original
                );
            }
            
            // Sinon essayer avec le disque par défaut
            if (\Illuminate\Support\Facades\Storage::exists($cheminReel)) {
                return \Illuminate\Support\Facades\Storage::download(
                    $cheminReel, 
                    $document->nom_original
                );
            }
        }
        
        \Log::error('Fichier non trouvé pour le document ID: ' . $documentId . ', chemin: ' . $document->chemin_fichier);
        abort(404, 'Fichier non trouvé sur le serveur.');
        
    } catch (\Exception $e) {
        \Log::error('Erreur téléchargement document: ' . $e->getMessage());
        abort(500, 'Erreur lors du téléchargement: ' . $e->getMessage());
    }
})->name('admin.document.download');

// Routes pour les candidats
Route::prefix('candidat')->name('candidat.')->group(function () {
    // Routes publiques (pour les candidats non connectés)
    Route::middleware('guest.candidat')->group(function () {
        Route::get('/register', [App\Http\Controllers\CandidatController::class, 'create'])->name('create');
        Route::post('/register', [App\Http\Controllers\CandidatController::class, 'store'])->name('store');
        Route::get('/login', [App\Http\Controllers\CandidatController::class, 'login'])->name('login');
        Route::post('/login', [App\Http\Controllers\CandidatController::class, 'authenticate'])->name('authenticate');
        
        // Routes de réinitialisation de mot de passe
        Route::get('/forgot-password', [App\Http\Controllers\CandidatController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\CandidatController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [App\Http\Controllers\CandidatController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [App\Http\Controllers\CandidatController::class, 'resetPassword'])->name('password.update');
    });
    
    // Routes protégées (pour les candidats connectés)
    Route::middleware('auth:candidat')->group(function () {
        Route::post('/logout', [App\Http\Controllers\CandidatController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [App\Http\Controllers\CandidatController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\CandidatController::class, 'profile'])->name('profile');
        Route::post('/profile', [App\Http\Controllers\CandidatController::class, 'updateProfile'])->name('update-profile');
        Route::post('/documents', [App\Http\Controllers\CandidatController::class, 'updateDocuments'])->name('update-documents');
        Route::post('/password', [App\Http\Controllers\CandidatController::class, 'changePassword'])->name('change-password');
        Route::get('/candidatures', [App\Http\Controllers\CandidatController::class, 'candidatures'])->name('candidatures');
        Route::get('/candidatures/{id}', [App\Http\Controllers\CandidatController::class, 'candidature'])->name('candidature');
        Route::get('/cv/download', [App\Http\Controllers\CandidatController::class, 'downloadCv'])->name('download-cv');
        Route::get('/documents/{document}/download', [App\Http\Controllers\CandidatController::class, 'downloadDocument'])->name('document.download');
        Route::get('/messages', \App\Livewire\Messagerie::class)->name('messages');
        Route::get('/messages/{candidatureId}', \App\Livewire\Messagerie::class)->name('messages.candidature');
    });
});

 