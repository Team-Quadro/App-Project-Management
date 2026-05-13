<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly SuperAdminDashboardService $dashboardService)
    {
    }

    public function index(): View
    {
        return view('superadmin.dashboard', $this->dashboardService->summary());
    }

    public function switchTenant(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id'
        ]);

        if ($request->tenant_id) {
            session(['superadmin_tenant_id' => $request->tenant_id]);
        } else {
            session()->forget('superadmin_tenant_id');
        }

        return redirect()->back();
    }
}
