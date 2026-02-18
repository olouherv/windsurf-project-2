<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->university_id) {
            abort(403, 'Vous n\'êtes associé à aucune université.');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Votre compte est désactivé.');
        }

        if (!$user->university->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Cette université est désactivée.');
        }

        return $next($request);
    }
}
