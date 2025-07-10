<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CandidatureForm;
use App\Livewire\SuiviCandidature;

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
Route::get('/candidature', CandidatureForm::class)->name('candidature.form');
Route::get('/suivi/{code?}', SuiviCandidature::class)->name('candidature.suivi');

// Route pour l'évaluation (après stage terminé)
Route::get('/evaluation/{candidature}', function ($candidature) {
    // TODO: Implémenter le composant d'évaluation
    return view('evaluation', compact('candidature'));
})->name('candidature.evaluation');

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