<?php

namespace App\Livewire\Company;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\CompanyRole;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Master Data')]
class MasterData extends Component
{
    public $stageName = '';
    public $stageSortOrder = '';
    public $stageIsActive = true;

    public $editingStageId = null;
    public $editingStageName = '';
    public $editingStageSortOrder = '';
    public $editingStageIsActive = true;

    public $roleName = '';
    public $editingRoleId = null;
    public $editingRoleName = '';

    public function render()
    {
        $user = auth()->user();

        if (! $user->isCompanyAdmin()) {
            abort(403);
        }

        $tenantId = $user->tenant_id;

        ProjectStage::ensureDefaultsForTenant($tenantId);
        CompanyRole::ensureDefaultsForTenant($tenantId);

        $projectStages = ProjectStage::where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->get();

        $companyRoles = CompanyRole::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('livewire.company.master-data', [
            'projectStages' => $projectStages,
            'companyRoles' => $companyRoles,
        ]);
    }

    public function addStage(): void
    {
        $tenantId = auth()->user()->tenant_id;

        $this->validate([
            'stageName' => 'required|string|max:255',
            'stageSortOrder' => 'nullable|integer|min:1',
            'stageIsActive' => 'boolean',
        ]);

        $key = $this->generateStageKey($this->stageName, $tenantId);
        $sortOrder = $this->stageSortOrder ?: (ProjectStage::where('tenant_id', $tenantId)->max('sort_order') + 1);

        ProjectStage::create([
            'tenant_id' => $tenantId,
            'name' => $this->stageName,
            'key' => $key,
            'sort_order' => $sortOrder,
            'is_active' => (bool) $this->stageIsActive,
            'is_default' => false,
        ]);

        $this->reset(['stageName', 'stageSortOrder', 'stageIsActive']);
        $this->stageIsActive = true;
        session()->flash('success', 'Stage proyek berhasil ditambahkan.');
    }

    public function startEditStage(ProjectStage $stage): void
    {
        $this->editingStageId = $stage->id;
        $this->editingStageName = $stage->name;
        $this->editingStageSortOrder = $stage->sort_order;
        $this->editingStageIsActive = $stage->is_active;
    }

    public function cancelEditStage(): void
    {
        $this->reset(['editingStageId', 'editingStageName', 'editingStageSortOrder', 'editingStageIsActive']);
        $this->editingStageIsActive = true;
    }

    public function updateStage(): void
    {
        $this->validate([
            'editingStageName' => 'required|string|max:255',
            'editingStageSortOrder' => 'nullable|integer|min:1',
            'editingStageIsActive' => 'boolean',
        ]);

        $stage = ProjectStage::find($this->editingStageId);

        if (! $stage) {
            session()->flash('error', 'Stage tidak ditemukan.');
            return;
        }

        $stage->update([
            'name' => $this->editingStageName,
            'sort_order' => $this->editingStageSortOrder ?: $stage->sort_order,
            'is_active' => (bool) $this->editingStageIsActive,
        ]);

        $this->cancelEditStage();
        session()->flash('success', 'Stage proyek berhasil diperbarui.');
    }

    public function toggleStage(ProjectStage $stage): void
    {
        $stage->update(['is_active' => ! $stage->is_active]);
        session()->flash('success', 'Status stage berhasil diperbarui.');
    }

    public function deleteStage(ProjectStage $stage): void
    {
        if ($stage->is_default) {
            session()->flash('error', 'Stage default tidak bisa dihapus.');
            return;
        }

        $hasProjects = Project::where('tenant_id', $stage->tenant_id)
            ->where('stage', $stage->key)
            ->exists();

        if ($hasProjects) {
            session()->flash('error', 'Stage tidak bisa dihapus karena masih digunakan oleh proyek.');
            return;
        }

        $stage->delete();
        session()->flash('success', 'Stage proyek berhasil dihapus.');
    }

    public function addRole(): void
    {
        $tenantId = auth()->user()->tenant_id;

        $this->validate([
            'roleName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('company_roles', 'name')->where('tenant_id', $tenantId),
            ],
        ]);

        CompanyRole::create([
            'tenant_id' => $tenantId,
            'name' => $this->roleName,
            'is_default' => false,
        ]);

        $this->reset('roleName');
        session()->flash('success', 'Role berhasil ditambahkan.');
    }

    public function startEditRole(CompanyRole $role): void
    {
        if ($role->is_default) {
            session()->flash('error', 'Role default tidak bisa diedit.');
            return;
        }

        $this->editingRoleId = $role->id;
        $this->editingRoleName = $role->name;
    }

    public function cancelEditRole(): void
    {
        $this->reset(['editingRoleId', 'editingRoleName']);
    }

    public function updateRole(): void
    {
        $role = CompanyRole::find($this->editingRoleId);

        $this->validate([
            'editingRoleName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('company_roles', 'name')->where('tenant_id', auth()->user()->tenant_id)->ignore($role?->id),
            ],
        ]);

        if (! $role) {
            session()->flash('error', 'Role tidak ditemukan.');
            return;
        }

        if ($role->is_default) {
            session()->flash('error', 'Role default tidak bisa diedit.');
            return;
        }

        $role->update(['name' => $this->editingRoleName]);
        $this->cancelEditRole();
        session()->flash('success', 'Role berhasil diperbarui.');
    }

    public function deleteRole(CompanyRole $role): void
    {
        if ($role->is_default) {
            session()->flash('error', 'Role default tidak bisa dihapus.');
            return;
        }

        $hasUsers = User::where('company_role_id', $role->id)->exists();

        if ($hasUsers) {
            session()->flash('error', 'Role tidak bisa dihapus karena masih digunakan oleh anggota.');
            return;
        }

        $role->delete();
        session()->flash('success', 'Role berhasil dihapus.');
    }

    private function generateStageKey(string $name, int $tenantId): string
    {
        $baseKey = Str::slug($name, '_');
        $key = $baseKey;
        $suffix = 1;

        while (ProjectStage::where('tenant_id', $tenantId)->where('key', $key)->exists()) {
            $suffix++;
            $key = $baseKey . '_' . $suffix;
        }

        return $key;
    }
}
