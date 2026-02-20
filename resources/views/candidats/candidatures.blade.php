@extends('layouts.modern')

@section('title', 'Mes candidatures - BRACONGO Stages')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-yellow-50 to-red-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-bracongo-red-600 mb-6">
            <div class="bg-gradient-to-r from-bracongo-red-600 via-bracongo-red-700 to-bracongo-red-800 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img class="h-12 w-auto rounded-full" src="{{ asset('images/logo.png') }}" alt="BRACONGO"/>
                        <div class="ml-4">
                            <h1 class="text-3xl font-bold text-white">Mes candidatures</h1>
                            <p class="text-yellow-200 text-sm">{{ $candidatures->total() }} candidature(s) au total</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('candidat.dashboard') }}" 
                           class="inline-flex items-center text-white hover:text-yellow-200 font-medium text-sm">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des candidatures -->
        @if($candidatures->count() > 0)
            <div class="space-y-4">
                @foreach($candidatures as $candidature)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <!-- Infos principales -->
                                <div class="flex-1">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-bracongo-red-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-bracongo-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $candidature->opportunite ? $candidature->opportunite->titre : 'Candidature générale' }}
                                            </h3>
                                            @if($candidature->poste_souhaite)
                                                <p class="text-sm text-gray-600 mt-1">{{ $candidature->poste_souhaite }}</p>
                                            @endif
                                            <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Postulé le {{ $candidature->created_at->format('d/m/Y') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                                    </svg>
                                                    {{ $candidature->code_suivi }}
                                                </span>
                                                @if($candidature->etablissement)
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        {{ $candidature->etablissement === 'Autres' ? $candidature->etablissement_autre : $candidature->etablissement }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statut et actions -->
                                <div class="mt-4 md:mt-0 md:ml-6 flex flex-col items-end space-y-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @switch($candidature->statut->value)
                                            @case('non_traite')
                                            @case('dossier_recu')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('analyse_dossier')
                                            @case('attente_test')
                                            @case('attente_resultats')
                                            @case('attente_affectation')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('admis')
                                            @case('en_stage')
                                            @case('valide')
                                            @case('stage_termine')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('rejete')
                                            @case('non_admis')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ $candidature->statut->getLabel() }}
                                    </span>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('candidat.candidature', $candidature->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-bracongo-red-600 text-bracongo-red-600 text-sm font-medium rounded-lg hover:bg-bracongo-red-50 transition duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Détails
                                        </a>
                                        <a href="{{ route('candidature.suivi.code', $candidature->code_suivi) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Suivi
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Barre de progression si applicable -->
                            @if($candidature->evaluation)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Évaluation disponible
                                        @if($candidature->evaluation->note_evaluation)
                                            — Note : {{ $candidature->evaluation->note_evaluation }}/20
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($candidature->date_test)
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Test prévu le {{ $candidature->date_test->format('d/m/Y') }}
                                        @if($candidature->lieu_test)
                                            à {{ $candidature->lieu_test }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $candidatures->links() }}
            </div>
        @else
            <!-- État vide -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune candidature</h3>
                <p class="mt-2 text-gray-500">Vous n'avez pas encore postulé à des opportunités de stage.</p>
                <div class="mt-6">
                    <a href="{{ route('opportunites') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-bracongo-red-600 hover:bg-bracongo-red-700 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Découvrir les opportunités
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
