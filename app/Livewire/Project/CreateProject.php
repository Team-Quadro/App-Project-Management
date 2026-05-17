<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Services\ProjectService;

#[Layout('layouts.app')]
#[Title('Proyek Baru')]
class CreateProject extends Component
{
    public $title = '';
    public $description = '';
    public $status = 'active';
    public $deadline = '';
    public $member_ids = [];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:' . implode(',', \App\Models\Project::STATUSES),
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

        return view('livewire.project.create-project', compact('tenantUsers'));
    }
}
