<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\SuperAdminDashboardService;

#[Layout('layouts.app')]
#[Title('Dashboard Holding')]
class Dashboard extends Component
{
    public function switchTenant($tenantId)
    {
        if ($tenantId) {
            session(['superadmin_tenant_id' => $tenantId]);
        } else {
            session()->forget('superadmin_tenant_id');
        }
    }

    public function render()
    {
        $data = app(SuperAdminDashboardService::class)->summary();
        return view('livewire.superadmin.dashboard', $data);
    }
}
