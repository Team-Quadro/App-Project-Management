<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Project;

#[Layout('layouts.app')]
#[\Livewire\Attributes\Title('Projects')]
class ProjectList extends Component
{
    use WithPagination;

    public $search = '';
    public $stage = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStage()
    {
        $this->resetPage();
    }

    public function switchTenant($tenantId)
    {
        if ($tenantId) {
            session(['superadmin_tenant_id' => $tenantId]);
        } else {
            session()->forget('superadmin_tenant_id');
        }
        
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $projectsQuery = Project::accessibleBy($user)
            ->search($this->search)
            ->filterStage($this->stage)
            ->with(['owner', 'tasks' => function ($query) {
                // Needed to calculate done tasks in the view easily
                $query->select('id', 'project_id', 'status');
            }])
            ->withCount('tasks')
            ->latest();

        $projects = $projectsQuery->paginate(10);

        return view('livewire.project.project-list', [
            'projects' => $projects,
        ]);
    }
}
