<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Rediriger vers la page de connexion appropriée selon le guard
            if ($request->is('candidat/*')) {
                return route('candidat.login');
            }
            
            return route('login');
        }
        
        return null;
    }
}
