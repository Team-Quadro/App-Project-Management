<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->tenant_id) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your account is not linked to a tenant. Contact support.');
        }

        $tenant = $user->tenant()->first();

        if (! $tenant || $tenant->status !== Tenant::STATUS_APPROVED) {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your tenant is not active. Contact the holding administrator.');
        }

        return $next($request);
    }
}
