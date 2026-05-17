<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->isActive()) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your account is not active yet. Please contact the administrator.');
        }

        return $next($request);
    }
}
