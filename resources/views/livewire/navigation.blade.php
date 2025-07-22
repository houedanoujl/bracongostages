<div x-data="{ scrolled: false }" 
     @scroll.window="scrolled = window.pageYOffset > 10"
     :class="{ 'bg-white/98 shadow-soft': scrolled, 'bg-white/95': !scrolled }"
     class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md border-b border-bracongo-gray-200 transition-all duration-300">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo Section -->
            <div class="flex items-center space-x-3">
                <a href="/" class="flex items-center space-x-3" wire:click="closeMobileMenu">
                <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="BRACONGO">
                    <div>
                        <div class="text-2xl font-bold text-bracongo-red">BRACONGO</div>
                        <div class="text-sm text-bracongo-gray-600 font-medium">Stages & Formations</div>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="/" 
                   class="nav-link-modern {{ $currentPage === 'home' ? 'text-bracongo-red' : '' }}">
                    Accueil
                </a>
                <a href="/candidature" 
                   class="nav-link-modern {{ $currentPage === 'candidature.form' ? 'text-bracongo-red' : '' }}">
                    Postuler
                </a>
                <a href="/suivi" 
                   class="nav-link-modern {{ $currentPage === 'candidature.suivi' ? 'text-bracongo-red' : '' }}">
                    Suivi
                </a>
                <a href="{{ route('opportunites') }}" class="nav-link-modern">Opportunités</a>
                <a href="{{ route('contact') }}" class="nav-link-modern">Contact</a>
            </nav>

            <!-- Desktop CTA -->
            <div class="hidden md:flex items-center space-x-4">
                @auth('candidat')
                    <a href="{{ route('candidat.dashboard') }}" class="btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Mon espace
                    </a>
                    <form method="POST" action="{{ route('candidat.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-outline">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                @else
                    <a href="{{ route('candidat.login') }}" class="btn-outline">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Se connecter
                    </a>
                    <a href="{{ route('candidat.create') }}" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Créer un compte
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button wire:click="toggleMobileMenu" 
                    class="md:hidden p-2 rounded-lg text-bracongo-gray-600 hover:text-bracongo-red hover:bg-bracongo-gray-100 transition-colors duration-300">
                @if($mobileMenuOpen)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                @endif
            </button>
        </div>

        <!-- Mobile Menu -->
        @if($mobileMenuOpen)
            <div class="md:hidden border-t border-bracongo-gray-200 bg-white animate-slide-down">
                <div class="px-4 py-6 space-y-4">
                    <a href="/" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300 {{ $currentPage === 'home' ? 'text-bracongo-red' : '' }}">
                        Accueil
                    </a>
                    <a href="/candidature" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300 {{ $currentPage === 'candidature.form' ? 'text-bracongo-red' : '' }}">
                        Postuler
                    </a>
                    <a href="/suivi" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300 {{ $currentPage === 'candidature.suivi' ? 'text-bracongo-red' : '' }}">
                        Suivi
                    </a>
                    <a href="{{ route('opportunites') }}" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300">
                        Opportunités
                    </a>
                    <a href="{{ route('contact') }}" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300">
                        Contact
                    </a>
                    
                    <div class="pt-4 space-y-3 border-t border-bracongo-gray-200">
                        @auth('candidat')
                            <a href="{{ route('candidat.dashboard') }}" 
                               wire:click="closeMobileMenu"
                               class="btn-outline w-full justify-center">
                                Mon espace
                            </a>
                            <form method="POST" action="{{ route('candidat.logout') }}" class="w-full">
                                @csrf
                                <button type="submit" 
                                        wire:click="closeMobileMenu"
                                        class="btn-outline w-full justify-center">
                                    Déconnexion
                                </button>
                            </form>
                        @else
                            <a href="{{ route('candidat.login') }}" 
                               wire:click="closeMobileMenu"
                               class="btn-outline w-full justify-center">
                                Se connecter
                            </a>
                            <a href="{{ route('candidat.create') }}" 
                               wire:click="closeMobileMenu"
                               class="btn-primary w-full justify-center">
                                Créer un compte
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>