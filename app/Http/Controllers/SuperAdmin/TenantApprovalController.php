<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTenantStatusRequest;
use App\Models\Tenant;
use App\Services\SuperAdminTenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantApprovalController extends Controller
{
    public function __construct(private readonly SuperAdminTenantService $tenantService)
    {
    }

    public function index(): View
    {
        $tenants = Tenant::latest()->paginate(15);

        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant): View
    {
        return view('superadmin.tenants.show', $this->tenantService->overview($tenant));
    }

    public function update(UpdateTenantStatusRequest $request, Tenant $tenant): RedirectResponse
    {
        $status = $request->validated('status');

        $tenant->update([
            'status' => $status,
            'approval_notes' => $request->validated('approval_notes'),
            'approved_at' => $status === Tenant::STATUS_APPROVED ? now() : null,
            'approved_by' => $status === Tenant::STATUS_APPROVED ? $request->user()->id : null,
        ]);

        if ($status === Tenant::STATUS_APPROVED && $tenant->requested_by) {
            $tenant->requester()?->update([
                'tenant_id' => $tenant->id,
                'role' => \App\Models\User::ROLE_PIC,
            ]);
        }

        return redirect()
            ->route('superadmin.tenants.show', $tenant)
            ->with('status', 'Tenant status updated.');
    }
}
