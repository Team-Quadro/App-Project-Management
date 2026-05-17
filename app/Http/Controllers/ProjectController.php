<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
    ) {}

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request)
    {
        $tenantUsers = \App\Models\User::where('tenant_id', $request->user()->tenant_id)
            ->where('id', '!=', $request->user()->id)
            ->orderBy('name')
            ->get();

        return view('projects.create', compact('tenantUsers'));
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->create($request->validated(), $request->user());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->load('members');

        $tenantUsers = \App\Models\User::where('tenant_id', $project->tenant_id)
            ->where('id', '!=', $project->owner_id)
            ->orderBy('name')
            ->get();

        return view('projects.edit', compact('project', 'tenantUsers'));
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $this->projectService->update($project, $request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}