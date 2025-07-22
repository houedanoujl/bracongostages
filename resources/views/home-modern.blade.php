@extends('layouts.modern')

@section('title', 'BRACONGO Stages - Construisez votre avenir avec nous')

@section('content')
<!-- Hero Section Moderne -->
<section class="hero-modern" id="hero">
    <!-- Vidéo en arrière-plan -->
    <video class="hero-video" autoplay muted loop playsinline id="hero-video">
        <source src="{{ asset('images/01.mp4') }}" type="video/mp4">
    </video>
    
    <div class="hero-content">
        <h1 class="hero-title">
            Construisez votre avenir avec 
            <span class="text-gradient">BRACONGO</span>
        </h1>
        <p class="hero-subtitle">
            Rejoignez l'équipe BRACONGO et développez vos compétences dans l'industrie brassicole leader en République Démocratique du Congo
        </p>
        <div class="hero-cta">
            <a href="/candidature" class="btn-primary-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Postuler maintenant
            </a>
            <a href="#opportunites" class="btn-secondary-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                Découvrir nos stages
            </a>
        </div>
    </div>
    
    <!-- Floating elements pour effet visuel -->
    <div class="absolute top-20 left-10 w-16 h-16 bg-white/10 rounded-full animate-float" style="animation-delay: 0s;"></div>
    <div class="absolute top-40 right-20 w-12 h-12 bg-white/10 rounded-full animate-float" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-40 left-20 w-20 h-20 bg-white/10 rounded-full animate-float" style="animation-delay: 2s;"></div>
</section>

<!-- Section Statistiques dynamiques et Établissements partenaires -->
@php
    $stats = \App\Models\StatistiqueAccueil::where('actif', true)->orderBy('ordre')->get();
    $etablissements = \App\Models\EtablissementPartenaire::where('actif', true)->orderBy('ordre')->get();
@endphp

@if($stats->count())
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach($stats as $stat)
                <div class="rounded-xl bg-bracongo-gray-50 shadow p-6 flex flex-col items-center">
                    <div class="text-3xl mb-2">{!! $stat->icone !!}</div>
                    <div class="text-3xl font-bold text-bracongo-red">{{ $stat->valeur }}</div>
                    <div class="text-sm text-bracongo-gray-600 mt-1">{{ $stat->label }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($etablissements->count())
<section class="py-8 bg-bracongo-gray-50">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-xl font-semibold text-bracongo-gray-800 mb-6 text-center">Top Établissements Partenaires</h2>
        <div class="flex flex-wrap justify-center items-center gap-8">
            @foreach($etablissements as $etab)
                <div class="flex flex-col items-center">
                    @if($etab->logo)
                        <img src="{{ asset('storage/'.$etab->logo) }}" alt="{{ $etab->nom }}" class="h-16 w-auto object-contain mb-2" loading="lazy">
                    @endif
                    <div class="text-sm text-bracongo-gray-700 font-medium">{{ $etab->nom }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Section Opportunités de Stage -->
<section class="section-modern bg-bracongo-gray-50" id="opportunites">
    <div class="section-header animate-on-scroll">
        <h2 class="section-title">Opportunités de Stage</h2>
        <p class="section-subtitle">
            Découvrez nos programmes de stage dans différents domaines et développez vos compétences 
            au sein de l'une des entreprises les plus innovantes du Congo
        </p>
    </div>
    
    @php
        $opportunites = \App\Models\Opportunite::publiee()->ordonne()->get();
    @endphp
    <div class="opportunities-grid">
        @forelse($opportunites as $index => $opportunite)
            <div class="opportunity-card animate-on-scroll" style="animation-delay: {{ $index * 0.1 }}s;">
                <div class="card-icon">{{ $opportunite->icone }}</div>
                <h3 class="card-title">{{ $opportunite->titre }}</h3>
                <p class="card-description">{{ $opportunite->description }}</p>
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-bracongo-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                            {{ $opportunite->duree }}
                    </span>
                        <span class="badge-modern badge-info">{{ \App\Models\Opportunite::getNiveauxRequis()[$opportunite->niveau_requis] ?? $opportunite->niveau_requis }}</span>
        </div>
                <div class="text-sm text-bracongo-gray-500">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ $opportunite->places_restantes }} place(s) disponible(s)
                </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach(array_slice($opportunite->competences_requises ?? [], 0, 3) as $skill)
                            <span class="px-2 py-1 bg-bracongo-gray-100 text-bracongo-gray-600 text-xs rounded-full">
                                {{ $skill }}
                    </span>
                        @endforeach
        </div>
                </div>
            <div class="flex items-center justify-between">
                    <a href="/candidature?domain={{ $opportunite->slug }}" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @empty
            <div class="col-span-3 text-center text-bracongo-gray-500 py-12">
                Aucune opportunité de stage n'est disponible pour le moment.
            </div>
        @endforelse
    </div>
</section>

<!-- Section Processus de Candidature -->
<section class="section-modern bg-white" id="processus">
    <div class="section-header animate-on-scroll">
        <h2 class="section-title">Processus de Candidature</h2>
        <p class="section-subtitle">
            Un processus transparent et efficace pour vous accompagner vers votre stage idéal
        </p>
    </div>

    <div class="timeline-modern animate-on-scroll">
        <!-- Étape 1 -->
        <div class="timeline-step">
            <div class="step-number">1</div>
            <div class="step-content">
                <h3 class="step-title">Candidature en ligne</h3>
                <p class="step-description">
                    Soumettez votre dossier complet via notre plateforme sécurisée. 
                    Upload de votre CV, lettre de motivation et documents académiques.
                </p>
            </div>
        </div>

        <!-- Ligne de connexion -->
        <div class="absolute left-6 top-12 bottom-0 w-0.5 bg-bracongo-gray-200"></div>

        <!-- Étape 2 -->
        <div class="timeline-step">
            <div class="step-number">2</div>
            <div class="step-content">
                <h3 class="step-title">Évaluation du dossier</h3>
                <p class="step-description">
                    Notre équipe RH analyse votre profil et vérifie l'adéquation avec nos 
                    opportunités disponibles. Délai de réponse : 5-7 jours ouvrables.
                </p>
            </div>
        </div>

        <!-- Étape 3 -->
        <div class="timeline-step">
            <div class="step-number">3</div>
            <div class="step-content">
                <h3 class="step-title">Entretien & Tests</h3>
                <p class="step-description">
                    Entretien avec le responsable de département et tests techniques si nécessaires. 
                    Échange sur vos motivations et objectifs professionnels.
                </p>
            </div>
        </div>

        <!-- Étape 4 -->
        <div class="timeline-step">
            <div class="step-number">4</div>
            <div class="step-content">
                <h3 class="step-title">Affectation & Intégration</h3>
                <p class="step-description">
                    Confirmation de votre affectation et démarrage de votre parcours de stage. 
                    Programme d'accueil et définition des objectifs avec votre tuteur.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Section Témoignages dynamiques -->
@livewire('temoignages-section')

<!-- Section CTA Finale -->
<section class="py-20 bg-bracongo-red">
    <div class="max-w-4xl mx-auto text-center px-6">
        <div class="animate-on-scroll">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                Prêt à débuter votre carrière ?
            </h2>
            <p class="text-xl text-white/90 mb-8">
                Rejoignez BRACONGO dès aujourd'hui et construisez votre avenir professionnel 
                dans l'industrie brassicole congolaise.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/candidature" class="btn-primary-large bg-white text-red-600 hover:bg-bracongo-gray-100">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Postuler maintenant
                </a>
                <a href="/suivi" class="btn-secondary-large border-white hover:bg-white hover:text-bracongo-red">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Suivre ma candidature
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroVideo = document.getElementById('hero-video');
    if (heroVideo) {
        // Réduire la vitesse de lecture à 50% (0.5)
        heroVideo.playbackRate = 0.7;
    }
});
</script>
@endsection