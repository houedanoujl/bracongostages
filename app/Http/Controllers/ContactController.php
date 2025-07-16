<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactMessage;

class ContactController extends Controller
{
    /**
     * Afficher la page de contact
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Traiter le formulaire de contact
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'newsletter' => 'nullable|boolean',
        ]);

        try {
            // Envoyer l'email
            Mail::to('stages@bracongo.cg')
                ->send(new ContactMessage($validated));

            // Log de l'action
            Log::info('Message de contact reçu', [
                'email' => $validated['email'],
                'sujet' => $validated['sujet'],
                'nom' => $validated['nom'] . ' ' . $validated['prenom'],
            ]);

            return back()->with('success', 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du message de contact', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer ou nous contacter directement.');
        }
    }
} 