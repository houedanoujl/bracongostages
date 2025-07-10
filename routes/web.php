<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes pour l'interface candidat
Route::prefix('candidature')->group(function () {
    Route::get('/', \App\Livewire\CandidatureForm::class)->name('candidature.create');
    Route::get('/suivi/{code}', \App\Livewire\SuiviCandidature::class)->name('candidature.suivi');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
}); 