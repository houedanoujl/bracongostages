@extends('layouts.modern')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(!isset($candidature))
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-orange-600 mb-4">
                        🔍 Suivi de Candidature
                    </h1>
                    <p class="text-gray-600">
                        Entrez votre code de suivi pour consulter l'état de votre candidature
                    </p>
                </div>

                <form action="{{ route('candidature.suivi.search') }}" method="POST" class="max-w-md mx-auto">
                    @csrf
                    <div class="mb-6">
                        <label for="searchCode" class="block text-sm font-medium text-gray-700 mb-2">
                            Code de suivi
                        </label>
                        <input 
                            type="text" 
                            name="searchCode"
                            id="searchCode"
                            placeholder="Ex: BRC-ABCD1234" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            autocomplete="off"
                            required
                        >
                    </div>
                    
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 transition duration-300 shadow-lg"
                    >
                        Rechercher ma candidature
                    </button>
                </form>
                
                <!-- Bouton de test rapide -->
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500 mb-2">Vous n'avez pas de code ? Testez avec :</p>
                    <button 
                        type="button"
                        onclick="document.getElementById('searchCode').value = 'BRC-TEST123'"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors"
                    >
                        📋 BRC-TEST123 (Test)
                    </button>
                </div>
                
                <div class="mt-8 text-center">
                    <a href="/candidature" class="text-orange-600 hover:text-orange-700 font-medium">
                        ← Retour au formulaire de candidature
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-6">
                <!-- Header with candidate info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $candidature->nom_complet }}</h1>
                            <p class="text-gray-600">{{ $candidature->etablissement }} - {{ $candidature->niveau_etude }}</p>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($candidature->statut->value === 'valide') bg-green-100 text-green-800
                                @elseif($candidature->statut->value === 'rejete') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $candidature->statut->getLabel() }}
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Code: {{ $candidature->code_suivi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Progression de votre candidature</h2>
                    
                    <div class="space-y-6">
                        @php
                            $steps = [
                                [
                                    'status' => 'non_traite',
                                    'label' => 'Candidature reçue',
                                    'description' => 'Votre candidature a été reçue et est en attente de traitement',
                                    'completed' => true,
                                ],
                                [
                                    'status' => 'analyse_dossier',
                                    'label' => 'Analyse du dossier',
                                    'description' => 'Votre dossier est en cours d\'analyse par nos équipes',
                                    'completed' => in_array($candidature->statut->value, [
                                        'analyse_dossier', 'attente_test', 'attente_resultats', 
                                        'attente_affectation', 'valide'
                                    ]),
                                ],
                                [
                                    'status' => 'attente_test',
                                    'label' => 'Test technique',
                                    'description' => 'Vous serez convoqué(e) pour un test technique',
                                    'completed' => in_array($candidature->statut->value, [
                                        'attente_test', 'attente_resultats', 'attente_affectation', 'valide'
                                    ]),
                                ],
                                [
                                    'status' => 'attente_resultats',
                                    'label' => 'Résultats du test',
                                    'description' => 'Analyse des résultats du test technique',
                                    'completed' => in_array($candidature->statut->value, [
                                        'attente_resultats', 'attente_affectation', 'valide'
                                    ]),
                                ],
                                [
                                    'status' => 'attente_affectation',
                                    'label' => 'Affectation',
                                    'description' => 'Attribution du stage dans la direction appropriée',
                                    'completed' => in_array($candidature->statut->value, [
                                        'attente_affectation', 'valide'
                                    ]),
                                ],
                                [
                                    'status' => 'valide',
                                    'label' => 'Stage validé',
                                    'description' => 'Félicitations ! Votre stage a été validé',
                                    'completed' => $candidature->statut->value === 'valide',
                                ],
                            ];

                            if ($candidature->statut->value === 'rejete') {
                                $steps = array_slice($steps, 0, 2);
                                $steps[] = [
                                    'status' => 'rejete',
                                    'label' => 'Candidature rejetée',
                                    'description' => $candidature->motif_rejet ?? 'Votre candidature n\'a pas été retenue',
                                    'completed' => true,
                                    'rejected' => true,
                                ];
                            }
                        @endphp

                        @foreach($steps as $step)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center 
                                    @if($step['completed']) bg-green-500 @else bg-gray-300 @endif
                                    @if(isset($step['rejected'])) bg-red-500 @endif">
                                    @if($step['completed'])
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <div class="w-3 h-3 rounded-full bg-white"></div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900">{{ $step['label'] }}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $step['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Stage details (if validated) -->
                @if($candidature->statut->value === 'valide' && $candidature->date_debut_stage)
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-green-800 mb-4">🎉 Détails de votre stage</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-green-700 font-medium">Date de début</p>
                                <p class="text-green-800">{{ $candidature->date_debut_stage->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-green-700 font-medium">Date de fin</p>
                                <p class="text-green-800">{{ $candidature->date_fin_stage->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-green-700 font-medium">Durée</p>
                                <p class="text-green-800">{{ $candidature->duree_souhaitee }} jours</p>
                            </div>
                            <div>
                                <p class="text-sm text-green-700 font-medium">Directions</p>
                                <p class="text-green-800">{{ implode(', ', $candidature->directions_souhaitees) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rejection reason (if rejected) -->
                @if($candidature->statut->value === 'rejete' && $candidature->motif_rejet)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-4">Motif de rejet</h3>
                        <p class="text-red-700">{{ $candidature->motif_rejet }}</p>
                    </div>
                @endif

                <!-- Documents -->
                @if($candidature->documents->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Documents soumis</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($candidature->documents as $document)
                                <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                                    <div class="text-2xl mr-3">{{ $document->icone }}</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $document->nom_original }}</p>
                                        <p class="text-xs text-gray-500">{{ $document->taille_formatee }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Évaluation (si stage terminé) -->
                @if($candidature->statut->value === 'valide' && $candidature->date_fin_stage && $candidature->date_fin_stage->isPast())
                    @if(!$candidature->evaluation)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4">📝 Évaluez votre stage</h3>
                            <p class="text-blue-700 mb-4">
                                Votre stage est terminé. Partagez votre expérience avec nous pour nous aider à améliorer l'accueil des futurs stagiaires.
                            </p>
                            <a href="{{ route('candidature.evaluation', $candidature) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Évaluer mon stage
                            </a>
                        </div>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-green-800 mb-4">✅ Évaluation soumise</h3>
                            <p class="text-green-700">
                                Merci d'avoir partagé votre expérience ! Votre évaluation nous aide à améliorer l'accueil des futurs stagiaires.
                            </p>
                        </div>
                    @endif
                @endif

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <a href="/suivi" class="text-orange-600 hover:text-orange-700 font-medium">
                        ← Rechercher une autre candidature
                    </a>
                    <a href="/candidature" class="text-orange-600 hover:text-orange-700 font-medium">
                        Nouvelle candidature →
                    </a>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 