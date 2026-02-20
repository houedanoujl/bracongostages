<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Messagerie</h1>
            <p class="text-gray-600 mt-1">Échangez avec l'administration concernant vos candidatures</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" style="height: 600px;">
            <div class="flex h-full">
                <!-- Sidebar - Liste des candidatures -->
                <div class="w-1/3 border-r border-gray-200 flex flex-col">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Mes candidatures</h2>
                        @if($unreadCount > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                {{ $unreadCount }} nouveau{{ $unreadCount > 1 ? 'x' : '' }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 overflow-y-auto">
                        @forelse($candidatures as $cand)
                            <button wire:click="selectCandidature({{ $cand->id }})"
                                    class="w-full text-left p-4 hover:bg-gray-50 transition-colors border-b border-gray-100
                                           {{ $candidatureId == $cand->id ? 'bg-bracongo-red-50 border-l-4 border-l-bracongo-red-500' : '' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900">{{ $cand->code_suivi }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $cand->statut->getColor() === 'success' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $cand->statut->getColor() === 'warning' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $cand->statut->getColor() === 'danger' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $cand->statut->getColor() === 'info' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $cand->statut->getColor() === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}
                                        {{ $cand->statut->getColor() === 'primary' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                    ">
                                        {{ $cand->statut->getLabel() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ $cand->opportunite_titre ?? $cand->objectif_stage }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $cand->created_at->format('d/m/Y') }}</p>
                            </button>
                        @empty
                            <div class="p-6 text-center text-gray-500">
                                <p class="text-sm">Aucune candidature</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Zone de chat -->
                <div class="flex-1 flex flex-col">
                    @if($candidatureId && $candidature)
                        <!-- Header du chat -->
                        <div class="p-4 border-b border-gray-200 bg-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $candidature->code_suivi }}</h3>
                                    <p class="text-sm text-gray-500">{{ $candidature->statut->getLabel() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                            @if(count($messages) === 0)
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <p class="mt-2 text-sm">Aucun message pour le moment</p>
                                        <p class="text-xs mt-1">Envoyez un message pour démarrer la conversation</p>
                                    </div>
                                </div>
                            @else
                                @foreach($messages as $msg)
                                    <div class="flex {{ $msg['sender_type'] === 'candidat' ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl
                                            {{ $msg['sender_type'] === 'candidat'
                                                ? 'bg-bracongo-red-500 text-white rounded-br-md'
                                                : 'bg-gray-100 text-gray-800 rounded-bl-md' }}">
                                            <p class="text-sm whitespace-pre-wrap">{{ $msg['contenu'] }}</p>
                                            <p class="text-xs mt-1 {{ $msg['sender_type'] === 'candidat' ? 'text-red-100' : 'text-gray-400' }}">
                                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('d/m H:i') }}
                                                @if($msg['sender_type'] === 'admin')
                                                    - Administration
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Input message -->
                        <div class="p-4 border-t border-gray-200 bg-white">
                            <form wire:submit.prevent="sendMessage" class="flex items-end space-x-3">
                                <div class="flex-1">
                                    <textarea wire:model="newMessage"
                                              rows="2"
                                              class="w-full border-gray-300 rounded-xl focus:ring-bracongo-red-500 focus:border-bracongo-red-500 resize-none text-sm"
                                              placeholder="Tapez votre message..."
                                              maxlength="2000"></textarea>
                                    @error('newMessage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <button type="submit"
                                        class="px-4 py-2 bg-bracongo-red-500 text-white rounded-xl hover:bg-bracongo-red-600 transition-colors flex items-center space-x-1">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span class="text-sm font-medium">Envoyer</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- État vide -->
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center text-gray-400">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p class="mt-3 text-lg font-medium">Sélectionnez une candidature</p>
                                <p class="text-sm mt-1">Choisissez une candidature dans la liste pour voir ou envoyer des messages</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('messagesLoaded', () => {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endscript
