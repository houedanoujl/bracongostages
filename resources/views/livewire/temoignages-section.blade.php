<section class="section-modern bg-bracongo-gray-50">
    <div class="section-header animate-on-scroll">
        <h2 class="section-title">Ce que disent nos stagiaires</h2>
        <p class="section-subtitle">
            Découvrez les expériences de ceux qui ont déjà vécu l'aventure BRACONGO
        </p>
    </div>

    @if($temoignages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
            @foreach($temoignages as $temoignage)
                <div class="bg-white rounded-2xl p-8 shadow-soft animate-on-scroll" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                    <div class="flex items-center mb-4">
                        @if($temoignage->photo_url)
                            <img src="{{ $temoignage->photo_url }}" alt="{{ $temoignage->nom_complet }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 bg-bracongo-red rounded-full flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($temoignage->prenom, 0, 1)) }}{{ strtoupper(substr($temoignage->nom, 0, 1)) }}
                            </div>
                        @endif
                        <div class="ml-4">
                            <div class="font-semibold text-bracongo-gray-800">{{ $temoignage->nom_complet }}</div>
                            <div class="text-sm text-bracongo-gray-600">{{ $temoignage->poste_occupe }}</div>
                            @if($temoignage->etablissement_origine)
                                <div class="text-xs text-bracongo-gray-500">{{ $temoignage->etablissement_origine }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-bracongo-gray-600 leading-relaxed mb-4">
                        "{{ $temoignage->citation_courte ?: Str::limit($temoignage->temoignage, 150) }}"
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $temoignage->note_experience ? '' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($temoignage->direction_stage)
                            <span class="text-xs text-bracongo-gray-500">{{ $temoignage->direction_stage }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-bracongo-gray-500">Aucun témoignage disponible pour le moment.</p>
            <a href="/candidature" class="inline-block mt-4 bg-bracongo-red text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                Soyez le premier à témoigner !
            </a>
        </div>
    @endif
</section>
