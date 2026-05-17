<?php

namespace App\Livewire\Company;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\TenantJoinRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
#[\Livewire\Attributes\Title('Members')]
class UserManager extends Component
{
    public $showCreate = false;

    public $name = '';
    public $email = '';
    public $job_title = '';

    public function render()
    {
        $user = auth()->user();

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

        return view('livewire.company.user-manager', [
            'members' => $members,
            'requests' => $requests,
            'tenant' => $tenant,
        ]);
    }

    public function saveMember()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'job_title' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make('password'),
            'job_title' => $this->job_title,
            'tenant_id' => $user->tenant_id,
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->reset(['name', 'email', 'job_title', 'showCreate']);
        session()->flash('success', 'Member added successfully.');
    }

    public function toggleActive(User $member)
    {
        if ($member->id === auth()->id() || $member->role === User::ROLE_PIC) {
            return;
        }

        $member->update(['is_active' => !$member->is_active]);
        session()->flash('success', 'Member status updated.');
    }

    public function deleteMember(User $member)
    {
        if ($member->id === auth()->id() || $member->role === User::ROLE_PIC) {
            return;
        }

        // Unlink from tenant instead of deleting if they have data? 
        // For now, let's follow the simple approach: set tenant_id to null and role to member.
        $member->update([
            'tenant_id' => null,
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
        ]);

        session()->flash('success', 'Member removed from company.');
    }

    public function approveRequest(TenantJoinRequest $joinRequest)
    {
        $user = auth()->user();

        if ($joinRequest->tenant_id !== $user->tenant_id || $joinRequest->status !== TenantJoinRequest::STATUS_PENDING) {
            return;
        }

        $joinRequest->user()->update([
            'tenant_id' => $joinRequest->tenant_id,
            'role' => User::ROLE_MEMBER,
        ]);

        $joinRequest->update([
            'status' => TenantJoinRequest::STATUS_APPROVED,
            'approved_by' => $user->id,
            'responded_at' => now(),
        ]);

        session()->flash('success', 'User approved.');
    }

    public function rejectRequest(TenantJoinRequest $joinRequest)
    {
        $user = auth()->user();

        if ($joinRequest->tenant_id !== $user->tenant_id || $joinRequest->status !== TenantJoinRequest::STATUS_PENDING) {
            return;
        }

        $joinRequest->update([
            'status' => TenantJoinRequest::STATUS_REJECTED,
            'approved_by' => $user->id,
            'responded_at' => now(),
        ]);

        session()->flash('success', 'Request rejected.');
    }
}
