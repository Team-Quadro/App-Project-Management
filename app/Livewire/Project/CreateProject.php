<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\ProjectStage;
use App\Services\ProjectService;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Proyek Baru')]
class CreateProject extends Component
{
    public $title = '';
    public $description = '';
    public $status = 'active';
    public $stage = 'approach_lead_client';
    public $deadline = '';
    public $member_ids = [];

    protected function rules()
    {
        $tenantId = auth()->user()->tenant_id;
        $stageKeys = ProjectStage::keysForTenant($tenantId);

        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', \App\Models\Project::STATUSES),
            'stage' => ['required', Rule::in($stageKeys)],
            'deadline' => 'nullable|date',
            'member_ids' => 'array',
            'member_ids.*' => 'exists:users,id',
        ];
    }

    public function save(ProjectService $projectService)
    {
        $validated = $this->validate();
        $project = $projectService->create($validated, auth()->user());

        session()->flash('success', 'Proyek berhasil dibuat.');
        return $this->redirect(route('projects.show', $project), navigate: true);
    }

    public function render()
    {
        $tenantUsers = User::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();

        $tenantId = auth()->user()->tenant_id;
        ProjectStage::ensureDefaultsForTenant($tenantId);
        $projectStages = ProjectStage::where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->get();

        if (! $this->stage && $projectStages->isNotEmpty()) {
            $this->stage = $projectStages->first()->key;
        }

        return view('livewire.project.create-project', compact('tenantUsers', 'projectStages'));
    }
}
