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