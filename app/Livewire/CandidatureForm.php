<?php

namespace App\Livewire;

use App\Models\Candidature;
use App\Enums\StatutCandidature;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    #[Validate('nullable|file|mimes:pdf,doc,docx|max:2048')]
    public $lettres_recommandation;

    #[Validate('nullable|file|mimes:pdf,jpg,jpeg,png|max:2048')]
    public $certificats_competences;

    #[Validate('required|string')]
    public $poste_souhaite = '';

    public $currentStep = 1;
    public $totalSteps = 4;
    public $showSuccess = false;
    public $candidatureCode = '';
    public $validationErrors = [];
    public $showInfoModal = true;
    
    // Paramètres de l'opportunité
    public $opportunite_id = '';
    public $opportunite_titre = '';
    
    // Champ établissement personnalisé
    public $etablissement_autre = '';
    
    // Sélection d'opportunité si pas venue d'un lien
    public $opportunite_selectionnee = '';
    public $afficher_selection_opportunite = false;

    public function mount()
    {
        $this->periode_debut_souhaitee = now()->addMonth()->format('Y-m-d');
        $this->periode_fin_souhaitee = now()->addMonths(4)->format('Y-m-d');
        
        // Récupérer les paramètres d'URL si on vient d'une opportunité
        if (request()->has('domain')) {
            $this->opportunite_id = request()->get('domain');
            $this->opportunite_titre = $this->getOpportuniteTitle($this->opportunite_id);
            
            // Pré-remplir le poste souhaité basé sur l'opportunité
            $this->poste_souhaite = $this->mapOpportuniteToPoste($this->opportunite_id);
        } else {
            // Si pas d'opportunité sélectionnée, afficher la sélection
            $this->afficher_selection_opportunite = true;
        }
    }

    public $erreur_opportunite = false;
    public $showToast = false;
    public $toastMessage = '';
    public $toastType = 'error';

    public function nextStep()
    {
        try {
            // Réinitialiser les erreurs
            $this->validationErrors = [];
            $this->resetErrorBag();
            $this->erreur_opportunite = false;
            
            // Vérifier si une opportunité est sélectionnée
            if ($this->afficher_selection_opportunite) {
                $this->erreur_opportunite = true;
                $this->validationErrors[] = 'Vous devez sélectionner une opportunité avant de continuer.';
                session()->flash('validation_error', 'Veuillez sélectionner une opportunité dans la liste déroulante avant de pouvoir continuer votre candidature.');
                
                // Afficher le toast
                $this->displayToast('Veuillez sélectionner une opportunité dans la liste avant de continuer !', 'error');
                return;
            }
            
            // Valider l'étape actuelle
            $this->validateCurrentStep();
            
            // Si la validation passe, passer à l'étape suivante
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->validationErrors = $e->validator->errors()->all();
            session()->flash('validation_error', 'Veuillez corriger les erreurs avant de continuer.');
            $this->displayToast('Veuillez corriger les erreurs dans le formulaire avant de continuer.', 'error');
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->validationErrors = [];
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
                $rules = [
                    'etablissement' => 'required|string',
                    'niveau_etude' => 'required|string',
                    'faculte' => 'nullable|string|max:255',
                ];
                
                // Si "Autres" est sélectionné, valider le champ établissement_autre
                if ($this->etablissement === 'Autres') {
                    $rules['etablissement_autre'] = 'required|string|max:255';
                }
                
                $this->validate($rules);
                break;
            case 3:
                // Validation des dates plus flexible
                $rules = [
                    'objectif_stage' => 'required|string',
                    'poste_souhaite' => 'required|string',
                    'directions_souhaitees' => 'required|array|min:1',
                    'periode_debut_souhaitee' => 'required|date',
                    'periode_fin_souhaitee' => 'required|date|after:periode_debut_souhaitee',
                ];
                
                // Vérifier si la date de début est dans le futur (avec tolérance)
                if ($this->periode_debut_souhaitee && \Carbon\Carbon::parse($this->periode_debut_souhaitee)->isPast()) {
                    $rules['periode_debut_souhaitee'] = 'required|date|after_or_equal:today';
                }
                
                $this->validate($rules);
                break;
            case 4:
                $this->validate([
                    'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
                    'lettre_motivation' => 'required|file|mimes:pdf,doc,docx|max:2048',
                    'certificat_scolarite' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'releves_notes' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'lettres_recommandation' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                    'certificats_competences' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                ]);
                break;
        }
    }

    public function testSubmit()
    {
        Log::info('Test button clicked!');
        session()->flash('success', 'Le bouton fonctionne !');
    }
    
    public function submitSimple()
    {
        try {
            Log::info('submitSimple - Soumission de candidature');
            
            // Validation minimale des champs requis
            $validationRules = [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telephone' => 'required|string|max:20',
                'etablissement' => 'required|string',
                'niveau_etude' => 'required|string',
                'objectif_stage' => 'required|string',
                'poste_souhaite' => 'required|string',
                'directions_souhaitees' => 'required|array|min:1',
                'periode_debut_souhaitee' => 'required|date',
                'periode_fin_souhaitee' => 'required|date|after:periode_debut_souhaitee',
            ];
            
            // Si "Autres" est sélectionné, valider le champ établissement_autre
            if ($this->etablissement === 'Autres') {
                $validationRules['etablissement_autre'] = 'required|string|max:255';
            }
            
            $this->validate($validationRules);
            Log::info('Validation réussie pour submitSimple');
            
            // Créer la candidature
            $candidature = Candidature::create([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'etablissement' => $this->etablissement,
                'etablissement_autre' => $this->etablissement === 'Autres' ? $this->etablissement_autre : null,
                'niveau_etude' => $this->niveau_etude,
                'faculte' => $this->faculte,
                'objectif_stage' => $this->objectif_stage,
                'poste_souhaite' => $this->poste_souhaite,
                'opportunite_id' => $this->opportunite_id,
                'directions_souhaitees' => $this->directions_souhaitees,
                'periode_debut_souhaitee' => $this->periode_debut_souhaitee,
                'periode_fin_souhaitee' => $this->periode_fin_souhaitee,
                'statut' => StatutCandidature::NON_TRAITE,
            ]);

            Log::info('Candidature créée avec ID: ' . $candidature->id);
            
            // Sauvegarder les documents s'ils existent
            if ($this->cv || $this->lettre_motivation || $this->certificat_scolarite) {
                $this->saveDocuments($candidature);
                Log::info('Documents sauvegardés');
            }
            
            $this->candidatureCode = $candidature->code_suivi;
            $this->showSuccess = true;
            $this->reset(['currentStep']);
            
            Log::info('Candidature finalisée avec code: ' . $this->candidatureCode);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation submitSimple:', $e->validator->errors()->toArray());
            $this->validationErrors = $e->validator->errors()->all();
            session()->flash('validation_error', 'Veuillez corriger les erreurs ci-dessous avant de continuer.');
        } catch (\Exception $e) {
            Log::error('Erreur submitSimple: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Une erreur est survenue lors de la soumission de votre candidature. Veuillez réessayer.');
        }
    }

    public function submitCandidature()
    {
        $this->validationErrors = [];
        
        try {
            Log::info('Début de submitCandidature - Étape: ' . $this->currentStep);
            Log::info('Données:', [
                'nom' => $this->nom,
                'email' => $this->email,
                'cv' => $this->cv ? 'Présent' : 'Absent'
            ]);
            
            // Validation complète de tous les champs
            $validationRules = [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'telephone' => 'required|string|max:20',
                'etablissement' => 'required|string',
                'niveau_etude' => 'required|string',
                'objectif_stage' => 'required|string',
                'poste_souhaite' => 'required|string',
                'directions_souhaitees' => 'required|array|min:1',
                'periode_debut_souhaitee' => 'required|date',
                'periode_fin_souhaitee' => 'required|date|after:periode_debut_souhaitee',
            ];
            
            // Si "Autres" est sélectionné, valider le champ établissement_autre
            if ($this->etablissement === 'Autres') {
                $validationRules['etablissement_autre'] = 'required|string|max:255';
            }
            
            // Validation des fichiers seulement s'ils sont présents
            if ($this->cv) {
                $validationRules['cv'] = 'file|mimes:pdf,doc,docx|max:2048';
            } else {
                $validationRules['cv'] = 'required';
            }
            
            if ($this->lettre_motivation) {
                $validationRules['lettre_motivation'] = 'file|mimes:pdf,doc,docx|max:2048';
            } else {
                $validationRules['lettre_motivation'] = 'required';
            }
            
            if ($this->certificat_scolarite) {
                $validationRules['certificat_scolarite'] = 'file|mimes:pdf,jpg,jpeg,png|max:2048';
            } else {
                $validationRules['certificat_scolarite'] = 'required';
            }
            
            $this->validate($validationRules);
            Log::info('Validation réussie');

            // Créer la candidature
            $candidature = Candidature::create([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'telephone' => $this->telephone,
                'etablissement' => $this->etablissement,
                'etablissement_autre' => $this->etablissement === 'Autres' ? $this->etablissement_autre : null,
                'niveau_etude' => $this->niveau_etude,
                'faculte' => $this->faculte,
                'objectif_stage' => $this->objectif_stage,
                'poste_souhaite' => $this->poste_souhaite,
                'opportunite_id' => $this->opportunite_id,
                'directions_souhaitees' => $this->directions_souhaitees,
                'periode_debut_souhaitee' => $this->periode_debut_souhaitee,
                'periode_fin_souhaitee' => $this->periode_fin_souhaitee,
                'statut' => StatutCandidature::NON_TRAITE,
            ]);

            Log::info('Candidature créée avec ID: ' . $candidature->id);

            // Sauvegarder les documents
            $this->saveDocuments($candidature);
            Log::info('Documents sauvegardés');

            $this->candidatureCode = $candidature->code_suivi;
            $this->showSuccess = true;
            $this->reset(['currentStep']);
            
            Log::info('Candidature finalisée avec code: ' . $this->candidatureCode);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation:', $e->validator->errors()->toArray());
            $this->validationErrors = $e->validator->errors()->all();
            session()->flash('validation_error', 'Veuillez corriger les erreurs ci-dessous avant de continuer.');
        } catch (\Exception $e) {
            Log::error('Erreur générale soumission candidature: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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
            'lettres_recommandation' => $this->lettres_recommandation,
            'certificats_competences' => $this->certificats_competences,
        ];

        foreach ($documents as $type => $file) {
            if ($file) {
                try {
                    $path = $file->store('documents', 'public');
                    
                    $candidature->documents()->create([
                        'type_document' => $type,
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin_fichier' => $path,
                        'taille_fichier' => $file->getSize(),
                        'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                    ]);
                    
                    Log::info("Document $type sauvegardé: " . $file->getClientOriginalName());
                } catch (\Exception $e) {
                    Log::error("Erreur sauvegarde document $type: " . $e->getMessage());
                    // Continue avec les autres documents même si un échoue
                }
            }
        }
    }

    public function closeInfoModal()
    {
        $this->showInfoModal = false;
    }

    public function openInfoModal()
    {
        $this->showInfoModal = true;
    }
    
    public function selectionnerOpportunite()
    {
        if ($this->opportunite_selectionnee) {
            $this->opportunite_id = $this->opportunite_selectionnee;
            $this->opportunite_titre = $this->getOpportuniteTitle($this->opportunite_id);
            $this->poste_souhaite = $this->mapOpportuniteToPoste($this->opportunite_id);
            $this->afficher_selection_opportunite = false;
            $this->erreur_opportunite = false;
            $this->validationErrors = [];
            session()->forget('validation_error');
            
            // Toast de succès
            $this->displayToast('Opportunité sélectionnée avec succès ! Vous pouvez maintenant continuer.', 'success');
        }
    }

    public function displayToast($message, $type = 'error')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
        $this->showToast = true;
        
        // Auto-hide après 5 secondes
        $this->dispatch('auto-hide-toast');
    }

    public function hideToast()
    {
        $this->showToast = false;
        $this->toastMessage = '';
    }

    public function resetForm()
    {
        $this->reset();
        $this->showSuccess = false;
        $this->currentStep = 1;
        $this->validationErrors = [];
        $this->showInfoModal = true;
        $this->mount();
    }

    public function render()
    {
        return view('livewire.candidature-form', [
            'etablissements' => \App\Models\ConfigurationListe::getOptions(\App\Models\ConfigurationListe::TYPE_ETABLISSEMENT),
            'niveaux_etude' => \App\Models\ConfigurationListe::getOptions(\App\Models\ConfigurationListe::TYPE_NIVEAU_ETUDE),
            'directions_disponibles' => \App\Models\ConfigurationListe::getOptions(\App\Models\ConfigurationListe::TYPE_DIRECTION),
            'postes_disponibles' => \App\Models\ConfigurationListe::getOptions(\App\Models\ConfigurationListe::TYPE_POSTE),
            'opportunites_disponibles' => $this->getOpportunitesDisponibles(),
        ])->layout('layouts.simple');
    }

    /**
     * Récupérer le titre de l'opportunité basé sur l'ID
     */
    private function getOpportuniteTitle($opportuniteId)
    {
        $opportunites = [
            'production' => 'Production & Qualité',
            'marketing' => 'Marketing & Commercial',
            'technique' => 'Technique & Maintenance',
            'rh' => 'Ressources Humaines',
            'finance' => 'Finance & Comptabilité',
            'it' => 'IT & Transformation Digitale',
        ];

        return $opportunites[$opportuniteId] ?? 'Opportunité générale';
    }

    /**
     * Mapper l'opportunité vers un poste suggéré
     */
    private function mapOpportuniteToPoste($opportuniteId)
    {
        $mapping = [
            'production' => 'Stagiaire Assistant(e) Production',
            'marketing' => 'Stagiaire Assistant(e) Marketing',
            'technique' => 'Stagiaire Assistant(e) Technique',
            'rh' => 'Stagiaire Assistant(e) RH',
            'finance' => 'Stagiaire Assistant(e) Financier(ère)',
            'it' => 'Stagiaire Développeur(euse)',
        ];

        return $mapping[$opportuniteId] ?? '';
    }

    /**
     * Obtenir toutes les opportunités disponibles
     */
    private function getOpportunitesDisponibles()
    {
        return \App\Models\Opportunite::getOpportuniteOptions();
    }
} 