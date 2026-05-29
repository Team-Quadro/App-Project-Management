<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class ActiveTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Tarik semua task di tenant ini yang stage-nya is_active = true
        // Lalu kelompokkan (groupBy) berdasarkan ID atau Nama Project
        $activeTasksGrouped = Task::with(['project', 'stage', 'assignee'])
            ->where('tenant_id', $user->tenant_id)
            ->whereHas('stage', function ($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->groupBy('project.title'); // Mengelompokkan berdasarkan nama project

        return view('tasks.active-monitoring', compact('activeTasksGrouped'));
    }
}
