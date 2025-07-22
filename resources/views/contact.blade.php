@extends('layouts.modern')

@section('title', 'Contact - BRACONGO Stages')

@section('content')
<!-- Hero Section pour Contact -->
<section id="herocontacts" class="hero-modern bg-gradient-to-br from-orange-50 to-red-50">
    <div class="hero-content">
        <h1 class="hero-title">
            Contactez 
            <span class="text-gradient">BRACONGO</span>
        </h1>
        <p class="hero-subtitle">
            Notre équipe est là pour répondre à toutes vos questions sur nos programmes de stage 
            et vous accompagner dans votre démarche
        </p>
    </div>
</section>

<!-- Section Contact Principal -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Informations de contact -->
            <div>
                <h2 class="text-3xl font-bold text-bracongo-gray-900 mb-8">Informations de Contact</h2>
                
                <div class="space-y-8">
                    <!-- Coordonnées principales -->
                    <div class="bg-bracongo-gray-50 rounded-2xl p-6">
                        <h3 class="text-xl font-semibold text-bracongo-gray-900 mb-4">Coordonnées Principales</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-bracongo-orange/10 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-bracongo-gray-900">Adresse</h4>
                                    <p class="text-bracongo-gray-600">Avenue du Port, 123<br>Kinshasa, République Démocratique du Congo</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-bracongo-orange/10 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-bracongo-gray-900">Téléphone</h4>
                                    <p class="text-bracongo-gray-600">
                                        <a href="tel:+242012345678" class="hover:text-bracongo-orange transition-colors">+242 01 234 5678</a><br>
                                        <a href="tel:+242098765432" class="hover:text-bracongo-orange transition-colors">+242 09 876 5432</a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-bracongo-orange/10 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-bracongo-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-bracongo-gray-900">Email</h4>
                                    <p class="text-bracongo-gray-600">
                                        <a href="mailto:stages@bracongo.cg" class="hover:text-bracongo-orange transition-colors">stages@bracongo.cg</a><br>
                                        <a href="mailto:rh@bracongo.cg" class="hover:text-bracongo-orange transition-colors">rh@bracongo.cg</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Horaires d'ouverture -->
                    <div class="bg-bracongo-gray-50 rounded-2xl p-6">
                        <h3 class="text-xl font-semibold text-bracongo-gray-900 mb-4">Horaires d'Ouverture</h3>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-bracongo-gray-600">Lundi - Vendredi</span>
                                <span class="font-medium text-bracongo-gray-900">8h00 - 17h00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-bracongo-gray-600">Samedi</span>
                                <span class="font-medium text-bracongo-gray-900">9h00 - 13h00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-bracongo-gray-600">Dimanche</span>
                                <span class="font-medium text-bracongo-gray-900">Fermé</span>
                            </div>
                        </div>
                    </div>

                    <!-- Réseaux sociaux -->
                    <div class="bg-bracongo-gray-50 rounded-2xl p-6">
                        <h3 class="text-xl font-semibold text-bracongo-gray-900 mb-4">Suivez-nous</h3>
                        
                        <div class="flex space-x-4">
                            <a href="#" class="w-10 h-10 bg-bracongo-orange text-white rounded-full flex items-center justify-center hover:bg-bracongo-red transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-bracongo-orange text-white rounded-full flex items-center justify-center hover:bg-bracongo-red transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-bracongo-orange text-white rounded-full flex items-center justify-center hover:bg-bracongo-red transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-bracongo-orange text-white rounded-full flex items-center justify-center hover:bg-bracongo-red transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de contact -->
            <div>
                <h2 class="text-3xl font-bold text-bracongo-gray-900 mb-8">Envoyez-nous un message</h2>
                
                <!-- Messages flash -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-bracongo-gray-700 mb-2">Nom *</label>
                            <input type="text" id="nom" name="nom" required 
                                   class="w-full px-4 py-3 border border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange"
                                   placeholder="Votre nom">
                        </div>
                        
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-bracongo-gray-700 mb-2">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" required 
                                   class="w-full px-4 py-3 border border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange"
                                   placeholder="Votre prénom">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-bracongo-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-3 border border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange"
                               placeholder="votre.email@exemple.com">
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-bracongo-gray-700 mb-2">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" 
                               class="w-full px-4 py-3 border border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange"
                               placeholder="+242 01 234 5678">
                    </div>

                    <div>
                        <label for="sujet" class="block text-sm font-medium text-bracongo-gray-700 mb-2">Sujet *</label>
                        <select id="sujet" name="sujet" required 
                                class="w-full px-4 py-3 border border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange">
                            <option value="">Sélectionnez un sujet</option>
                            <option value="information">Demande d'information sur les stages</option>
                            <option value="candidature">Question sur ma candidature</option>
                            <option value="partenariat">Partenariat école/université</option>
                            <option value="technique">Problème technique</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-bracongo-gray-700 mb-2">Message *</label>
                        <textarea id="message" name="message" rows="6" required 
                                  class="w-full px-4 py-3 border border-bracongo-gray-300 rounded-lg focus:ring-bracongo-orange focus:border-bracongo-orange"
                                  placeholder="Décrivez votre demande en détail..."></textarea>
                    </div>

                    <div class="flex items-start">
                        <input type="checkbox" id="newsletter" name="newsletter" 
                               class="mt-1 h-4 w-4 text-bracongo-orange focus:ring-bracongo-orange border-bracongo-gray-300 rounded">
                        <label for="newsletter" class="ml-2 text-sm text-bracongo-gray-600">
                            Je souhaite recevoir les actualités et opportunités de stage par email
                        </label>
                    </div>

                    <button type="submit" class="w-full btn-primary-large">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Section FAQ -->
<section class="py-16 bg-bracongo-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-bracongo-gray-900 mb-4">Questions Fréquentes</h2>
            <p class="text-lg text-bracongo-gray-600">
                Trouvez rapidement des réponses à vos questions les plus courantes
            </p>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-bracongo-gray-200">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-bracongo-gray-50 transition-colors" 
                        onclick="toggleFAQ(this)">
                    <span class="font-medium text-bracongo-gray-900">Comment postuler à un stage chez BRACONGO ?</span>
                    <svg class="w-5 h-5 text-bracongo-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-bracongo-gray-600">
                        Pour postuler à un stage chez BRACONGO, rendez-vous sur notre page de candidature, 
                        remplissez le formulaire en ligne et téléchargez vos documents (CV, lettre de motivation, etc.). 
                        Notre équipe étudiera votre dossier et vous recontactera dans les 5-7 jours ouvrables.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-bracongo-gray-200">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-bracongo-gray-50 transition-colors" 
                        onclick="toggleFAQ(this)">
                    <span class="font-medium text-bracongo-gray-900">Quels sont les documents requis pour une candidature ?</span>
                    <svg class="w-5 h-5 text-bracongo-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-bracongo-gray-600">
                        Les documents requis sont : CV à jour, lettre de motivation, certificat de scolarité, 
                        relevés de notes, et éventuellement des lettres de recommandation. Tous les documents 
                        doivent être au format PDF et ne pas dépasser 2MB chacun.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-bracongo-gray-200">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-bracongo-gray-50 transition-colors" 
                        onclick="toggleFAQ(this)">
                    <span class="font-medium text-bracongo-gray-900">Combien de temps dure un stage chez BRACONGO ?</span>
                    <svg class="w-5 h-5 text-bracongo-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-bracongo-gray-600">
                        La durée des stages varie selon les opportunités disponibles, généralement entre 2 et 6 mois. 
                        La durée exacte est précisée dans chaque offre de stage. Nous proposons également des stages 
                        d'été de 2-3 mois.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-bracongo-gray-200">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-bracongo-gray-50 transition-colors" 
                        onclick="toggleFAQ(this)">
                    <span class="font-medium text-bracongo-gray-900">Les stages sont-ils rémunérés ?</span>
                    <svg class="w-5 h-5 text-bracongo-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-bracongo-gray-600">
                        Oui, tous nos stages sont rémunérés. Le montant varie selon le niveau d'étude et la durée 
                        du stage. Nous offrons également des avantages comme les repas, le transport, et une 
                        attestation de stage reconnue.
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-bracongo-gray-200">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-bracongo-gray-50 transition-colors" 
                        onclick="toggleFAQ(this)">
                    <span class="font-medium text-bracongo-gray-900">Comment suivre l'état de ma candidature ?</span>
                    <svg class="w-5 h-5 text-bracongo-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="px-6 pb-4 hidden">
                    <p class="text-bracongo-gray-600">
                        Après soumission de votre candidature, vous recevrez un code de suivi unique. 
                        Utilisez ce code sur notre page de suivi pour consulter l'état de votre candidature 
                        en temps réel. Vous recevrez également des notifications par email à chaque étape.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section CTA -->
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
            <a href="/opportunites" class="btn-outline-white-large">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Voir les opportunités
            </a>
        </div>
    </div>
</section>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

<style>
.btn-primary-large {
    @apply inline-flex items-center px-6 py-3 bg-bracongo-orange text-white font-semibold rounded-lg hover:bg-bracongo-red transition-colors duration-200;
}

.btn-white-large {
    @apply inline-flex items-center px-6 py-3 bg-white text-bracongo-orange font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200;
}

.btn-outline-white-large {
    @apply inline-flex items-center px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-bracongo-orange transition-colors duration-200;
}
</style>
@endsection 