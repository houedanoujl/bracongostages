<div class="min-h-screen bg-gradient-to-br from-yellow-50 via-red-50 to-green-50 py-12">
    <!-- Toast Notifications -->
    @if($showToast)
        <div class="fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300 ease-in-out" 
             x-data="{ show: true }" 
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2">
            <div class="rounded-lg shadow-lg p-4 border-l-4 @if($toastType === 'error') bg-red-50 border-red-500 @elseif($toastType === 'success') bg-green-50 border-green-500 @else bg-blue-50 border-blue-500 @endif">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($toastType === 'error')
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @elseif($toastType === 'success')
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium @if($toastType === 'error') text-red-800 @elseif($toastType === 'success') text-green-800 @else text-blue-800 @endif">
                            {{ $toastMessage }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button wire:click="hideToast" class="@if($toastType === 'error') text-red-400 hover:text-red-600 @elseif($toastType === 'success') text-green-400 hover:text-green-600 @else text-blue-400 hover:text-blue-600 @endif">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('auto-hide-toast', () => {
                    setTimeout(() => {
                        @this.call('hideToast');
                    }, 5000);
                });
            });
        </script>
    @endif
    <!-- Modal d'information -->
    @if($showInfoModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="closeInfoModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto border-t-4 border-red-600" @click.stop>
                <!-- Header du modal -->
                <div class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                        <img class="h-12 w-auto rounded-full" src="{{ asset('images/logo.png') }}" alt="BRACONGO"/>&nbsp;
                            <h2 class="text-xl font-bold text-white">Informations importantes</h2>
                        </div>
                        <button wire:click="closeInfoModal" class="text-yellow-200 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Contenu du modal -->
                <div class="p-6 space-y-6">
                    <!-- Introduction -->
                    <div class="bg-gradient-to-r from-yellow-50 to-red-50 border-l-4 border-red-600 p-4 rounded-r-lg">
                        <h3 class="text-lg font-semibold text-red-800 mb-2">Candidature de Stage BRACONGO</h3>
                        <p class="text-red-700 text-sm">
                            Bienvenue dans le processus de candidature pour un stage chez BRACONGO. 
                            Veuillez prendre connaissance des informations ci-dessous avant de commencer.
                        </p>
                    </div>

                    <!-- Informations requises -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informations à fournir
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <h5 class="font-medium text-red-700 mb-2">Informations personnelles</h5>
                                <ul class="space-y-1 text-gray-600">
                                    <li>• Nom et prénom complets</li>
                                    <li>• Adresse email valide</li>
                                    <li>• Numéro de téléphone</li>
                                </ul>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-3">
                                <h5 class="font-medium text-red-700 mb-2">Formation académique</h5>
                                <ul class="space-y-1 text-gray-600">
                                    <li>• Établissement d'études</li>
                                    <li>• Niveau d'étude actuel</li>
                                    <li>• Faculté ou département</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Documents requis -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Documents requis
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <h5 class="font-medium text-green-700 mb-2">Documents obligatoires</h5>
                                <ul class="space-y-1 text-green-600">
                                    <li>• CV actualisé (PDF, DOC, DOCX)</li>
                                    <li>• Lettre de motivation (PDF, DOC, DOCX)</li>
                                    <li>• Lettres de recommandation (PDF, DOC, DOCX)</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <h5 class="font-medium text-yellow-700 mb-2">Documents optionnels</h5>
                                <ul class="space-y-1 text-yellow-600">
                                    <li>• Certificat de scolarité (PDF, JPG, PNG)</li>
                                    <li>• Relevés de notes récents</li>
                                    <li>• Certificats de compétences</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Contraintes techniques -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Contraintes techniques
                        </h4>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Taille maximale par fichier : 2 MB</li>
                            <li>• Formats acceptés : PDF, DOC, DOCX pour CV/lettres | PDF, JPG, PNG pour certificats</li>
                            <li>• Assurez-vous que vos documents sont lisibles et de bonne qualité</li>
                        </ul>
                    </div>

                    <!-- Processus de sélection -->
                    <div class="bg-gradient-to-r from-red-50 to-yellow-50 border border-red-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-red-800 mb-2">Processus de sélection</h4>
                        <p class="text-xs text-red-700">
                            Après soumission, vous recevrez un code de suivi unique. Notre équipe RH analysera votre candidature 
                            et vous contactera dans un délai de 7 à 14 jours ouvrables pour vous informer de la suite du processus.
                        </p>
                    </div>
                </div>

                <!-- Footer du modal -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-xl border-t">
                    <div class="flex justify-between items-center">
                        <div class="space-x-2">
                            <button wire:click="openInfoModal" class="text-sm text-red-600 hover:text-red-800 transition-colors">
                                Revoir ces informations plus tard
                            </button>
                        </div>
                        <button wire:click="closeInfoModal" 
                                class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-medium rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-md">
                            Commencer ma candidature
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($showSuccess)
            <div class="bg-white rounded-xl shadow-2xl p-8 text-center border-t-4 border-green-500">
                <div class="flex justify-center mb-6">
                    <div class="bg-gradient-to-br from-green-400 to-green-500 rounded-full p-4 shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Candidature soumise avec succès !</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Votre candidature a été enregistrée. Votre code de suivi est :
                </p>
                <div class="bg-gradient-to-r from-yellow-100 to-yellow-200 border-2 border-yellow-400 rounded-lg p-4 mb-6">
                    <span class="text-2xl font-bold text-red-700">{{ $candidatureCode }}</span>
                </div>
                <p class="text-gray-600 mb-8">
                    Conservez précieusement ce code pour suivre l'évolution de votre candidature.
                </p>
                <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                    <a href="{{ route('candidature.suivi.code', $candidatureCode) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-md">
                        Suivre ma candidature
                    </a>
                    <button wire:click="resetForm" 
                            class="inline-flex items-center px-6 py-3 border-2 border-yellow-400 text-base font-medium rounded-md text-red-700 bg-white hover:bg-yellow-50 transition-all duration-200 shadow-md shadow-md">
                        Nouvelle candidature
                    </button>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-t-4 border-red-600">
                <!-- Header BRACONGO -->
                <div class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 px-8 py-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                        <img class="h-12 w-auto rounded-full" src="{{ asset('images/logo.png') }}" alt="BRACONGO"/>
                            <div>
                                <h1 class="text-3xl font-bold text-white">Candidature de Stage</h1>
                                @if($opportunite_titre)
                                    <p class="text-yellow-200 text-sm">{{ $opportunite_titre }}</p>
                                @endif
                            </div>
                        </div>
                        <button wire:click="openInfoModal" 
                                class="inline-flex items-center px-3 py-2 text-sm text-yellow-200 hover:text-white border border-yellow-300 hover:border-white rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Infos
                        </button>
                    </div>
                    <p class="text-yellow-200 mt-2">Rejoignez l'équipe BRACONGO et développez vos compétences</p>
                </div>

                <!-- Sélection d'opportunité si pas venue d'un lien -->
                @if($afficher_selection_opportunite)
                    <div class="mx-8 mt-4 bg-gradient-to-r from-yellow-50 to-green-50 border-2 border-yellow-400 rounded-lg p-6 shadow-md">
                        <h3 class="text-lg font-semibold text-red-800 mb-3">📋 Sélectionnez l'opportunité qui vous intéresse</h3>
                        <p class="text-sm text-red-700 mb-4">Choisissez le domaine dans lequel vous souhaitez effectuer votre stage :</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-1">
                                <select wire:model.live="opportunite_selectionnee" 
                                        class="w-full rounded-lg px-4 py-3 focus:ring-red-600 bg-white transition-all duration-200 @if($erreur_opportunite) border-2 border-red-500 focus:border-red-500 animate-pulse @else border-2 border-yellow-400 focus:border-red-600 @endif">
                                    <option value="">-- Choisissez une opportunité --</option>
                                    @foreach($opportunites_disponibles as $id => $titre)
                                        <option value="{{ $id }}">{{ $titre }}</option>
                                    @endforeach
                                </select>
                                @if($erreur_opportunite)
                                    <div class="mt-2 flex items-center text-red-600 text-sm">
                                        <svg class="w-4 h-4 mr-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <span class="font-medium">Vous devez sélectionner une opportunité avant de continuer</span>
                                    </div>
                                @endif
                            </div>
                            <button wire:click="selectionnerOpportunite" 
                                    {{ $opportunite_selectionnee ? '' : 'disabled' }}
                                    class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-md">
                                Confirmer
                            </button>
                        </div>
                        
                        <div class="mt-3 text-xs text-red-600">
                            💡 <strong>Conseil :</strong> Cette sélection nous aide à personnaliser votre expérience et à mieux traiter votre candidature.
                        </div>
                    </div>
                @endif

                <!-- Affichage des erreurs -->
                @if(session()->has('validation_error') || count($validationErrors) > 0)
                    <div class="mx-8 mt-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md animate-shake">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 mb-2">⚠️ Action requise</h3>
                                @if(session()->has('validation_error'))
                                    <p class="text-sm text-red-700 font-medium">{{ session('validation_error') }}</p>
                                @endif
                                @if(count($validationErrors) > 0)
                                    <ul class="mt-2 list-disc list-inside space-y-1 text-sm text-red-700">
                                        @foreach($validationErrors as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Progress Bar BRACONGO -->
                <div class="px-8 py-4 bg-gradient-to-r from-yellow-50 to-red-50 border-b">
                    <div class="flex items-center justify-center">
                        @for($i = 1; $i <= $totalSteps; $i++)
                            <div class="flex items-center {{ $i <= $currentStep ? 'text-red-600' : 'text-gray-400' }}">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 {{ $i <= $currentStep ? 'border-red-600 bg-gradient-to-br from-red-600 to-red-700 text-white shadow-md' : 'border-gray-300' }}">
                                    {{ $i }}
                                </div>
                                @if($i < $totalSteps)
                                    <div class="w-16 h-1 mx-2 {{ $i < $currentStep ? 'bg-gradient-to-r from-red-600 to-red-700' : 'bg-gray-300' }}"></div>
                                @endif
                            </div>
                        @endfor
                    </div>
                    <div class="mt-2 text-sm text-red-700 text-center font-medium">
                        Étape {{ $currentStep }} sur {{ $totalSteps }} - 
                        @if($currentStep == 1) Informations personnelles
                        @elseif($currentStep == 2) Formation
                        @elseif($currentStep == 3) Stage souhaité
                        @elseif($currentStep == 4) Documents
                        @endif
                    </div>
                </div>

                <!-- Formulaire -->
                <form wire:submit.prevent="submitCandidature" class="p-8">
                    @if($currentStep == 1)
                        <!-- Étape 1: Informations personnelles -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Informations personnelles</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                                    <input wire:model="nom" type="text" id="nom" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('nom') border-red-500 @enderror">
                                    @error('nom') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                                    <input wire:model="prenom" type="text" id="prenom" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('prenom') border-red-500 @enderror">
                                    @error('prenom') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input wire:model="email" type="email" id="email" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('email') border-red-500 @enderror">
                                    @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                                    <input wire:model="telephone" type="tel" id="telephone" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('telephone') border-red-500 @enderror">
                                    @error('telephone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    @elseif($currentStep == 2)
                        <!-- Étape 2: Formation -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Formation académique</h3>
                            
                            <div>
                                <label for="etablissement" class="block text-sm font-medium text-gray-700 mb-2">Établissement *</label>
                                <select wire:model.live="etablissement" id="etablissement" 
                                        class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('etablissement') border-red-500 @enderror">
                                    <option value="">Sélectionnez votre établissement</option>
                                    @foreach($etablissements as $etablissement_key => $etablissement_label)
                                        <option value="{{ $etablissement_key }}">{{ $etablissement_label }}</option>
                                    @endforeach
                                </select>
                                @error('etablissement') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                
                                @if($etablissement === 'autres' || $etablissement === 'Autres')
                                    <div class="mt-4">
                                        <label for="etablissement_autre" class="block text-sm font-medium text-gray-700 mb-2">Nom de votre établissement *</label>
                                        <input wire:model="etablissement_autre" type="text" id="etablissement_autre" 
                                               placeholder="Saisissez le nom de votre établissement"
                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('etablissement_autre') border-red-500 @enderror">
                                        @error('etablissement_autre') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="niveau_etude" class="block text-sm font-medium text-gray-700 mb-2">Niveau d'étude *</label>
                                    <select wire:model="niveau_etude" id="niveau_etude" 
                                            class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('niveau_etude') border-red-500 @enderror">
                                        <option value="">Sélectionnez votre niveau</option>
                                        @foreach($niveaux_etude as $niveau_key => $niveau_label)
                                            <option value="{{ $niveau_key }}">{{ $niveau_label }}</option>
                                        @endforeach
                                    </select>
                                    @error('niveau_etude') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="faculte" class="block text-sm font-medium text-gray-700 mb-2">Faculté/Département</label>
                                    <input wire:model="faculte" type="text" id="faculte" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('faculte') border-red-500 @enderror">
                                    @error('faculte') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    @elseif($currentStep == 3)
                        <!-- Étape 3: Stage souhaité -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Détails du stage souhaité</h3>
                            
                            <div>
                                <label for="objectif_stage" class="block text-sm font-medium text-gray-700 mb-2">Objectif du stage *</label>
                                <textarea wire:model="objectif_stage" id="objectif_stage" rows="4" 
                                          class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('objectif_stage') border-red-500 @enderror"
                                          placeholder="Décrivez vos objectifs et ce que vous espérez apprendre..."></textarea>
                                @error('objectif_stage') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="poste_souhaite" class="block text-sm font-medium text-gray-700 mb-2">Poste souhaité *</label>
                                <select wire:model="poste_souhaite" id="poste_souhaite" 
                                        class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('poste_souhaite') border-red-500 @enderror">
                                    <option value="">Sélectionner un poste</option>
                                    @foreach(\App\Models\Candidature::getPostesDisponibles() as $poste_key => $poste_label)
                                        <option value="{{ $poste_key }}">{{ $poste_label }}</option>
                                    @endforeach
                                </select>
                                @error('poste_souhaite') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Directions souhaitées *</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($directions_disponibles as $direction_key => $direction_label)
                                        <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                            <input wire:model="directions_souhaitees" type="checkbox" value="{{ $direction_key }}" 
                                                   class="rounded border-gray-300 text-red-600 focus:ring-red-600">
                                            <span class="ml-3 text-sm text-gray-700">{{ $direction_label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('directions_souhaitees') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="periode_debut_souhaitee" class="block text-sm font-medium text-gray-700 mb-2">Date de début souhaitée *</label>
                                    <input wire:model="periode_debut_souhaitee" type="date" id="periode_debut_souhaitee" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('periode_debut_souhaitee') border-red-500 @enderror">
                                    @error('periode_debut_souhaitee') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="periode_fin_souhaitee" class="block text-sm font-medium text-gray-700 mb-2">Date de fin souhaitée *</label>
                                    <input wire:model="periode_fin_souhaitee" type="date" id="periode_fin_souhaitee" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('periode_fin_souhaitee') border-red-500 @enderror">
                                    @error('periode_fin_souhaitee') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    @elseif($currentStep == 4)
                        <!-- Étape 4: Documents -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Documents requis</h3>
                            
                            <!-- Section de choix de documents si l'utilisateur a des documents existants -->
                            @if($documents_existants_disponibles)
                                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-green-50 border-2 border-blue-300 rounded-lg">
                                    <h4 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Choix des documents
                                    </h4>
                                    <p class="text-sm text-blue-700 mb-4">
                                        Vous avez déjà des documents dans votre profil. Choisissez comment procéder :
                                    </p>
                                    
                                    <div class="space-y-3">
                                        <label class="flex items-center p-3 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 cursor-pointer">
                                            <input type="radio" wire:click="$set('utiliser_documents_existants', true)" 
                                                   {{ $utiliser_documents_existants ? 'checked' : '' }}
                                                   class="text-blue-600 focus:ring-blue-600">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-blue-800">Utiliser mes documents existants</div>
                                                <div class="text-xs text-blue-600">Postuler avec les documents déjà présents dans votre profil</div>
                                            </div>
                                        </label>
                                        
                                        <label class="flex items-center p-3 bg-white border border-green-200 rounded-lg hover:bg-green-50 cursor-pointer">
                                            <input type="radio" wire:click="$set('utiliser_documents_existants', false)" 
                                                   {{ !$utiliser_documents_existants ? 'checked' : '' }}
                                                   class="text-green-600 focus:ring-green-600">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-green-800">Uploader de nouveaux documents</div>
                                                <div class="text-xs text-green-600">Remplacer par de nouveaux fichiers pour cette candidature</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Section des documents obligatoires -->
                            @if($utiliser_documents_existants && $documents_existants_disponibles)
                                <!-- Affichage des documents existants -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">CV *</label>
                                        @if($cv_existant)
                                            <div class="p-4 bg-green-50 border-2 border-green-300 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm font-semibold text-green-800">CV du profil sélectionné</p>
                                                            <p class="text-xs text-green-600">{{ basename($cv_existant) }}</p>
                                                        </div>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $cv_existant) }}" target="_blank" 
                                                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Voir
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- Option pour uploader un nouveau CV -->
                                            <div class="mt-3">
                                                <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="remplacer_cv" class="rounded border-gray-300 text-red-600 focus:ring-red-500 mr-2">
                                                    <span>Remplacer par un nouveau CV</span>
                                                </label>
                                                @if($remplacer_cv ?? false)
                                                    <div class="mt-2">
                                                        <input wire:model="cv" type="file" id="cv_nouveau" accept=".pdf,.doc,.docx" 
                                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('cv') border-red-500 @enderror">
                                                        @error('cv') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Lettre de motivation *</label>
                                        @if($lettre_motivation_existante)
                                            <div class="p-4 bg-green-50 border-2 border-green-300 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm font-semibold text-green-800">Lettre du profil sélectionnée</p>
                                                            <p class="text-xs text-green-600">{{ basename($lettre_motivation_existante) }}</p>
                                                        </div>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $lettre_motivation_existante) }}" target="_blank" 
                                                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Voir
                                                    </a>
                                                </div>
                                            </div>
                                            <!-- Option pour uploader une nouvelle lettre -->
                                            <div class="mt-3">
                                                <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="remplacer_lettre" class="rounded border-gray-300 text-red-600 focus:ring-red-500 mr-2">
                                                    <span>Remplacer par une nouvelle lettre</span>
                                                </label>
                                                @if($remplacer_lettre ?? false)
                                                    <div class="mt-2">
                                                        <input wire:model="lettre_motivation" type="file" id="lettre_nouveau" accept=".pdf,.doc,.docx" 
                                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('lettre_motivation') border-red-500 @enderror">
                                                        @error('lettre_motivation') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="p-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                                                <div class="flex items-center">
                                                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-semibold text-yellow-800">Lettre de motivation requise</p>
                                                        <p class="text-xs text-yellow-600">Vous devez uploader une lettre de motivation</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Si pas de lettre existante, afficher le champ d'upload -->
                                @if(!$lettre_motivation_existante)
                                    <div class="mt-4">
                                        <label for="lettre_motivation" class="block text-sm font-medium text-gray-700 mb-2">Lettre de motivation * (requise)</label>
                                        <input wire:model="lettre_motivation" type="file" id="lettre_motivation" accept=".pdf,.doc,.docx" 
                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('lettre_motivation') border-red-500 @enderror">
                                        @error('lettre_motivation') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            @else
                                <!-- Champs d'upload pour nouveaux documents -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="cv" class="block text-sm font-medium text-gray-700 mb-2">CV *</label>
                                        @auth('candidat')
                                            @php $cvCandidat = auth('candidat')->user()->getDocumentByType('cv'); @endphp
                                            @if($cvCandidat && !$utiliser_documents_existants)
                                                <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm text-yellow-800">📄 CV du profil disponible mais non utilisé</p>
                                                            <p class="text-xs text-yellow-600">{{ $cvCandidat->nom_original }} ({{ $cvCandidat->taille_formatee }})</p>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $cvCandidat->chemin_fichier) }}" target="_blank" 
                                                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200 transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            Voir
                                                        </a>
                                                    </div>
                                                </div>
                                            @elseif($cvCandidat)
                                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm text-green-800">📄 CV du profil disponible</p>
                                                            <p class="text-xs text-green-600">{{ $cvCandidat->nom_original }} ({{ $cvCandidat->taille_formatee }})</p>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <a href="{{ asset('storage/' . $cvCandidat->chemin_fichier) }}" target="_blank" 
                                                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded hover:bg-green-200 transition-colors">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                Voir
                                                            </a>
                                                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ Utilisé</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth
                                        <input wire:model="cv" type="file" id="cv" accept=".pdf,.doc,.docx" 
                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('cv') border-red-500 @enderror">
                                        @auth('candidat')
                                            @if(auth('candidat')->user()->getDocumentByType('cv') && $utiliser_documents_existants)
                                                <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser votre CV du profil, ou uploadez un nouveau fichier pour le remplacer</p>
                                            @endif
                                        @endauth
                                        @error('cv') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="lettre_motivation" class="block text-sm font-medium text-gray-700 mb-2">Lettre de motivation *</label>
                                        @auth('candidat')
                                            @php $lettreCandidat = auth('candidat')->user()->getDocumentByType('lettre_motivation'); @endphp
                                            @if($lettreCandidat && !$utiliser_documents_existants)
                                                <div class="mb-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm text-yellow-800">📝 Lettre du profil disponible mais non utilisée</p>
                                                            <p class="text-xs text-yellow-600">{{ $lettreCandidat->nom_original }} ({{ $lettreCandidat->taille_formatee }})</p>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $lettreCandidat->chemin_fichier) }}" target="_blank" 
                                                           class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200 transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            Voir
                                                        </a>
                                                    </div>
                                                </div>
                                            @elseif($lettreCandidat)
                                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm text-green-800">📝 Lettre du profil disponible</p>
                                                            <p class="text-xs text-green-600">{{ $lettreCandidat->nom_original }} ({{ $lettreCandidat->taille_formatee }})</p>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <a href="{{ asset('storage/' . $lettreCandidat->chemin_fichier) }}" target="_blank" 
                                                               class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded hover:bg-green-200 transition-colors">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                                Voir
                                                            </a>
                                                            <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ Utilisée</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth
                                        <input wire:model="lettre_motivation" type="file" id="lettre_motivation" accept=".pdf,.doc,.docx" 
                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('lettre_motivation') border-red-500 @enderror">
                                        <p class="text-xs text-amber-600 mt-1">📄 Taille maximale : 2 MB (formats acceptés : PDF, DOC, DOCX)</p>
                                        @auth('candidat')
                                            @if(auth('candidat')->user()->getDocumentByType('lettre_motivation') && $utiliser_documents_existants)
                                                <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser votre lettre du profil, ou uploadez un nouveau fichier</p>
                                            @endif
                                        @endauth
                                        @error('lettre_motivation') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="certificat_scolarite" class="block text-sm font-medium text-gray-700 mb-2">
                                        Certificat de scolarité <span class="text-gray-400 text-xs">(optionnel)</span>
                                    </label>
                                    @auth('candidat')
                                        @php $certificatCandidat = auth('candidat')->user()->getDocumentByType('certificat_scolarite'); @endphp
                                        @if($certificatCandidat)
                                            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm text-green-800">🎓 Certificat du profil disponible</p>
                                                        <p class="text-xs text-green-600">{{ $certificatCandidat->nom_original }} ({{ $certificatCandidat->taille_formatee }})</p>
                                                    </div>
                                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ Utilisé automatiquement</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                <p class="text-sm text-amber-800">⚠️ Aucun certificat dans votre profil. Veuillez en télécharger un.</p>
                                            </div>
                                        @endif
                                    @endauth
                                    <input wire:model="certificat_scolarite" type="file" id="certificat_scolarite" accept=".pdf,.jpg,.jpeg,.png" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('certificat_scolarite') border-red-500 @enderror">
                                    <p class="text-xs text-gray-500 mt-1">📄 Taille max : 5 MB (formats : PDF, JPG, PNG)</p>
                                    @auth('candidat')
                                        @if(auth('candidat')->user()->getDocumentByType('certificat_scolarite'))
                                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser votre certificat du profil, ou uploadez un nouveau fichier</p>
                                        @endif
                                    @endauth
                                    @error('certificat_scolarite') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="releves_notes" class="block text-sm font-medium text-gray-700 mb-2">Relevés de notes récents</label>
                                    @auth('candidat')
                                        @php $relevesCandidat = auth('candidat')->user()->getDocumentByType('releves_notes'); @endphp
                                        @if($relevesCandidat && $relevesCandidat->fichierExiste())
                                            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm text-green-800">📊 Relevés du profil disponibles</p>
                                                        <p class="text-xs text-green-600">{{ $relevesCandidat->nom_original }} ({{ $relevesCandidat->taille_formatee }})</p>
                                                    </div>
                                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ Utilisés automatiquement</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endauth
                                    <input wire:model="releves_notes" type="file" id="releves_notes" accept=".pdf,.jpg,.jpeg,.png" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('releves_notes') border-red-500 @enderror">
                                    @auth('candidat')
                                        @if($relevesCandidat ?? false)
                                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser vos relevés du profil</p>
                                        @endif
                                    @endauth
                                    @error('releves_notes') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Lettres de recommandation (obligatoire) -->
                            <div class="border-t border-gray-200 pt-6">
                                <div class="mb-6">
                                    <label for="lettres_recommandation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Lettres de recommandation <span class="text-red-600 font-bold">* (obligatoire)</span>
                                    </label>
                                    @auth('candidat')
                                        @php $lettresRecoCandidat = auth('candidat')->user()->getDocumentByType('lettres_recommandation'); @endphp
                                        @if($lettresRecoCandidat && $lettresRecoCandidat->fichierExiste())
                                            <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm text-green-800">📋 Lettres du profil disponibles</p>
                                                        <p class="text-xs text-green-600">{{ $lettresRecoCandidat->nom_original }} ({{ $lettresRecoCandidat->taille_formatee }})</p>
                                                    </div>
                                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ Utilisées automatiquement</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                <p class="text-sm text-amber-800">⚠️ Aucune lettre dans votre profil. Veuillez en télécharger une.</p>
                                            </div>
                                        @endif
                                    @endauth
                                    <input wire:model="lettres_recommandation" type="file" id="lettres_recommandation" accept=".pdf,.doc,.docx"
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('lettres_recommandation') border-red-500 @enderror">
                                    <p class="text-xs text-gray-500 mt-1">📄 Taille max : 5 MB (formats : PDF, DOC, DOCX) - Lettres de professeurs ou employeurs</p>
                                    @auth('candidat')
                                        @if($lettresRecoCandidat ?? false)
                                            <p class="text-xs text-gray-500 mt-1">Laissez vide pour utiliser vos lettres du profil, ou uploadez un nouveau fichier</p>
                                        @endif
                                    @endauth
                                    @error('lettres_recommandation') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Documents optionnels -->
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Documents optionnels</h4>
                                <p class="text-sm text-gray-600 mb-4">Ces documents peuvent renforcer votre candidature.</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="certificats_competences" class="block text-sm font-medium text-gray-700 mb-2">Certificats de compétences</label>
                                        @auth('candidat')
                                            @php $certifsCandidat = auth('candidat')->user()->getDocumentByType('certificats_competences'); @endphp
                                            @if($certifsCandidat && $certifsCandidat->fichierExiste())
                                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm text-green-800">🏆 Certificats du profil disponibles</p>
                                                            <p class="text-xs text-green-600">{{ $certifsCandidat->nom_original }} ({{ $certifsCandidat->taille_formatee }})</p>
                                                        </div>
                                                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded">✓ Utilisés</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endauth
                                        <input wire:model="certificats_competences" type="file" id="certificats_competences" accept=".pdf,.jpg,.jpeg,.png" 
                                               class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-red-600 focus:ring-red-600 @error('certificats_competences') border-red-500 @enderror">
                                        @error('certificats_competences') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                                        <p class="text-xs text-gray-500 mt-1">Certifications professionnelles ou techniques</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-yellow-50 to-green-50 border-2 border-yellow-400 rounded-lg p-4 shadow-md">
                                <p class="text-sm text-red-700">
                                    <strong>Note :</strong> Formats acceptés : PDF, DOC, DOCX (CV et lettres) et PDF, JPG, PNG (certificats). 
                                    Taille max : 2 MB par fichier. Les documents optionnels peuvent améliorer vos chances de sélection.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Boutons de navigation -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <div>
                            @if($currentStep > 1)
                                <button type="button" wire:click="previousStep" wire:loading.attr="disabled"
                                        class="inline-flex items-center px-6 py-3 border-2 border-yellow-400 text-base font-medium rounded-md text-red-700 bg-white hover:bg-yellow-50 disabled:opacity-50 transition-all duration-200 shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="previousStep">Précédent</span>
                                    <span wire:loading wire:target="previousStep">Chargement...</span>
                                </button>
                            @endif
                        </div>
                        
                        <div>
                            @if($currentStep < $totalSteps)
                                <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 disabled:opacity-50 transition-all duration-200 shadow-md">
                                    <span wire:loading.remove wire:target="nextStep">Suivant</span>
                                    <span wire:loading wire:target="nextStep">Chargement...</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @else
                                <button type="submit" wire:loading.attr="disabled"
                                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:opacity-50 transition-all duration-200 shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="submitCandidature">Soumettre ma candidature</span>
                                    <span wire:loading wire:target="submitCandidature">Envoi en cours...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <!-- Messages d'erreur globaux -->
        @if (session()->has('error'))
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Messages de debug -->
        @if (session()->has('info'))
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700">{{ session('info') }}</p>
            </div>
        @endif

        @if (session()->has('success'))
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif
    </div>
</div>