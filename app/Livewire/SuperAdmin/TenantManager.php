<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Tenant;

#[Layout('layouts.app')]
#[Title('Kelola Anak Perusahaan')]
class TenantManager extends Component
{
    use WithPagination;

    public $search = '';

    // Create/Edit form
    public $showForm = false;
    public $editingTenantId = null;
    public $company_name = '';
    public $industry = '';
    public $pic_name = '';
    public $pic_email = '';
    public $estimated_users = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->reset(['editingTenantId', 'company_name', 'industry', 'pic_name', 'pic_email', 'estimated_users']);
        $this->showForm = true;
    }

    public function editTenant($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $this->editingTenantId = $tenant->id;
        $this->company_name = $tenant->company_name;
        $this->industry = $tenant->industry ?? '';
        $this->pic_name = $tenant->pic_name ?? '';
        $this->pic_email = $tenant->pic_email ?? '';
        $this->estimated_users = $tenant->estimated_users ?? '';
        $this->showForm = true;
    }

    public function saveTenant()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'pic_name' => 'required|string|max:255',
            'pic_email' => 'required|email|max:255',
            'estimated_users' => 'nullable|integer|min:0',
        ]);

        if ($this->editingTenantId) {
            $tenant = Tenant::findOrFail($this->editingTenantId);
            $tenant->update([
                'company_name' => $this->company_name,
                'industry' => $this->industry ?: null,
                'pic_name' => $this->pic_name,
                'pic_email' => $this->pic_email,
                'estimated_users' => $this->estimated_users ?: null,
            ]);
            session()->flash('success', 'Anak perusahaan berhasil diperbarui.');
        } else {
            Tenant::create([
                'company_name' => $this->company_name,
                'industry' => $this->industry ?: null,
                'pic_name' => $this->pic_name,
                'pic_email' => $this->pic_email,
                'estimated_users' => $this->estimated_users ?: null,
                'status' => 'approved',
                'approved_at' => now(),
            ]);
            session()->flash('success', 'Anak perusahaan berhasil dibuat.');
        }

        $this->reset(['showForm', 'editingTenantId', 'company_name', 'industry', 'pic_name', 'pic_email', 'estimated_users']);
    }

    public function updateStatus($tenantId, $status)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $tenant->update([
            'status' => $status,
            'approved_at' => $status === 'approved' ? now() : $tenant->approved_at,
        ]);
        session()->flash('success', 'Status berhasil diperbarui.');
    }

    public function deleteTenant($tenantId)
    {
        Tenant::findOrFail($tenantId)->delete();
        session()->flash('success', 'Anak perusahaan berhasil dihapus.');
    }

    public function render()
    {
        $tenants = Tenant::query()
            ->when($this->search, fn($q) => $q->where('company_name', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.superadmin.tenant-manager', compact('tenants'));
    }
}
