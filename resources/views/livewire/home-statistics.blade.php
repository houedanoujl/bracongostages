<div class="py-12 bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Statistiques -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">BRACONGO en Chiffres</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Découvrez l'impact de notre programme de stages et rejoignez une communauté d'excellence
            </p>
        </div>

        <!-- Grille des Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-16">
            @foreach($statistics as $stat)
                <div class="bg-white rounded-xl shadow-lg p-6 text-center transform hover:scale-105 transition-transform duration-200">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-{{ $stat['color'] }}-100 text-{{ $stat['color'] }}-600 rounded-lg mb-4">
                        @switch($stat['icon'])
                            @case('users')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                @break
                            @case('check-circle')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @break
                            @case('clock')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @break
                            @case('academic-cap')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                </svg>
                                @break
                            @case('building-office')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                @break
                            @case('heart')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                @break
                            @case('star')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                @break
                            @case('building-library')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                                @break
                            @default
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                        @endswitch
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $stat['value'] }}</h3>
                    <p class="text-gray-600 text-sm font-medium">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Section Top Établissements -->
        @if(count($topEtablissements) > 0)
            <div class="bg-white rounded-xl shadow-lg p-8 mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Top Établissements Partenaires</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($topEtablissements as $etablissement)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $etablissement['nom'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $etablissement['count'] }} candidatures</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Section Témoignages -->
        @if(count($temoignages) > 0)
            <div class="mb-16">
                <div class="text-center mb-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Témoignages de nos Anciens Stagiaires</h3>
                    <p class="text-lg text-gray-600">Découvrez les expériences de ceux qui ont commencé leur carrière chez BRACONGO</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($temoignages as $temoignage)
                        <div class="bg-white rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                            <!-- Photo et informations -->
                            <div class="flex items-center mb-4">
                                @if($temoignage['photo_url'])
                                    <img src="{{ $temoignage['photo_url'] }}" alt="{{ $temoignage['nom_complet'] }}" class="w-12 h-12 rounded-full object-cover mr-4">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                        {{ substr($temoignage['nom_complet'], 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $temoignage['nom_complet'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $temoignage['poste_occupe'] }}</p>
                                    @if($temoignage['entreprise'] !== 'BRACONGO')
                                        <p class="text-xs text-blue-600">{{ $temoignage['entreprise'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Citation -->
                            @if($temoignage['citation_courte'])
                                <blockquote class="text-gray-700 italic mb-4 border-l-4 border-blue-500 pl-4">
                                    "{{ $temoignage['citation_courte'] }}"
                                </blockquote>
                            @endif

                            <!-- Détails du stage -->
                            <div class="space-y-2 text-sm">
                                @if($temoignage['direction_stage'])
                                    <p class="text-gray-600">
                                        <span class="font-medium">Direction:</span> {{ $temoignage['direction_stage'] }}
                                    </p>
                                @endif
                                @if($temoignage['duree_stage_formattee'])
                                    <p class="text-gray-600">
                                        <span class="font-medium">Durée:</span> {{ $temoignage['duree_stage_formattee'] }}
                                    </p>
                                @endif
                                @if($temoignage['etablissement_origine'])
                                    <p class="text-gray-600">
                                        <span class="font-medium">Formation:</span> {{ $temoignage['etablissement_origine'] }}
                                    </p>
                                @endif
                            </div>

                            <!-- Note d'évaluation -->
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-yellow-400">
                                    {{ $temoignage['etoiles'] }}
                                </div>
                                <span class="text-xs text-gray-500">{{ $temoignage['note_experience'] }}/5</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bouton de rafraîchissement pour l'admin -->
        @auth
            <div class="text-center">
                <button wire:click="refreshStatistics" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Actualiser les statistiques
                </button>
            </div>
        @endauth
    </div>
</div> 