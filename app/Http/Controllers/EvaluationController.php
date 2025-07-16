<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class EvaluationController extends Controller
{
    /**
     * Afficher le formulaire d'évaluation
     */
    public function show(Candidature $candidature)
    {
        // Vérifier que la candidature est validée et terminée
        if ($candidature->statut->value !== 'valide') {
            return redirect()->route('candidature.suivi.code', $candidature->code_suivi)
                ->with('error', 'Cette candidature n\'est pas encore validée.');
        }

        // Vérifier que le stage est terminé
        if ($candidature->date_fin_stage && $candidature->date_fin_stage->isFuture()) {
            return redirect()->route('candidature.suivi.code', $candidature->code_suivi)
                ->with('error', 'Votre stage n\'est pas encore terminé. Vous pourrez évaluer votre expérience après la fin du stage.');
        }

        // Vérifier si une évaluation existe déjà
        if ($candidature->evaluation) {
            return redirect()->route('candidature.suivi.code', $candidature->code_suivi)
                ->with('info', 'Vous avez déjà évalué votre stage. Merci pour votre retour !');
        }

        return view('evaluation', compact('candidature'));
    }

    /**
     * Stocker l'évaluation
     */
    public function store(Request $request, Candidature $candidature)
    {
        // Vérifier que la candidature est validée
        if ($candidature->statut->value !== 'valide') {
            return redirect()->route('candidature.suivi.code', $candidature->code_suivi)
                ->with('error', 'Cette candidature n\'est pas encore validée.');
        }

        // Vérifier qu'aucune évaluation n'existe déjà
        if ($candidature->evaluation) {
            return redirect()->route('candidature.suivi.code', $candidature->code_suivi)
                ->with('error', 'Une évaluation existe déjà pour cette candidature.');
        }

        // Validation des données
        $validated = $request->validate([
            'satisfaction_generale' => 'required|integer|min:1|max:5',
            'recommandation' => 'required|in:oui,peut_etre,non',
            'accueil_integration' => 'required|in:excellent,bon,moyen,insuffisant',
            'encadrement_suivi' => 'required|in:excellent,bon,moyen,insuffisant',
            'conditions_travail' => 'required|in:excellent,bon,moyen,insuffisant',
            'ambiance_travail' => 'required|in:excellent,bon,moyen,insuffisant',
            'competences_developpees' => 'nullable|string|max:2000',
            'reponse_attentes' => 'nullable|string|max:2000',
            'aspects_enrichissants' => 'nullable|string|max:2000',
            'suggestions_amelioration' => 'nullable|string|max:2000',
            'contact_futur' => 'required|in:oui,non',
            'commentaire_libre' => 'nullable|string|max:2000',
        ]);

        try {
            // Créer l'évaluation
            $evaluation = $candidature->evaluation()->create($validated);

            Log::info('Évaluation créée avec succès', [
                'candidature_id' => $candidature->id,
                'code_suivi' => $candidature->code_suivi,
                'evaluation_id' => $evaluation->id,
                'note_moyenne' => $evaluation->note_moyenne,
            ]);

            return redirect()->route('candidature.suivi.code', $candidature->code_suivi)
                ->with('success', 'Merci pour votre évaluation ! Votre retour nous aide à améliorer l\'expérience des futurs stagiaires.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'évaluation', [
                'candidature_id' => $candidature->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de votre évaluation. Veuillez réessayer.');
        }
    }

    /**
     * Afficher l'évaluation (pour l'administration)
     */
    public function showAdmin(Evaluation $evaluation)
    {
        $evaluation->load('candidature');
        
        return view('admin.evaluation.show', compact('evaluation'));
    }

    /**
     * Obtenir les statistiques des évaluations
     */
    public function statistiques()
    {
        $evaluations = Evaluation::with('candidature')->get();
        
        if ($evaluations->isEmpty()) {
            return response()->json([
                'total' => 0,
                'note_moyenne_globale' => 0,
                'satisfaction_positive' => 0,
                'taux_satisfaction' => 0,
                'recommandations' => [
                    'oui' => 0,
                    'peut_etre' => 0,
                    'non' => 0,
                ],
                'environnement_travail' => [
                    'accueil_integration' => [],
                    'encadrement_suivi' => [],
                    'conditions_travail' => [],
                    'ambiance_travail' => [],
                ],
            ]);
        }

        $noteMoyenneGlobale = $evaluations->avg('note_moyenne');
        $satisfactionPositive = $evaluations->where('note_moyenne', '>=', 4.0)->count();
        $tauxSatisfaction = ($satisfactionPositive / $evaluations->count()) * 100;

        $recommandations = [
            'oui' => $evaluations->where('recommandation', 'oui')->count(),
            'peut_etre' => $evaluations->where('recommandation', 'peut_etre')->count(),
            'non' => $evaluations->where('recommandation', 'non')->count(),
        ];

        $environnementTravail = [
            'accueil_integration' => $evaluations->groupBy('accueil_integration')->map->count(),
            'encadrement_suivi' => $evaluations->groupBy('encadrement_suivi')->map->count(),
            'conditions_travail' => $evaluations->groupBy('conditions_travail')->map->count(),
            'ambiance_travail' => $evaluations->groupBy('ambiance_travail')->map->count(),
        ];

        return response()->json([
            'total' => $evaluations->count(),
            'note_moyenne_globale' => round($noteMoyenneGlobale, 1),
            'satisfaction_positive' => $satisfactionPositive,
            'taux_satisfaction' => round($tauxSatisfaction, 1),
            'recommandations' => $recommandations,
            'environnement_travail' => $environnementTravail,
        ]);
    }
} 