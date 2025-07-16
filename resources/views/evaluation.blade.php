@extends('layouts.modern')

@section('title', 'Évaluation de Stage - BRACONGO')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-red-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo-bracongo.png') }}" alt="BRACONGO" class="h-12 w-auto">
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-900">Évaluation de Stage</h1>
                        <p class="text-gray-600">Partagez votre expérience avec nous</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Code: {{ $candidature->code_suivi }}</p>
                    <p class="text-sm text-gray-500">{{ $candidature->nom_complet }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- En-tête de l'évaluation -->
            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-8 py-6">
                <h2 class="text-2xl font-bold text-white mb-2">Votre Évaluation de Stage</h2>
                <p class="text-orange-100">
                    Stage du {{ $candidature->date_debut_stage?->format('d/m/Y') }} 
                    au {{ $candidature->date_fin_stage?->format('d/m/Y') }}
                </p>
            </div>

            <!-- Formulaire d'évaluation -->
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('evaluation.store', $candidature) }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Section 1: Satisfaction générale -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Satisfaction Générale</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Comment évaluez-vous votre expérience de stage chez BRACONGO ?
                                </label>
                                <div class="flex space-x-4">
                                    @foreach(['1' => 'Très décevant', '2' => 'Décevant', '3' => 'Moyen', '4' => 'Satisfaisant', '5' => 'Excellent'] as $value => $label)
                                        <label class="flex items-center">
                                            <input type="radio" name="satisfaction_generale" value="{{ $value }}" 
                                                   class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500" required>
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Recommanderiez-vous BRACONGO à d'autres étudiants ?
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="recommandation" value="oui" 
                                               class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500" required>
                                        <span class="ml-2 text-sm text-gray-700">Oui, absolument</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="recommandation" value="peut_etre" 
                                               class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <span class="ml-2 text-sm text-gray-700">Peut-être</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="recommandation" value="non" 
                                               class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <span class="ml-2 text-sm text-gray-700">Non</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Environnement de travail -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Environnement de Travail</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Accueil et intégration
                                </label>
                                <select name="accueil_integration" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="excellent">Excellent</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="insuffisant">Insuffisant</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Encadrement et suivi
                                </label>
                                <select name="encadrement_suivi" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="excellent">Excellent</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="insuffisant">Insuffisant</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Conditions de travail
                                </label>
                                <select name="conditions_travail" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="excellent">Excellent</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="insuffisant">Insuffisant</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ambiance de travail
                                </label>
                                <select name="ambiance_travail" class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="excellent">Excellent</option>
                                    <option value="bon">Bon</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="insuffisant">Insuffisant</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Apprentissages -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Apprentissages et Compétences</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Quelles compétences avez-vous développées pendant ce stage ?
                                </label>
                                <textarea name="competences_developpees" rows="4" 
                                          class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Décrivez les compétences techniques, relationnelles, organisationnelles..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ce stage a-t-il répondu à vos attentes ?
                                </label>
                                <textarea name="reponse_attentes" rows="3" 
                                          class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="En quoi ce stage a-t-il répondu ou non à vos attentes initiales ?"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Quels aspects du stage ont été les plus enrichissants ?
                                </label>
                                <textarea name="aspects_enrichissants" rows="3" 
                                          class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Projets, missions, rencontres, découvertes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Améliorations -->
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Suggestions d'Amélioration</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Que pourrions-nous améliorer pour les futurs stagiaires ?
                                </label>
                                <textarea name="suggestions_amelioration" rows="4" 
                                          class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                          placeholder="Vos suggestions pour améliorer l'expérience des futurs stagiaires..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Souhaiteriez-vous rester en contact avec BRACONGO ?
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="contact_futur" value="oui" 
                                               class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500" required>
                                        <span class="ml-2 text-sm text-gray-700">Oui, pour des opportunités futures</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="contact_futur" value="non" 
                                               class="h-4 w-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                        <span class="ml-2 text-sm text-gray-700">Non</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Commentaire libre -->
                    <div class="pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Commentaire Libre</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Avez-vous d'autres commentaires ou suggestions ?
                            </label>
                            <textarea name="commentaire_libre" rows="4" 
                                      class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500"
                                      placeholder="Tout autre commentaire que vous souhaitez partager..."></textarea>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('candidature.suivi.code', $candidature->code_suivi) }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Retour au suivi
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg hover:from-orange-600 hover:to-red-700 transition-all transform hover:scale-105">
                            Envoyer l'évaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 