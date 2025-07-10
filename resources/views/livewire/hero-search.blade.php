<div class="relative max-w-2xl mx-auto" x-data="{ showSuggestions: @entangle('showSuggestions') }">
    <form wire:submit.prevent="search" class="relative">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Champ de recherche -->
            <div class="flex-1 relative">
                <input wire:model.live="searchTerm" 
                       type="text" 
                       placeholder="Rechercher un stage (ex: marketing, technique...)"
                       class="w-full px-6 py-4 text-lg bg-white/90 backdrop-blur-sm rounded-xl border-2 border-transparent focus:border-white focus:bg-white transition-all duration-300 placeholder-bracongo-gray-500"
                       @click.away="hideSuggestions">
                
                <!-- Suggestions dropdown -->
                <div x-show="showSuggestions" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-large border border-bracongo-gray-200 z-10">
                    
                    <div class="p-4">
                        <div class="text-sm font-semibold text-bracongo-gray-700 mb-3">Domaines suggérés :</div>
                        <div class="space-y-2">
                            @foreach($domains as $key => $label)
                                <button type="button" 
                                        wire:click="selectDomain('{{ $key }}')"
                                        class="w-full text-left px-3 py-2 text-sm text-bracongo-gray-600 hover:text-bracongo-red hover:bg-bracongo-gray-50 rounded-lg transition-colors duration-200">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton de recherche -->
            <button type="submit" 
                    class="px-8 py-4 bg-white text-bracongo-red font-semibold rounded-xl hover:bg-bracongo-gray-50 transition-all duration-300 transform hover:scale-105 shadow-medium">
                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Rechercher
            </button>
        </div>
    </form>

    <!-- Tags de domaines populaires -->
    <div class="mt-6 text-center">
        <div class="text-white/80 text-sm mb-3">Domaines populaires :</div>
        <div class="flex flex-wrap justify-center gap-2">
            @foreach($domains as $key => $label)
                <button wire:click="selectDomain('{{ $key }}')"
                        class="px-4 py-2 bg-white/20 text-white text-sm rounded-full hover:bg-white/30 transition-all duration-300 transform hover:scale-105">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</div>