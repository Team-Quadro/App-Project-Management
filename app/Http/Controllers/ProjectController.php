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
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $projects = $this->projectService->list($request->user(), $request->only(['search', 'status']));

        return view('projects.index', [
            'projects' => $projects,
            'filters'  => $request->only(['search', 'status']),
        ]);
    }

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
     * Display the specified project.
     */
    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['owner', 'members']);

        // Use a large page size so all tasks are available for client-side grouping
        $tasks = $project->tasks()
            ->search($request->query('search'))
            ->filterStatus($request->query('task_status'))
            ->filterPriority($request->query('priority'))
            ->filterAssignee($request->query('assignee') ? (int) $request->query('assignee') : null)
            ->with('assignee')
            ->orderBy('created_at')
            ->get(); // Use get() instead of paginate so all tasks are available for grouping

        // Users available for task assignment (owner + members)
        $projectUsers = collect([$project->owner])
            ->merge($project->members)
            ->unique('id')
            ->sortBy('name');

        // Use project's tenant_id so superadmin (who has no tenant_id) can still see stages
        $tenantId = auth()->user()->tenant_id ?? $project->tenant_id;
        $workflowStages = \App\Models\WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->orderBy('sort_order')
            ->get();

        return view('projects.show', [
            'project'        => $project,
            'tasks'          => $tasks,
            'projectUsers'   => $projectUsers,
            'workflowStages' => $workflowStages,
            'filters'        => $request->only(['search', 'task_status', 'priority', 'assignee']),
        ]);
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