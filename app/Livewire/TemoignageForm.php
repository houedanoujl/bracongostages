<?php

namespace App\Livewire;

use App\Models\Evaluation;
use App\Models\Candidature;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TemoignageForm extends Component
{
    use WithFileUploads;

    public string $nom = '';
    public string $prenom = '';
    public string $poste_occupe = '';
    public string $etablissement_origine = '';
    public string $direction_stage = '';
    public string $temoignage = '';
    public string $citation_courte = '';
    public int $note_experience = 5;
    public $photo;
    public array $competences_acquises = [];
    public string $nouvelle_competence = '';

    public bool $showSuccess = false;
    public bool $hasExistingTemoignage = false;
    public bool $stageNonTermine = false;

    private ?Candidature $derniereCandidature = null;

    /**
     * Statuts de candidature qui correspondent à un stage terminé
     */
    private const STATUTS_STAGE_TERMINE = [
        'stage_en_cours',
        'en_evaluation',
        'evaluation_terminee',
        'attestation_generee',
        'remboursement_en_cours',
        'termine',
    ];

    public function mount()
    {
        $candidat = Auth::guard('candidat')->user();

        if ($candidat) {
            $this->nom = $candidat->nom ?? '';
            $this->prenom = $candidat->prenom ?? '';
            $this->etablissement_origine = $candidat->etablissement ?? '';

            // Vérifier si le candidat a une candidature avec un stage terminé
            $this->derniereCandidature = Candidature::where('email', $candidat->email)
                ->whereIn('statut', self::STATUTS_STAGE_TERMINE)
                ->latest()
                ->first();

            if (!$this->derniereCandidature) {
                // Aucun stage terminé : bloquer l'accès au formulaire
                $this->stageNonTermine = true;
                return;
            }

            // Pré-remplir depuis la candidature terminée
            $this->poste_occupe = $this->derniereCandidature->poste_souhaite ?? '';
            if (is_array($this->derniereCandidature->directions_souhaitees) && count($this->derniereCandidature->directions_souhaitees) > 0) {
                $this->direction_stage = $this->derniereCandidature->directions_souhaitees[0];
            }

            // Vérifier si un retour d'expérience avec témoignage existe déjà pour cette candidature
            $existing = Evaluation::where('candidature_id', $this->derniereCandidature->id)
                ->whereNotNull('temoignage_texte')
                ->exists();

            if ($existing) {
                $this->hasExistingTemoignage = true;
            }
        }
    }

    public function ajouterCompetence()
    {
        $competence = trim($this->nouvelle_competence);
        if ($competence && !in_array($competence, $this->competences_acquises)) {
            $this->competences_acquises[] = $competence;
            $this->nouvelle_competence = '';
        }
    }

    public function retirerCompetence(int $index)
    {
        if (isset($this->competences_acquises[$index])) {
            unset($this->competences_acquises[$index]);
            $this->competences_acquises = array_values($this->competences_acquises);
        }
    }

    public function submit()
    {
        // Double vérification côté serveur
        if ($this->stageNonTermine) {
            session()->flash('error', 'Vous ne pouvez soumettre un retour d\'expérience que lorsque votre stage est terminé.');
            return;
        }

        $this->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'poste_occupe' => 'required|string|max:255',
            'etablissement_origine' => 'nullable|string|max:255',
            'direction_stage' => 'nullable|string|max:255',
            'temoignage' => 'required|string|min:20|max:2000',
            'citation_courte' => 'nullable|string|max:300',
            'note_experience' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        try {
            $candidat = Auth::guard('candidat')->user();
            $candidature = Candidature::where('email', $candidat->email)
                ->whereIn('statut', self::STATUTS_STAGE_TERMINE)
                ->latest()
                ->first();

            if (!$candidature) {
                session()->flash('error', 'Aucune candidature avec stage terminé trouvée.');
                return;
            }

            // Upload photo si fournie
            $photoPath = null;
            if ($this->photo) {
                $ext = $this->photo->getClientOriginalExtension();
                $photoPath = $this->photo->storeAs(
                    'retours',
                    Str::uuid() . '.' . $ext,
                    'public'
                );
            }

            // Trouver ou créer une évaluation liée à la candidature
            $evaluation = Evaluation::firstOrNew(['candidature_id' => $candidature->id]);

            // Mettre à jour les champs témoignage
            $evaluation->temoignage_texte = $this->temoignage;
            $evaluation->citation_accueil = $this->citation_courte ?: null;
            $evaluation->note_experience = $this->note_experience;
            $evaluation->competences_tags = !empty($this->competences_acquises) ? $this->competences_acquises : null;
            $evaluation->afficher_en_accueil = false; // L'admin décide

            if ($photoPath) {
                $evaluation->photo = $photoPath;
            }

            $evaluation->save();

            $this->showSuccess = true;

            Log::info('Retour d\'expérience soumis par: ' . $this->prenom . ' ' . $this->nom);

        } catch (\Exception $e) {
            Log::error('Erreur soumission retour d\'expérience: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function render()
    {
        return view('livewire.temoignage-form')
            ->layout('layouts.modern', ['title' => 'Retour d\'expérience - BRACONGO Stages']);
    }
}
