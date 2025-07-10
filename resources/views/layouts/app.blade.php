<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'BRACONGO Stages' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-orange-600 to-orange-500 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <img class="h-12 w-auto" src="{{ asset('images/bracongo-logo-white.png') }}" alt="BRACONGO">
                            <div class="ml-4">
                                <h1 class="text-2xl font-bold text-white">BRACONGO</h1>
                                <p class="text-orange-100 text-sm">Stages & Formations</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('candidature.create') }}" 
                           class="text-white hover:text-orange-100 transition-colors duration-200 font-medium">
                            Postuler
                        </a>
                        <a href="{{ route('candidature.suivi', ['code' => '']) }}" 
                           class="text-white hover:text-orange-100 transition-colors duration-200 font-medium">
                            Suivi de candidature
                        </a>
                        <a href="/admin" 
                           class="bg-white text-orange-600 hover:bg-orange-50 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Administration
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button type="button" class="mobile-menu-button text-white hover:text-orange-100 focus:outline-none focus:text-orange-100" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Ouvrir le menu principal</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="hidden md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-orange-700">
                    <a href="{{ route('candidature.create') }}" class="text-white hover:text-orange-100 block px-3 py-2 rounded-md text-base font-medium">Postuler</a>
                    <a href="{{ route('candidature.suivi', ['code' => '']) }}" class="text-white hover:text-orange-100 block px-3 py-2 rounded-md text-base font-medium">Suivi</a>
                    <a href="/admin" class="text-white hover:text-orange-100 block px-3 py-2 rounded-md text-base font-medium">Administration</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">BRACONGO</h3>
                        <p class="text-gray-300 text-sm">
                            Brasseries du Congo, leader de l'industrie brassicole en République Démocratique du Congo.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact</h3>
                        <div class="text-gray-300 text-sm space-y-2">
                            <p>📍 Avenue Colonel Lukusa, Kinshasa</p>
                            <p>📞 +243 81 000 0000</p>
                            <p>✉️ stages@bracongo.cd</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Suivez-nous</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-300 hover:text-white transition-colors duration-200">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                    <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} BRACONGO. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
    
    <script>
        // Mobile menu toggle
        const btn = document.querySelector('.mobile-menu-button');
        const menu = document.querySelector('#mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html> 