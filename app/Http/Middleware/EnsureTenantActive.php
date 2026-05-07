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

        // 1. Kalau belum login, atau dia Super Admin, silakan lewat.
        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        // 2. CEK RUANG TUNGGU: Belum punya tenant ATAU masih pending approval
        if (! $user->tenant_id || $user->approval_status === 'pending') {
            // Jangan di-logout! Arahkan ke Ruang Tunggu
            return redirect()->route('onboarding.index');
        }

        // 3. CEK STATUS PERUSAHAAN (TENANT)
        $tenant = $user->tenant()->first();

        if (! $tenant || $tenant->status !== Tenant::STATUS_APPROVED) {
            // Kalau perusahaannya diblokir/tidak aktif, kurung juga di onboarding
            return redirect()->route('onboarding.index')
                ->with('error', 'Perusahaan/Workspace Anda sedang tidak aktif. Hubungi Super Admin.');
        }

        // Kalau lolos semua syarat, silakan masuk ke Dashboard
        return $next($request);
    }
}
