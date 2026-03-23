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

// Route de diagnostic email supprimée pour raisons de sécurité

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
    ->middleware('throttle:5,1')
    ->name('contact.store');

// Health check minimaliste (ne pas exposer de détails techniques en production)
Route::get('/api/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->format('Y-m-d H:i:s'),
    ]);
});

// Route de login pour les administrateurs (Filament gère l'authentification)
Route::get('/login', function () {
    return redirect('/admin');
})->name('login');

// Route pour servir les images uploadées (workaround Nginx)
Route::get('/uploads/{path}', function ($path) {
    try {
        // Sécurité: empêcher le path traversal en interdisant ".." et chemins absolus
        if (str_contains($path, '..') || str_starts_with($path, '/') || str_starts_with($path, '\\')) {
            abort(403, 'Accès interdit.');
        }

        // Vérifier que le fichier existe via le disque Storage (évite manipulation directe du chemin)
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404);
        }

        // Limiter aux types MIME autorisés (images, PDF, documents)
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $mimeType = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($path);
        if (!in_array($mimeType, $allowedMimes)) {
            abort(403, 'Type de fichier non autorisé.');
        }

        $file = storage_path('app/public/' . $path);
        return response()->file($file, [
            'Content-Type' => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (\Exception $e) {
        \Log::error('Error serving file: ' . $e->getMessage());
        abort(500);
    }
})->where('path', '.*')->name('uploads.serve');

// Route pour télécharger les documents depuis l'admin (protégée par auth)
Route::get('/admin/documents/{document}/download', function ($documentId) {
    try {
        $document = \App\Models\Document::findOrFail($documentId);

        // Utiliser la méthode getCheminReel() pour trouver le bon chemin
        $cheminReel = $document->getCheminReel();

        if ($cheminReel) {
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

        \Log::error('Fichier non trouvé pour le document ID: ' . $documentId);
        abort(404, 'Fichier non trouvé sur le serveur.');

    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        throw $e;
    } catch (\Exception $e) {
        \Log::error('Erreur téléchargement document: ' . $e->getMessage());
        abort(500, 'Erreur lors du téléchargement.');
    }
})->middleware('auth')->name('admin.document.download');

// Routes pour les candidats
Route::prefix('candidat')->name('candidat.')->group(function () {
    // Routes publiques (pour les candidats non connectés)
    Route::middleware('guest.candidat')->group(function () {
        Route::get('/register', [App\Http\Controllers\CandidatController::class, 'create'])->name('create');
        Route::post('/register', [App\Http\Controllers\CandidatController::class, 'store'])->name('store');
        Route::get('/login', [App\Http\Controllers\CandidatController::class, 'login'])->name('login');
        Route::post('/login', [App\Http\Controllers\CandidatController::class, 'authenticate'])->middleware('throttle:10,1')->name('authenticate');
        
        // Routes de réinitialisation de mot de passe
        Route::get('/forgot-password', [App\Http\Controllers\CandidatController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [App\Http\Controllers\CandidatController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
        Route::get('/reset-password/{token}', [App\Http\Controllers\CandidatController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password', [App\Http\Controllers\CandidatController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
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
        Route::get('/temoignage', \App\Livewire\TemoignageForm::class)->name('temoignage');
    });
});

 