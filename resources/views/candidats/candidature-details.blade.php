@extends('layouts.modern')

@section('title', 'Détails de la candidature - BRACONGO')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 to-red-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-bracongo-red-600 mb-6">
            <div class="bg-gradient-to-r from-bracongo-red-600 via-bracongo-red-700 to-bracongo-red-800 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img class="h-12 w-auto rounded-full" src="{{ asset('images/logo.png') }}" alt="BRACONGO"/>
                        <div class="ml-4">
                            <h1 class="text-3xl font-bold text-white">Détails de la candidature</h1>
                            <p class="text-yellow-200 text-sm">Code de suivi: {{ $candidature->code_suivi }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($candidature->statut->value === 'non_traite') bg-yellow-100 text-yellow-800
                            @elseif($candidature->statut->value === 'en_cours') bg-blue-100 text-blue-800
                            @elseif($candidature->statut->value === 'accepte') bg-green-100 text-green-800
                            @elseif($candidature->statut->value === 'refuse') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $candidature->statut->getLabel() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mb-6">
            <a href="{{ route('candidat.candidatures') }}" 
               class="inline-flex items-center text-bracongo-red-600 hover:text-bracongo-red-700 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour à mes candidatures
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations de la candidature -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-bracongo-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informations personnelles
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nom complet</label>
                            <p class="text-lg text-gray-900">{{ $candidature->nom }} {{ $candidature->prenom }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-lg text-gray-900">{{ $candidature->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Téléphone</label>
                            <p class="text-lg text-gray-900">{{ $candidature->telephone }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Établissement</label>
                            <p class="text-lg text-gray-900">
                                {{ $candidature->etablissement === 'Autres' ? $candidature->etablissement_autre : $candidature->etablissement }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Niveau d'étude</label>
                            <p class="text-lg text-gray-900">{{ $candidature->niveau_etude }}</p>
                        </div>
                        @if($candidature->faculte)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Faculté</label>
                            <p class="text-lg text-gray-900">{{ $candidature->faculte }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Détails du stage -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-bracongo-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                        </svg>
                        Détails du stage souhaité
                    </h2>
                    
                    @if($candidature->opportunite)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500">Opportunité</label>
                        <p class="text-lg text-gray-900">{{ $candidature->opportunite->titre }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500">Objectif du stage</label>
                        <p class="text-gray-900">{{ $candidature->objectif_stage }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date de début souhaitée</label>
                            <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($candidature->periode_debut_souhaitee)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date de fin souhaitée</label>
                            <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($candidature->periode_fin_souhaitee)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    @if($candidature->directions_souhaitees && count($candidature->directions_souhaitees) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Directions souhaitées</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($candidature->directions_souhaitees as $direction)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-bracongo-red-100 text-bracongo-red-800">
                                    {{ $direction }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Documents soumis -->
                @if($candidature->documents && $candidature->documents->count() > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-bracongo-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Documents soumis
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($candidature->documents as $document)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-bracongo-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->type_document)) }}</p>
                                        <p class="text-xs text-gray-500">{{ $document->nom_original }}</p>
                                        <p class="text-xs text-gray-400">{{ $document->taille_formatee }}</p>
                                    </div>
                                </div>
                                @if($document->fichierExiste())
                                <a href="{{ route('candidat.document.download', $document->id) }}" 
                                   class="text-bracongo-red-600 hover:text-bracongo-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m3-1a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Évaluation -->
                @if($candidature->evaluation)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-6 h-6 text-bracongo-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Évaluation RH
                    </h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-500">Note globale</label>
                            <div class="flex items-center">
                                <span class="text-2xl font-bold text-bracongo-red-600">{{ $candidature->evaluation->note_globale }}/5</span>
                                <div class="ml-3 flex">
                                    @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $candidature->evaluation->note_globale ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        
                        @if($candidature->evaluation->commentaires)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Commentaires</label>
                            <p class="text-gray-700">{{ $candidature->evaluation->commentaires }}</p>
                        </div>
                        @endif
                        
                        <div class="mt-3 text-xs text-gray-500">
                            Évaluée le {{ $candidature->evaluation->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statut et dates -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de suivi</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date de soumission</label>
                            <p class="text-sm text-gray-900">{{ $candidature->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Dernière mise à jour</label>
                            <p class="text-sm text-gray-900">{{ $candidature->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Code de suivi</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <code class="text-sm font-mono text-bracongo-red-600">{{ $candidature->code_suivi }}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('candidature.suivi.code', $candidature->code_suivi) }}" 
                           class="w-full bg-bracongo-red-600 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-bracongo-red-700 transition duration-200 block">
                            Suivi public
                        </a>
                        
                        @if($candidature->documents->count() > 0)
                        <button onclick="downloadAllDocuments()" 
                                class="w-full bg-gray-600 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-gray-700 transition duration-200">
                            Télécharger tous les documents
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Conseils -->
                <div class="bg-gradient-to-br from-blue-50 to-green-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">💡 Conseils</h4>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>• Gardez ce code de suivi précieusement</li>
                        <li>• Vous serez contacté dans les 7-14 jours</li>
                        <li>• Vérifiez régulièrement vos emails</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadAllDocuments() {
    @if($candidature->documents->count() > 0)
        @foreach($candidature->documents as $document)
            @if($document->fichierExiste())
                setTimeout(() => {
                    window.open('{{ route("candidat.document.download", $document->id) }}', '_blank');
                }, {{ $loop->index * 500 }});
            @endif
        @endforeach
    @endif
}
</script>
@endsection