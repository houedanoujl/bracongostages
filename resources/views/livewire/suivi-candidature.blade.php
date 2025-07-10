<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!$showDetails)
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-orange-600 mb-4">
                        🔍 Suivi de Candidature
                    </h1>
                    <p class="text-gray-600">
                        Entrez votre code de suivi pour consulter l'état de votre candidature
                    </p>
                </div>

                <form wire:submit.prevent="searchCandidature" class="max-w-md mx-auto">
                    <div class="mb-6">
                        <label for="searchCode" class="block text-sm font-medium text-gray-700 mb-2">
                            Code de suivi
                        </label>
                        <input 
                            wire:model="searchCode"
                            type="text" 
                            id="searchCode"
                            placeholder="Ex: BRC-ABCD1234" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                        @error('searchCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 transition duration-300 shadow-lg"
                    >
                        Rechercher ma candidature
                    </button>
                </form>
                
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
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $this->getStatutColor() }}-100 text-{{ $this->getStatutColor() }}-800">
                                {{ $this->getStatutLabel() }}
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Code: {{ $candidature->code_suivi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Progression de votre candidature</h2>
                    
                    <div class="space-y-6">
                        @foreach($timelineSteps as $step)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $step['completed'] ? 'bg-green-500' : 'bg-gray-300' }} {{ isset($step['rejected']) ? 'bg-red-500' : '' }}">
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
                                        @if($step['date'])
                                            <p class="text-xs text-gray-500">{{ $step['date']->format('d/m/Y H:i') }}</p>
                                        @endif
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

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <button wire:click="resetSearch" class="text-orange-600 hover:text-orange-700 font-medium">
                        ← Rechercher une autre candidature
                    </button>
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