@extends('layouts.modern')

@section('title', 'Tableau de bord - BRACONGO Stages')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    @if($candidat->photo_url)
                        <img src="{{ $candidat->photo_url }}" alt="Photo de profil" class="w-12 h-12 rounded-full object-cover">
                    @else
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ substr($candidat->prenom, 0, 1) }}{{ substr($candidat->nom, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Bonjour {{ $candidat->prenom }} !</h1>
                        <p class="text-gray-600">{{ $candidat->email }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('candidat.profile') }}" class="text-blue-600 hover:text-blue-500">Mon profil</a>
                    <form method="POST" action="{{ route('candidat.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-500">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total candidatures</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $candidatures->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Candidatures actives</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $candidatures->whereNotIn('statut', ['valide', 'rejete'])->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En cours</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $candidatures->whereIn('statut', ['analyse_dossier', 'attente_test', 'attente_resultats', 'attente_affectation'])->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Opportunités</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $opportunites->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Mes candidatures -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow mb-10">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Mes candidatures récentes</h2>
                    </div>
                    <div class="p-6">
                        @if($candidatures->count() > 0)
                            <div class="space-y-4">
                                @foreach($candidatures->take(5) as $candidature)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h3 class="font-medium text-gray-900">
                                                    {{ $candidature->opportunite ? $candidature->opportunite->titre : 'Candidature générale' }}
                                                </h3>
                                                <p class="text-sm text-gray-600">{{ $candidature->poste_souhaite }}</p>
                                                <p class="text-xs text-gray-500">Postulé le {{ $candidature->created_at->format('d/m/Y') }}</p>
                                            </div>
                                            <div class="ml-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($candidature->statut->value === 'valide') bg-green-100 text-green-800
                                                    @elseif($candidature->statut->value === 'rejete') bg-red-100 text-red-800
                                                    @else bg-blue-100 text-blue-800 @endif">
                                                    {{ $candidature->statut->getLabel() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex space-x-2">
                                            <a href="{{ route('candidat.candidature', $candidature->id) }}" 
                                                class="text-sm text-blue-600 hover:text-blue-500">Voir les détails</a>
                                            <a href="{{ route('candidature.suivi.code', $candidature->code_suivi) }}" 
                                                class="text-sm text-gray-600 hover:text-gray-500">Suivre en ligne</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($candidatures->count() > 5)
                                <div class="mt-4 text-center">
                                    <a href="{{ route('candidat.candidatures') }}" 
                                        class="text-blue-600 hover:text-blue-500 font-medium">
                                        Voir toutes mes candidatures
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune candidature</h3>
                                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore postulé à d'opportunités.</p>
                                <div class="mt-6">
                                    <a href="{{ route('opportunites') }}" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        Voir les opportunités
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                   <!-- Opportunités récentes -->
                   <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Opportunités récentes</h2>
                    </div>
                    <div class="p-6">
                        @if($opportunites->count() > 0)
                            <div class="space-y-3">
                                @foreach($opportunites as $opportunite)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $opportunite->titre }}</h3>
                                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($opportunite->description, 80) }}</p>
                                        <div class="mt-2">
                                            <a href="{{ route('opportunite.detail', $opportunite->slug) }}" 
                                                class="text-xs text-blue-600 hover:text-blue-500">Voir l'opportunité</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucune opportunité disponible pour le moment.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Profil rapide -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Mon profil</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Établissement</p>
                                <p class="text-sm text-gray-900">{{ $candidat->etablissement ?: 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Niveau d'étude</p>
                                <p class="text-sm text-gray-900">{{ $candidat->niveau_etude ?: 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">CV</p>
                                @if($candidat->hasCv())
                                    <a href="{{ route('candidat.download-cv') }}" 
                                        class="text-sm text-blue-600 hover:text-blue-500">Télécharger mon CV</a>
                                @else
                                    <p class="text-sm text-gray-500">Aucun CV</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('candidat.profile') }}" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Modifier mon profil
                            </a>
                        </div>
                    </div>
                </div>

             
            </div>
        </div>
    </div>
</div>
@endsection 