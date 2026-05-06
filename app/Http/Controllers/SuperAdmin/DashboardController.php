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
}
