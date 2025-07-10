<?php

namespace App\Livewire;

use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CandidatureForm extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $nom = '';

    #[Validate('required|string|max:255')]
    public $prenom = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('required|string|max:20')]
    public $telephone = '';

    #[Validate('required|string')]
    public $etablissement = '';

    #[Validate('required|string')]
    public $niveau_etude = '';

    #[Validate('nullable|string|max:255')]
    public $faculte = '';

    #[Validate('required|string')]
    public $objectif_stage = '';

    #[Validate('required|array|min:1')]
    public $directions_souhaitees = [];

    #[Validate('required|date|after:today')]
    public $periode_debut_souhaitee = '';

    #[Validate('required|date|after:periode_debut_souhaitee')]
    public $periode_fin_souhaitee = '';

    #[Validate('required|file|mimes:pdf,doc,docx|max:2048')]
    public $cv;

    #[Validate('required|file|mimes:pdf,doc,docx|max:2048')]
    public $lettre_motivation;

    #[Validate('required|file|mimes:pdf,jpg,jpeg,png|max:2048')]
    public $certificat_scolarite;

    #[Validate('nullable|file|mimes:pdf,jpg,jpeg,png|max:2048')]
    public $releves_notes;

    public $currentStep = 1;
    public $totalSteps = 4;
    public $showSuccess = false;
    public $candidatureCode = '';

    public function mount()
    {
        $this->periode_debut_souhaitee = now()->addMonth()->format('Y-m-d');
        $this->periode_fin_souhaitee = now()->addMonths(4)->format('Y-m-d');
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'nom' => 'required|string|max:255',
                    'prenom' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'telephone' => 'required|string|max:20',
                ]);
                break;
            case 2:
                $this->validate([
                    'etablissement' => 'required|string',
                    'niveau_etude' => 'required|string',
                    'faculte' => 'nullable|string|max:255',
                ]);
                break;
            case 3:
                $this->validate([
                    'objectif_stage' => 'required|string',
                    'directions_souhaitees' => 'required|array|min:1',
                    'periode_debut_souhaitee' => 'required|date|after:today',
                    'periode_fin_souhaitee' => 'required|date|after:periode_debut_souhaitee',
                ]);
                break;
        }
    }

    public function submitCandidature()
    {
        $this->validate();

        try {
            // Créer la candidature
            $candidature = Candidature::create([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'etablissement' => $this->etablissement,
                'niveau_etude' => $this->niveau_etude,
                'faculte' => $this->faculte,
                'objectif_stage' => $this->objectif_stage,
                'directions_souhaitees' => $this->directions_souhaitees,
                'periode_debut_souhaitee' => $this->periode_debut_souhaitee,
                'periode_fin_souhaitee' => $this->periode_fin_souhaitee,
                'statut' => StatutCandidature::NON_TRAITE,
            ]);

            // Sauvegarder les documents
            $this->saveDocuments($candidature);

            $this->candidatureCode = $candidature->code_suivi;
            $this->showSuccess = true;
            $this->reset(['currentStep']);

        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la soumission de votre candidature. Veuillez réessayer.');
        }
    }

    private function saveDocuments(Candidature $candidature)
    {
        $documents = [
            'cv' => $this->cv,
            'lettre_motivation' => $this->lettre_motivation,
            'certificat_scolarite' => $this->certificat_scolarite,
            'releves_notes' => $this->releves_notes,
        ];

        foreach ($documents as $type => $file) {
            if ($file) {
                $path = $file->store('documents', 'public');
                
                $candidature->documents()->create([
                    'type_document' => $type,
                    'nom_original' => $file->getClientOriginalName(),
                    'chemin_fichier' => $path,
                    'taille_fichier' => $file->getSize(),
                ]);
            }
        }
    }

    public function resetForm()
    {
        $this->reset();
        $this->showSuccess = false;
        $this->currentStep = 1;
        $this->mount();
    }

    public function render()
    {
        return view('livewire.candidature-form', [
            'etablissements' => Candidature::getEtablissements(),
            'niveaux_etude' => Candidature::getNiveauxEtude(),
            'directions_disponibles' => Candidature::getDirectionsDisponibles(),
        ])->layout('layouts.modern');
    }
} 