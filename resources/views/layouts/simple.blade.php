<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'BRACONGO Stages' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gradient-to-br from-yellow-50 to-red-50">
    <!-- Header BRACONGO -->
    <header class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-red-800 font-bold text-xl">B</span>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-white">BRACONGO</h1>
                        <p class="text-yellow-200 text-sm font-medium">Stages & Formations</p>
                    </div>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-yellow-100 hover:text-yellow-300 transition-colors font-medium">Accueil</a>
                    <a href="/candidature" class="text-yellow-100 hover:text-yellow-300 transition-colors font-medium">Candidature</a>
                    <a href="/suivi" class="text-yellow-100 hover:text-yellow-300 transition-colors font-medium">Suivi</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    <!-- Footer BRACONGO -->
    <footer class="bg-gradient-to-r from-red-800 via-red-900 to-red-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center mr-3 shadow-md">
                        <span class="text-red-800 font-bold text-lg">B</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white">BRACONGO</h2>
                </div>
                <p class="text-yellow-200 text-sm">
                    &copy; {{ date('Y') }} BRACONGO - Brasseries du Congo. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>