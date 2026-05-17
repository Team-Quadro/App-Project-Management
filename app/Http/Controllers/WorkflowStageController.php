<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkflowStageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
        ]);

        // Superadmin uses session tenant, regular user uses their own tenant_id
        $tenantId = auth()->user()->tenant_id
            ?? session('superadmin_tenant_id');

        if (!$tenantId) {
            return back()->with('error', 'No subsidiary selected.');
        }

        $maxOrder = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->max('sort_order') ?? 0;

        WorkflowStage::withoutGlobalScopes()->create([
            'tenant_id'  => $tenantId,
            'project_id' => null,
            'name'       => $validated['name'],
            'key'        => Str::slug($validated['name'], '_'),
            'color'      => $validated['color'] ?? '#6B7280',
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Section added successfully.');
    }

    public function destroy(WorkflowStage $workflowStage)
    {
        // Move tasks in this stage to the first available stage
        $tenantId = $workflowStage->tenant_id;
        $fallback = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->where('id', '!=', $workflowStage->id)
            ->orderBy('sort_order')
            ->first();

        if ($fallback) {
            \App\Models\Task::withoutGlobalScopes()
                ->where('stage_id', $workflowStage->id)
                ->update([
                    'stage_id' => $fallback->id,
                    'status'   => $fallback->key,
                ]);
        }

        $workflowStage->delete();

        return back()->with('success', 'Section deleted.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:workflow_stages,id',
        ]);

        foreach ($validated['order'] as $index => $stageId) {
            WorkflowStage::withoutGlobalScopes()
                ->where('id', $stageId)
                ->update(['sort_order' => $index + 1]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Sections reordered.');
    }
}
