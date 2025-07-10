<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if($showSuccess)
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="flex justify-center mb-6">
                    <div class="bg-green-100 rounded-full p-4">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Candidature soumise avec succès !</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Votre candidature a été enregistrée. Votre code de suivi est :
                </p>
                <div class="bg-orange-50 border-2 border-orange-200 rounded-lg p-4 mb-6">
                    <span class="text-2xl font-bold text-orange-600">{{ $candidatureCode }}</span>
                </div>
                <p class="text-gray-600 mb-8">
                    Conservez précieusement ce code pour suivre l'évolution de votre candidature.
                </p>
                <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                    <a href="{{ route('candidature.suivi', ['code' => $candidatureCode]) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200">
                        Suivre ma candidature
                    </a>
                    <button wire:click="resetForm" 
                            class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                        Nouvelle candidature
                    </button>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-orange-600 to-orange-500 px-8 py-6">
                    <h1 class="text-3xl font-bold text-white">Candidature de Stage</h1>
                    <p class="text-orange-100 mt-2">Rejoignez l'équipe BRACONGO et développez vos compétences</p>
                </div>

                <!-- Progress Bar -->
                <div class="px-8 py-4 bg-gray-50 border-b">
                    <div class="flex items-center justify-between">
                        @for($i = 1; $i <= $totalSteps; $i++)
                            <div class="flex items-center {{ $i <= $currentStep ? 'text-orange-600' : 'text-gray-400' }}">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full border-2 {{ $i <= $currentStep ? 'border-orange-600 bg-orange-600 text-white' : 'border-gray-300' }}">
                                    {{ $i }}
                                </div>
                                @if($i < $totalSteps)
                                    <div class="w-16 h-1 mx-2 {{ $i < $currentStep ? 'bg-orange-600' : 'bg-gray-300' }}"></div>
                                @endif
                            </div>
                        @endfor
                    </div>
                    <div class="mt-2 text-sm text-gray-600 text-center">
                        @if($currentStep == 1) Informations personnelles
                        @elseif($currentStep == 2) Formation
                        @elseif($currentStep == 3) Stage souhaité
                        @elseif($currentStep == 4) Documents
                        @endif
                    </div>
                </div>

                <form wire:submit.prevent="submitCandidature" class="p-8">
                    @if($currentStep == 1)
                        <!-- Step 1: Personal Information -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Informations personnelles</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                                    <input wire:model="nom" type="text" id="nom" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                                    <input wire:model="prenom" type="text" id="prenom" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input wire:model="email" type="email" id="email" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                                    <input wire:model="telephone" type="tel" id="telephone" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('telephone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    @elseif($currentStep == 2)
                        <!-- Step 2: Education -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Formation académique</h3>
                            
                            <div>
                                <label for="etablissement" class="block text-sm font-medium text-gray-700 mb-2">Établissement *</label>
                                <select wire:model="etablissement" id="etablissement" 
                                        class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    <option value="">Sélectionnez votre établissement</option>
                                    @foreach($etablissements as $etablissement_option)
                                        <option value="{{ $etablissement_option }}">{{ $etablissement_option }}</option>
                                    @endforeach
                                </select>
                                @error('etablissement') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="niveau_etude" class="block text-sm font-medium text-gray-700 mb-2">Niveau d'étude *</label>
                                    <select wire:model="niveau_etude" id="niveau_etude" 
                                            class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                        <option value="">Sélectionnez votre niveau</option>
                                        @foreach($niveaux_etude as $niveau_key => $niveau_label)
                                            <option value="{{ $niveau_key }}">{{ $niveau_label }}</option>
                                        @endforeach
                                    </select>
                                    @error('niveau_etude') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="faculte" class="block text-sm font-medium text-gray-700 mb-2">Faculté/Département</label>
                                    <input wire:model="faculte" type="text" id="faculte" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('faculte') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    @elseif($currentStep == 3)
                        <!-- Step 3: Internship Details -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Détails du stage souhaité</h3>
                            
                            <div>
                                <label for="objectif_stage" class="block text-sm font-medium text-gray-700 mb-2">Objectif du stage *</label>
                                <textarea wire:model="objectif_stage" id="objectif_stage" rows="4" 
                                          class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500"
                                          placeholder="Décrivez vos objectifs et ce que vous espérez apprendre..."></textarea>
                                @error('objectif_stage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Directions souhaitées *</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($directions_disponibles as $direction)
                                        <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                            <input wire:model="directions_souhaitees" type="checkbox" value="{{ $direction }}" 
                                                   class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                            <span class="ml-3 text-sm text-gray-700">{{ $direction }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('directions_souhaitees') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="periode_debut_souhaitee" class="block text-sm font-medium text-gray-700 mb-2">Date de début souhaitée *</label>
                                    <input wire:model="periode_debut_souhaitee" type="date" id="periode_debut_souhaitee" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('periode_debut_souhaitee') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="periode_fin_souhaitee" class="block text-sm font-medium text-gray-700 mb-2">Date de fin souhaitée *</label>
                                    <input wire:model="periode_fin_souhaitee" type="date" id="periode_fin_souhaitee" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('periode_fin_souhaitee') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                    @elseif($currentStep == 4)
                        <!-- Step 4: Documents -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Documents requis</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="cv" class="block text-sm font-medium text-gray-700 mb-2">CV *</label>
                                    <input wire:model="cv" type="file" id="cv" accept=".pdf,.doc,.docx" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('cv') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="lettre_motivation" class="block text-sm font-medium text-gray-700 mb-2">Lettre de motivation *</label>
                                    <input wire:model="lettre_motivation" type="file" id="lettre_motivation" accept=".pdf,.doc,.docx" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('lettre_motivation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="certificat_scolarite" class="block text-sm font-medium text-gray-700 mb-2">Certificat de scolarité *</label>
                                    <input wire:model="certificat_scolarite" type="file" id="certificat_scolarite" accept=".pdf,.jpg,.jpeg,.png" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('certificat_scolarite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="releves_notes" class="block text-sm font-medium text-gray-700 mb-2">Relevés de notes</label>
                                    <input wire:model="releves_notes" type="file" id="releves_notes" accept=".pdf,.jpg,.jpeg,.png" 
                                           class="w-full rounded-lg border-gray-300 border px-4 py-3 focus:border-orange-500 focus:ring-orange-500">
                                    @error('releves_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            <strong>Note :</strong> Les fichiers acceptés sont PDF, DOC, DOCX (pour CV et lettre) et PDF, JPG, PNG (pour les certificats). 
                                            Taille maximale : 2 MB par fichier.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <div>
                            @if($currentStep > 1)
                                <button type="button" wire:click="previousStep" 
                                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Précédent
                                </button>
                            @endif
                        </div>
                        
                        <div>
                            @if($currentStep < $totalSteps)
                                <button type="button" wire:click="nextStep" 
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors duration-200">
                                    Suivant
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            @else
                                <button type="submit" 
                                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Soumettre ma candidature
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div> 