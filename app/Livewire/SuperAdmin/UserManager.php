<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
#[Title('Kelola Pengguna')]
class UserManager extends Component
{
    use WithPagination;

    public $search = '';

    // Create form
    public $showCreate = false;
    public $showEdit = false;
    public $createName = '';
    public $createEmail = '';
    public $createPassword = '';
    public $createRole = 'member';
    public $createTenantId = '';
    public $createIsActive = true;

    // Inline edit
    public $editingUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';
    public $editTenantId = '';
    public $editIsActive = true;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createUser()
    {
        $this->validate([
            'createName' => 'required|string|max:255',
            'createEmail' => 'required|email|unique:users,email',
            'createPassword' => 'required|string|min:6',
            'createRole' => 'required|in:superadmin,pic,member',
            'createTenantId' => 'nullable|exists:tenants,id',
        ]);

        User::create([
            'name' => $this->createName,
            'email' => $this->createEmail,
            'password' => Hash::make($this->createPassword),
            'role' => $this->createRole,
            'tenant_id' => $this->createTenantId ?: null,
            'is_active' => $this->createIsActive,
        ]);

        $this->reset(['showCreate', 'createName', 'createEmail', 'createPassword', 'createRole', 'createTenantId']);
        $this->createIsActive = true;
        session()->flash('success', 'Pengguna berhasil dibuat.');
    }

    public function startEdit($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->role;
        $this->editTenantId = $user->tenant_id ?? '';
        $this->editIsActive = $user->is_active;
        $this->showEdit = true;
    }

    public function saveEdit()
    {
        $user = User::findOrFail($this->editingUserId);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $user->id,
            'editRole' => 'required|in:superadmin,pic,member',
            'editTenantId' => 'nullable|exists:tenants,id',
        ]);

        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'role' => $this->editRole,
            'tenant_id' => $this->editTenantId ?: null,
            'is_active' => $this->editIsActive,
        ]);

        $this->editingUserId = null;
        $this->showEdit = false;
        session()->flash('success', 'Pengguna berhasil diperbarui.');
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
        $this->showEdit = false;
    }

    public function deleteUser($userId)
    {
        User::findOrFail($userId)->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
    }

    public function approveUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'approval_status' => 'approved',
            'is_active' => true,
        ]);

        session()->flash('success', "Akun {$user->name} berhasil disetujui.");
    }

    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Tergantung business logic-mu, ditolak bisa berarti dihapus atau di-set reject
        $user->update([
            'approval_status' => 'rejected',
            'is_active' => false,
        ]);

        session()->flash('success', "Akun {$user->name} telah ditolak.");
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->with('tenant')
            ->orderBy('name')
            ->paginate(20);

        $tenants = Tenant::orderBy('company_name')->get();

        return view('livewire.superadmin.user-manager', compact('users', 'tenants'));
    }
}
