<?php

namespace App\Livewire\Company;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\CompanyRole;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
#[Title('Anggota')]
class UserManager extends Component
{
    public $showCreate = false;

    public $name = '';
    public $email = '';
    public $company_role_id = '';

    public function render()
    {
        $user = auth()->user();

        if (! $user->isCompanyAdmin()) {
            abort(403);
        }

        $tenant = $user->tenant;

        if ($tenant) {
            CompanyRole::ensureDefaultsForTenant($tenant->id);
        }

        // Hanya mengambil data member, logika $requests dihapus
        $members = $tenant?->users()->orderBy('name')->get() ?? collect();

        $companyRoles = $tenant
            ? CompanyRole::where('tenant_id', $tenant->id)->orderBy('name')->get()
            : collect();

        return view('livewire.company.user-manager', [
            'members' => $members,
            'tenant' => $tenant,
            'companyRoles' => $companyRoles,
        ]);
    }

    public function saveMember()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'company_role_id' => 'nullable|exists:company_roles,id',
        ]);

        $user = auth()->user();

        if ($this->company_role_id) {
            $role = CompanyRole::where('tenant_id', $user->tenant_id)
                ->where('id', $this->company_role_id)
                ->first();

            if (! $role) {
                session()->flash('error', 'Role tidak valid untuk perusahaan ini.');
                return;
            }
        }

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make('password'),
            'company_role_id' => $this->company_role_id ?: null,
            'tenant_id' => $user->tenant_id,
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
            'approval_status' => 'pending', // Status awal langsung pending untuk Superadmin
            'email_verified_at' => now(),
        ]);

        $this->reset(['name', 'email', 'company_role_id', 'showCreate']);
        session()->flash('success', 'Anggota berhasil ditambahkan dan menunggu persetujuan Superadmin.');
    }

    public function toggleActive(User $member)
    {
        if ($member->id === auth()->id() || $member->role === User::ROLE_PIC) {
            return;
        }

        $member->update(['is_active' => !$member->is_active]);
        session()->flash('success', 'Status anggota berhasil diperbarui.');
    }

    public function deleteMember(User $member)
    {
        if ($member->id === auth()->id() || $member->role === User::ROLE_PIC) {
            return;
        }

        $member->update([
            'tenant_id' => null,
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
        ]);

        session()->flash('success', 'Anggota berhasil dihapus dari perusahaan.');
    }
}