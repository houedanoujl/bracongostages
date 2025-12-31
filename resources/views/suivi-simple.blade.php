@extends('layouts.modern')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-bracongo-red-50 to-bracongo-red-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(!isset($candidature))
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-bracongo-red-600 mb-4">
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-bracongo-red-500 focus:border-bracongo-red-500"
                            autocomplete="off"
                            required
                        >
                    </div>
                    
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-red-700 hover:to-red-800 transition duration-300 shadow-lg"
                    >
                        Rechercher ma candidature
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <a href="/candidature" class="text-red-600 hover:text-red-700 font-medium">
                        ← Retour au formulaire de candidature
                    </a>
                </div>
            </div>
        @else
            <div class="space-y-6">
                <!-- Header with candidate info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $candidature->nom_complet }}</h1>
                            <p class="text-gray-600">{{ $candidature->etablissement }} - {{ $candidature->niveau_etude }}</p>
                            <p class="text-sm text-gray-500 mt-1">Candidature du {{ $candidature->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" 
                                 style="background-color: {{ match($candidature->statut->getColor()) {
                                     'success' => '#dcfce7',
                                     'danger' => '#fee2e2',
                                     'warning' => '#fef3c7',
                                     'info' => '#dbeafe',
                                     'primary' => '#e0e7ff',
                                     default => '#f3f4f6'
                                 } }}; color: {{ match($candidature->statut->getColor()) {
                                     'success' => '#166534',
                                     'danger' => '#991b1b',
                                     'warning' => '#92400e',
                                     'info' => '#1e40af',
                                     'primary' => '#3730a3',
                                     default => '#374151'
                                 } }};">
                                {{ $candidature->statut->getLabel() }}
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Code: <span class="font-mono font-medium">{{ $candidature->code_suivi }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Indicateur d'étape actuelle -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Étape actuelle</h2>
                        <span class="text-sm text-gray-500">Étape {{ $candidature->statut->getEtape() }} / 13</span>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $candidature->statut->value === 'rejete' ? 'bg-red-500' : 'bg-green-500' }}" 
                             style="width: {{ ($candidature->statut->getEtape() / 13) * 100 }}%"></div>
                    </div>

                    <div class="p-4 rounded-lg {{ $candidature->statut->value === 'rejete' ? 'bg-red-50' : 'bg-blue-50' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 text-2xl mr-3">
                                {!! match($candidature->statut->value) {
                                    'dossier_recu', 'non_traite' => '📥',
                                    'analyse_dossier' => '🔍',
                                    'dossier_incomplet' => '⚠️',
                                    'attente_test', 'test_planifie' => '📝',
                                    'test_passe', 'attente_resultats' => '✍️',
                                    'attente_decision' => '⏳',
                                    'accepte', 'valide' => '✅',
                                    'planification', 'attente_affectation' => '📅',
                                    'affecte' => '🏢',
                                    'reponse_lettre_envoyee' => '📨',
                                    'induction_planifiee', 'induction_terminee' => '🎓',
                                    'accueil_service' => '👋',
                                    'stage_en_cours' => '💼',
                                    'en_evaluation', 'evaluation_terminee' => '⭐',
                                    'attestation_generee' => '📜',
                                    'remboursement_en_cours' => '💰',
                                    'termine' => '🎉',
                                    'rejete' => '❌',
                                    default => '📋'
                                } !!}
                            </div>
                            <div>
                                <h3 class="font-semibold {{ $candidature->statut->value === 'rejete' ? 'text-red-800' : 'text-blue-800' }}">
                                    {{ $candidature->statut->getLabel() }}
                                </h3>
                                <p class="text-sm {{ $candidature->statut->value === 'rejete' ? 'text-red-700' : 'text-blue-700' }} mt-1">
                                    {!! match($candidature->statut->value) {
                                        'dossier_recu', 'non_traite' => 'Votre candidature a été reçue et sera bientôt examinée par notre équipe RH.',
                                        'analyse_dossier' => 'Votre dossier est actuellement en cours d\'analyse par notre équipe RH.',
                                        'dossier_incomplet' => 'Des informations complémentaires sont requises. Veuillez vérifier votre email.',
                                        'attente_test', 'test_planifie' => 'Un test de niveau est programmé. Consultez votre email pour les détails.',
                                        'test_passe', 'attente_resultats' => 'Vous avez passé le test. Les résultats sont en cours d\'analyse.',
                                        'attente_decision' => 'Votre dossier est en attente de décision finale.',
                                        'accepte', 'valide' => 'Félicitations ! Votre candidature a été acceptée.',
                                        'planification', 'attente_affectation' => 'Nous préparons votre affectation dans un de nos services.',
                                        'affecte' => 'Vous avez été affecté(e) à un service. Préparez-vous pour votre intégration.',
                                        'reponse_lettre_envoyee' => 'La réponse officielle a été envoyée à votre établissement.',
                                        'induction_planifiee' => 'Votre session d\'induction RH est planifiée.',
                                        'induction_terminee' => 'L\'induction RH est terminée. Bienvenue chez BRACONGO !',
                                        'accueil_service' => 'Vous avez été accueilli(e) dans votre service d\'affectation.',
                                        'stage_en_cours' => 'Votre stage est en cours. Bon courage !',
                                        'en_evaluation' => 'L\'évaluation de fin de stage est en cours.',
                                        'evaluation_terminee' => 'L\'évaluation de votre stage est terminée.',
                                        'attestation_generee' => 'Votre attestation de stage a été générée.',
                                        'remboursement_en_cours' => 'Le remboursement de vos frais de transport est en cours.',
                                        'termine' => 'Votre stage est officiellement terminé. Merci pour votre contribution !',
                                        'rejete' => $candidature->motif_rejet ?? 'Votre candidature n\'a pas été retenue.',
                                        default => 'Votre candidature est en cours de traitement.'
                                    } !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline complète -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Progression de votre candidature</h2>
                    
                    @php
                        $currentStep = $candidature->statut->getEtape();
                        $isRejected = $candidature->statut->value === 'rejete';
                        
                        $phases = [
                            [
                                'name' => 'Réception & Analyse',
                                'steps' => [
                                    ['etape' => 1, 'label' => 'Dossier reçu', 'icon' => '📥'],
                                    ['etape' => 2, 'label' => 'Analyse DRH', 'icon' => '🔍'],
                                ]
                            ],
                            [
                                'name' => 'Tests & Décision',
                                'steps' => [
                                    ['etape' => 3, 'label' => 'Test de niveau', 'icon' => '📝'],
                                    ['etape' => 4, 'label' => 'Décision finale', 'icon' => '⚖️'],
                                    ['etape' => 5, 'label' => 'Candidature acceptée', 'icon' => '✅'],
                                ]
                            ],
                            [
                                'name' => 'Intégration',
                                'steps' => [
                                    ['etape' => 6, 'label' => 'Affectation service', 'icon' => '🏢'],
                                    ['etape' => 7, 'label' => 'Réponse établissement', 'icon' => '📨'],
                                    ['etape' => 8, 'label' => 'Induction RH', 'icon' => '🎓'],
                                ]
                            ],
                            [
                                'name' => 'Stage',
                                'steps' => [
                                    ['etape' => 9, 'label' => 'Accueil service', 'icon' => '👋'],
                                    ['etape' => 10, 'label' => 'Stage en cours', 'icon' => '💼'],
                                    ['etape' => 11, 'label' => 'Évaluation', 'icon' => '⭐'],
                                ]
                            ],
                            [
                                'name' => 'Clôture',
                                'steps' => [
                                    ['etape' => 12, 'label' => 'Attestation', 'icon' => '📜'],
                                    ['etape' => 13, 'label' => 'Stage terminé', 'icon' => '🎉'],
                                ]
                            ],
                        ];
                    @endphp

                    <div class="space-y-8">
                        @foreach($phases as $phase)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
                                    {{ $phase['name'] }}
                                </h3>
                                <div class="space-y-4">
                                    @foreach($phase['steps'] as $step)
                                        @php
                                            $isCompleted = $currentStep > $step['etape'] || ($currentStep == 13 && $step['etape'] == 13);
                                            $isCurrent = $currentStep == $step['etape'] && !$isRejected;
                                            $isPending = $currentStep < $step['etape'];
                                        @endphp
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg
                                                @if($isRejected && $step['etape'] >= $currentStep) 
                                                    bg-red-100 text-red-500
                                                @elseif($isCompleted) 
                                                    bg-green-100 text-green-600
                                                @elseif($isCurrent) 
                                                    bg-blue-100 text-blue-600 ring-2 ring-blue-400 ring-offset-2
                                                @else 
                                                    bg-gray-100 text-gray-400
                                                @endif">
                                                @if($isCompleted && !$isRejected)
                                                    ✓
                                                @elseif($isRejected && $step['etape'] >= $currentStep)
                                                    ✗
                                                @else
                                                    {{ $step['icon'] }}
                                                @endif
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <p class="text-sm font-medium @if($isCompleted && !$isRejected) text-green-700 @elseif($isCurrent) text-blue-700 @elseif($isRejected && $step['etape'] >= $currentStep) text-red-400 @else text-gray-500 @endif">
                                                    {{ $step['label'] }}
                                                </p>
                                                @if($isCurrent)
                                                    <p class="text-xs text-blue-500 mt-1">← Vous êtes ici</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        @if($isRejected)
                            <div class="border-t border-red-200 pt-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-red-500 text-white text-lg">
                                        ❌
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-red-700">Candidature rejetée</p>
                                        @if($candidature->motif_rejet)
                                            <p class="text-xs text-red-500 mt-1">{{ $candidature->motif_rejet }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations détaillées selon l'étape -->
                @if(in_array($candidature->statut->value, ['attente_test', 'test_planifie']) && $candidature->date_test)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-4">📝 Informations sur votre test</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-yellow-700 font-medium">Date du test</p>
                                <p class="text-yellow-800 font-semibold">{{ \Carbon\Carbon::parse($candidature->date_test)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-yellow-100 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <strong>Rappel :</strong> Veuillez vous présenter 15 minutes avant l'heure prévue avec une pièce d'identité valide.
                            </p>
                        </div>
                    </div>
                @endif

                @if(in_array($candidature->statut->value, ['test_passe', 'attente_resultats']) && $candidature->note_test)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4">📊 Résultats de votre test</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-blue-700 font-medium">Note obtenue</p>
                                <p class="text-blue-800 font-semibold text-2xl">{{ $candidature->note_test }}/20</p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700 font-medium">Résultat</p>
                                <p class="text-blue-800 font-semibold">
                                    @if($candidature->resultat_test === 'admis')
                                        ✅ Admis
                                    @elseif($candidature->resultat_test === 'ajourne')
                                        ⏳ Ajourné
                                    @else
                                        {{ ucfirst($candidature->resultat_test ?? 'En attente') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(in_array($candidature->statut->value, ['affecte', 'reponse_lettre_envoyee', 'induction_planifiee', 'induction_terminee', 'accueil_service', 'stage_en_cours', 'en_evaluation', 'evaluation_terminee', 'attestation_generee', 'remboursement_en_cours', 'termine']))
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-green-800 mb-4">🏢 Informations d'affectation</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($candidature->service_affecte)
                                <div>
                                    <p class="text-sm text-green-700 font-medium">Service d'affectation</p>
                                    <p class="text-green-800">
                                        @php
                                            $directions = \App\Models\Candidature::getDirectionsDisponibles();
                                        @endphp
                                        {{ $directions[$candidature->service_affecte] ?? $candidature->service_affecte }}
                                    </p>
                                </div>
                            @endif
                            @if($candidature->tuteur)
                                <div>
                                    <p class="text-sm text-green-700 font-medium">Tuteur de stage</p>
                                    <p class="text-green-800">{{ $candidature->tuteur->name }}</p>
                                </div>
                            @endif
                            @if($candidature->date_debut_stage)
                                <div>
                                    <p class="text-sm text-green-700 font-medium">Date de début</p>
                                    <p class="text-green-800">{{ $candidature->date_debut_stage->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($candidature->date_fin_stage)
                                <div>
                                    <p class="text-sm text-green-700 font-medium">Date de fin</p>
                                    <p class="text-green-800">{{ $candidature->date_fin_stage->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($candidature->date_debut_stage && $candidature->date_fin_stage)
                                <div>
                                    <p class="text-sm text-green-700 font-medium">Durée</p>
                                    <p class="text-green-800">{{ $candidature->date_debut_stage->diffInDays($candidature->date_fin_stage) }} jours</p>
                                </div>
                            @endif
                        </div>
                        
                        @if($candidature->programme_stage)
                            <div class="mt-4 p-3 bg-green-100 rounded-lg">
                                <p class="text-sm text-green-700 font-medium mb-2">Programme de stage</p>
                                <p class="text-sm text-green-800">{{ $candidature->programme_stage }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if(in_array($candidature->statut->value, ['induction_terminee', 'accueil_service', 'stage_en_cours', 'en_evaluation', 'evaluation_terminee', 'attestation_generee', 'remboursement_en_cours', 'termine']) && $candidature->date_induction)
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-purple-800 mb-4">🎓 Dates clés de votre intégration</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @if($candidature->date_induction)
                                <div class="text-center p-3 bg-purple-100 rounded-lg">
                                    <p class="text-xs text-purple-600 font-medium">Induction RH</p>
                                    <p class="text-purple-800 font-semibold">{{ \Carbon\Carbon::parse($candidature->date_induction)->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($candidature->date_accueil_service)
                                <div class="text-center p-3 bg-purple-100 rounded-lg">
                                    <p class="text-xs text-purple-600 font-medium">Accueil service</p>
                                    <p class="text-purple-800 font-semibold">{{ \Carbon\Carbon::parse($candidature->date_accueil_service)->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($candidature->date_evaluation)
                                <div class="text-center p-3 bg-purple-100 rounded-lg">
                                    <p class="text-xs text-purple-600 font-medium">Évaluation</p>
                                    <p class="text-purple-800 font-semibold">{{ \Carbon\Carbon::parse($candidature->date_evaluation)->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if(in_array($candidature->statut->value, ['evaluation_terminee', 'attestation_generee', 'remboursement_en_cours', 'termine']) && $candidature->note_evaluation)
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-orange-800 mb-4">⭐ Évaluation de fin de stage</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-orange-700 font-medium">Note finale</p>
                                <p class="text-orange-800 font-semibold text-3xl">{{ $candidature->note_evaluation }}/20</p>
                            </div>
                            @if($candidature->commentaire_evaluation)
                                <div>
                                    <p class="text-sm text-orange-700 font-medium">Commentaires du tuteur</p>
                                    <p class="text-orange-800 text-sm">{{ $candidature->commentaire_evaluation }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if(in_array($candidature->statut->value, ['attestation_generee', 'remboursement_en_cours', 'termine']) && $candidature->attestation_generee)
                    <div class="bg-teal-50 border border-teal-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-teal-800 mb-4">📜 Attestation de stage</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-teal-700">Votre attestation a été générée le {{ $candidature->date_attestation ? \Carbon\Carbon::parse($candidature->date_attestation)->format('d/m/Y') : '' }}</p>
                                <p class="text-xs text-teal-600 mt-1">Vous pouvez la récupérer auprès du service RH</p>
                            </div>
                            <div class="text-4xl">📄</div>
                        </div>
                    </div>
                @endif

                @if($candidature->statut->value === 'termine' && $candidature->remboursement_effectue)
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-emerald-800 mb-4">💰 Remboursement transport</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-emerald-700 font-medium">Montant remboursé</p>
                                <p class="text-emerald-800 font-semibold text-xl">{{ number_format($candidature->montant_transport ?? 0, 0, ',', ' ') }} FCFA</p>
                            </div>
                            @if($candidature->date_remboursement)
                                <div>
                                    <p class="text-sm text-emerald-700 font-medium">Date du remboursement</p>
                                    <p class="text-emerald-800">{{ \Carbon\Carbon::parse($candidature->date_remboursement)->format('d/m/Y') }}</p>
                                </div>
                            @endif
                            @if($candidature->reference_paiement)
                                <div>
                                    <p class="text-sm text-emerald-700 font-medium">Référence</p>
                                    <p class="text-emerald-800 font-mono">{{ $candidature->reference_paiement }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Message de fin de stage -->
                @if($candidature->statut->value === 'termine')
                    <div class="bg-gradient-to-r from-bracongo-red-500 to-bracongo-red-600 rounded-xl p-6 text-white">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">🎉</div>
                            <div>
                                <h3 class="text-xl font-bold">Félicitations !</h3>
                                <p class="text-bracongo-red-100 mt-1">
                                    Votre stage chez BRACONGO est officiellement terminé. Nous vous remercions pour votre contribution et vous souhaitons une excellente continuation dans votre parcours professionnel.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Rejection reason (if rejected) -->
                @if($candidature->statut->value === 'rejete' && $candidature->motif_rejet)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-4">❌ Motif du rejet</h3>
                        <p class="text-red-700">{{ $candidature->motif_rejet }}</p>
                        <div class="mt-4 p-3 bg-red-100 rounded-lg">
                            <p class="text-sm text-red-800">
                                Nous vous remercions pour l'intérêt que vous portez à BRACONGO. N'hésitez pas à soumettre une nouvelle candidature pour une prochaine opportunité.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Documents -->
                @if($candidature->documents->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📎 Documents soumis</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($candidature->documents as $document)
                                <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="text-2xl mr-3 flex-shrink-0">{{ $document->icone }}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" title="{{ $document->nom_original }}">
                                            {{ $document->nom_original }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $document->taille_formatee }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <a href="/suivi" class="text-bracongo-red-600 hover:text-bracongo-red-700 font-medium text-center sm:text-left">
                        ← Rechercher une autre candidature
                    </a>
                    <a href="/candidature" class="text-bracongo-red-600 hover:text-bracongo-red-700 font-medium text-center sm:text-right">
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