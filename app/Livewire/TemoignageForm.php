<?php

namespace App\Livewire;

use App\Models\Temoignage;
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

    public function mount()
    {
        $candidat = Auth::guard('candidat')->user();

        if ($candidat) {
            $this->nom = $candidat->nom ?? '';
            $this->prenom = $candidat->prenom ?? '';
            $this->etablissement_origine = $candidat->etablissement ?? '';

            // Pré-remplir la direction depuis la dernière candidature terminée
            $derniereCandidature = Candidature::where('email', $candidat->email)
                ->whereIn('statut', ['evaluation_terminee', 'attestation_generee', 'termine'])
                ->latest()
                ->first();

            if ($derniereCandidature) {
                $this->poste_occupe = $derniereCandidature->poste_souhaite ?? '';
                if (is_array($derniereCandidature->directions_souhaitees) && count($derniereCandidature->directions_souhaitees) > 0) {
                    $this->direction_stage = $derniereCandidature->directions_souhaitees[0];
                }
            }

            // Vérifier si le candidat a déjà soumis un témoignage
            $existing = Temoignage::where('nom', $candidat->nom)
                ->where('prenom', $candidat->prenom)
                ->first();

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
            // Upload photo si fournie
            $photoPath = null;
            if ($this->photo) {
                $ext = $this->photo->getClientOriginalExtension();
                $photoPath = $this->photo->storeAs(
                    'temoignages',
                    Str::uuid() . '.' . $ext,
                    'public'
                );
            }

            // Créer le témoignage avec actif=false et mis_en_avant=false
            Temoignage::create([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'poste_occupe' => $this->poste_occupe,
                'entreprise' => 'BRACONGO',
                'etablissement_origine' => $this->etablissement_origine ?: null,
                'direction_stage' => $this->direction_stage ?: null,
                'photo' => $photoPath,
                'temoignage' => $this->temoignage,
                'citation_courte' => $this->citation_courte ?: null,
                'note_experience' => $this->note_experience,
                'competences_acquises' => !empty($this->competences_acquises) ? $this->competences_acquises : null,
                'actif' => false,        // ⚠️ Désactivé par défaut - l'admin doit approuver
                'mis_en_avant' => false,  // ⚠️ Pas sur la homepage par défaut
            ]);

            $this->showSuccess = true;

            Log::info('Témoignage soumis par: ' . $this->prenom . ' ' . $this->nom);

        } catch (\Exception $e) {
            Log::error('Erreur soumission témoignage: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    public function render()
    {
        return view('livewire.temoignage-form')
            ->layout('layouts.modern', ['title' => 'Soumettre un témoignage - BRACONGO Stages']);
    }
}
