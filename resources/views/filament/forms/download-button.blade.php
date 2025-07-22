@if($document && $url)
    <div class="flex items-center space-x-2">
        <a href="{{ $url }}" 
           target="_blank"
           class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            Télécharger
        </a>
        @if($document->fichierExiste())
            <span class="text-xs text-green-600">✓ Disponible</span>
        @else
            <span class="text-xs text-red-600">✗ Introuvable</span>
        @endif
    </div>
@else
    <span class="text-xs text-gray-500">Aucun document</span>
@endif