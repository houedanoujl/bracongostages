@extends('layouts.modern')

@section('title', 'Mon profil - BRACONGO Stages')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('candidat.dashboard') }}" class="text-blue-600 hover:text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informations personnelles -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Photo de profil -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Photo de profil</h2>
                    <div class="flex items-center space-x-4">
                        @if($candidat->photo_url)
                            <img src="{{ $candidat->photo_url }}" alt="Photo de profil" class="w-20 h-20 rounded-full object-cover">
                        @else
                            <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-bold text-xl">{{ substr($candidat->prenom, 0, 1) }}{{ substr($candidat->nom, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Photo actuelle</p>
                            <p class="text-xs text-gray-500">Vous pouvez la modifier ci-dessous</p>
                        </div>
                    </div>
                </div>

                <!-- Informations personnelles -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h2>
                    <form method="POST" action="{{ route('candidat.update-profile') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $candidat->prenom) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('prenom')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                <input type="text" id="nom" name="nom" value="{{ old('nom', $candidat->nom) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('nom')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" value="{{ $candidat->email }}" disabled
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                            <p class="text-xs text-gray-500 mt-1">L'email ne peut pas être modifié</p>
                        </div>

                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $candidat->telephone) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('telephone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="etablissement" class="block text-sm font-medium text-gray-700 mb-1">Établissement</label>
                            <select id="etablissement" name="etablissement"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner un établissement</option>
                                @foreach(\App\Models\Candidature::getEtablissements() as $etablissement)
                                    <option value="{{ $etablissement }}" {{ old('etablissement', $candidat->etablissement) == $etablissement ? 'selected' : '' }}>
                                        {{ $etablissement }}
                                    </option>
                                @endforeach
                                <option value="autre" {{ old('etablissement', $candidat->etablissement) == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('etablissement')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="niveau_etude" class="block text-sm font-medium text-gray-700 mb-1">Niveau d'étude</label>
                                <select id="niveau_etude" name="niveau_etude"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Sélectionner un niveau</option>
                                    @foreach(\App\Models\Candidature::getNiveauxEtude() as $niveau)
                                        <option value="{{ $niveau }}" {{ old('niveau_etude', $candidat->niveau_etude) == $niveau ? 'selected' : '' }}>
                                            {{ $niveau }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('niveau_etude')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="faculte" class="block text-sm font-medium text-gray-700 mb-1">Faculté</label>
                                <input type="text" id="faculte" name="faculte" value="{{ old('faculte', $candidat->faculte) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('faculte')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Section Documents de candidature -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Documents de candidature</h2>
                    <p class="text-sm text-gray-600 mb-4">Gérez vos documents pour les candidatures futures. Ces documents seront automatiquement utilisés lors de vos candidatures.</p>
                    
                    <form method="POST" action="{{ route('candidat.update-documents') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        @foreach(\App\Models\DocumentCandidat::getTypesDocument() as $type => $label)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                                    @php $document = $candidat->getDocumentByType($type); @endphp
                                    @if($document)
                                        <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">✓ Disponible</span>
                                    @endif
                                </div>
                                
                                <input type="file" name="documents[{{ $type }}]" accept=".pdf,.doc,.docx"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                
                                @if($document)
                                    <div class="mt-2 text-sm">
                                        <span class="text-gray-600">Fichier actuel :</span>
                                        <span class="text-gray-800">{{ $document->nom_original }}</span>
                                        <span class="text-gray-500">({{ $document->taille_formatee }})</span>
                                    </div>
                                @endif
                                
                                @error('documents.'.$type)
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="text-xs text-gray-500">
                            Formats acceptés : PDF, DOC, DOCX (max 2MB par fichier)
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 font-medium">
                                Mettre à jour les documents
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Changement de mot de passe -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Changer le mot de passe</h2>
                    <form method="POST" action="{{ route('candidat.change-password') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <button type="submit" 
                            class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 font-medium">
                            Changer le mot de passe
                        </button>
                    </form>
                </div>

                <!-- Informations du compte -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Informations du compte</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Membre depuis</p>
                            <p class="text-sm text-gray-900">{{ $candidat->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Dernière connexion</p>
                            <p class="text-sm text-gray-900">{{ $candidat->last_login_at ? $candidat->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Statut</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $candidat->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $candidat->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 