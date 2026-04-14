<x-filament-panels::page>
    <style>
        /* ===== Layout principal ===== */
        .msg-container {
            height: calc(100vh - 11rem);
            display: flex;
            border-radius: 1rem;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 24px rgba(0,0,0,.04);
            border: 1px solid #e5e7eb;
        }

        /* ===== Sidebar ===== */
        .msg-sidebar {
            width: 340px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e5e7eb;
            background: #fafafa;
        }
        .msg-sidebar-header {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
            color: #fff;
        }
        .msg-sidebar-header h2 {
            font-size: .875rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin: 0;
        }
        .msg-sidebar-search {
            padding: .75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            background: #fff;
        }
        .msg-sidebar-search input {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: .5rem .75rem .5rem 2.25rem;
            font-size: .8125rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            background: #f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E") .625rem center / 1rem no-repeat;
        }
        .msg-sidebar-search input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.1);
        }
        .msg-list {
            flex: 1;
            overflow-y: auto;
        }
        .msg-list::-webkit-scrollbar { width: 4px; }
        .msg-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        /* Conversation item */
        .conv-item {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .875rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background .15s;
            position: relative;
        }
        .conv-item:hover { background: #f3f4f6; }
        .conv-item.active {
            background: #fff7ed;
            border-left: 3px solid #f97316;
        }
        .conv-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .875rem;
            flex-shrink: 0;
            color: #fff;
            text-transform: uppercase;
        }
        .conv-avatar.has-unread { background: linear-gradient(135deg, #dc2626, #f97316); }
        .conv-avatar.no-unread { background: #9ca3af; }
        .conv-info { flex: 1; min-width: 0; }
        .conv-name {
            font-size: .8125rem;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .conv-code {
            font-size: .6875rem;
            color: #6b7280;
            margin-top: 1px;
            font-family: monospace;
        }
        .conv-preview {
            font-size: .75rem;
            color: #9ca3af;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 2px;
        }
        .conv-meta {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }
        .conv-time {
            font-size: .6875rem;
            color: #9ca3af;
        }
        .conv-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            background: #dc2626;
            color: #fff;
            font-size: .6875rem;
            font-weight: 700;
            padding: 0 6px;
            line-height: 1;
        }

        /* ===== Zone Chat ===== */
        .msg-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: #f9fafb;
        }
        .msg-chat-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .msg-chat-header-info h3 {
            font-size: .9375rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .msg-chat-header-info .meta {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-top: 2px;
            flex-wrap: wrap;
        }
        .msg-chat-header-info .meta span {
            font-size: .75rem;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: .6875rem;
            font-weight: 600;
        }
        .link-candidature {
            font-size: .75rem;
            color: #f97316;
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }
        .link-candidature:hover { color: #dc2626; text-decoration: underline; }

        /* Messages area */
        .msg-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            background: linear-gradient(180deg, #f9fafb 0%, #f3f4f6 100%);
        }
        .msg-messages::-webkit-scrollbar { width: 4px; }
        .msg-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        /* Date separator */
        .msg-date-sep {
            text-align: center;
            margin: .75rem 0;
        }
        .msg-date-sep span {
            display: inline-block;
            background: #e5e7eb;
            color: #6b7280;
            font-size: .6875rem;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 10px;
        }

        /* Bubble */
        .msg-bubble-row {
            display: flex;
            margin-bottom: 2px;
        }
        .msg-bubble-row.from-admin { justify-content: flex-end; }
        .msg-bubble-row.from-candidat { justify-content: flex-start; }

        .msg-bubble {
            max-width: 65%;
            padding: .625rem .875rem;
            border-radius: 1rem;
            position: relative;
            line-height: 1.5;
            word-break: break-word;
        }
        .msg-bubble.admin {
            background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .msg-bubble.candidat {
            background: #fff;
            color: #1f2937;
            border: 1px solid #e5e7eb;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .msg-bubble .bubble-text {
            font-size: .8125rem;
        }
        .msg-bubble .bubble-text p { margin: 0; }
        .msg-bubble .bubble-meta {
            font-size: .6875rem;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .msg-bubble.admin .bubble-meta { color: rgba(255,255,255,.7); justify-content: flex-end; }
        .msg-bubble.candidat .bubble-meta { color: #9ca3af; }

        .read-check { font-size: .75rem; }
        .read-check.read { color: rgba(255,255,255,.9); }
        .read-check.unread { color: rgba(255,255,255,.4); }

        /* Input area */
        .msg-input-area {
            padding: 1rem 1.25rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        .msg-input-form {
            display: flex;
            align-items: flex-end;
            gap: .75rem;
        }
        .msg-input-wrap {
            flex: 1;
            position: relative;
        }
        .msg-input-wrap textarea {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: .875rem;
            padding: .625rem 1rem;
            font-size: .8125rem;
            resize: none;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            line-height: 1.5;
            min-height: 44px;
            max-height: 120px;
            color: #1f2937;
            background-color: #fff;
        }
        .msg-input-wrap textarea:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.1);
        }
        .msg-send-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
            color: #fff;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            flex-shrink: 0;
        }
        .msg-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(220,38,38,.3);
        }
        .msg-send-btn:active { transform: scale(.95); }
        .msg-shortcut-hint {
            font-size: .6875rem;
            color: #9ca3af;
            margin-top: .375rem;
            padding-left: .25rem;
        }

        /* Empty states */
        .msg-empty-state {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .msg-empty-content {
            text-align: center;
            max-width: 280px;
        }
        .msg-empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .msg-empty-content h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin: 0 0 .375rem;
        }
        .msg-empty-content p {
            font-size: .8125rem;
            color: #9ca3af;
            margin: 0;
            line-height: 1.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .msg-sidebar { width: 280px; }
            .msg-bubble { max-width: 80%; }
        }
    </style>

    <div class="msg-container" wire:poll.10s="loadMessages">
        {{-- ===== SIDEBAR ===== --}}
        <div class="msg-sidebar">
            <div class="msg-sidebar-header">
                <h2>Messagerie</h2>
                @php $totalUnread = $this->conversations->sum('unread_count'); @endphp
                @if($totalUnread > 0)
                    <div style="margin-top: .375rem;">
                        <span style="background: rgba(255,255,255,.2); padding: 2px 10px; border-radius: 10px; font-size: .75rem; font-weight: 600;">
                            {{ $totalUnread }} non lu{{ $totalUnread > 1 ? 's' : '' }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="msg-sidebar-search">
                <input type="text" placeholder="Rechercher un candidat..." id="search-conversations"
                       onkeyup="filterConversations(this.value)" />
            </div>

            <div class="msg-list" id="conversations-list">
                @forelse($this->conversations as $conv)
                    @php
                        $lastMsg = $conv->messages->first();
                        $initials = strtoupper(mb_substr($conv->prenom, 0, 1) . mb_substr($conv->nom, 0, 1));
                    @endphp
                    <div
                        wire:click="selectCandidature({{ $conv->id }})"
                        class="conv-item {{ $selectedCandidatureId == $conv->id ? 'active' : '' }}"
                        data-name="{{ strtolower($conv->prenom . ' ' . $conv->nom) }}"
                        data-code="{{ strtolower($conv->code_suivi) }}"
                    >
                        <div class="conv-avatar {{ $conv->unread_count > 0 ? 'has-unread' : 'no-unread' }}">
                            {{ $initials }}
                        </div>
                        <div class="conv-info">
                            <div class="conv-name">{{ $conv->prenom }} {{ $conv->nom }}</div>
                            <div class="conv-code">{{ $conv->code_suivi }}</div>
                            @if($lastMsg)
                                <div class="conv-preview">
                                    @if($lastMsg->sender_type === 'admin')
                                        <span style="color:#f97316;">Vous :</span>
                                    @endif
                                    {{ \Illuminate\Support\Str::limit(strip_tags($lastMsg->contenu), 40) }}
                                </div>
                            @endif
                        </div>
                        <div class="conv-meta">
                            @if($lastMsg)
                                <span class="conv-time">{{ $lastMsg->created_at->format('d/m H:i') }}</span>
                            @endif
                            @if($conv->unread_count > 0)
                                <span class="conv-badge">{{ $conv->unread_count }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="msg-empty-state" style="padding: 3rem 1rem;">
                        <div class="msg-empty-content">
                            <div class="msg-empty-icon">
                                <x-heroicon-o-inbox style="width:2.25rem; height:2.25rem; color:#d1d5db;" />
                            </div>
                            <h4>Aucune conversation</h4>
                            <p>Les messages des candidats apparaîtront ici</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ===== ZONE CHAT ===== --}}
        <div class="msg-chat">
            @if($selectedCandidatureId)
                @php $candidature = \App\Models\Candidature::find($selectedCandidatureId); @endphp

                {{-- Header --}}
                <div class="msg-chat-header">
                    <div class="msg-chat-header-info">
                        <h3>{{ $candidature->prenom }} {{ $candidature->nom }}</h3>
                        <div class="meta">
                            <span style="color: #6b7280;">{{ $candidature->code_suivi }}</span>
                            <span style="color:#d1d5db;">·</span>
                            <span class="status-pill" style="
                                @switch($candidature->statut->getColor())
                                    @case('success') background: #dcfce7; color: #15803d; @break
                                    @case('warning') background: #fef9c3; color: #a16207; @break
                                    @case('danger')  background: #fee2e2; color: #dc2626; @break
                                    @case('info')    background: #dbeafe; color: #2563eb; @break
                                    @case('primary') background: #fff7ed; color: #ea580c; @break
                                    @default         background: #f3f4f6; color: #4b5563;
                                @endswitch
                            ">
                                {{ $candidature->statut->getLabel() }}
                            </span>
                            <span style="color:#d1d5db;">·</span>
                            <a href="{{ \App\Filament\Resources\CandidatureResource::getUrl('edit', ['record' => $candidature]) }}"
                               class="link-candidature">
                                Ouvrir le dossier →
                            </a>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:.5rem;">
                        <span style="font-size:.75rem; color:#6b7280;">
                            {{ count($messages) }} message{{ count($messages) > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="msg-messages" id="messages-container">
                    @if(count($messages) === 0)
                        <div class="msg-empty-state">
                            <div class="msg-empty-content">
                                <div class="msg-empty-icon">
                                    <x-heroicon-o-chat-bubble-left-right style="width:2.25rem; height:2.25rem; color:#d1d5db;" />
                                </div>
                                <h4>Aucun message</h4>
                                <p>Envoyez un message pour démarrer la conversation avec ce candidat</p>
                            </div>
                        </div>
                    @else
                        @php $lastDate = null; @endphp
                        @foreach($messages as $msg)
                            @php
                                $msgDate = \Carbon\Carbon::parse($msg['created_at'])->format('d/m/Y');
                                $showDate = $msgDate !== $lastDate;
                                $lastDate = $msgDate;
                                $isToday = $msgDate === now()->format('d/m/Y');
                                $isYesterday = $msgDate === now()->subDay()->format('d/m/Y');
                            @endphp

                            @if($showDate)
                                <div class="msg-date-sep">
                                    <span>
                                        @if($isToday) Aujourd'hui
                                        @elseif($isYesterday) Hier
                                        @else {{ $msgDate }}
                                        @endif
                                    </span>
                                </div>
                            @endif

                            <div class="msg-bubble-row {{ $msg['sender_type'] === 'admin' ? 'from-admin' : 'from-candidat' }}">
                                <div class="msg-bubble {{ $msg['sender_type'] === 'admin' ? 'admin' : 'candidat' }}">
                                    <div class="bubble-text">
                                        @if($msg['sender_type'] === 'admin')
                                            {!! \Illuminate\Support\Str::of($msg['contenu'])->stripTags('<p><br><b><strong><i><em><u><ul><ol><li><a>') !!}
                                        @else
                                            <p>{{ $msg['contenu'] }}</p>
                                        @endif
                                    </div>
                                    <div class="bubble-meta">
                                        {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                                        @if($msg['sender_type'] === 'candidat')
                                            <span>— {{ $candidature->prenom }}</span>
                                        @endif
                                        @if($msg['sender_type'] === 'admin' && $msg['lu_at'])
                                            <span class="read-check read" title="Lu le {{ \Carbon\Carbon::parse($msg['lu_at'])->format('d/m à H:i') }}">✓✓</span>
                                        @elseif($msg['sender_type'] === 'admin')
                                            <span class="read-check unread" title="Non lu">✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- Zone de saisie --}}
                <div class="msg-input-area">
                    <form wire:submit.prevent="sendMessage" class="msg-input-form">
                        <div class="msg-input-wrap">
                            <textarea
                                wire:model="newMessage"
                                rows="1"
                                placeholder="Répondre à {{ $candidature->prenom }}..."
                                maxlength="2000"
                                wire:keydown.ctrl.enter="sendMessage"
                                wire:keydown.meta.enter="sendMessage"
                                oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight, 120)+'px'"
                            ></textarea>
                            @error('newMessage')
                                <span style="color:#dc2626; font-size:.75rem; margin-top:2px; display:block;">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="msg-send-btn" title="Envoyer (Ctrl+Entrée)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                    <div class="msg-shortcut-hint">⌘+Entrée ou Ctrl+Entrée pour envoyer</div>
                </div>

            @else
                {{-- État vide : aucune conversation sélectionnée --}}
                <div class="msg-empty-state">
                    <div class="msg-empty-content">
                        <div class="msg-empty-icon" style="width:100px; height:100px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:44px; height:44px; color:#9ca3af;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <h4 style="font-size:1.125rem;">Boîte de réception</h4>
                        <p>Sélectionnez une conversation dans la liste pour consulter et répondre aux messages des candidats.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        // Auto-scroll en bas des messages
        $wire.on('messagesLoaded', () => {
            setTimeout(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                }
            }, 50);
        });

        // Scroll initial après navigation Livewire
        document.addEventListener('livewire:navigated', () => {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });

        // Filtre de recherche dans les conversations
        window.filterConversations = function(query) {
            const items = document.querySelectorAll('.conv-item');
            const q = query.toLowerCase().trim();
            items.forEach(item => {
                const name = item.dataset.name || '';
                const code = item.dataset.code || '';
                item.style.display = (!q || name.includes(q) || code.includes(q)) ? '' : 'none';
            });
        };
    </script>
    @endscript
</x-filament-panels::page>
