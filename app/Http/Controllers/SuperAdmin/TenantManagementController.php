<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantManagementController extends Controller
{
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('superadmin.tenants.create', compact('users'));
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $pic = null;
        if (! empty($data['pic_user_id'])) {
            $pic = User::find($data['pic_user_id']);
        }

        $tenant = Tenant::create([
            'company_name' => $data['company_name'],
            'industry' => $data['industry'] ?? null,
            'pic_user_id' => $pic?->id,
            'pic_name' => $pic?->name,
            'pic_email' => $pic?->email,
            'status' => Tenant::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        if ($pic) {
            $pic->update([
                'tenant_id' => $tenant->id,
                'role' => User::ROLE_PIC,
                'is_active' => true,
            ]);
        }

        if (! empty($data['member_ids'])) {
            User::whereIn('id', $data['member_ids'])->update([
                'tenant_id' => $tenant->id,
                'role' => User::ROLE_MEMBER,
            ]);
        }

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Subsidiary created.');
    }

    public function edit(Tenant $tenant): View
    {
        $users = User::orderBy('name')->get();
        $memberIds = $tenant->users()->pluck('id')->toArray();

        return view('superadmin.tenants.edit', compact('tenant', 'users', 'memberIds'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        $tenant->update([
            'company_name' => $data['company_name'],
            'industry' => $data['industry'] ?? null,
        ]);

        if (! empty($data['pic_user_id'])) {
            $pic = User::find($data['pic_user_id']);

            if ($pic) {
                $tenant->update([
                    'pic_user_id' => $pic->id,
                    'pic_name' => $pic->name,
                    'pic_email' => $pic->email,
                ]);

                $pic->update([
                    'tenant_id' => $tenant->id,
                    'role' => User::ROLE_PIC,
                    'is_active' => true,
                ]);
            }
        }

        $memberIds = $data['member_ids'] ?? [];

        if (! empty($memberIds)) {
            User::whereIn('id', $memberIds)->update([
                'tenant_id' => $tenant->id,
                'role' => User::ROLE_MEMBER,
            ]);
        }

        return redirect()->route('superadmin.tenants.show', $tenant)
            ->with('success', 'Subsidiary updated.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('superadmin.tenants.index')
            ->with('success', 'Subsidiary deleted.');
    }
}
