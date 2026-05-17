<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class EditProject extends Component
{
    use AuthorizesRequests;

    public Project $project;
    public $title = '';
    public $description = '';
    public $status = '';
    public $deadline = '';
    public $member_ids = [];

    public function mount(Project $project)
    {
        $this->authorize('update', $project);
        $this->project = $project;
        $this->title = $project->title;
        $this->description = $project->description ?? '';
        $this->status = $project->status;
        $this->deadline = $project->deadline?->format('Y-m-d') ?? '';
        $this->member_ids = $project->members->pluck('id')->toArray();
    }

    public function title()
    {
        return 'Edit: ' . $this->project->title;
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', Project::STATUSES),
            'deadline' => 'nullable|date',
            'member_ids' => 'array',
            'member_ids.*' => 'exists:users,id',
        ];
    }

    public function save(ProjectService $projectService)
    {
        $this->authorize('update', $this->project);
        $validated = $this->validate();
        $projectService->update($this->project, $validated);

        session()->flash('success', 'Proyek berhasil diperbarui.');
        return $this->redirect(route('projects.show', $this->project), navigate: true);
    }

    public function deleteProject(ProjectService $projectService)
    {
        $this->authorize('delete', $this->project);
        $projectService->delete($this->project);

        session()->flash('success', 'Proyek berhasil dihapus.');
        return $this->redirect(route('projects.index'), navigate: true);
    }

    public function render()
    {
        $tenantUsers = User::where('tenant_id', $this->project->tenant_id)
            ->where('id', '!=', $this->project->owner_id)
            ->orderBy('name')
            ->get();

        return view('livewire.project.edit-project', compact('tenantUsers'));
    }
}
