<div x-data="{ scrolled: false }" 
     @scroll.window="scrolled = window.pageYOffset > 10"
     :class="{ 'bg-white/98 shadow-soft': scrolled, 'bg-white/95': !scrolled }"
     class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md border-b border-bracongo-gray-200 transition-all duration-300">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo Section -->
            <div class="flex items-center space-x-3">
                <a href="/" class="flex items-center space-x-3" wire:click="closeMobileMenu">
                    <div class="w-12 h-12 bg-bracongo-red rounded-xl flex items-center justify-center transition-transform duration-300 hover:scale-105">
                        <span class="text-white font-bold text-xl">B</span>
                    </div>
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
                <a href="#opportunites" class="nav-link-modern">Opportunités</a>
                <a href="#contact" class="nav-link-modern">Contact</a>
            </nav>

            <!-- Desktop CTA -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="/candidature" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Postuler maintenant
                </a>
                <a href="/admin" class="btn-outline">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Administration
                </a>
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
                    <a href="#opportunites" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300">
                        Opportunités
                    </a>
                    <a href="#contact" 
                       wire:click="closeMobileMenu"
                       class="block text-bracongo-gray-600 hover:text-bracongo-red font-medium py-2 transition-colors duration-300">
                        Contact
                    </a>
                    
                    <div class="pt-4 space-y-3 border-t border-bracongo-gray-200">
                        <a href="/candidature" 
                           wire:click="closeMobileMenu"
                           class="btn-primary w-full justify-center">
                            Postuler maintenant
                        </a>
                        <a href="/admin" 
                           wire:click="closeMobileMenu"
                           class="btn-outline w-full justify-center">
                            Administration
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>