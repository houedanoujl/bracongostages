@extends('layouts.modern')

@section('title', $opportunite->titre . ' - BRACONGO')

@section('content')
<!-- Hero Section pour l'opportunité -->
<section id="herosectonopp" class="hero-modern bg-gradient-to-br from-orange-50 to-red-50">
    <div class="hero-content">
        <div class="mb-4">
            <a href="{{ route('opportunites') }}" class="inline-flex items-center text-bracongo-red-600 hover:text-bracongo-red-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour aux opportunités
            </a>
        </div>
        <h1 class="hero-title">
            {{ $opportunite->titre }}
        </h1>
        <p class="hero-subtitle">
            {{ $opportunite->description }}
        </p>
        <div class="hero-cta">
            <a href="/candidature?domain={{ $opportunite->slug }}" class="btn-primary-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Postuler à ce stage
            </a>
        </div>
    </div>
</section>

<!-- Section Détails de l'opportunité -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Contenu principal -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Description du poste</h2>
                    
                    @if($opportunite->description_longue)
                        <div class="prose prose-lg max-w-none text-gray-700 mb-8">
                            {!! nl2br(e($opportunite->description_longue)) !!}
                        </div>
                    @else
                        <div class="prose prose-lg max-w-none text-gray-700 mb-8">
                            {!! nl2br(e($opportunite->description)) !!}
                        </div>
                    @endif

                    @if($opportunite->competences_requises && count($opportunite->competences_requises) > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Compétences requises</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($opportunite->competences_requises as $competence)
                                    <div class="flex items-center bg-bracongo-red-50 rounded-lg p-3">
                                        <svg class="w-5 h-5 text-bracongo-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $competence }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($opportunite->competences_acquises && count($opportunite->competences_acquises) > 0)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Compétences à acquérir</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($opportunite->competences_acquises as $competence)
                                    <div class="flex items-center bg-blue-50 rounded-lg p-3">
                                        <svg class="w-5 h-5 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $competence }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-gray-50 rounded-2xl p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Informations du stage</h3>
                    
                    <div class="space-y-4">
                        @if($opportunite->duree)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-bracongo-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Durée</div>
                                    <div class="text-gray-600">{{ $opportunite->duree }}</div>
                                </div>
                            </div>
                        @endif

                        @if($opportunite->niveau_requis)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-bracongo-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Niveau requis</div>
                                    <div class="text-gray-600">{{ $opportunite->niveau_requis }}</div>
                                </div>
                            </div>
                        @endif

                        @if($opportunite->places_disponibles)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-bracongo-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Places disponibles</div>
                                    <div class="text-gray-600">{{ $opportunite->places_disponibles }} place(s)</div>
                                </div>
                            </div>
                        @endif

                        @if($opportunite->categorie)
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-bracongo-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">Catégorie</div>
                                    <div class="text-gray-600">{{ $opportunite->categorie }}</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- CTA Button -->
                    <div class="mt-8">
                        <a href="/candidature?domain={{ $opportunite->slug }}" class="w-full bg-bracongo-red-600 text-white text-center py-3 px-4 rounded-lg font-medium hover:bg-bracongo-red-700 transition duration-200 block">
                            Postuler maintenant
                        </a>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('candidature.suivi') }}" class="text-sm bg-bracongo-red-600 text-white px-4 py-2 rounded-lg hover:bg-bracongo-red-700 transition duration-200">
                            Suivre une candidature
                        </a>
                    </div>

                    <!-- Section Partage -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4 text-center">Partager cette opportunité</h4>
                        <div class="flex justify-center space-x-3">
                            <!-- WhatsApp -->
                            <a href="https://wa.me/?text=Découvrez cette opportunité de stage chez BRACONGO : {{ $opportunite->titre }} - {{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="flex items-center justify-center w-10 h-10 bg-green-500 text-white rounded-full hover:bg-green-600 transition duration-200"
                               title="Partager sur WhatsApp">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                            </a>

                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition duration-200"
                               title="Partager sur Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>

                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="flex items-center justify-center w-10 h-10 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition duration-200"
                               title="Partager sur LinkedIn">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>

                            <!-- Twitter/X -->
                            <a href="https://twitter.com/intent/tweet?text=Découvrez cette opportunité de stage chez BRACONGO : {{ $opportunite->titre }}&url={{ urlencode(request()->fullUrl()) }}" 
                               target="_blank" 
                               class="flex items-center justify-center w-10 h-10 bg-black text-white rounded-full hover:bg-gray-800 transition duration-200"
                               title="Partager sur X (Twitter)">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>

                            <!-- Email -->
                            <a href="mailto:?subject=Opportunité de stage chez BRACONGO : {{ $opportunite->titre }}&body=Découvrez cette opportunité de stage chez BRACONGO : {{ $opportunite->titre }}%0D%0A%0D%0A{{ $opportunite->description }}%0D%0A%0D%0APlus d'informations : {{ request()->fullUrl() }}" 
                               class="flex items-center justify-center w-10 h-10 bg-gray-600 text-white rounded-full hover:bg-gray-700 transition duration-200"
                               title="Partager par email">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section CTA -->
<section class="py-16 bg-gradient-to-r from-bracongo-red-600 to-bracongo-red-700">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-3xl font-bold text-white mb-4">
            Prêt à rejoindre notre équipe ?
        </h2>
        <p class="text-xl text-bracongo-red-100 mb-8">
            Postulez dès maintenant et commencez votre parcours professionnel avec BRACONGO
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/candidature?domain={{ $opportunite->slug }}" class="bg-bracongo-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-bracongo-red-700 transition duration-200">
                Postuler à ce stage
            </a>
            <a href="{{ route('opportunites') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-bracongo-red-600 transition duration-200">
                Voir autres opportunités
            </a>
        </div>
    </div>
</section>
@endsection