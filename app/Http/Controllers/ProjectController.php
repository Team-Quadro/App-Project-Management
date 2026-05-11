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
    public function create()
    {
        return view('projects.create');
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


    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        // 1. Hitung total task di project ini untuk basis persentase workload
        $totalProjectTasks = $project->tasks()->count();

        // 2. Load owner dan members BERSERTA jumlah task yang di-assign ke mereka KHUSUS di project ini
        $project->load([
            'owner' => function ($query) use ($project) {
                $query->withCount(['assignedTasks' => function ($q) use ($project) {
                    $q->where('project_id', $project->id);
                }]);
            },
            'members' => function ($query) use ($project) {
                $query->withCount(['assignedTasks' => function ($q) use ($project) {
                    $q->where('project_id', $project->id);
                }]);
            }
        ]);

        $tasks = $project->tasks()
            ->search($request->query('search'))
            ->filterStatus($request->query('task_status'))
            ->filterPriority($request->query('priority'))
            ->filterAssignee($request->query('assignee') ? (int) $request->query('assignee') : null)
            ->with('assignee')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 3. Gabungkan users, hilangkan duplikat, dan kalkulasi persentase workload-nya
        $projectUsers = collect([$project->owner])
            ->merge($project->members)
            ->unique('id')
            ->map(function ($user) use ($totalProjectTasks) {
                // Kalkulasi workload: (Tugas User di Project / Total Tugas Project) * 100
                $user->workload_percentage = $totalProjectTasks > 0 
                    ? round(($user->assigned_tasks_count / $totalProjectTasks) * 100) 
                    : 0;
                
                return $user;
            })
            ->sortBy('name');

        return view('projects.show', [
            'project'      => $project,
            'tasks'        => $tasks,
            'projectUsers' => $projectUsers,
            'filters'      => $request->only(['search', 'task_status', 'priority', 'assignee']),
        ]);
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $project->load('members');

        return view('projects.edit', compact('project'));
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