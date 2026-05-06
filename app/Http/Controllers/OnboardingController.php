<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        $user = request()->user();
        $pendingTenant = null;

        if ($user) {
            $pendingTenant = Tenant::where('requested_by', $user->id)
                ->latest()
                ->first();
        }

        return view('onboarding.index', compact('pendingTenant'));
    }
}
