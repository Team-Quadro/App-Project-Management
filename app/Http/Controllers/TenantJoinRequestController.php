<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantJoinRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantJoinRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
        ]);

        if (! $user->isActive()) {
            return redirect()->route('onboarding.index')
                ->with('error', 'Your account is not active yet.');
        }

        if ($user->tenant_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account is already assigned to a subsidiary.');
        }

        $tenant = Tenant::where('id', $request->integer('tenant_id'))
            ->where('status', Tenant::STATUS_APPROVED)
            ->first();

        if (! $tenant) {
            return redirect()->route('onboarding.index')
                ->with('error', 'Selected subsidiary is not available.');
        }

        $existing = TenantJoinRequest::where('user_id', $user->id)
            ->where('status', TenantJoinRequest::STATUS_PENDING)
            ->first();

        if ($existing) {
            return redirect()->route('onboarding.index')
                ->with('error', 'You already have a pending join request.');
        }

        TenantJoinRequest::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'status' => TenantJoinRequest::STATUS_PENDING,
        ]);

        return redirect()->route('onboarding.index')
            ->with('status', 'Join request submitted.');
    }
}
