<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantJoinRequest;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::orderBy('company_name')->get();

        $pendingRequest = TenantJoinRequest::withoutGlobalScopes()
            ->where('user_id', auth()->id())
            ->where('status', TenantJoinRequest::STATUS_PENDING)
            ->with('tenant')
            ->first();

        return view('onboarding.index', compact('tenants', 'pendingRequest'));
    }
}
