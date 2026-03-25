@props([
    'record' => null,
    'currentWizardStep' => 1,
    'totalSteps' => 11,
])

@php
    if (!$record || !$record->statut) return;
    
    $statut = $record->statut;
    $etape = $statut->getEtape();
    $pct = round(($etape / 13) * 100);
    $isRejected = $statut->value === 'rejete';
    $isTerminal = $statut->isTerminal();
    
    $wizardSteps = [
        1 => 'Candidat',
        2 => 'Stage souhaité',
        3 => 'Documents',
        4 => 'Gestion',
        5 => 'Tests',
        6 => 'Affectation',
        7 => 'Induction & Réponse',
        8 => 'Évaluation',
        9 => 'Attestation',
        10 => 'Remboursement',
    ];
    
    $nextStatuts = $statut->getNextStatuts();
    $nextLabel = !empty($nextStatuts) ? collect($nextStatuts)->first()->getLabel() : null;
    
    // Email status per step
    $emailSteps = ['Gestion', 'Tests', 'Affectation', 'Induction & Réponse', 'Évaluation', 'Attestation', 'Remboursement'];
    $currentStepName = $wizardSteps[$currentWizardStep] ?? '';
    $emailSent = $record->emailEtapeEnvoye($currentStepName);
    $emailDate = $record->dateEmailEtape($currentStepName);
@endphp

<div class="fi-wizard-progress-bar" 
     style="position: sticky; bottom: 0; z-index: 40; background: white; border-top: 2px solid rgb(229, 231, 235); padding: 12px 20px; box-shadow: 0 -4px 12px rgba(0,0,0,0.08);"
     x-data="{ expanded: false }">
    
    {{-- Compact bar --}}
    <div class="flex items-center justify-between gap-4 cursor-pointer" @click="expanded = !expanded">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            {{-- Step indicator --}}
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold text-white"
                      style="background-color: {{ $isRejected ? '#ef4444' : '#3b82f6' }};">
                    {{ $currentWizardStep }}
                </span>
                <span class="text-sm font-semibold text-gray-700">
                    {{ $wizardSteps[$currentWizardStep] ?? '' }}
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="flex-1 max-w-xs">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500"
                         style="width: {{ $pct }}%; background-color: {{ $isRejected ? '#ef4444' : '#22c55e' }};"></div>
                </div>
            </div>

            {{-- Status badge --}}
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium shrink-0"
                  style="background-color: {{ $isRejected ? '#fef2f2' : '#f0fdf4' }}; color: {{ $isRejected ? '#ef4444' : '#16a34a' }};">
                <x-dynamic-component :component="$statut->getIcon()" class="w-3.5 h-3.5" />
                {{ $statut->getLabel() }}
            </span>

            {{-- Email status --}}
            @if(in_array($currentStepName, $emailSteps))
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium shrink-0
                    {{ $emailSent ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                    @if($emailSent)
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Email envoyé
                    @else
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        Email non envoyé
                    @endif
                </span>
            @endif

            {{-- Next step --}}
            @if($nextLabel && !$isTerminal)
                <span class="text-xs text-gray-400 shrink-0">
                    Suivant : <strong class="text-gray-600">{{ $nextLabel }}</strong>
                </span>
            @elseif($isTerminal)
                <span class="text-xs text-gray-400 shrink-0">🏁 Processus terminé</span>
            @endif
        </div>

        {{-- Expand button --}}
        <button class="text-gray-400 hover:text-gray-600 transition shrink-0">
            <svg class="w-5 h-5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
        </button>
    </div>

    {{-- Expanded details --}}
    <div x-show="expanded" x-collapse class="mt-3 pt-3 border-t border-gray-100">
        <div class="flex items-center gap-1 overflow-x-auto pb-2">
            @foreach($wizardSteps as $stepNum => $stepName)
                @php
                    $maxStep = \App\Filament\Resources\CandidatureResource\Pages\EditCandidature::getWizardStepForStatut($statut);
                    $isCompleted = $stepNum < $maxStep;
                    $isCurrent = $stepNum === $currentWizardStep;
                    $isLocked = $stepNum > $maxStep;
                @endphp
                <div class="flex items-center gap-1 shrink-0">
                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-blue-500 text-white ring-2 ring-blue-200' : ($isLocked ? 'bg-gray-200 text-gray-400' : 'bg-gray-300 text-gray-600')) }}">
                            @if($isCompleted)
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        <span class="text-[10px] mt-0.5 whitespace-nowrap {{ $isCurrent ? 'text-blue-600 font-semibold' : 'text-gray-400' }}">
                            {{ \Illuminate\Support\Str::limit($stepName, 10) }}
                        </span>
                    </div>
                    @if($stepNum < count($wizardSteps))
                        <div class="w-4 h-0.5 {{ $isCompleted ? 'bg-green-400' : 'bg-gray-200' }} mt-[-12px]"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
