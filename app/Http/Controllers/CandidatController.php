<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Opportunite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class CandidatController extends Controller
{
    public function __construct()
    {
        // Les middlewares sont gérés au niveau des routes
    }

    /**
     * Afficher le formulaire de création de compte
     */
    public function create()
    {
        return view('candidats.register');
    }

    /**
     * Créer un nouveau compte candidat
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:candidats',
            'password' => ['required', 'confirmed', Password::defaults()],
            'telephone' => 'nullable|string|max:20',
            'etablissement' => 'nullable|string|max:255',
            'niveau_etude' => 'nullable|string|max:255',
            'faculte' => 'nullable|string|max:255',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Traitement du CV
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        // Traitement de la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // Création du candidat
        $candidat = Candidat::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'etablissement' => $request->etablissement,
            'niveau_etude' => $request->niveau_etude,
            'faculte' => $request->faculte,
            'cv_path' => $cvPath,
            'photo_path' => $photoPath,
            'is_active' => true,
        ]);

        // Connexion automatique
        Auth::guard('candidat')->login($candidat);

        return redirect()->route('candidat.dashboard')
            ->with('success', 'Compte créé avec succès ! Bienvenue sur BRACONGO Stages.');
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function login()
    {
        return view('candidats.login');
    }

    /**
     * Authentifier le candidat
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('candidat')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Mettre à jour la dernière connexion
            Auth::guard('candidat')->user()->updateLastLogin();

            return redirect()->intended(route('candidat.dashboard'))
                ->with('success', 'Connexion réussie !');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('candidat')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Tableau de bord du candidat
     */
    public function dashboard()
    {
        $candidat = Auth::guard('candidat')->user();
        $candidatures = $candidat->candidatures()->with('opportunite')->latest()->get();
        $opportunites = Opportunite::where('actif', true)->latest()->take(5)->get();

        return view('candidats.dashboard', compact('candidat', 'candidatures', 'opportunites'));
    }

    /**
     * Profil du candidat
     */
    public function profile()
    {
        $candidat = Auth::guard('candidat')->user();
        return view('candidats.profile', compact('candidat'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $candidat = Auth::guard('candidat')->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'etablissement' => 'nullable|string|max:255',
            'niveau_etude' => 'nullable|string|max:255',
            'faculte' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mise à jour des informations de base
        $candidat->update($request->only([
            'nom', 'prenom', 'telephone', 'etablissement', 'niveau_etude', 'faculte'
        ]));

        // Mise à jour du CV
        if ($request->hasFile('cv')) {
            // Supprimer l'ancien CV
            if ($candidat->cv_path) {
                Storage::disk('public')->delete($candidat->cv_path);
            }
            
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $candidat->update(['cv_path' => $cvPath]);
        }

        // Mise à jour de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($candidat->photo_path) {
                Storage::disk('public')->delete($candidat->photo_path);
            }
            
            $photoPath = $request->file('photo')->store('photos', 'public');
            $candidat->update(['photo_path' => $photoPath]);
        }

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password:candidat',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $candidat = Auth::guard('candidat')->user();
        $candidat->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe modifié avec succès !');
    }

    /**
     * Mes candidatures
     */
    public function candidatures()
    {
        $candidat = Auth::guard('candidat')->user();
        $candidatures = $candidat->candidatures()
            ->with(['opportunite', 'evaluation'])
            ->latest()
            ->paginate(10);

        return view('candidats.candidatures', compact('candidat', 'candidatures'));
    }

    /**
     * Détails d'une candidature
     */
    public function candidature($id)
    {
        $candidat = Auth::guard('candidat')->user();
        $candidature = $candidat->candidatures()
            ->with(['opportunite', 'evaluation', 'documents'])
            ->findOrFail($id);

        return view('candidats.candidature-details', compact('candidat', 'candidature'));
    }

    /**
     * Télécharger le CV
     */
    public function downloadCv()
    {
        $candidat = Auth::guard('candidat')->user();
        
        if (!$candidat->cv_path) {
            return back()->with('error', 'Aucun CV disponible.');
        }

        return Storage::disk('public')->download($candidat->cv_path);
    }

    /**
     * Télécharger un document de candidature
     */
    public function downloadDocument($documentId)
    {
        $candidat = Auth::guard('candidat')->user();
        
        // Vérifier que le document appartient bien à une candidature du candidat connecté
        $document = \App\Models\Document::whereHas('candidature', function ($query) use ($candidat) {
            $query->where('email', $candidat->email);
        })->findOrFail($documentId);
        
        if (!$document->fichierExiste()) {
            return back()->with('error', 'Fichier non trouvé sur le serveur.');
        }
        
        try {
            return Storage::disk('public')->download($document->chemin_fichier, $document->nom_original);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du téléchargement du fichier.');
        }
    }

    /**
     * Mettre à jour les documents du candidat
     */
    public function updateDocuments(Request $request)
    {
        $candidat = Auth::guard('candidat')->user();

        // Validation des documents uploadés
        $rules = [];
        foreach (array_keys(\App\Models\DocumentCandidat::getTypesDocument()) as $type) {
            $rules["documents.{$type}"] = 'nullable|file|mimes:pdf,doc,docx|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $documentsUploades = 0;

        // Traitement des documents uploadés
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $type => $file) {
                if ($file && $file->isValid()) {
                    // Supprimer l'ancien document s'il existe
                    $ancienDocument = $candidat->getDocumentByType($type);
                    if ($ancienDocument) {
                        $ancienDocument->delete(); // Le hook supprimera automatiquement le fichier
                    }

                    // Stocker le nouveau document
                    $path = $file->store('documents_candidat', 'public');

                    // Créer l'enregistrement en base
                    \App\Models\DocumentCandidat::create([
                        'candidat_id' => $candidat->id,
                        'type_document' => $type,
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin_fichier' => $path,
                        'taille_fichier' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);

                    $documentsUploades++;
                }
            }
        }

        if ($documentsUploades > 0) {
            return back()->with('success', "Documents mis à jour avec succès ! {$documentsUploades} document(s) uploadé(s).");
        }

        return back()->with('error', 'Aucun document à mettre à jour.');
    }

    /**
     * Afficher le formulaire de demande de réinitialisation de mot de passe
     */
    public function showForgotPasswordForm()
    {
        return view('candidats.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation de mot de passe
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:candidats,email',
        ], [
            'email.exists' => 'Aucun compte candidat trouvé avec cette adresse email.'
        ]);

        // Générer un token de réinitialisation
        $token = Str::random(64);
        
        // Sauvegarder le token en base (utilise la table password_reset_tokens)
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Envoyer l'email avec le lien de réinitialisation
        $candidat = Candidat::where('email', $request->email)->first();
        $resetUrl = route('candidat.password.reset', ['token' => $token, 'email' => $request->email]);
        
        try {
            $contenuHtml = view('emails.candidat-password-reset', [
                'candidat' => $candidat,
                'resetUrl' => $resetUrl,
                'token' => $token
            ])->render();

            NotificationFacade::route('mail', $candidat->email)
                ->notify(new ResetPasswordNotification(
                    'Réinitialisation de votre mot de passe - BRACONGO Stages',
                    $contenuHtml
                ));

            return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email reset password: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
        }
    }

    /**
     * Afficher le formulaire de réinitialisation de mot de passe
     */
    public function showResetPasswordForm(Request $request)
    {
        $token = $request->route('token');
        $email = $request->get('email');

        // Vérifier que le token existe et n'est pas expiré (24h)
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', now()->subDay())
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return redirect()->route('candidat.login')
                ->with('error', 'Ce lien de réinitialisation est invalide ou expiré.');
        }

        return view('candidats.reset-password', compact('token', 'email'));
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:candidats,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Vérifier le token
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>', now()->subDay())
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->with('error', 'Token invalide ou expiré.');
        }

        // Mettre à jour le mot de passe
        $candidat = Candidat::where('email', $request->email)->first();
        $candidat->update([
            'password' => Hash::make($request->password)
        ]);

        // Supprimer le token utilisé
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('candidat.login')
            ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
