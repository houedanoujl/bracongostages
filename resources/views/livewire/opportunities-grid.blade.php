<div class="max-w-7xl mx-auto">
    <!-- Filters et contrôles -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <!-- Filtres par catégorie -->
        <div class="flex flex-wrap gap-2">
            @foreach($categories as $key => $label)
                <button wire:click="filterByCategory('{{ $key }}')"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105
                               {{ $selectedCategory === $key 
                                   ? 'bg-bracongo-red text-white shadow-bracongo' 
                                   : 'bg-white text-bracongo-gray-600 border border-bracongo-gray-300 hover:border-bracongo-red hover:text-bracongo-red' }}">
                    {{ $label }}
                    @if($key !== 'all')
                        <span class="ml-1 text-xs opacity-75">
                            ({{ count(array_filter($opportunities, fn($o) => $o['category'] === $key)) }})
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        <!-- Contrôles d'affichage -->
        <div class="flex items-center gap-4">
            <div class="text-sm text-bracongo-gray-600">
                {{ count($filteredOpportunities) }} opportunité(s)
            </div>
            
            <button wire:click="toggleViewMode"
                    class="p-2 rounded-lg text-bracongo-gray-600 hover:text-bracongo-red hover:bg-bracongo-gray-100 transition-colors duration-300">
                @if($viewMode === 'grid')
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                @endif
            </button>
        </div>
    </div>

    <!-- Grille des opportunités -->
    <div class="grid {{ $viewMode === 'grid' ? 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3' : 'grid-cols-1' }} gap-8">
        @foreach($filteredOpportunities as $index => $opportunity)
            <div class="opportunity-card animate-on-scroll {{ $viewMode === 'list' ? 'flex items-center space-x-6' : '' }}" 
                 style="animation-delay: {{ $index * 0.1 }}s;"
                 wire:key="{{ $opportunity['id'] }}">
                
                @if($viewMode === 'grid')
                    <!-- Vue grille -->
                    <div class="card-icon">{{ $opportunity['icon'] }}</div>
                    <h3 class="card-title">{{ $opportunity['title'] }}</h3>
                    <p class="card-description">{{ $opportunity['description'] }}</p>
                    
                    <!-- Métadonnées -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-bracongo-gray-500">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $opportunity['duration'] }}
                            </span>
                            <span class="badge-modern badge-info">{{ $opportunity['level'] }}</span>
                        </div>
                        
                        <div class="text-sm text-bracongo-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            {{ $opportunity['available_spots'] }} place(s) disponible(s)
                        </div>
                        
                        <!-- Compétences -->
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($opportunity['skills'], 0, 3) as $skill)
                                <span class="px-2 py-1 bg-bracongo-gray-100 text-bracongo-gray-600 text-xs rounded-full">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <a href="/candidature?domain={{ $opportunity['id'] }}" class="card-cta">
                            Postuler
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        
                        <button class="text-bracongo-gray-400 hover:text-bracongo-red transition-colors duration-300"
                                title="Ajouter aux favoris">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                @else
                    <!-- Vue liste -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-bracongo-red-light rounded-xl flex items-center justify-center text-3xl">
                            {{ $opportunity['icon'] }}
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-bracongo-gray-800 mb-2">{{ $opportunity['title'] }}</h3>
                                <p class="text-bracongo-gray-600 mb-4">{{ $opportunity['description'] }}</p>
                                
                                <div class="flex items-center space-x-6 text-sm text-bracongo-gray-500 mb-3">
                                    <span>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $opportunity['duration'] }}
                                    </span>
                                    <span class="badge-modern badge-info">{{ $opportunity['level'] }}</span>
                                    <span>{{ $opportunity['available_spots'] }} place(s)</span>
                                </div>
                                
                                <div class="flex flex-wrap gap-1">
                                    @foreach($opportunity['skills'] as $skill)
                                        <span class="px-2 py-1 bg-bracongo-gray-100 text-bracongo-gray-600 text-xs rounded-full">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end space-y-2">
                                <a href="/candidature?domain={{ $opportunity['id'] }}" 
                                   class="btn-primary">
                                    Postuler
                                </a>
                                <button class="text-bracongo-gray-400 hover:text-bracongo-red transition-colors duration-300"
                                        title="Ajouter aux favoris">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Message si aucune opportunité -->
    @if(empty($filteredOpportunities))
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-bracongo-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-bracongo-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-bracongo-gray-900 mb-2">Aucune opportunité trouvée</h3>
            <p class="text-bracongo-gray-600 mb-4">Essayez de modifier vos filtres ou consultez toutes les opportunités.</p>
            <button wire:click="filterByCategory('all')" class="btn-primary">
                Voir toutes les opportunités
            </button>
        </div>
    @endif
</div>