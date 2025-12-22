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

    public $nom = '';
    public $prenom = '';
    public $email = '';
    public $telephone = '';
    public $etablissement = '';
    public $niveau_etude = '';
    public $faculte = '';
    public $objectif_stage = '';
    public $poste_souhaite = '';
    public $directions_souhaitees = [];
    public $periode_debut_souhaitee = '';
    public $periode_fin_souhaitee = '';
    public $cv;
    public $lettre_motivation;
    public $certificat_scolarite;
    public $releves_notes;
    public $lettres_recommandation;
    public $certificats_competences;

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
    
    // Gestion des documents existants
    public $utiliser_documents_existants = true;
    public $documents_existants_disponibles = false;
    public $cv_existant = null;
    public $lettre_motivation_existante = null;
    public $certificat_scolarite_existant = null;
    public $releves_notes_existants = null;
    public $lettres_recommandation_existantes = null;
    public $certificats_competences_existants = null;
    public $remplacer_cv = false;
    public $remplacer_lettre = false;
    public $remplacer_certificat_scolarite = false;
    public $remplacer_releves_notes = false;
    public $remplacer_lettres_recommandation = false;
    public $remplacer_certificats_competences = false;

    public function mount()
    {
        $this->periode_debut_souhaitee = now()->addMonth()->format('Y-m-d');
        $this->periode_fin_souhaitee = now()->addMonths(4)->format('Y-m-d');
        
        // Pré-remplir avec les données du candidat connecté
        $candidat = auth('candidat')->user();
        if ($candidat) {
            $this->nom = $candidat->nom;
            $this->prenom = $candidat->prenom;
            $this->email = $candidat->email;
            $this->telephone = $candidat->telephone ?? '';
            $this->etablissement = $candidat->etablissement ?? '';
            $this->niveau_etude = $candidat->niveau_etude ?? '';
            $this->faculte = $candidat->faculte ?? '';
            
            // Vérifier tous les documents existants
            $documentsTypes = [
                'cv' => 'cv_existant',
                'lettre_motivation' => 'lettre_motivation_existante',
                'certificat_scolarite' => 'certificat_scolarite_existant',
                'releves_notes' => 'releves_notes_existants',
                'lettres_recommandation' => 'lettres_recommandation_existantes',
                'certificats_competences' => 'certificats_competences_existants',
            ];
            
            foreach ($documentsTypes as $type => $property) {
                $document = $candidat->getDocumentByType($type);
                if ($document && $document->fichierExiste()) {
                    $this->$property = $document->chemin_fichier;
                    $this->documents_existants_disponibles = true;
                }
            }
        }
        
        // Récupérer les paramètres d'URL si on vient d'une opportunité
        if (request()->has('domain')) {
            $this->opportunite_id = request()->get('domain');
            $this->opportunite_titre = $this->getOpportuniteTitle($this->opportunite_id);
            $this->opportunite_selectionnee = $this->opportunite_id;
            $this->afficher_selection_opportunite = false;
            
            // Pré-remplir les directions souhaitées avec celles de l'opportunité
            $this->preRemplirDirections($this->opportunite_id);
        } else {
            // Si pas d'opportunité sélectionnée, afficher la sélection
            $this->afficher_selection_opportunite = true;
        }
    }
    
    /**
     * Pré-remplit les directions souhaitées avec les directions associées de l'opportunité
     */
    protected function preRemplirDirections($opportuniteSlug)
    {
        $opportunite = \App\Models\Opportunite::where('slug', $opportuniteSlug)->first();
        
        if ($opportunite && !empty($opportunite->directions_associees)) {
            $this->directions_souhaitees = $opportunite->directions_associees;
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
                $candidat = auth('candidat')->user();
                $rules = [];
                
                // Si on utilise les documents existants et qu'ils sont disponibles
                if ($this->utiliser_documents_existants && $this->documents_existants_disponibles) {
                    // CV : obligatoire seulement si pas de CV existant
                    if (!$this->cv_existant) {
                        $rules['cv'] = 'required|file|mimes:pdf,doc,docx|max:5120';
                    } elseif ($this->cv) {
                        $rules['cv'] = 'file|mimes:pdf,doc,docx|max:5120';
                    }
                    
                    // Lettre de motivation : obligatoire seulement si pas de lettre existante (max 2MB)
                    if (!$this->lettre_motivation_existante) {
                        $rules['lettre_motivation'] = 'required|file|mimes:pdf,doc,docx|max:2048';
                    } elseif ($this->lettre_motivation) {
                        $rules['lettre_motivation'] = 'file|mimes:pdf,doc,docx|max:2048';
                    }
                    
                    // Certificat de scolarité : OBLIGATOIRE - seulement si pas existant dans le profil
                    if (!$this->certificat_scolarite_existant) {
                        $rules['certificat_scolarite'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
                    } elseif ($this->certificat_scolarite) {
                        $rules['certificat_scolarite'] = 'file|mimes:pdf,jpg,jpeg,png|max:5120';
                    }
                } else {
                    // Mode upload de nouveaux documents
                    // CV : requis seulement si pas de CV dans le profil candidat
                    if ($candidat && $candidat->getDocumentByType('cv')) {
                        $rules['cv'] = 'nullable|file|mimes:pdf,doc,docx|max:5120';
                    } else {
                        $rules['cv'] = 'required|file|mimes:pdf,doc,docx|max:5120';
                    }
                    
                    // Lettre de motivation : requis seulement si pas de lettre dans le profil candidat (max 2MB)
                    if ($candidat && $candidat->getDocumentByType('lettre_motivation')) {
                        $rules['lettre_motivation'] = 'nullable|file|mimes:pdf,doc,docx|max:2048';
                    } else {
                        $rules['lettre_motivation'] = 'required|file|mimes:pdf,doc,docx|max:2048';
                    }
                    
                    // Certificat de scolarité : TOUJOURS OBLIGATOIRE sauf si existe dans le profil
                    if ($candidat && $candidat->getDocumentByType('certificat_scolarite')) {
                        $rules['certificat_scolarite'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
                    } else {
                        $rules['certificat_scolarite'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
                    }
                }
                
                // Documents optionnels
                $rules['releves_notes'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
                $rules['lettres_recommandation'] = 'nullable|file|mimes:pdf,doc,docx|max:5120';
                $rules['certificats_competences'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
                
                // Messages personnalisés
                $messages = [
                    'cv.required' => 'Le CV est obligatoire.',
                    'cv.max' => 'Le CV ne doit pas dépasser 5 MB.',
                    'cv.mimes' => 'Le CV doit être au format PDF, DOC ou DOCX.',
                    'lettre_motivation.required' => 'La lettre de motivation est obligatoire.',
                    'lettre_motivation.max' => 'La lettre de motivation ne doit pas dépasser 2 MB.',
                    'lettre_motivation.mimes' => 'La lettre de motivation doit être au format PDF, DOC ou DOCX.',
                    'certificat_scolarite.required' => 'Le certificat de scolarité est obligatoire.',
                    'certificat_scolarite.max' => 'Le certificat de scolarité ne doit pas dépasser 5 MB.',
                    'certificat_scolarite.mimes' => 'Le certificat de scolarité doit être au format PDF, JPG ou PNG.',
                    'releves_notes.max' => 'Les relevés de notes ne doivent pas dépasser 5 MB.',
                    'lettres_recommandation.max' => 'Les lettres de recommandation ne doivent pas dépasser 5 MB.',
                    'certificats_competences.max' => 'Les certificats de compétences ne doivent pas dépasser 5 MB.',
                ];
                
                $this->validate($rules, $messages);
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
                'directions_souhaitees' => 'required|array|min:1',
                'periode_debut_souhaitee' => 'required|date',
                'periode_fin_souhaitee' => 'required|date|after:periode_debut_souhaitee',
            ];
            
            // Si "Autres" est sélectionné, valider le champ établissement_autre
            if ($this->etablissement === 'Autres') {
                $validationRules['etablissement_autre'] = 'required|string|max:255';
            }
            
            // Vérifier les documents du candidat pour pré-remplir
            $candidat = auth('candidat')->user();
            $documentsCandidat = $candidat ? $candidat->documentsCandidat : collect();
            
            // Validation des fichiers seulement s'ils sont présents ou absents du profil
            if ($this->cv) {
                $validationRules['cv'] = 'file|mimes:pdf,doc,docx|max:2048';
            } elseif (!$candidat->getDocumentByType('cv')) {
                $validationRules['cv'] = 'required';
            }
            
            if ($this->lettre_motivation) {
                $validationRules['lettre_motivation'] = 'file|mimes:pdf,doc,docx|max:2048';
            } elseif (!$candidat->getDocumentByType('lettre_motivation')) {
                $validationRules['lettre_motivation'] = 'required';
            }
            
            if ($this->certificat_scolarite) {
                $validationRules['certificat_scolarite'] = 'file|mimes:pdf,jpg,jpeg,png|max:2048';
            } elseif (!$candidat->getDocumentByType('certificat_scolarite')) {
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

        $candidat = auth('candidat')->user();
        
        foreach ($documents as $type => $file) {
            if ($file) {
                // Document uploadé dans ce formulaire
                try {
                    $path = $file->store('documents', 'public');
                    
                    $candidature->documents()->create([
                        'type_document' => $type,
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin_fichier' => $path,
                        'taille_fichier' => $file->getSize(),
                        'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
                    ]);
                    
                    Log::info("Document $type uploadé et sauvegardé: " . $file->getClientOriginalName());
                } catch (\Exception $e) {
                    Log::error("Erreur sauvegarde document $type: " . $e->getMessage());
                }
            } elseif ($candidat) {
                // Utiliser le document du profil candidat si disponible
                $documentCandidat = $candidat->getDocumentByType($type);
                if ($documentCandidat && $documentCandidat->fichierExiste()) {
                    try {
                        // Copier le document du profil vers le dossier de candidature
                        // Utiliser getCheminReel() pour obtenir le chemin correct
                        $originalPath = $documentCandidat->getCheminReel() ?? $documentCandidat->chemin_fichier;
                        $newPath = 'documents/' . uniqid() . '_' . basename($originalPath);
                        
                        if (Storage::disk('public')->copy($originalPath, $newPath)) {
                            $candidature->documents()->create([
                                'type_document' => $type,
                                'nom_original' => $documentCandidat->nom_original,
                                'chemin_fichier' => $newPath,
                                'taille_fichier' => $documentCandidat->taille_fichier,
                                'mime_type' => $documentCandidat->mime_type,
                            ]);
                            
                            Log::info("Document $type copié depuis le profil: " . $documentCandidat->nom_original);
                        } else {
                            Log::warning("Impossible de copier le document $type depuis $originalPath vers $newPath");
                        }
                    } catch (\Exception $e) {
                        Log::error("Erreur copie document profil $type: " . $e->getMessage());
                    }
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
            $this->afficher_selection_opportunite = false;
            $this->erreur_opportunite = false;
            $this->validationErrors = [];
            session()->forget('validation_error');
            
            // Pré-remplir les directions souhaitées avec celles de l'opportunité
            $this->preRemplirDirections($this->opportunite_id);
            
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

    public function toggleDocumentsMode()
    {
        $this->utiliser_documents_existants = !$this->utiliser_documents_existants;
        
        // Si on bascule vers les documents existants, effacer les nouveaux uploads
        if ($this->utiliser_documents_existants) {
            $this->cv = null;
            $this->lettre_motivation = null;
        }
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
     * Obtenir toutes les opportunités disponibles
     */
    private function getOpportunitesDisponibles()
    {
        return \App\Models\Opportunite::getOpportuniteOptions();
    }
} 