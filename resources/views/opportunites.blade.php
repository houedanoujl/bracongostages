@extends('layouts.modern')

@section('title', 'Opportunités de Stage - BRACONGO')

@section('content')
<!-- Hero Section pour Opportunités -->
<section id="heropportunites" class="hero-modern bg-gradient-to-br from-orange-50 to-red-50">
    <div class="hero-content">
        <h1 class="hero-title">
            Opportunités de 
            <span class="text-gradient">Stage</span>
        </h1>
        <p class="hero-subtitle">
            Découvrez nos programmes de stage dans différents domaines et développez vos compétences 
            au sein de l'une des entreprises les plus innovantes du Congo
        </p>
        <div class="hero-cta">
            <a href="/candidature" class="btn-primary-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Postuler maintenant
            </a>
            <a href="#filtres" class="btn-secondary-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                </svg>
                Filtrer les stages
            </a>
        </div>
    </div>
</section>

<!-- Section Filtres -->
<section class="py-12 bg-white" id="filtres">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-bracongo-gray-50 rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-bracongo-gray-900 mb-6">Filtrer les Opportunités</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Filtre par domaine -->
                <div>
                    <label class="block text-sm font-medium text-bracongo-gray-700 mb-2">Domaine</label>
                    <select id="filtre-domaine" class="w-full border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange">
                        <option value="">Tous les domaines</option>
                        <option value="marketing">Marketing & Communication</option>
                        <option value="finance">Finance & Comptabilité</option>
                        <option value="rh">Ressources Humaines</option>
                        <option value="production">Production & Logistique</option>
                        <option value="informatique">Informatique & Digital</option>
                        <option value="commercial">Commercial & Ventes</option>
                    </select>
                </div>

                <!-- Filtre par niveau -->
                <div>
                    <label class="block text-sm font-medium text-bracongo-gray-700 mb-2">Niveau d'étude</label>
                    <select id="filtre-niveau" class="w-full border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange">
                        <option value="">Tous les niveaux</option>
                        <option value="bac+2">BAC+2</option>
                        <option value="bac+3">BAC+3</option>
                        <option value="bac+4">BAC+4</option>
                        <option value="bac+5">BAC+5</option>
                    </select>
                </div>

                <!-- Filtre par durée -->
                <div>
                    <label class="block text-sm font-medium text-bracongo-gray-700 mb-2">Durée</label>
                    <select id="filtre-duree" class="w-full border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange">
                        <option value="">Toutes les durées</option>
                        <option value="1-2">1-2 mois</option>
                        <option value="3-4">3-4 mois</option>
                        <option value="5-6">5-6 mois</option>
                        <option value="6+">6+ mois</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-center">
                <button id="appliquer-filtres" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                    </svg>
                    Appliquer les filtres
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Section Opportunités -->
<section class="py-16 bg-bracongo-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-bracongo-gray-900 mb-4">Nos Opportunités de Stage</h2>
            <p class="text-lg text-bracongo-gray-600 max-w-3xl mx-auto">
                Découvrez nos programmes de stage dans différents domaines. Chaque opportunité est conçue 
                pour vous permettre de développer vos compétences et de vous préparer à votre future carrière.
            </p>
        </div>

        @php
            $opportunites = \App\Models\Opportunite::publiee()->ordonne()->get();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="opportunites-grid">
            @forelse($opportunites as $opportunite)
                <div class="opportunity-card-full bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- En-tête de la carte -->
                    <div class="p-6 border-b border-bracongo-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-4xl">{{ $opportunite->icone }}</div>
                            <span class="badge-modern badge-{{ $opportunite->statut === 'active' ? 'success' : 'warning' }}">
                                {{ $opportunite->statut === 'active' ? 'Disponible' : 'Bientôt disponible' }}
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-bracongo-gray-900 mb-2">{{ $opportunite->titre }}</h3>
                        <p class="text-bracongo-gray-600 text-sm leading-relaxed">{{ $opportunite->description }}</p>
                    </div>

                    <!-- Détails de la carte -->
                    <div class="p-6">
                        <div class="space-y-4 mb-6">
                            <!-- Informations clés -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="flex items-center text-bracongo-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $opportunite->duree }}</span>
                                </div>
                                <div class="flex items-center text-bracongo-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>{{ $opportunite->places_restantes }} place(s)</span>
                                </div>
                            </div>

                            <!-- Niveau requis -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-bracongo-gray-600">Niveau requis :</span>
                                <span class="badge-modern badge-info">
                                    {{ \App\Models\Opportunite::getNiveauxRequis()[$opportunite->niveau_requis] ?? $opportunite->niveau_requis }}
                                </span>
                            </div>

                            <!-- Compétences requises -->
                            @if(!empty($opportunite->competences_requises))
                                <div>
                                    <h4 class="text-sm font-medium text-bracongo-gray-700 mb-2">Compétences requises :</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($opportunite->competences_requises, 0, 4) as $skill)
                                            <span class="px-3 py-1 bg-bracongo-gray-100 text-bracongo-gray-700 text-xs rounded-full">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                        @if(count($opportunite->competences_requises) > 4)
                                            <span class="px-3 py-1 bg-bracongo-gray-100 text-bracongo-gray-700 text-xs rounded-full">
                                                +{{ count($opportunite->competences_requises) - 4 }} autres
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Missions principales -->
                            @if(!empty($opportunite->missions_principales))
                                <div>
                                    <h4 class="text-sm font-medium text-bracongo-gray-700 mb-2">Missions principales :</h4>
                                    <ul class="text-sm text-bracongo-gray-600 space-y-1">
                                        @foreach(array_slice($opportunite->missions_principales, 0, 3) as $mission)
                                            <li class="flex items-start">
                                                <svg class="w-3 h-3 mr-2 mt-1 text-bracongo-orange flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $mission }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-bracongo-gray-100">
                            <a href="/candidature?domain={{ $opportunite->slug }}" class="btn-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Postuler
                            </a>
                            <button class="text-bracongo-orange hover:text-bracongo-red font-medium text-sm transition-colors" 
                                    onclick="afficherDetails('{{ $opportunite->id }}')">
                                Voir détails
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-bracongo-gray-900 mb-2">Aucune opportunité trouvée</h3>
                    <p class="text-bracongo-gray-600 mb-6">Aucune opportunité de stage ne correspond à vos critères pour le moment.</p>
                    <a href="/candidature" class="btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Postuler quand même
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination ou "Voir plus" -->
        @if($opportunites->count() > 6)
            <div class="text-center mt-12">
                <button class="btn-secondary-large" onclick="chargerPlus()">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    Voir plus d'opportunités
                </button>
            </div>
        @endif
    </div>
</section>

<!-- Section Avantages -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-bracongo-gray-900 mb-4">Pourquoi choisir BRACONGO ?</h2>
            <p class="text-lg text-bracongo-gray-600 max-w-3xl mx-auto">
                Rejoignez une entreprise leader et bénéficiez d'une expérience professionnelle enrichissante
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-bracongo-orange/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-bracongo-gray-900 mb-2">Expérience Concrète</h3>
                <p class="text-bracongo-gray-600 text-sm">Travaillez sur des projets réels et développez des compétences pratiques</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-bracongo-orange/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-bracongo-gray-900 mb-2">Encadrement Qualifié</h3>
                <p class="text-bracongo-gray-600 text-sm">Bénéficiez du suivi d'experts expérimentés dans votre domaine</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-bracongo-orange/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-bracongo-gray-900 mb-2">Certification</h3>
                <p class="text-bracongo-gray-600 text-sm">Obtenez une attestation de stage reconnue par les employeurs</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-bracongo-orange/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-bracongo-gray-900 mb-2">Opportunités Futures</h3>
                <p class="text-bracongo-gray-600 text-sm">Augmentez vos chances d'être recruté après votre stage</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-bracongo-orange to-bracongo-red">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Prêt à rejoindre l'équipe BRACONGO ?</h2>
        <p class="text-xl text-orange-100 mb-8">
            Commencez votre aventure professionnelle dès aujourd'hui
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/candidature" class="btn-white-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Postuler maintenant
            </a>
            <a href="/contact" class="btn-outline-white-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Nous contacter
            </a>
        </div>
    </div>
</section>

<!-- Modal pour les détails -->
<div id="modal-details" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-bracongo-gray-900" id="modal-title">Détails de l'opportunité</h3>
                    <button onclick="fermerModal()" class="text-bracongo-gray-400 hover:text-bracongo-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="modal-content">
                    <!-- Le contenu sera chargé dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonctionnalités JavaScript pour les filtres et modals
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres
    document.getElementById('appliquer-filtres').addEventListener('click', function() {
        // Logique de filtrage à implémenter
        console.log('Filtres appliqués');
    });

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('modal-details').addEventListener('click', function(e) {
        if (e.target === this) {
            fermerModal();
        }
    });
});

function afficherDetails(opportuniteId) {
    // Logique pour afficher les détails dans le modal
    document.getElementById('modal-details').classList.remove('hidden');
}

function fermerModal() {
    document.getElementById('modal-details').classList.add('hidden');
}

function chargerPlus() {
    // Logique pour charger plus d'opportunités
    console.log('Chargement de plus d\'opportunités');
}
</script>

<style>
.opportunity-card-full {
    transition: all 0.3s ease;
}

.opportunity-card-full:hover {
    transform: translateY(-4px);
}

.badge-modern {
    @apply px-3 py-1 text-xs font-medium rounded-full;
}

.badge-success {
    @apply bg-green-100 text-green-800;
}

.badge-warning {
    @apply bg-yellow-100 text-yellow-800;
}

.badge-info {
    @apply bg-blue-100 text-blue-800;
}

.btn-primary {
    @apply inline-flex items-center px-4 py-2 bg-bracongo-orange text-white rounded-lg hover:bg-bracongo-red transition-colors duration-200;
}

.btn-secondary {
    @apply inline-flex items-center px-4 py-2 border border-bracongo-gray-300 text-bracongo-gray-700 rounded-lg hover:bg-bracongo-gray-50 transition-colors duration-200;
}

.btn-white-large {
    @apply inline-flex items-center px-6 py-3 bg-white text-bracongo-orange font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200;
}

.btn-outline-white-large {
    @apply inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-bracongo-orange transition-colors duration-200;
}
</style>
@endsection 