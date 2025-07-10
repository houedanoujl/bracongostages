<?php

namespace App\Livewire;

use App\Models\Candidature;
use Livewire\Component;
use Livewire\Attributes\Validate;

class SuiviCandidature extends Component
{
    public $candidature = null;
    public $code = '';
    public $showDetails = false;

    #[Validate('required|string|min:3')]
    public $searchCode = '';

    public function mount($code = null)
    {
        if ($code) {
            $this->searchCode = $code;
            // Charger directement la candidature sans redirection
            $this->candidature = Candidature::where('code_suivi', $code)
                ->with(['documents', 'evaluation'])
                ->first();
            
            if ($this->candidature) {
                $this->showDetails = true;
                $this->code = $this->candidature->code_suivi;
            }
        }
    }

    public function searchCandidature()
    {
        $this->validate([
            'searchCode' => 'required|string|min:3'
        ]);

        $candidature = Candidature::where('code_suivi', $this->searchCode)
            ->with(['documents', 'evaluation'])
            ->first();

        if ($candidature) {
            // Redirection vers l'URL avec le code de suivi
            return $this->redirect('/suivi/' . $candidature->code_suivi);
        } else {
            session()->flash('error', 'Aucune candidature trouvée avec ce code de suivi.');
            $this->showDetails = false;
        }
    }

    public function resetSearch()
    {
        $this->reset(['candidature', 'searchCode', 'showDetails', 'code']);
    }

    public function getStatutBadgeClass()
    {
        if (!$this->candidature) return '';

        return $this->candidature->statut->getBadgeClass();
    }

    public function getStatutLabel()
    {
        if (!$this->candidature) return '';

        return $this->candidature->statut->getLabel();
    }

    public function getStatutColor()
    {
        if (!$this->candidature) return '';

        return $this->candidature->statut->getColor();
    }

    public function getTimelineSteps()
    {
        if (!$this->candidature) return [];

        $steps = [
            [
                'status' => 'non_traite',
                'label' => 'Candidature reçue',
                'description' => 'Votre candidature a été reçue et est en attente de traitement',
                'date' => $this->candidature->created_at,
                'completed' => true,
            ],
            [
                'status' => 'analyse_dossier',
                'label' => 'Analyse du dossier',
                'description' => 'Votre dossier est en cours d\'analyse par nos équipes',
                'date' => null,
                'completed' => in_array($this->candidature->statut->value, [
                    'analyse_dossier', 'attente_test', 'attente_resultats', 
                    'attente_affectation', 'valide'
                ]),
            ],
            [
                'status' => 'attente_test',
                'label' => 'Test technique',
                'description' => 'Vous serez convoqué(e) pour un test technique',
                'date' => null,
                'completed' => in_array($this->candidature->statut->value, [
                    'attente_test', 'attente_resultats', 'attente_affectation', 'valide'
                ]),
            ],
            [
                'status' => 'attente_resultats',
                'label' => 'Résultats du test',
                'description' => 'Analyse des résultats du test technique',
                'date' => null,
                'completed' => in_array($this->candidature->statut->value, [
                    'attente_resultats', 'attente_affectation', 'valide'
                ]),
            ],
            [
                'status' => 'attente_affectation',
                'label' => 'Affectation',
                'description' => 'Attribution du stage dans la direction appropriée',
                'date' => null,
                'completed' => in_array($this->candidature->statut->value, [
                    'attente_affectation', 'valide'
                ]),
            ],
            [
                'status' => 'valide',
                'label' => 'Stage validé',
                'description' => 'Félicitations ! Votre stage a été validé',
                'date' => $this->candidature->statut->value === 'valide' ? $this->candidature->updated_at : null,
                'completed' => $this->candidature->statut->value === 'valide',
            ],
        ];

        // Si rejeté, on arrête le processus
        if ($this->candidature->statut->value === 'rejete') {
            $steps = array_slice($steps, 0, 2); // Garde seulement les 2 premières étapes
            $steps[] = [
                'status' => 'rejete',
                'label' => 'Candidature rejetée',
                'description' => $this->candidature->motif_rejet ?? 'Votre candidature n\'a pas été retenue',
                'date' => $this->candidature->updated_at,
                'completed' => true,
                'rejected' => true,
            ];
        }

        return $steps;
    }

    public function render()
    {
        return view('livewire.suivi-candidature', [
            'timelineSteps' => $this->getTimelineSteps(),
        ])->layout('layouts.modern');
    }
} 