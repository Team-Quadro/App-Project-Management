<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRegistrationRequest;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantRegistrationController extends Controller
{
    public function create(): View
    {
        return view('tenants.register');
    }

    public function store(StoreTenantRegistrationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->tenant_id) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Your account is already linked to a tenant.');
        }

        if (Tenant::where('pic_email', $user->email)->exists()) {
            return redirect()
                ->route('onboarding.index')
                ->with('error', 'You already have a company registration in progress.');
        }

        Tenant::create(array_merge($request->validated(), [
            'status' => Tenant::STATUS_PENDING,
            'holding_company_name' => config('app.name', 'Holding Company'),
            'pic_name' => $user->name,
            'pic_email' => $user->email,
            'requested_by' => $user->id,
        ]));

        return redirect()
            ->route('onboarding.index')
            ->with('status', 'Registration submitted. Our team will review your request.');
    }
}
