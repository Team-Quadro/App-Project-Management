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

        // 1. Cek apakah user punya tenant
        if (! $user->tenant_id) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is not linked to a tenant. Contact support.');
        }

        // 2. Cek apakah tenant-nya aktif
        $tenant = $user->tenant()->first();
        if (! $tenant || $tenant->status !== Tenant::STATUS_APPROVED) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your tenant is not active. Contact the holding administrator.');
        }

        // 3. Cek apakah akun user ini sudah di-approve Superadmin
        if ($user->approval_status !== 'approved') {
            auth()->logout();
            return redirect()->route('waiting.room')
                ->with('error', 'Your account is waiting for Superadmin approval.');
        }

        return $next($request);
    }
}