<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Partagez votre expérience</h1>
            <p class="mt-2 text-gray-600">Votre retour d'expérience aide les futurs stagiaires à découvrir BRACONGO</p>
        </div>

        {{-- Message de succès --}}
        @if($showSuccess)
            <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-green-800 mb-2">Merci pour votre retour d'expérience !</h2>
                <p class="text-green-700 mb-4">
                    Votre retour d'expérience a été soumis avec succès. Il sera examiné par notre équipe avant d'être publié sur le site.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('candidat.dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Retour au tableau de bord
                    </a>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Page d'accueil
                    </a>
                </div>
            </div>
        @elseif($hasExistingTemoignage)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-amber-800 mb-2">Vous avez déjà soumis un retour d'expérience</h2>
                <p class="text-amber-700 mb-4">
                    Un seul retour d'expérience par stagiaire est autorisé. Votre retour est en cours de traitement par notre équipe.
                </p>
                <a href="{{ route('candidat.dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                    Retour au tableau de bord
                </a>
            </div>
        @elseif($stageNonTermine)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-blue-800 mb-2">Votre stage n’est pas encore terminé</h2>
                <p class="text-blue-700 mb-4">
                    Vous pourrez soumettre votre retour d'expérience une fois que votre stage sera officiellement terminé et que votre évaluation aura été complétée.
                </p>
                <p class="text-blue-600 text-sm mb-6">
                    Votre candidature doit avoir atteint au minimum l'étape « Évaluation terminée ».
                </p>
                <a href="{{ route('candidat.dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Retour au tableau de bord
                </a>
            </div>
        @else
            {{-- Formulaire --}}
            <form wire:submit="submit" class="space-y-8">
                {{-- Messages d'erreur globaux --}}
                @if(session()->has('error'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Section : Informations personnelles --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informations personnelles
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input wire:model="nom" type="text" id="nom"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-red-600 focus:ring-red-600 @error('nom') border-red-500 @enderror"
                                   placeholder="Votre nom">
                            @error('nom') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                            <input wire:model="prenom" type="text" id="prenom"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-red-600 focus:ring-red-600 @error('prenom') border-red-500 @enderror"
                                   placeholder="Votre prénom">
                            @error('prenom') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="poste_occupe" class="block text-sm font-medium text-gray-700 mb-1">Poste occupé lors du stage *</label>
                            <input wire:model="poste_occupe" type="text" id="poste_occupe"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-red-600 focus:ring-red-600 @error('poste_occupe') border-red-500 @enderror"
                                   placeholder="Ex: Stagiaire marketing">
                            @error('poste_occupe') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="etablissement_origine" class="block text-sm font-medium text-gray-700 mb-1">Établissement d'origine</label>
                            <input wire:model="etablissement_origine" type="text" id="etablissement_origine"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-red-600 focus:ring-red-600"
                                   placeholder="Ex: Université Marien Ngouabi">
                        </div>

                        <div>
                            <label for="direction_stage" class="block text-sm font-medium text-gray-700 mb-1">Direction de stage</label>
                            <input wire:model="direction_stage" type="text" id="direction_stage"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-red-600 focus:ring-red-600"
                                   placeholder="Ex: Direction commerciale">
                        </div>

                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Photo (optionnel)</label>
                            <input wire:model="photo" type="file" id="photo" accept="image/jpeg,image/png"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            <p class="text-xs text-gray-500 mt-1">JPEG ou PNG, max 1 Mo</p>
                            @error('photo') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section : Votre témoignage --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        Votre témoignage
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="temoignage" class="block text-sm font-medium text-gray-700 mb-1">
                                Racontez votre expérience de stage chez BRACONGO *
                            </label>
                            <textarea wire:model="temoignage" id="temoignage" rows="6"
                                      class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('temoignage') border-red-500 @enderror"
                                      placeholder="Décrivez ce que vous avez appris, les projets auxquels vous avez participé, l'ambiance de travail..."></textarea>
                            <div class="flex justify-between mt-1">
                                @error('temoignage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <span class="text-xs text-gray-400">Min 20 caractères, max 2000</span>
                            </div>
                        </div>

                        <div>
                            <label for="citation_courte" class="block text-sm font-medium text-gray-700 mb-1">
                                Citation courte (optionnel)
                            </label>
                            <input wire:model="citation_courte" type="text" id="citation_courte"
                                   class="w-full rounded-lg border-gray-300 border px-4 py-2.5 focus:border-red-600 focus:ring-red-600"
                                   placeholder="Une phrase qui résume votre expérience...">
                            <p class="text-xs text-gray-500 mt-1">Cette citation pourra être mise en avant sur la page d'accueil</p>
                        </div>
                    </div>
                </div>

                {{-- Section : Évaluation --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Évaluation de votre expérience
                    </h2>

                    {{-- Note étoiles --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notez votre expérience *</label>
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('note_experience', {{ $i }})"
                                        class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-10 h-10 {{ $i <= $note_experience ? 'text-yellow-400' : 'text-gray-300' }}" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <span class="ml-3 text-sm text-gray-600">{{ $note_experience }}/5</span>
                        </div>
                    </div>

                    {{-- Compétences acquises --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Compétences acquises (optionnel)</label>
                        <div class="flex space-x-2 mb-2">
                            <input wire:model="nouvelle_competence" type="text" 
                                   wire:keydown.enter.prevent="ajouterCompetence"
                                   class="flex-1 rounded-lg border-gray-300 border px-4 py-2 focus:border-red-600 focus:ring-red-600"
                                   placeholder="Ex: Gestion de projet, Communication...">
                            <button type="button" wire:click="ajouterCompetence"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                                Ajouter
                            </button>
                        </div>
                        @if(!empty($competences_acquises))
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($competences_acquises as $index => $competence)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-red-700 border border-red-200">
                                        {{ $competence }}
                                        <button type="button" wire:click="retirerCompetence({{ $index }})" class="ml-2 text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Notice modération --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 font-medium">Modération</p>
                            <p class="text-sm text-blue-700 mt-1">
                                Votre retour d'expérience sera examiné par l'équipe BRACONGO avant publication.
                                L'administrateur pourra choisir de le mettre en avant sur la page d'accueil.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Bouton de soumission --}}
                <div class="flex justify-between items-center">
                    <a href="{{ route('candidat.dashboard') }}" 
                       class="text-gray-600 hover:text-gray-900 transition">
                        ← Retour au tableau de bord
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-8 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                        <span wire:loading.remove>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </span>
                        <span wire:loading class="mr-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove>Soumettre mon retour d'expérience</span>
                        <span wire:loading>Envoi en cours...</span>
                    </button>
                </div>
            </form>
        @endif

    </div>
</div>
