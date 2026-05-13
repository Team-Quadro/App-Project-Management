<?php

namespace App\Http\Controllers;

use App\Models\TenantJoinRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyUserController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        if (! $user->isCompanyAdmin()) {
            abort(403);
        }

        $tenant = $user->tenant;

        $members = $tenant?->users()->orderBy('name')->get() ?? collect();
        $requests = $tenant?->joinRequests()
            ->where('status', TenantJoinRequest::STATUS_PENDING)
            ->with('user')
            ->latest()
            ->get() ?? collect();

        return view('company.users.index', compact('members', 'requests', 'tenant'));
    }

    public function approve(TenantJoinRequest $joinRequest): RedirectResponse
    {
        $user = request()->user();

        if (! $user->isCompanyAdmin()) {
            abort(403);
        }

        if ($joinRequest->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        if ($joinRequest->status !== TenantJoinRequest::STATUS_PENDING) {
            return redirect()->route('company.users.index')
                ->with('error', 'Request already processed.');
        }

        $joinRequest->user()->update([
            'tenant_id' => $joinRequest->tenant_id,
            'role' => \App\Models\User::ROLE_MEMBER,
        ]);

        $joinRequest->update([
            'status' => TenantJoinRequest::STATUS_APPROVED,
            'approved_by' => $user->id,
            'responded_at' => now(),
        ]);

        return redirect()->route('company.users.index')
            ->with('success', 'User approved.');
    }

    public function reject(TenantJoinRequest $joinRequest): RedirectResponse
    {
        $user = request()->user();

        if (! $user->isCompanyAdmin()) {
            abort(403);
        }

        if ($joinRequest->tenant_id !== $user->tenant_id) {
            abort(403);
        }

        if ($joinRequest->status !== TenantJoinRequest::STATUS_PENDING) {
            return redirect()->route('company.users.index')
                ->with('error', 'Request already processed.');
        }

        $joinRequest->update([
            'status' => TenantJoinRequest::STATUS_REJECTED,
            'approved_by' => $user->id,
            'responded_at' => now(),
        ]);

        return redirect()->route('company.users.index')
            ->with('success', 'Request rejected.');
    }
}
