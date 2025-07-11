@extends('layouts.modern')

@section('title', 'BRACONGO Stages - Construisez votre avenir avec nous')

@section('content')
<!-- Hero Section Moderne -->
<section class="hero-modern" id="hero">
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

<!-- Section Statistiques Dynamiques -->
@livewire('home-statistics')

<!-- Section Opportunités de Stage -->
<section class="section-modern bg-bracongo-gray-50" id="opportunites">
    <div class="section-header animate-on-scroll">
        <h2 class="section-title">Opportunités de Stage</h2>
        <p class="section-subtitle">
            Découvrez nos programmes de stage dans différents domaines et développez vos compétences 
            au sein de l'une des entreprises les plus innovantes du Congo
        </p>
    </div>
    
    <div class="opportunities-grid">
        <!-- Production & Qualité -->
        <div class="opportunity-card animate-on-scroll" style="animation-delay: 0.1s;">
            <div class="card-icon">🏭</div>
            <h3 class="card-title">Production & Qualité</h3>
            <p class="card-description">
                Participez aux processus de production et de contrôle qualité. 
                Apprenez les standards internationaux et les technologies modernes de brassage.
            </p>
            <div class="flex items-center justify-between">
                <div class="text-sm text-bracongo-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        3-6 mois
                    </span>
                </div>
                <a href="/candidature" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Marketing & Commercial -->
        <div class="opportunity-card animate-on-scroll" style="animation-delay: 0.2s;">
            <div class="card-icon">📊</div>
            <h3 class="card-title">Marketing & Commercial</h3>
            <p class="card-description">
                Développez vos compétences en marketing digital, stratégie commerciale et 
                gestion de marque dans un environnement dynamique.
            </p>
            <div class="flex items-center justify-between">
                <div class="text-sm text-bracongo-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        3-6 mois
                    </span>
                </div>
                <a href="/candidature" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Technique & Maintenance -->
        <div class="opportunity-card animate-on-scroll" style="animation-delay: 0.3s;">
            <div class="card-icon">⚙️</div>
            <h3 class="card-title">Technique & Maintenance</h3>
            <p class="card-description">
                Maîtrisez la maintenance industrielle, l'automatisation et la gestion 
                des équipements de pointe dans l'industrie brassicole.
            </p>
            <div class="flex items-center justify-between">
                <div class="text-sm text-bracongo-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        4-6 mois
                    </span>
                </div>
                <a href="/candidature" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Ressources Humaines -->
        <div class="opportunity-card animate-on-scroll" style="animation-delay: 0.4s;">
            <div class="card-icon">👥</div>
            <h3 class="card-title">Ressources Humaines</h3>
            <p class="card-description">
                Découvrez la gestion des talents, le recrutement et le développement 
                organisationnel dans une entreprise de référence.
            </p>
            <div class="flex items-center justify-between">
                <div class="text-sm text-bracongo-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        3-4 mois
                    </span>
                </div>
                <a href="/candidature" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Finance & Comptabilité -->
        <div class="opportunity-card animate-on-scroll" style="animation-delay: 0.5s;">
            <div class="card-icon">💼</div>
            <h3 class="card-title">Finance & Comptabilité</h3>
            <p class="card-description">
                Approfondissez vos connaissances en gestion financière, contrôle de gestion 
                et analyse des performances dans un contexte international.
            </p>
            <div class="flex items-center justify-between">
                <div class="text-sm text-bracongo-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        3-6 mois
                    </span>
                </div>
                <a href="/candidature" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- IT & Digital -->
        <div class="opportunity-card animate-on-scroll" style="animation-delay: 0.6s;">
            <div class="card-icon">💻</div>
            <h3 class="card-title">IT & Transformation Digitale</h3>
            <p class="card-description">
                Participez à la digitalisation des processus et au développement 
                des solutions technologiques innovantes.
            </p>
            <div class="flex items-center justify-between">
                <div class="text-sm text-bracongo-gray-500">
                    <span class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        4-6 mois
                    </span>
                </div>
                <a href="/candidature" class="card-cta">
                    Postuler
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
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
                <a href="/candidature" class="btn-primary-large bg-white text-bracongo-red hover:bg-bracongo-gray-100">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Postuler maintenant
                </a>
                <a href="/suivi" class="btn-secondary-large border-white text-white hover:bg-white hover:text-bracongo-red">
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
@endsection